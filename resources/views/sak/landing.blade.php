@extends('layouts.app')

@section('title', 'Ujian Sertifikasi Ahli Kepabeanan')

@section('content')
<div class="document-page">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('landing') }}">Platform</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Sertifikasi Ahli Kepabeanan</span>
    </nav>

    <section class="access-layout" aria-labelledby="sak-title">
        <div class="access-intro">
            <p class="document-kicker">Program / SAK</p>
            <h1 id="sak-title" class="display-title">Meja ujian <em>kepabeanan.</em></h1>
            <p class="lead-copy mt-6">Latihan bab dan tryout dari materi GAEKS Publishing. Sistem mencatat setiap attempt pada Order ID yang digunakan saat masuk.</p>

            <dl class="hero-ledger mt-8" aria-label="Isi program SAK">
                <div class="ledger-row"><dt>Paket tersedia</dt><dd>{{ $examCount }}</dd></div>
                <div class="ledger-row"><dt>Soal aktif</dt><dd>{{ $questionCount }}</dd></div>
                <div class="ledger-row"><dt>Pengulangan</dt><dd>Ya</dd></div>
            </dl>
        </div>

        <div class="access-panel">
            <div class="access-panel__head">
                <strong>Verifikasi akses</strong>
                <span>GDP → CBT</span>
            </div>
            <div class="access-panel__body">
                @if ($candidate)
                    <div class="alert-success">
                        <strong>Order ID tersambung</strong><br>
                        <span class="mono-data break-all">{{ $candidate->public_id }}</span>
                    </div>
                    <div class="mb-5 flex flex-wrap gap-3">
                        <a class="button-secondary" href="{{ route('sak.results') }}">Lihat seluruh hasil</a>
                        <form method="post" action="{{ route('sak.logout') }}">
                            @csrf
                            <button class="button-quiet" type="submit">Ganti Order ID</button>
                        </form>
                    </div>
                    <p class="mb-5 text-sm leading-6 text-slate-600">Masukkan token buku untuk membuka paket lain yang tercakup dalam pembelian ini.</p>
                @endif

                @if ($errors->has('access'))
                    <div role="alert" class="alert-error">{{ $errors->first('access') }}</div>
                @endif

                @if (config('cbt.preview_only'))
                    <div class="alert-warning">
                        <strong>Mode pratinjau aktif.</strong> Verifikasi Order ID dan pengerjaan ujian belum dibuka.
                    </div>
                @else
                    <form method="post" action="{{ route('sak.access') }}" novalidate>
                        @csrf
                        <div class="form-field">
                            <label for="order_id">
                                <span>Order ID pembelian</span>
                                <small>Wajib</small>
                            </label>
                            <input id="order_id" name="order_id" value="{{ old('order_id', $candidate?->public_id) }}" autocomplete="off" autocapitalize="characters" spellcheck="false" required maxlength="40" placeholder="GDP-20261017123456-ABCD1234" class="form-control mono-data uppercase">
                            @error('order_id')<p role="alert" class="mt-2 text-sm text-red-800">{{ $message }}</p>@enderror
                            <p class="field-note">Salin nomor referensi pada email status pembayaran GDP.</p>
                        </div>

                        <div class="form-field">
                            <label for="token">
                                <span>Token ujian pada buku</span>
                                <small>Wajib</small>
                            </label>
                            <input id="token" name="token" type="password" autocomplete="off" required maxlength="100" placeholder="Masukkan token paket" class="form-control mono-data uppercase">
                            @error('token')<p role="alert" class="mt-2 text-sm text-red-800">{{ $message }}</p>@enderror
                            <p class="field-note">Token menentukan bab atau tryout yang akan dibuka.</p>
                        </div>

                        <button class="button-primary mt-6 w-full" type="submit">Verifikasi dan buka panduan</button>
                    </form>
                @endif

                <p class="mt-5 border-t border-stone-300 pt-4 text-xs leading-5 text-slate-600">Akses diberikan hanya jika pembayaran berstatus dibayar dan buku dalam transaksi tersebut memuat paket SAK. Data nama serta email pembeli tidak dipindahkan ke CBT.</p>
            </div>
        </div>
    </section>

    <section aria-labelledby="source-heading">
        <div class="section-rule">
            <span class="section-number">01</span>
            <div>
                <h2 id="source-heading" class="section-heading">Di mana saya mendapat data akses?</h2>
                <p class="section-summary">Dua data diperiksa bersamaan agar token buku tidak dapat digunakan tanpa transaksi yang sesuai.</p>
            </div>
        </div>
        <div class="process-line">
            <div class="process-step"><span class="process-step__no">ORDER ID</span><strong>Dikirim lewat email transaksi</strong><p>Nomor dimulai dengan GDP- dan menjadi identitas riwayat ujian Anda.</p></div>
            <div class="process-step"><span class="process-step__no">TOKEN</span><strong>Tercantum pada materi buku</strong><p>Token memilih paket bab atau tryout yang hendak dikerjakan.</p></div>
            <div class="process-step"><span class="process-step__no">COCOK</span><strong>Buku menentukan program</strong><p>Order ID hanya membuka ujian yang terhubung dengan buku pada transaksi.</p></div>
            <div class="process-step"><span class="process-step__no">HASIL</span><strong>Tersimpan per Order ID</strong><p>Skor lama tetap ada meski ujian dikerjakan ulang.</p></div>
        </div>
    </section>

    <section aria-labelledby="curriculum-heading">
        <div class="section-rule">
            <span class="section-number">02</span>
            <div>
                <h2 id="curriculum-heading" class="section-heading">Cakupan program SAK</h2>
                <p class="section-summary">Paket disusun dari materi dasar klasifikasi sampai simulasi komprehensif.</p>
            </div>
        </div>
        <div class="curriculum-list">
            <div class="curriculum-row"><strong>Bab 01 sampai 02</strong><p>Teknik Klasifikasi BTKI 2022, NPKB, serta Perhitungan Penerimaan Negara.</p></div>
            <div class="curriculum-row"><strong>Bab 03 sampai 07</strong><p>UU Kepabeanan, prosedur ekspor-impor, fasilitas TPB/KITE, WCO, dan Nilai Pabean WTO.</p></div>
            <div class="curriculum-row"><strong>Bab 08 sampai 11</strong><p>Penagihan, keberatan dan banding, lartas INSW, serta ekosistem CEISA 4.0.</p></div>
            <div class="curriculum-row"><strong>Bab 12</strong><p>Tryout Akbar SAK dalam dua sesi: uraian atau hitungan dan pilihan ganda.</p></div>
        </div>
    </section>
</div>
@endsection
