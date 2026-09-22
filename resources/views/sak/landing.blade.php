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
            CBT module foundation initialized.
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#012E34] tracking-tight mb-3">
            Sertifikasi Ahli Kepabeanan
        </h1>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
            Simulasi pembelajaran mandiri dan uji kompetensi Ahli Kepabeanan berbasis Buku Panduan Resmi GAEKS Publishing.
        </p>

        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-xs text-amber-800 flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="leading-relaxed">
                <strong>Informasi Akses:</strong> Akses User ID + Token akan diaktifkan pada development task berikutnya. Halaman ini adalah verifikasi routing program pada Milestone Foundation.
            </div>
        </div>
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
