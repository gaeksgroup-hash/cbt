import { mkdir, writeFile } from 'node:fs/promises';
import process from 'node:process';

const cdpPort = process.env.CBT_CDP_PORT ?? '9223';
const baseUrl = (process.env.CBT_BASE_URL ?? 'http://127.0.0.1:8000').replace(/\/$/, '');
const outputDir = process.env.CBT_QA_OUTPUT ?? 'storage/app/qa';
const pages = await fetch(`http://127.0.0.1:${cdpPort}/json`).then((response) => response.json());
const page = pages.find((candidate) => candidate.type === 'page' && candidate.url === 'about:blank')
    ?? pages.find((candidate) => candidate.type === 'page');
if (!page) throw new Error('No browser page is available for QA.');

const socket = new WebSocket(page.webSocketDebuggerUrl);
const pending = new Map();
let nextId = 1;

await new Promise((resolve, reject) => {
    socket.addEventListener('open', resolve, { once: true });
    socket.addEventListener('error', reject, { once: true });
});

socket.addEventListener('message', (event) => {
    const message = JSON.parse(event.data);
    if (!message.id || !pending.has(message.id)) return;
    const { resolve, reject } = pending.get(message.id);
    pending.delete(message.id);
    message.error ? reject(new Error(message.error.message)) : resolve(message.result);
});

function send(method, params = {}) {
    const id = nextId++;
    socket.send(JSON.stringify({ id, method, params }));
    return new Promise((resolve, reject) => pending.set(id, { resolve, reject }));
}

async function evaluate(expression) {
    const result = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
    if (result.exceptionDetails) throw new Error(result.exceptionDetails.text);
    return result.result.value;
}

async function waitFor(expression, timeout = 10000) {
    const started = Date.now();
    while (Date.now() - started < timeout) {
        if (await evaluate(expression)) return;
        await new Promise((resolve) => setTimeout(resolve, 100));
    }
    throw new Error(`Timed out waiting for: ${expression}`);
}

async function navigate(path) {
    await send('Page.navigate', { url: `${baseUrl}${path}` });
    await waitFor(`document.readyState === 'complete'`);
    await waitFor(`document.fonts?.status === 'loaded'`);
}

async function screenshot(name) {
    const { data } = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: false });
    await writeFile(`${outputDir}/${name}.png`, Buffer.from(data, 'base64'));
}

async function inspect(path, name) {
    await navigate(path);
    await screenshot(name);
    return evaluate(`({
        path: location.pathname,
        title: document.title,
        h1: document.querySelector('h1')?.textContent.trim(),
        viewportWidth: window.innerWidth,
        contentWidth: document.documentElement.scrollWidth,
        hasHorizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
        orderFieldPresent: Boolean(document.querySelector('#order_id'))
    })`);
}

await mkdir(outputDir, { recursive: true });
await send('Page.enable');
await send('Runtime.enable');

const report = {};
await send('Emulation.setDeviceMetricsOverride', { width: 1365, height: 900, deviceScaleFactor: 1, mobile: false });
report.landingDesktop = await inspect('/', 'landing-desktop');
report.sakDesktop = await inspect('/sak', 'sak-desktop');

await send('Emulation.setDeviceMetricsOverride', { width: 390, height: 844, deviceScaleFactor: 1, mobile: true });
report.landingMobile = await inspect('/', 'landing-mobile');
report.sakMobile = await inspect('/sak', 'sak-mobile');

const failures = Object.entries(report).filter(([, item]) => item.hasHorizontalOverflow);
process.stdout.write(`${JSON.stringify(report, null, 2)}\n`);
socket.close();

if (failures.length > 0) {
    throw new Error(`Horizontal overflow detected in: ${failures.map(([name]) => name).join(', ')}`);
}
