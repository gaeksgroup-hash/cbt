import { mkdir, writeFile } from 'node:fs/promises';
import process from 'node:process';

const cdpPort = process.env.CBT_CDP_PORT ?? '9223';
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

async function navigate(url) {
    await send('Page.navigate', { url });
    await waitFor(`document.readyState === 'complete'`);
}

async function screenshot(name) {
    const { data } = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: false });
    await writeFile(`${outputDir}/${name}.png`, Buffer.from(data, 'base64'));
}

await mkdir(outputDir, { recursive: true });
await send('Page.enable');
await send('Runtime.enable');
await send('Emulation.setDeviceMetricsOverride', { width: 1365, height: 900, deviceScaleFactor: 1, mobile: false });

await navigate('http://127.0.0.1:8000/sak');
await screenshot('01-access-desktop');
await evaluate(`(() => {
    document.querySelector('#user_id').value = 'GSAK_CBT001';
    document.querySelector('#token').value = 'SAK-BAB03-UUBC';
    document.querySelector('form').requestSubmit();
    return true;
})()`);
await waitFor(`location.pathname.includes('/guide')`);
await screenshot('02-guide-desktop');
await evaluate(`(() => {
    const form = document.querySelector('form[action*="/start"]');
    form.querySelector('[name="understood"]').checked = true;
    form.requestSubmit();
    return true;
})()`);
await waitFor(`location.pathname.includes('/attempts/')`);
await waitFor(`document.querySelector('#timer')?.textContent !== '--:--'`);
await screenshot('03-attempt-desktop');

await send('Emulation.setDeviceMetricsOverride', { width: 390, height: 844, deviceScaleFactor: 1, mobile: true });
await screenshot('04-attempt-mobile');
await evaluate(`document.querySelector('input[type="radio"]')?.click()`);
await waitFor(`document.querySelector('#save-state')?.textContent === 'Tersimpan'`);
await evaluate(`document.querySelector('#open-submit-side').click()`);
await screenshot('05-submit-dialog-mobile');
await evaluate(`document.querySelector('#submit-form').requestSubmit()`);
await waitFor(`location.pathname.endsWith('/result')`);
await evaluate(`window.scrollTo(0, 0)`);
await screenshot('06-result-mobile');

await send('Emulation.setDeviceMetricsOverride', { width: 1365, height: 900, deviceScaleFactor: 1, mobile: false });
await navigate('http://127.0.0.1:8000/sak/results');
await screenshot('07-history-desktop');

const summary = await evaluate(`({
    path: location.pathname,
    title: document.title,
    h1: document.querySelector('h1')?.textContent.trim(),
    body: document.body.innerText.slice(0, 1000)
})`);
process.stdout.write(`${JSON.stringify(summary, null, 2)}\n`);
socket.close();
