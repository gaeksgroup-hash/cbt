@extends('layouts.app')

@section('title', 'Riwayat Hasil')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-cyan-800">Ringkasan User ID</p>
            <h1 class="text-3xl font-bold text-[#012E34]">{{ $candidate->public_id }}</h1>
            <p class="mt-2 text-sm text-slate-600">Semua attempt disimpan. Mengulang ujian tidak menghapus hasil lama.</p>
        </div>
        <a href="{{ route('sak.landing') }}" class="inline-flex min-h-11 items-center font-semibold text-cyan-800 underline underline-offset-4">Buka paket lain</a>
    </div>

    <section class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Total hasil User ID">
        <div class="rounded-xl border border-slate-200 bg-white p-5"><p class="text-xs font-semibold text-slate-600">Total attempt</p><p class="mt-2 text-3xl font-bold text-[#012E34]">{{ $totalAttempts }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5"><p class="text-xs font-semibold text-slate-600">Paket selesai</p><p class="mt-2 text-3xl font-bold text-[#012E34]">{{ $completedExams }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5"><p class="text-xs font-semibold text-slate-600">Total best score</p><p class="mt-2 text-3xl font-bold text-[#012E34]">{{ number_format($totalBestScore, 2) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5"><p class="text-xs font-semibold text-slate-600">Rata-rata best score</p><p class="mt-2 text-3xl font-bold text-[#012E34]">{{ $averageBestScore === null ? '-' : number_format($averageBestScore, 2) }}</p></div>
    </section>

    @if ($tryoutPairs->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-xl font-bold text-[#012E34]">Rekap Try Out SAK</h2>
            <p class="mt-1 text-sm text-slate-600">Sesi 1 dan Sesi 2 masing-masing berbobot 50%. Syarat lulus: nilai gabungan minimal 60 dan setiap sesi minimal 40.</p>
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-700"><tr><th class="px-4 py-3">Attempt</th><th class="px-4 py-3">Sesi 1</th><th class="px-4 py-3">Sesi 2</th><th class="px-4 py-3">Gabungan</th><th class="px-4 py-3">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($tryoutPairs as $pair)
                            <tr>
                                <td class="px-4 py-3 font-semibold">#{{ $pair['attempt_no'] }}</td>
                                <td class="px-4 py-3">{{ $pair['s1']?->score_final === null ? 'Menunggu review' : number_format((float) $pair['s1']->score_final, 2) }}</td>
                                <td class="px-4 py-3">{{ $pair['s2']?->score_final === null ? 'Belum selesai' : number_format((float) $pair['s2']->score_final, 2) }}</td>
                                <td class="px-4 py-3 font-bold">{{ $pair['score'] === null ? '-' : number_format((float) $pair['score'], 2) }}</td>
                                <td class="px-4 py-3">{{ ! $pair['is_final'] ? 'Belum final' : ($pair['passed'] ? 'Lulus' : 'Belum lulus') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section class="mt-8">
        <h2 class="text-xl font-bold text-[#012E34]">Riwayat attempt</h2>
        @if ($attempts->isEmpty())
            <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
                <p class="font-semibold text-slate-800">Belum ada hasil ujian.</p>
                <p class="mt-1 text-sm text-slate-600">Masukkan token bab untuk membuka panduan dan memulai attempt pertama.</p>
            </div>
        @else
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-700"><tr><th class="px-4 py-3">Paket</th><th class="px-4 py-3">Attempt</th><th class="px-4 py-3">Skor</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3"><span class="sr-only">Aksi</span></th></tr></thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($attempts as $attempt)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-[#012E34]">{{ $attempt->exam->title }}{{ $attempt->session_code ? ' · '.$attempt->session_code : '' }}</td>
                                <td class="px-4 py-3">#{{ $attempt->attempt_no }}</td>
                                <td class="px-4 py-3">{{ $attempt->score_final === null ? 'Menunggu' : number_format((float) $attempt->score_final, 2) }}</td>
                                <td class="px-4 py-3">{{ $attempt->grading_status === 'final' ? ($attempt->passed ? 'Target tercapai' : 'Perlu diulang') : 'Menunggu review' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $attempt->submitted_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3"><a class="font-semibold text-cyan-800 underline" href="{{ route('sak.attempts.result', $attempt) }}">Lihat</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
