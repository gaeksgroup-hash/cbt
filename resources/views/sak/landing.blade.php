@extends('layouts.app')

@section('title', 'Sertifikasi Ahli Kepabeanan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <nav class="flex items-center text-xs font-medium text-slate-500 space-x-2 mb-8">
        <a href="{{ route('landing') }}" class="hover:text-[#012E34] transition-colors">Platform</a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-800">Sertifikasi Ahli Kepabeanan</span>
    </nav>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 sm:p-10 mb-8">
        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-4">
            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
            Simulasi dan pembelajaran mandiri SAK
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#012E34] tracking-tight mb-3">
            Sertifikasi Ahli Kepabeanan
        </h1>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
            Simulasi pembelajaran mandiri dan uji kompetensi Ahli Kepabeanan berbasis Buku Panduan Resmi GAEKS Publishing.
        </p>

        <h2 class="font-bold text-[#012E34] mb-3">Cara menggunakan CBT</h2>
        <p class="text-sm text-slate-600 mb-6">Masukkan User ID dan token dari materi Anda. Token menentukan paket ujian yang terbuka. Baca panduan dan coba demo sebelum memulai; waktu ujian tidak berhenti saat halaman direfresh.</p>

        @if ($errors->has('access'))
            <div role="alert" class="rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm p-4 mb-5">{{ $errors->first('access') }}</div>
        @endif
        <form action="{{ route('sak.access') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="user_id" class="block text-sm font-semibold mb-1">User ID</label>
                <input id="user_id" name="user_id" type="text" value="{{ old('user_id') }}" autocomplete="username" required maxlength="40" class="w-full rounded-lg border border-slate-300 px-4 py-3 uppercase focus:ring-2 focus:ring-cyan-600" placeholder="GSAK_CBT001">
                @error('user_id') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="token" class="block text-sm font-semibold mb-1">Token ujian</label>
                <input id="token" name="token" type="text" required maxlength="100" autocomplete="off" class="w-full rounded-lg border border-slate-300 px-4 py-3 uppercase focus:ring-2 focus:ring-cyan-600" placeholder="SAK-BAB01-BTKI">
                @error('token') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
            </div>
            <p class="text-xs text-slate-500">User ID dan token diproses di server, tidak ditampilkan dalam alamat halaman.</p>
            <button type="submit" class="rounded-lg bg-[#012E34] text-white font-semibold px-6 py-3 hover:bg-[#0F4C5C] focus:ring-2 focus:ring-cyan-600">Masuk ke panduan</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-[#012E34] mb-4">Struktur Kurikulum CBT SAK</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-600">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                <span class="font-bold text-[#012E34] block mb-1">Bab 01–02: Klasifikasi & Hitungan</span>
                <span>Teknik Klasifikasi BTKI 2022, NPKB, serta Perhitungan Penerimaan Negara.</span>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                <span class="font-bold text-[#012E34] block mb-1">Bab 03–07: Hukum & Konsep Teknis</span>
                <span>UU Kepabeanan, Prosedur Ekspor-Impor, Fasilitas TPB/KITE, WCO, dan Nilai Pabean WTO.</span>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                <span class="font-bold text-[#012E34] block mb-1">Bab 08–11: Prosedural & Sistemik</span>
                <span>Penagihan, Keberatan/Banding, Lartas INSW, dan Ekosistem CEISA 4.0.</span>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                <span class="font-bold text-[#012E34] block mb-1">Bab 12: Tryout Akbar SAK</span>
                <span>Simulasi komprehensif Sesi 1 (Uraian/Hitungan 75m) dan Sesi 2 (Pilihan Ganda 75m).</span>
            </div>
        </div>
    </div>
</div>
@endsection
