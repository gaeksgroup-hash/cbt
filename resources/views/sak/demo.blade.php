@extends('layouts.app')

@section('title', 'Contoh Penggunaan CBT')

@section('content')
<div class="document-page max-w-5xl">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('sak.exam.guide', $exam) }}">Panduan ujian</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Contoh navigasi</span>
    </nav>

    <section class="content-sheet content-sheet--white" aria-labelledby="demo-title">
        <p class="document-kicker">Simulasi antarmuka</p>
        <h1 id="demo-title" class="mt-4 text-3xl font-bold tracking-tight text-[#062F33]">Kenali kontrol sebelum timer berjalan</h1>
        <div class="alert-info mt-6 mb-0">Timer belum berjalan. Halaman ini tidak memakai soal dari bank soal dan tidak menyimpan nilai.</div>

        <div class="mt-8 grid gap-7 lg:grid-cols-[1fr_15rem]">
            <div class="border border-stone-400 bg-[#FBF9F3] p-5 sm:p-7">
                <div class="flex items-center justify-between gap-4 border-b border-stone-300 pb-4">
                    <div><p class="mono-data text-xs font-semibold text-[#0D737B]">SOAL 01 / 02</p><h2 class="mt-1 font-bold text-[#062F33]">Contoh pilihan jawaban</h2></div>
                    <span class="text-xs font-medium text-slate-600">Belum tersimpan</span>
                </div>
                <fieldset class="mt-6">
                    <legend class="text-base leading-7 text-slate-800">Kontrol mana yang digunakan untuk memilih satu jawaban?</legend>
                    <div class="mt-4 space-y-3">
                        <label class="flex min-h-14 cursor-pointer items-center gap-3 border border-stone-400 bg-white p-4 hover:border-[#0D737B] has-[:checked]:border-[#0D737B] has-[:checked]:bg-teal-50"><input type="radio" name="demo_one" class="h-5 w-5"> <span><strong class="mr-2 text-[#062F33]">A.</strong>Tombol pilihan</span></label>
                        <label class="flex min-h-14 cursor-pointer items-center gap-3 border border-stone-400 bg-white p-4 hover:border-[#0D737B] has-[:checked]:border-[#0D737B] has-[:checked]:bg-teal-50"><input type="radio" name="demo_one" class="h-5 w-5"> <span><strong class="mr-2 text-[#062F33]">B.</strong>Indikator waktu</span></label>
                    </div>
                    <label class="mt-5 flex min-h-11 items-center gap-3 text-sm font-medium"><input type="checkbox" class="h-5 w-5"> Tandai untuk ditinjau</label>
                </fieldset>
            </div>

            <aside class="border-l-0 border-stone-300 lg:border-l lg:pl-6" aria-label="Keterangan navigasi">
                <p class="mono-data text-xs font-semibold text-[#9A5D0B]">NAVIGATOR</p>
                <div class="mt-4 grid grid-cols-4 gap-2 lg:grid-cols-3">
                    <span class="grid aspect-square min-h-11 place-items-center bg-[#062F33] font-bold text-white">1</span>
                    <span class="grid aspect-square min-h-11 place-items-center border-2 border-[#0D737B] bg-teal-50 font-bold text-[#062F33]">2</span>
                    <span class="grid aspect-square min-h-11 place-items-center border-2 border-stone-400 bg-white font-bold text-slate-700">3</span>
                </div>
                <ul class="mt-5 space-y-3 text-xs leading-5 text-slate-600">
                    <li><strong class="text-[#062F33]">Gelap:</strong> soal yang sedang dibuka.</li>
                    <li><strong class="text-[#062F33]">Teal:</strong> jawaban sudah tersimpan.</li>
                    <li><strong class="text-[#062F33]">Putih:</strong> soal belum dijawab.</li>
                </ul>
            </aside>
        </div>

        <div class="mt-8 border-t border-stone-300 pt-6">
            <h2 class="text-lg font-bold text-[#062F33]">Saat ujian berlangsung</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Waktu tampil di bagian atas. Jawaban disimpan ke server saat berubah. Sebelum dikumpulkan, ringkasan akan menunjukkan jumlah terjawab, kosong, dan ditandai. Jika waktu habis, server menutup attempt secara otomatis.</p>
            <a href="{{ route('sak.exam.guide', $exam) }}" class="button-primary mt-5">Kembali ke panduan</a>
        </div>
    </section>
</div>
@endsection
