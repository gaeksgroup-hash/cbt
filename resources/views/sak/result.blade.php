@extends('layouts.app')

@section('title', 'Hasil Ujian')

@section('content')
<div class="document-page max-w-5xl">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('sak.results') }}">Riwayat hasil</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Attempt #{{ $attempt->attempt_no }}</span>
    </nav>

    <section class="content-sheet content-sheet--white" aria-labelledby="result-title">
        <header class="grid gap-6 border-b border-stone-300 pb-7 sm:grid-cols-[1fr_auto] sm:items-start">
            <div>
                <p class="document-kicker">Hasil attempt {{ $attempt->session_code ? '/ '.$attempt->session_code : '' }}</p>
                <h1 id="result-title" class="mt-4 text-3xl font-bold tracking-tight text-[#062F33]">{{ $attempt->exam->title }}</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $attempt->status === 'timed_out' ? 'Waktu habis. Jawaban dikumpulkan otomatis oleh server.' : 'Jawaban berhasil dikumpulkan.' }}</p>
            </div>
            <span class="mono-data border border-stone-400 px-3 py-2 text-sm font-semibold text-[#062F33]">ATTEMPT #{{ $attempt->attempt_no }}</span>
        </header>

        @if ($attempt->grading_status === 'final')
            <div class="mt-8 grid gap-7 sm:grid-cols-[13rem_1fr] sm:items-center">
                <div class="border-2 border-[#062F33] bg-[#F3EFE5] p-6 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Skor final</p>
                    <p class="mono-data mt-2 text-6xl font-semibold text-[#062F33]">{{ number_format((float) $attempt->score_final, 0) }}</p>
                    <p class="text-sm text-slate-600">dari 100</p>
                </div>
                <div>
                    <span class="status-stamp {{ $attempt->passed ? '' : '!text-[#9A5D0B]' }}">{{ $attempt->passed ? 'Target tercapai' : 'Perlu diulang' }}</span>
                    <h2 class="mt-4 text-2xl font-bold text-[#062F33]">{{ $attempt->passed ? 'Pemahaman memenuhi target paket.' : 'Skor belum mencapai target paket.' }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Target paket ini {{ number_format((float) $attempt->exam->pass_score, 0) }} dari 100. Anda dapat mengulang tanpa menghapus attempt ini.</p>
                </div>
            </div>
        @else
            <div class="alert-warning mt-8 mb-0">
                <strong>Nilai sedang dihitung ulang.</strong><br>
                Muat ulang halaman ini beberapa saat lagi. Tidak diperlukan peninjauan manual.
            </div>
        @endif

        @if ($tryoutSummary)
            <div class="mt-8 border-t border-stone-300 pt-7">
                <h2 class="text-xl font-bold text-[#062F33]">Rekap Tryout Attempt #{{ $tryoutSummary['attempt_no'] }}</h2>
                @if ($tryoutSummary['is_final'])
                    <dl class="summary-grid mt-4 !grid-cols-3">
                        <div class="summary-stat"><dt>Sesi 1</dt><dd>{{ number_format((float) $tryoutSummary['s1']->score_final, 2) }}</dd></div>
                        <div class="summary-stat"><dt>Sesi 2</dt><dd>{{ number_format((float) $tryoutSummary['s2']->score_final, 2) }}</dd></div>
                        <div class="summary-stat"><dt>Gabungan 50:50</dt><dd>{{ number_format((float) $tryoutSummary['score'], 2) }}</dd></div>
                    </dl>
                    <p class="mt-4 text-sm font-semibold {{ $tryoutSummary['passed'] ? 'text-emerald-800' : 'text-amber-900' }}">{{ $tryoutSummary['passed'] ? 'Lulus simulasi SAK.' : 'Belum lulus. Nilai gabungan minimal 60 dan setiap sesi minimal 40.' }}</p>
                @else
                    <p class="mt-2 text-sm leading-6 text-slate-600">Nilai gabungan tersedia setelah kedua sesi selesai dikerjakan dan dinilai otomatis.</p>
                @endif
            </div>
        @endif

        <dl class="summary-grid mt-8" aria-label="Rincian pengerjaan">
            <div class="summary-stat"><dt>Terjawab</dt><dd>{{ $answered }}</dd></div>
            <div class="summary-stat"><dt>Benar otomatis</dt><dd>{{ $correct }}</dd></div>
            <div class="summary-stat"><dt>Salah otomatis</dt><dd>{{ $incorrect }}</dd></div>
            <div class="summary-stat"><dt>Waktu dipakai</dt><dd>{{ intdiv((int) $attempt->duration_used_seconds, 60) }}m {{ (int) $attempt->duration_used_seconds % 60 }}d</dd></div>
        </dl>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('sak.exam.guide', $attempt->exam) }}" class="button-primary">Ulangi paket ini</a>
            <a href="{{ route('sak.results') }}" class="button-secondary">Lihat seluruh riwayat</a>
        </div>
    </section>

    <section aria-labelledby="package-history-heading">
        <div class="section-rule">
            <span class="section-number">01</span>
            <div>
                <h2 id="package-history-heading" class="section-heading">Riwayat paket ini</h2>
                <p class="section-summary">Bandingkan perkembangan skor dari setiap pengulangan.</p>
            </div>
        </div>
        <div class="table-shell mt-6">
            <table class="data-table">
                <thead><tr><th>Attempt</th><th>Status</th><th>Skor</th><th>Tanggal</th></tr></thead>
                <tbody>
                    @foreach ($history as $item)
                        <tr><td class="mono-data font-semibold">#{{ $item->attempt_no }}</td><td>{{ $item->grading_status === 'final' ? ($item->passed ? 'Target tercapai' : 'Perlu diulang') : 'Sedang dihitung ulang' }}</td><td class="mono-data">{{ $item->score_final === null ? '-' : number_format((float) $item->score_final, 2) }}</td><td>{{ $item->submitted_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section aria-labelledby="answer-review-heading">
        <div class="section-rule">
            <span class="section-number">02</span>
            <div>
                <h2 id="answer-review-heading" class="section-heading">Pembahasan jawaban</h2>
                <p class="section-summary">Bandingkan jawaban Anda dengan kunci dan penjelasan yang digunakan sistem untuk menghitung skor.</p>
            </div>
        </div>
        <div class="mt-6 space-y-5">
            @foreach ($attempt->attemptQuestions as $index => $attemptQuestion)
                @php
                    $snapshot = $attemptQuestion->question_snapshot_json;
                    $answer = $attemptQuestion->answer;
                    $type = $snapshot['type'] ?? '';
                    $selected = $answer?->answer_json['selected'] ?? null;
                    $value = $answer?->answer_json['value'] ?? null;
                    $correctKey = $snapshot['answer_key']['correct_options'][0] ?? null;
                    $expected = $snapshot['answer_key']['expected'] ?? null;
                @endphp
                <article class="border border-stone-400 bg-white p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><p class="mono-data text-xs font-semibold text-[#9A5D0B]">SOAL {{ $index + 1 }}</p><h3 class="mt-2 font-bold text-[#062F33]">{{ $snapshot['title'] ?: ($snapshot['code'] ?? 'Pembahasan') }}</h3></div>
                        <span class="admin-status {{ $answer?->is_correct ? 'admin-status--active' : 'admin-status--suspended' }}">{{ number_format((float) ($answer?->awarded_points ?? 0), 2) }} / {{ number_format((float) $attemptQuestion->max_points, 2) }}</span>
                    </div>
                    <div class="mt-4 text-sm leading-6 text-slate-700">
                        @if ($type === 'single_choice')
                            <p><strong>Jawaban Anda:</strong> {{ $selected ?: 'Tidak dijawab' }}</p>
                            <p><strong>Kunci:</strong> {{ $correctKey ?: '-' }}</p>
                        @elseif ($type === 'numeric')
                            <p><strong>Jawaban Anda:</strong> {{ $value ?? 'Tidak dijawab' }}</p>
                            <p><strong>Nilai acuan:</strong> {{ $expected ?? '-' }}</p>
                        @else
                            <p><strong>Penilaian:</strong> Skor diberikan untuk setiap unsur kunci yang ditemukan pada jawaban uraian.</p>
                        @endif
                        <p class="mt-3"><strong>Pembahasan:</strong> {{ $snapshot['explanation'] ?: 'Pembahasan belum tersedia.' }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
