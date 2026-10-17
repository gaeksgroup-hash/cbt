@extends('layouts.app')

@section('title', 'Hasil Ujian')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <a href="{{ route('sak.results') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-cyan-800 underline underline-offset-4">Lihat semua riwayat</a>
    <section class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-[#012E34] p-6 text-white sm:p-8">
            <p class="text-sm font-semibold text-cyan-100">Attempt #{{ $attempt->attempt_no }}{{ $attempt->session_code ? ' · '.$attempt->session_code : '' }}</p>
            <h1 class="mt-1 text-2xl font-bold">{{ $attempt->exam->title }}</h1>
            <p class="mt-2 text-sm text-slate-200">{{ $attempt->status === 'timed_out' ? 'Waktu habis dan jawaban dikumpulkan otomatis.' : 'Jawaban berhasil dikumpulkan.' }}</p>
        </div>

        <div class="p-6 sm:p-8">
            @if ($attempt->grading_status === 'final')
                <div class="grid gap-5 sm:grid-cols-[12rem_1fr] sm:items-center">
                    <div class="rounded-xl bg-slate-100 p-6 text-center">
                        <p class="text-sm font-semibold text-slate-600">Skor final</p>
                        <p class="mt-1 text-5xl font-bold text-[#012E34]">{{ number_format((float) $attempt->score_final, 0) }}</p>
                        <p class="text-sm text-slate-600">dari 100</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold {{ $attempt->passed ? 'text-emerald-700' : 'text-amber-800' }}">{{ $attempt->passed ? 'Target tercapai' : 'Target belum tercapai' }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Target paket ini {{ number_format((float) $attempt->exam->pass_score, 0) }}/100. Anda dapat mengulang tanpa menghapus hasil sebelumnya.</p>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <h2 class="font-bold text-amber-950">Menunggu peninjauan jawaban uraian</h2>
                    <p class="mt-1 text-sm leading-6 text-amber-900">Nilai otomatis untuk bagian objektif: <strong>{{ $attempt->score_auto === null ? 'Belum tersedia' : number_format((float) $attempt->score_auto, 2).'/100' }}</strong>. Nilai final belum diterbitkan agar penilaian uraian tidak dipalsukan sebagai hasil otomatis.</p>
                </div>
            @endif

            @if ($tryoutSummary)
                <div class="mt-7 rounded-xl border border-cyan-200 bg-cyan-50 p-5">
                    <h2 class="font-bold text-[#012E34]">Rekap Try Out Attempt #{{ $tryoutSummary['attempt_no'] }}</h2>
                    @if ($tryoutSummary['is_final'])
                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                            <div><p class="text-xs font-semibold text-slate-600">Sesi 1</p><p class="text-2xl font-bold">{{ number_format((float) $tryoutSummary['s1']->score_final, 2) }}</p></div>
                            <div><p class="text-xs font-semibold text-slate-600">Sesi 2</p><p class="text-2xl font-bold">{{ number_format((float) $tryoutSummary['s2']->score_final, 2) }}</p></div>
                            <div><p class="text-xs font-semibold text-slate-600">Nilai gabungan 50:50</p><p class="text-2xl font-bold">{{ number_format((float) $tryoutSummary['score'], 2) }}</p></div>
                        </div>
                        <p class="mt-3 text-sm font-semibold {{ $tryoutSummary['passed'] ? 'text-emerald-800' : 'text-amber-900' }}">{{ $tryoutSummary['passed'] ? 'Lulus simulasi SAK' : 'Belum lulus: nilai gabungan minimal 60 dan setiap sesi minimal 40.' }}</p>
                    @else
                        <p class="mt-2 text-sm leading-6 text-slate-700">Nilai gabungan diterbitkan setelah Sesi 1 selesai ditinjau dan Sesi 2 selesai dinilai. Bobot tiap sesi 50%.</p>
                    @endif
                </div>
            @endif

            <dl class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs text-slate-600">Terjawab</dt><dd class="mt-1 text-xl font-bold">{{ $answered }}</dd></div>
                <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs text-slate-600">Benar otomatis</dt><dd class="mt-1 text-xl font-bold">{{ $correct }}</dd></div>
                <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs text-slate-600">Salah otomatis</dt><dd class="mt-1 text-xl font-bold">{{ $incorrect }}</dd></div>
                <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs text-slate-600">Waktu terpakai</dt><dd class="mt-1 text-xl font-bold">{{ intdiv((int) $attempt->duration_used_seconds, 60) }}m {{ (int) $attempt->duration_used_seconds % 60 }}d</dd></div>
            </dl>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('sak.exam.guide', $attempt->exam) }}" class="inline-flex min-h-11 items-center rounded-lg bg-[#012E34] px-5 font-bold text-white hover:bg-[#0F4C5C]">Ulangi paket ini</a>
                <a href="{{ route('sak.results') }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 px-5 font-semibold text-slate-800 hover:bg-slate-50">Riwayat User ID</a>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-lg font-bold text-[#012E34]">Riwayat paket ini</h2>
        <div class="mt-3 overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-700"><tr><th class="px-4 py-3">Attempt</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Skor</th><th class="px-4 py-3">Tanggal</th></tr></thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($history as $item)
                        <tr><td class="px-4 py-3 font-semibold">#{{ $item->attempt_no }}</td><td class="px-4 py-3">{{ $item->grading_status === 'final' ? ($item->passed ? 'Target tercapai' : 'Perlu diulang') : 'Menunggu review' }}</td><td class="px-4 py-3">{{ $item->score_final === null ? '-' : number_format((float) $item->score_final, 2) }}</td><td class="px-4 py-3">{{ $item->submitted_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
