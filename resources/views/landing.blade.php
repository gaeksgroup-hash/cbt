@extends('layouts.app')

@section('title', 'Platform Ujian Berbasis Buku')

@section('content')
<div class="document-page">
    <section class="hero-grid" aria-labelledby="platform-title">
        <div class="hero-copy">
            <p class="document-kicker">Platform ujian GAEKS</p>
            <h1 id="platform-title" class="display-title">Baca bukunya. <em>Uji pemahamannya.</em></h1>
            <p class="lead-copy">GAEKS CBT menghubungkan buku yang Anda beli di GDP dengan paket ujian yang sesuai. Satu Order ID menyimpan seluruh riwayat pengerjaan, skor terbaik, dan kesempatan mengulang.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="button-primary" href="{{ route('sak.landing') }}">Masuk ke ujian SAK</a>
                <a class="button-secondary" href="https://gdp.gaeks.com/ebooks" rel="noopener">Lihat buku di GDP</a>
            </div>
        </div>

        <dl class="hero-ledger" aria-label="Isi platform saat ini">
            <div class="ledger-row"><dt>Program aktif</dt><dd>{{ $programCount }}</dd></div>
            <div class="ledger-row"><dt>Paket ujian</dt><dd>{{ $examCount }}</dd></div>
            <div class="ledger-row"><dt>Soal tersedia</dt><dd>{{ $questionCount }}</dd></div>
        </dl>
    </section>

    <section aria-labelledby="program-heading">
        <div class="section-rule">
            <span class="section-number">01</span>
            <div>
                <h2 id="program-heading" class="section-heading">Program yang dapat dikerjakan</h2>
                <p class="section-summary">Setiap program memiliki bank soal, aturan waktu, dan syarat akses sendiri. Hanya paket yang terhubung dengan buku pada Order ID Anda yang akan dibuka.</p>
            </div>
        </div>

        <article class="program-file">
            <div class="program-file__index">
                <div>
                    <span class="program-file__code">PROGRAM / SAK / 001</span>
                    <h3>Sertifikasi Ahli Kepabeanan</h3>
                    <p>Latihan per bab dan tryout terpadu berdasarkan materi kepabeanan GAEKS Publishing.</p>
                </div>
                <span class="status-stamp">Aktif</span>
            </div>
            <div class="program-file__body">
                <dl class="file-meta">
                    <div><dt>Paket</dt><dd>{{ $examCount }}</dd></div>
                    <div><dt>Soal</dt><dd>{{ $questionCount }}</dd></div>
                    <div><dt>Riwayat</dt><dd>Tetap</dd></div>
                </dl>
                <ol class="file-list">
                    <li><span>01</span><span>Latihan materi per bab dengan timer dan penilaian otomatis untuk soal objektif.</span></li>
                    <li><span>02</span><span>Tryout dua sesi untuk pilihan ganda, hitungan, dan uraian.</span></li>
                    <li><span>03</span><span>Skor tiap attempt tersimpan dan dapat dibandingkan saat mengulang.</span></li>
                </ol>
                <a class="button-primary mt-6" href="{{ route('sak.landing') }}">Buka berkas ujian</a>
            </div>
        </article>
    </section>

    <section aria-labelledby="access-heading">
        <div class="section-rule">
            <span class="section-number">02</span>
            <div>
                <h2 id="access-heading" class="section-heading">Akses mengikuti pembelian buku</h2>
                <p class="section-summary">Order ID bukan akun terpisah. Nomor ini menjadi bukti pembelian sekaligus identitas hasil ujian Anda.</p>
            </div>
        </div>

        <ol class="process-line">
            <li class="process-step"><span class="process-step__no">01 / BELI</span><strong>Pilih e-book di GDP</strong><p>Selesaikan checkout pada katalog resmi GAEKS Digital Publishing.</p></li>
            <li class="process-step"><span class="process-step__no">02 / BAYAR</span><strong>Tunggu pembayaran terverifikasi</strong><p>Akses baru berlaku ketika transaksi sudah berstatus dibayar.</p></li>
            <li class="process-step"><span class="process-step__no">03 / CATAT</span><strong>Ambil Order ID dari email</strong><p>Email status transaksi memuat referensi dengan format GDP-...</p></li>
            <li class="process-step"><span class="process-step__no">04 / UJIAN</span><strong>Masukkan token buku</strong><p>Order ID dan token diperiksa bersama sebelum paket dibuka.</p></li>
        </ol>
    </section>
</div>
@endsection
