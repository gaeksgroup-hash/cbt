@extends('layouts.app')

@section('title', 'Panduan Ujian')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <a href="{{ route('sak.landing') }}" class="text-sm text-[#0F4C5C] underline">Kembali ke SAK</a>
    <div class="bg-white rounded-xl border border-slate-200 p-8 mt-5 shadow-sm">
        <p class="text-xs font-semibold uppercase text-cyan-700 mb-2">Panduan sebelum ujian</p>
        <h1 class="text-2xl font-bold text-[#012E34] mb-6">{{ $exam->title }}</h1>
        <dl class="grid grid-cols-2 gap-4 text-sm mb-7">
            <div><dt class="text-slate-500">Jumlah soal</dt><dd class="font-bold">{{ $questionCount }}</dd></div>
            <div><dt class="text-slate-500">Durasi</dt><dd class="font-bold">{{ $exam->exam_type === 'tryout' ? '2 sesi × 75 menit' : '20 menit' }}</dd></div>
            <div><dt class="text-slate-500">Target</dt><dd class="font-bold">{{ $exam->pass_score }}/100</dd></div>
            <div><dt class="text-slate-500">Attempt sebelumnya</dt><dd class="font-bold">{{ $attemptCount }}</dd></div>
            <div><dt class="text-slate-500">Best score final</dt><dd class="font-bold">{{ $bestScore ?? 'Belum ada' }}</dd></div>
            <div><dt class="text-slate-500">Tipe soal</dt><dd class="font-bold">Pilihan ganda, hitungan, dan/atau uraian</dd></div>
        </dl>
        <p class="text-sm text-slate-600 mb-5">Timer dihitung oleh server sejak attempt dimulai. Refresh atau membuka tab lain tidak menambah waktu. Jawaban uraian memerlukan peninjauan sebelum nilai final.</p>
        <a href="{{ route('sak.exam.demo', $exam) }}" class="inline-block rounded-lg border border-[#012E34] text-[#012E34] font-semibold px-5 py-3 mr-3 mb-3">Lihat contoh</a>
        <label class="flex items-center gap-2 text-sm mb-3"><input type="checkbox" disabled> Saya memahami petunjuk</label>
        <button type="button" disabled aria-disabled="true" class="rounded-lg bg-slate-300 text-slate-700 px-5 py-3 cursor-not-allowed">Mulai attempt — belum tersedia</button>
        <p class="text-xs text-amber-800 mt-3">Engine ujian sedang dibangun. Belum ada attempt atau timer yang dimulai dari halaman ini.</p>
    </div>
</div>
@endsection
