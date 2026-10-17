<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#062F33">

    <title>@yield('title', 'GAEKS CBT') | Platform Ujian GAEKS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="{{ asset('resources/css/app.css') }}">
    @endif
</head>
<body class="cbt-shell">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>

    <header class="site-header">
        <div class="site-header__inner">
            <a href="{{ route('landing') }}" class="brand-lockup" aria-label="Beranda GAEKS CBT">
                <span class="brand-lockup__mark" aria-hidden="true">G</span>
                <span class="brand-lockup__copy">
                    <strong>GAEKS CBT</strong>
                    <small>Exam desk</small>
                </span>
            </a>

            <nav class="site-nav" aria-label="Navigasi utama">
                <a href="{{ route('landing') }}" @class(['is-current' => request()->routeIs('landing')])>Platform</a>
                <a href="{{ route('sak.landing') }}" @class(['is-current' => request()->routeIs('sak.*')])>Ujian SAK</a>
                <a href="{{ route('health') }}" class="site-nav__status">Status</a>
            </nav>
        </div>
    </header>

    <main id="main-content" class="site-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer__inner">
            <div>
                <strong>GAEKS CBT</strong>
                <p>Ujian berbasis buku dan transaksi terverifikasi.</p>
            </div>
            <div class="site-footer__meta">
                <span>&copy; {{ date('Y') }} GAEKS GROUP</span>
                <span>{{ config('cbt.preview_only') ? 'Mode pratinjau' : 'Platform ujian aktif' }}</span>
            </div>
        </div>
    </footer>
</body>
</html>
