@extends('layouts.app')

@section('title', 'Panduan Ujian')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <a href="{{ route('sak.landing') }}" class="text-sm text-[#0F4C5C] underline">Kembali ke SAK</a>
    <div class="bg-white rounded-xl border border-slate-200 p-8 mt-5 shadow-sm">
        <p class="text-sm font-semibold text-cyan-800 mb-2">Panduan sebelum ujian</p>
        <h1 class="text-2xl font-bold text-[#012E34] mb-6">{{ $exam->title }}</h1>
        <dl class="grid grid-cols-2 gap-4 text-sm mb-7">
            <div><dt class="text-slate-500">Jumlah soal</dt><dd class="font-bold">{{ $questionCount }}</dd></div>
            <div><dt class="text-slate-500">Durasi</dt><dd class="font-bold">{{ $exam->exam_type === 'tryout' ? '2 sesi × 75 menit' : '20 menit' }}</dd></div>
            <div><dt class="text-slate-500">Target</dt><dd class="font-bold">{{ $exam->pass_score }}/100</dd></div>
            <div><dt class="text-slate-500">Attempt sebelumnya</dt><dd class="font-bold">{{ $attemptCount }}</dd></div>
            <div><dt class="text-slate-500">Best score final</dt><dd class="font-bold">{{ $bestScore ?? 'Belum ada' }}</dd></div>
            <div><dt class="text-slate-500">Tipe soal</dt><dd class="font-bold">Pilihan ganda, hitungan, dan/atau uraian</dd></div>
        </dl>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950 mb-6">
            Timer dihitung oleh server dan tidak dapat dihentikan. Refresh tidak mengulang waktu. Soal uraian memerlukan peninjauan sebelum nilai final.
        </div>
        @if ($errors->has('attempt'))
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 mb-5">{{ $errors->first('attempt') }}</div>
        @endif
        <a href="{{ route('sak.exam.demo', $exam) }}" class="inline-flex min-h-11 items-center rounded-lg border border-[#012E34] text-[#012E34] font-semibold px-5 mr-3 mb-5 hover:bg-slate-50">Lihat contoh penggunaan</a>
        @if ($exam->exam_type === 'tryout')
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach (['S1' => 'Sesi 1: Uraian dan hitungan', 'S2' => 'Sesi 2: 30 pilihan ganda'] as $sessionCode => $label)
                    <form method="post" action="{{ route('sak.exam.start', ['exam' => $exam, 'sessionCode' => $sessionCode]) }}" class="rounded-lg border border-slate-200 p-4">
                        @csrf
                        <h2 class="font-bold text-[#012E34]">{{ $label }}</h2>
                        <p class="text-sm text-slate-600 mt-1 mb-4">Durasi 75 menit. Timer dimulai setelah tombol ditekan.</p>
                        <label class="flex gap-3 text-sm mb-4"><input name="understood" value="1" type="checkbox" required class="mt-0.5 h-5 w-5"> <span>Saya memahami petunjuk.</span></label>
                        <button class="min-h-11 w-full rounded-lg bg-[#012E34] px-4 font-semibold text-white hover:bg-[#0F4C5C]">Mulai {{ $sessionCode }}</button>
                    </form>
                @endforeach
            </div>
        @else
            <form method="post" action="{{ route('sak.exam.start', $exam) }}">
                @csrf
                <label class="flex gap-3 text-sm mb-4"><input name="understood" value="1" type="checkbox" required class="mt-0.5 h-5 w-5"> <span>Saya memahami petunjuk dan siap memulai timer {{ (int) ($exam->duration_seconds / 60) }} menit.</span></label>
                <button class="min-h-12 rounded-lg bg-[#012E34] text-white font-bold px-6 hover:bg-[#0F4C5C]">Mulai attempt berikutnya</button>
            </form>
        @endif
    </div>
</div>
@endsection
