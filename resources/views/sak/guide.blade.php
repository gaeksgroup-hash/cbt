@extends('layouts.app')

@section('title', 'Panduan Ujian')

@section('content')
<div class="document-page max-w-5xl">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('sak.landing') }}">Ujian SAK</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Panduan</span>
    </nav>

    <section class="content-sheet content-sheet--white" aria-labelledby="guide-title">
        <div class="flex flex-col gap-5 border-b border-stone-300 pb-7 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="document-kicker">Panduan sebelum ujian</p>
                <h1 id="guide-title" class="mt-4 max-w-2xl text-3xl font-bold tracking-tight text-[#062F33] sm:text-4xl">{{ $exam->title }}</h1>
            </div>
            <span class="status-stamp shrink-0">Akses valid</span>
        </div>

        <dl class="summary-grid" aria-label="Ringkasan paket">
            <div class="summary-stat"><dt>Jumlah soal</dt><dd>{{ $questionCount }}</dd></div>
            <div class="summary-stat"><dt>Durasi</dt><dd>{{ $exam->exam_type === 'tryout' ? '2 × 75m' : (int) ($exam->duration_seconds / 60).'m' }}</dd></div>
            <div class="summary-stat"><dt>Target</dt><dd>{{ number_format((float) $exam->pass_score, 0) }}</dd></div>
            <div class="summary-stat"><dt>Attempt</dt><dd>{{ $attemptCount }}</dd></div>
        </dl>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_18rem]">
            <div>
                <h2 class="text-xl font-bold text-[#062F33]">Ketentuan pengerjaan</h2>
                <ol class="file-list mt-3">
                    <li><span>01</span><span>Timer dihitung oleh server dan tetap berjalan jika halaman dimuat ulang.</span></li>
                    <li><span>02</span><span>Jawaban disimpan otomatis setelah dipilih atau selesai diketik.</span></li>
                    <li><span>03</span><span>Gunakan penanda untuk soal yang ingin diperiksa kembali sebelum dikumpulkan.</span></li>
                    <li><span>04</span><span>Jawaban uraian menunggu peninjauan sebelum skor final diterbitkan.</span></li>
                </ol>
            </div>
            <div class="border-l-0 border-stone-300 lg:border-l lg:pl-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Skor terbaik saat ini</p>
                <p class="mono-data mt-2 text-4xl font-semibold text-[#062F33]">{{ $bestScore === null ? '-' : number_format((float) $bestScore, 2) }}</p>
                <p class="mt-2 text-sm leading-6 text-slate-600">Mengulang paket tidak menghapus hasil lama.</p>
                <a href="{{ route('sak.exam.demo', $exam) }}" class="button-secondary mt-5 w-full">Lihat contoh navigasi</a>
            </div>
        </div>

        @if ($errors->has('attempt'))
            <div role="alert" class="alert-error mt-6">{{ $errors->first('attempt') }}</div>
        @endif

        <div class="mt-8 border-t border-stone-300 pt-7">
            @if ($exam->exam_type === 'tryout')
                <h2 class="text-xl font-bold text-[#062F33]">Pilih sesi yang akan dimulai</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Masing-masing sesi memiliki timer 75 menit dan dikumpulkan secara terpisah.</p>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    @foreach (['S1' => 'Uraian dan hitungan', 'S2' => '30 pilihan ganda'] as $sessionCode => $label)
                        <form method="post" action="{{ route('sak.exam.start', ['exam' => $exam, 'sessionCode' => $sessionCode]) }}" class="border border-stone-400 bg-[#FBF9F3] p-5">
                            @csrf
                            <p class="mono-data text-xs font-semibold text-[#9A5D0B]">SESI {{ $sessionCode }}</p>
                            <h3 class="mt-2 text-lg font-bold text-[#062F33]">{{ $label }}</h3>
                            <label class="mt-5 flex min-h-11 items-start gap-3 text-sm leading-6"><input name="understood" value="1" type="checkbox" required class="mt-1 h-5 w-5"> <span>Saya memahami ketentuan dan siap memulai timer.</span></label>
                            <button class="button-primary mt-4 w-full">Mulai sesi {{ $sessionCode }}</button>
                        </form>
                    @endforeach
                </div>
            @else
                <form method="post" action="{{ route('sak.exam.start', $exam) }}">
                    @csrf
                    <label class="flex min-h-11 items-start gap-3 text-sm leading-6"><input name="understood" value="1" type="checkbox" required class="mt-1 h-5 w-5"> <span>Saya memahami ketentuan dan siap memulai timer {{ (int) ($exam->duration_seconds / 60) }} menit.</span></label>
                    <button class="button-primary mt-5">Mulai attempt berikutnya</button>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection
