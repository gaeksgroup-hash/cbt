@extends('layouts.app')

@section('title', 'Contoh Penggunaan CBT')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <a href="{{ route('sak.exam.guide', $exam) }}" class="text-sm text-[#0F4C5C] underline">Kembali ke panduan</a>
    <div class="bg-white rounded-xl border border-slate-200 p-8 mt-5 shadow-sm">
        <h1 class="text-2xl font-bold text-[#012E34] mb-3">Contoh penggunaan</h1>
        <p class="text-sm text-slate-600 mb-7">Ini hanya latihan navigasi. Soal demo tidak berasal dari bank soal dan tidak dinilai. Timer belum berjalan.</p>
        <div class="space-y-6">
            <fieldset class="border border-slate-200 rounded-lg p-5">
                <legend class="font-semibold px-2">Contoh 1 dari 2</legend>
                <p class="mb-3">Tombol mana yang dipakai untuk memilih jawaban?</p>
                <label class="block mb-2"><input type="radio" name="demo_one"> Pilihan jawaban</label>
                <label class="block"><input type="radio" name="demo_one"> Indikator waktu</label>
            </fieldset>
            <fieldset class="border border-slate-200 rounded-lg p-5">
                <legend class="font-semibold px-2">Contoh 2 dari 2</legend>
                <p class="mb-3">Anda boleh menandai soal untuk ditinjau dan kembali dengan tombol Sebelumnya / Berikutnya atau navigator. Nomor yang sudah dijawab akan diberi indikator.</p>
                <label><input type="checkbox"> Tandai untuk ditinjau (contoh)</label>
            </fieldset>
        </div>
        <p class="text-sm text-slate-600 mt-6">Saat ujian, countdown ada di bagian atas. Jawaban tersimpan ke server; konfirmasi submit merangkum soal dijawab, kosong, dan bertanda. Setelah waktu habis, server akan menutup attempt meski halaman tidak aktif.</p>
    </div>
</div>
@endsection
