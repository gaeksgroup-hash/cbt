@extends('layouts.app')

@section('title', 'Riwayat Hasil')

@section('content')
<div class="document-page">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('sak.landing') }}">Ujian SAK</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Riwayat hasil</span>
    </nav>

    <header class="grid gap-6 border-b border-stone-400 pb-7 md:grid-cols-[1fr_auto] md:items-end">
        <div>
            <p class="document-kicker">Rekap per Order ID</p>
            <h1 class="mono-data mt-4 break-all text-2xl font-semibold tracking-tight text-[#062F33] sm:text-4xl">{{ $candidate->public_id }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Semua attempt tetap disimpan. Mengerjakan ulang tidak mengganti atau menghapus hasil sebelumnya.</p>
        </div>
        <a href="{{ route('sak.landing') }}" class="button-secondary">Buka paket lain</a>
    </header>

    <dl class="summary-grid" aria-label="Ringkasan hasil Order ID">
        <div class="summary-stat"><dt>Total attempt</dt><dd>{{ $totalAttempts }}</dd></div>
        <div class="summary-stat"><dt>Paket selesai</dt><dd>{{ $completedExams }}</dd></div>
        <div class="summary-stat"><dt>Total skor terbaik</dt><dd>{{ number_format($totalBestScore, 2) }}</dd></div>
        <div class="summary-stat"><dt>Rata-rata terbaik</dt><dd>{{ $averageBestScore === null ? '-' : number_format($averageBestScore, 2) }}</dd></div>
    </dl>

    @if ($tryoutPairs->isNotEmpty())
        <section aria-labelledby="tryout-history-heading">
            <div class="section-rule">
                <span class="section-number">01</span>
                <div>
                    <h2 id="tryout-history-heading" class="section-heading">Rekap Tryout SAK</h2>
                    <p class="section-summary">Sesi 1 dan Sesi 2 masing-masing berbobot 50%. Target gabungan minimal 60 dengan nilai tiap sesi minimal 40.</p>
                </div>
            </div>
            <div class="table-shell mt-6">
                <table class="data-table">
                    <thead><tr><th>Attempt</th><th>Sesi 1</th><th>Sesi 2</th><th>Gabungan</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($tryoutPairs as $pair)
                            <tr>
                                <td class="mono-data font-semibold">#{{ $pair['attempt_no'] }}</td>
                                <td>{{ $pair['s1']?->score_final === null ? 'Menunggu review' : number_format((float) $pair['s1']->score_final, 2) }}</td>
                                <td>{{ $pair['s2']?->score_final === null ? 'Belum selesai' : number_format((float) $pair['s2']->score_final, 2) }}</td>
                                <td class="mono-data font-semibold">{{ $pair['score'] === null ? '-' : number_format((float) $pair['score'], 2) }}</td>
                                <td>{{ ! $pair['is_final'] ? 'Belum final' : ($pair['passed'] ? 'Lulus' : 'Belum lulus') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section aria-labelledby="attempt-history-heading">
        <div class="section-rule">
            <span class="section-number">{{ $tryoutPairs->isNotEmpty() ? '02' : '01' }}</span>
            <div>
                <h2 id="attempt-history-heading" class="section-heading">Riwayat attempt</h2>
                <p class="section-summary">Urutan pengerjaan, status penilaian, dan skor tiap paket.</p>
            </div>
        </div>

        @if ($attempts->isEmpty())
            <div class="mt-6 border border-dashed border-stone-500 bg-[#FBF9F3] p-8 text-center">
                <p class="font-semibold text-[#062F33]">Belum ada hasil ujian.</p>
                <p class="mt-2 text-sm text-slate-600">Masukkan token buku untuk membuka panduan dan memulai attempt pertama.</p>
            </div>
        @else
            <div class="table-shell mt-6">
                <table class="data-table">
                    <thead><tr><th>Paket</th><th>Attempt</th><th>Skor</th><th>Status</th><th>Tanggal</th><th><span class="sr-only">Aksi</span></th></tr></thead>
                    <tbody>
                        @foreach ($attempts as $attempt)
                            <tr>
                                <td class="font-semibold text-[#062F33]">{{ $attempt->exam->title }}{{ $attempt->session_code ? ' / '.$attempt->session_code : '' }}</td>
                                <td class="mono-data">#{{ $attempt->attempt_no }}</td>
                                <td class="mono-data">{{ $attempt->score_final === null ? 'Menunggu' : number_format((float) $attempt->score_final, 2) }}</td>
                                <td>{{ $attempt->grading_status === 'final' ? ($attempt->passed ? 'Target tercapai' : 'Perlu diulang') : 'Menunggu review' }}</td>
                                <td class="whitespace-nowrap">{{ $attempt->submitted_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                                <td><a class="font-semibold text-[#07575D] underline underline-offset-4" href="{{ route('sak.attempts.result', $attempt) }}">Lihat hasil</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
