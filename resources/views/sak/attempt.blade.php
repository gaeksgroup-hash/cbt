@extends('layouts.app')

@section('title', 'Mengerjakan Ujian')

@section('content')
@php
    $snapshot = $current->question_snapshot_json;
    $saved = $current->answer?->answer_json;
    $savedValue = $snapshot['type'] === 'single_choice' ? ($saved['selected'] ?? null) : ($saved['value'] ?? null);
    $unanswered = $questions->count() - $answered;
    $marked = $questions->filter(fn ($question) => $question->answer?->is_marked_review)->count();
@endphp
<div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-8 lg:px-8">
    <header class="sticky top-16 z-30 -mx-4 mb-6 border-y border-slate-200 bg-white px-4 py-3 sm:mx-0 sm:rounded-lg sm:border sm:px-5">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="truncate text-xs font-semibold text-slate-600">{{ $attempt->exam->title }}</p>
                <p class="text-sm font-bold text-[#012E34]">Attempt #{{ $attempt->attempt_no }}{{ $attempt->session_code ? ' · '.$attempt->session_code : '' }}</p>
            </div>
            <div class="shrink-0 text-right" aria-live="polite">
                <p class="text-xs font-medium text-slate-600">Waktu tersisa</p>
                <p id="timer" data-expires="{{ $attempt->expires_at->toIso8601String() }}" class="font-mono text-xl font-bold tabular-nums text-[#012E34]">--:--</p>
            </div>
        </div>
        <div id="progress-track" class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200" aria-label="{{ $answered }} dari {{ $questions->count() }} soal terjawab">
            <div id="progress-bar" class="h-full bg-[#0891B2]" style="width: {{ $questions->count() ? ($answered / $questions->count()) * 100 : 0 }}%"></div>
        </div>
        <p id="progress-label" class="mt-1 text-xs text-slate-600">{{ $answered }} dari {{ $questions->count() }} terjawab</p>
    </header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8" aria-labelledby="question-title">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <p class="text-sm font-semibold text-cyan-800">Soal {{ $requested }} dari {{ $questions->count() }}</p>
                    <h1 id="question-title" class="mt-1 text-xl font-bold text-[#012E34]">{{ $snapshot['title'] ?: 'Pertanyaan' }}</h1>
                </div>
                <span id="save-state" role="status" class="shrink-0 text-xs font-medium text-slate-600">Tersimpan</span>
            </div>

            <p class="mt-6 whitespace-pre-line text-base leading-7 text-slate-800">{{ $snapshot['stem'] }}</p>

            <form id="answer-form" data-save-url="{{ route('sak.attempts.save', [$attempt, $current]) }}" class="mt-6 space-y-3">
                @csrf
                @if ($snapshot['type'] === 'single_choice')
                    @foreach ($snapshot['options'] as $option)
                        <label class="flex min-h-14 cursor-pointer items-start gap-3 rounded-lg border border-slate-300 p-4 hover:border-[#0891B2] has-[:checked]:border-[#0891B2] has-[:checked]:bg-cyan-50">
                            <input type="radio" name="value" value="{{ $option['key'] }}" @checked($savedValue === $option['key']) class="mt-1 h-5 w-5 border-slate-400 text-[#012E34] focus:ring-[#0891B2]">
                            <span><strong class="mr-2 text-[#012E34]">{{ $option['key'] }}.</strong>{{ $option['text'] }}</span>
                        </label>
                    @endforeach
                @elseif ($snapshot['type'] === 'numeric')
                    <label for="numeric-answer" class="block text-sm font-semibold">Jawaban angka</label>
                    <input id="numeric-answer" name="value" type="number" step="any" value="{{ $savedValue }}" class="min-h-12 w-full rounded-lg border border-slate-300 px-4 focus:border-[#0891B2] focus:ring-2 focus:ring-[#0891B2]/30">
                @else
                    <label for="essay-answer" class="block text-sm font-semibold">Jawaban uraian</label>
                    <textarea id="essay-answer" name="value" rows="10" class="w-full rounded-lg border border-slate-300 p-4 leading-6 focus:border-[#0891B2] focus:ring-2 focus:ring-[#0891B2]/30">{{ $savedValue }}</textarea>
                @endif
                <label class="flex min-h-11 items-center gap-3 text-sm font-medium text-slate-700">
                    <input id="marked" type="checkbox" @checked($current->answer?->is_marked_review) class="h-5 w-5 rounded border-slate-400 text-amber-600 focus:ring-amber-500">
                    Tandai untuk ditinjau
                </label>
                <div id="save-error" role="alert" class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">Jawaban belum tersimpan. Periksa koneksi lalu coba lagi.</div>
            </form>

            <div class="mt-7 flex flex-wrap justify-between gap-3 border-t border-slate-200 pt-5">
                @if ($requested > 1)
                    <a href="{{ route('sak.attempts.show', ['attempt' => $attempt, 'q' => $requested - 1]) }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 px-5 font-semibold text-slate-800 hover:bg-slate-50">Sebelumnya</a>
                @else
                    <span></span>
                @endif
                @if ($requested < $questions->count())
                    <a href="{{ route('sak.attempts.show', ['attempt' => $attempt, 'q' => $requested + 1]) }}" class="inline-flex min-h-11 items-center rounded-lg bg-[#012E34] px-5 font-semibold text-white hover:bg-[#0F4C5C]">Berikutnya</a>
                @else
                    <button type="button" id="open-submit" class="min-h-11 rounded-lg bg-[#012E34] px-5 font-semibold text-white hover:bg-[#0F4C5C]">Periksa dan kumpulkan</button>
                @endif
            </div>
        </section>

        <aside class="rounded-xl border border-slate-200 bg-white p-5 lg:sticky lg:top-44 lg:self-start" aria-label="Navigator soal">
            <h2 class="font-bold text-[#012E34]">Navigator soal</h2>
            <p class="mt-1 text-xs text-slate-600">Kotak berwarna berarti sudah dijawab. Garis kuning berarti ditandai.</p>
            <div class="mt-4 grid grid-cols-5 gap-2">
                @foreach ($questions as $index => $question)
                    @php $isAnswered = filled($question->answer?->answer_json); @endphp
                    <a href="{{ route('sak.attempts.show', ['attempt' => $attempt, 'q' => $index + 1]) }}" @if ($requested === $index + 1) id="current-question-link" @endif aria-label="Soal {{ $index + 1 }}{{ $isAnswered ? ', sudah dijawab' : ', belum dijawab' }}{{ $question->answer?->is_marked_review ? ', ditandai' : '' }}" class="flex aspect-square min-h-11 items-center justify-center rounded-md border-2 text-sm font-bold {{ $requested === $index + 1 ? 'border-[#012E34] bg-[#012E34] text-white' : ($isAnswered ? 'border-cyan-700 bg-cyan-50 text-cyan-900' : 'border-slate-300 bg-white text-slate-700') }} {{ $question->answer?->is_marked_review ? 'ring-2 ring-amber-400 ring-offset-1' : '' }}">{{ $index + 1 }}</a>
                @endforeach
            </div>
            <button type="button" id="open-submit-side" class="mt-6 min-h-11 w-full rounded-lg border border-[#012E34] px-4 font-semibold text-[#012E34] hover:bg-slate-50">Kumpulkan ujian</button>
        </aside>
    </div>
</div>

<dialog id="submit-dialog" class="w-[calc(100%-2rem)] max-w-lg rounded-xl p-0 shadow-2xl backdrop:bg-slate-950/50">
    <div class="p-6">
        <h2 class="text-xl font-bold text-[#012E34]">Kumpulkan jawaban?</h2>
        <p class="mt-2 text-sm text-slate-600">Setelah dikumpulkan, jawaban tidak dapat diubah.</p>
        <dl class="mt-5 grid grid-cols-3 gap-3 text-center">
            <div class="rounded-lg bg-emerald-50 p-3"><dt class="text-xs text-emerald-800">Terjawab</dt><dd id="answered-count" class="text-xl font-bold">{{ $answered }}</dd></div>
            <div class="rounded-lg bg-slate-100 p-3"><dt class="text-xs text-slate-700">Kosong</dt><dd id="unanswered-count" class="text-xl font-bold">{{ $unanswered }}</dd></div>
            <div class="rounded-lg bg-amber-50 p-3"><dt class="text-xs text-amber-900">Ditandai</dt><dd id="marked-count" class="text-xl font-bold">{{ $marked }}</dd></div>
        </dl>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button" id="close-submit" class="min-h-11 rounded-lg border border-slate-300 px-5 font-semibold">Kembali memeriksa</button>
            <form id="submit-form" method="post" action="{{ route('sak.attempts.submit', $attempt) }}">@csrf<button class="min-h-11 w-full rounded-lg bg-[#012E34] px-5 font-bold text-white">Ya, kumpulkan</button></form>
        </div>
    </div>
</dialog>

<script>
(() => {
    const form = document.getElementById('answer-form');
    const saveState = document.getElementById('save-state');
    const saveError = document.getElementById('save-error');
    const totalQuestions = {{ $questions->count() }};
    let answeredCount = {{ $answered }};
    let markedCount = {{ $marked }};
    let currentWasAnswered = {{ filled($current->answer?->answer_json) ? 'true' : 'false' }};
    let currentWasMarked = {{ $current->answer?->is_marked_review ? 'true' : 'false' }};
    let saveTimer;
    function updateProgress(answeredNow, markedNow) {
        if (answeredNow !== currentWasAnswered) answeredCount += answeredNow ? 1 : -1;
        if (markedNow !== currentWasMarked) markedCount += markedNow ? 1 : -1;
        currentWasAnswered = answeredNow;
        currentWasMarked = markedNow;

        document.getElementById('progress-label').textContent = `${answeredCount} dari ${totalQuestions} terjawab`;
        document.getElementById('progress-track').setAttribute('aria-label', `${answeredCount} dari ${totalQuestions} soal terjawab`);
        document.getElementById('progress-bar').style.width = `${totalQuestions ? answeredCount / totalQuestions * 100 : 0}%`;
        document.getElementById('answered-count').textContent = answeredCount;
        document.getElementById('unanswered-count').textContent = totalQuestions - answeredCount;
        document.getElementById('marked-count').textContent = markedCount;
        document.getElementById('current-question-link')?.classList.toggle('ring-2', markedNow);
        document.getElementById('current-question-link')?.classList.toggle('ring-amber-400', markedNow);
        document.getElementById('current-question-link')?.classList.toggle('ring-offset-1', markedNow);
    }
    async function saveAnswer() {
        const valueInput = form.querySelector('[name="value"]:checked') || form.querySelector('[name="value"]');
        saveState.textContent = 'Menyimpan...';
        saveError.classList.add('hidden');
        try {
            const response = await fetch(form.dataset.saveUrl, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value},
                body: JSON.stringify({value: valueInput ? valueInput.value : null, marked: document.getElementById('marked').checked})
            });
            if (!response.ok) throw new Error('save failed');
            const saved = await response.json();
            updateProgress(saved.answered, document.getElementById('marked').checked);
            saveState.textContent = 'Tersimpan';
        } catch (error) {
            saveState.textContent = 'Belum tersimpan';
            saveError.classList.remove('hidden');
        }
    }
    form.addEventListener('change', saveAnswer);
    const textInput = form.querySelector('textarea, input[type="number"]');
    if (textInput) textInput.addEventListener('input', () => { clearTimeout(saveTimer); saveTimer = setTimeout(saveAnswer, 700); });

    const dialog = document.getElementById('submit-dialog');
    document.querySelectorAll('#open-submit, #open-submit-side').forEach(button => button?.addEventListener('click', () => dialog.showModal()));
    document.getElementById('close-submit').addEventListener('click', () => dialog.close());

    const timer = document.getElementById('timer');
    const expires = new Date(timer.dataset.expires).getTime();
    const interval = setInterval(() => {
        const remaining = Math.max(0, Math.floor((expires - Date.now()) / 1000));
        const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
        const seconds = String(remaining % 60).padStart(2, '0');
        timer.textContent = `${minutes}:${seconds}`;
        if (remaining <= 0) {
            clearInterval(interval);
            timer.textContent = '00:00';
            document.getElementById('submit-form').submit();
        }
    }, 250);
})();
</script>
@endsection
