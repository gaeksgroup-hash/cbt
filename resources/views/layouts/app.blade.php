<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GAEKS CBT')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-slate-50 text-slate-800 antialiased">

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        <a href="{{ route('landing') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#012E34] text-lg font-bold text-white">
                G
            </div>

            <div class="leading-tight">
                <div class="font-bold tracking-tight text-[#012E34]">
                    GAEKS CBT
                </div>
                <div class="text-xs text-slate-500">
                    Assessment Platform
                </div>
            </div>
        </a>

        <nav class="flex items-center gap-2 sm:gap-4">
            <a
                href="{{ route('landing') }}"
                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-[#012E34]"
            >
                Platform
            </a>

            <a
                href="{{ route('sak.landing') }}"
                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-[#012E34]"
            >
                Program SAK
            </a>
        </nav>
    </div>
</header>

@if (session('notice'))
    <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
        <div class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
            {{ session('notice') }}
        </div>
    </div>
@endif

<main>
    @yield('content')
</main>

<footer class="mt-16 border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <p>
            © {{ date('Y') }} GAEKS GROUP. Seluruh hak cipta dilindungi.
        </p>

        <div class="flex items-center gap-3">
            <span>cbt.gaeks.com</span>
            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
            <span>Computer-Based Testing Platform</span>
        </div>
    </div>
</footer>

</body>
</html>
