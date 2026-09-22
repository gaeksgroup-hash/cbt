@extends('layouts.app')

@section('title', 'GAEKS CBT — Computer-Based Testing Platform')

@section('content')

<section class="relative overflow-hidden bg-[#012E34]">
    <div class="absolute inset-0 opacity-20"
         style="background-image: radial-gradient(circle at 80% 20%, #0891B2 0, transparent 32%), radial-gradient(circle at 10% 80%, #0E7490 0, transparent 28%);">
    </div>

    <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">

        <div class="max-w-2xl">
            <div class="mb-6 inline-flex rounded-full border border-cyan-700/50 bg-cyan-900/30 px-4 py-2 text-xs font-semibold tracking-wide text-cyan-100">
                GAEKS LEARNING & ASSESSMENT
            </div>

            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Belajar.
                <span class="text-cyan-300">Uji.</span>
                Evaluasi.
            </h1>

            <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                Platform simulasi ujian dan pembelajaran mandiri yang dirancang untuk memberikan pengalaman CBT yang terstruktur, aman, dan konsisten.
            </p>

            <div class="mt-9 flex flex-wrap gap-3">
                <a
                    href="{{ route('sak.landing') }}"
                    class="rounded-xl bg-cyan-500 px-6 py-3.5 text-sm font-bold text-[#012E34] transition hover:bg-cyan-400"
                >
                    Buka Program SAK
                </a>

                <a
                    href="#program"
                    class="rounded-xl border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10"
                >
                    Lihat Program
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 self-center">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-6 backdrop-blur">
                <div class="text-3xl font-bold text-white">11</div>
                <div class="mt-2 text-sm text-slate-300">Chapter CBT</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/10 p-6 backdrop-blur">
                <div class="text-3xl font-bold text-white">1</div>
                <div class="mt-2 text-sm text-slate-300">Tryout Akbar</div>
            </div>

            <div class="col-span-2 rounded-2xl border border-cyan-700/40 bg-cyan-900/30 p-6">
                <div class="text-xs font-semibold uppercase tracking-widest text-cyan-300">
                    Current Program
                </div>

                <div class="mt-3 text-xl font-bold text-white">
                    Sertifikasi Ahli Kepabeanan
                </div>

                <p class="mt-2 text-sm leading-6 text-slate-300">
                    Simulasi belajar mandiri dari materi per bab hingga tryout komprehensif.
                </p>
            </div>
        </div>

    </div>
</section>

<section id="program" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">

    <div class="mb-10">
        <p class="text-sm font-semibold uppercase tracking-wider text-cyan-700">
            Program Ujian
        </p>

        <h2 class="mt-2 text-3xl font-bold tracking-tight text-[#012E34]">
            Pilih program pembelajaran
        </h2>

        <p class="mt-3 max-w-2xl text-slate-600">
            Setiap program memiliki jalur belajar, simulasi, dan evaluasi yang disusun secara terstruktur.
        </p>
    </div>

    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="grid lg:grid-cols-[1.4fr_.6fr]">

            <div class="p-8 sm:p-10">
                <div class="mb-5 inline-flex rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    Program Aktif
                </div>

                <h3 class="text-2xl font-bold text-[#012E34] sm:text-3xl">
                    Sertifikasi Ahli Kepabeanan
                </h3>

                <p class="mt-4 max-w-2xl leading-7 text-slate-600">
                    Program evaluasi mandiri yang mencakup materi kepabeanan Bab 01 hingga Bab 11 dan Tryout Akbar SAK dua sesi.
                </p>

                <div class="mt-7 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="font-bold text-[#012E34]">110</div>
                        <div class="mt-1 text-xs text-slate-500">Soal chapter</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="font-bold text-[#012E34]">12</div>
                        <div class="mt-1 text-xs text-slate-500">Paket CBT</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="font-bold text-[#012E34]">2 Sesi</div>
                        <div class="mt-1 text-xs text-slate-500">Tryout Akbar</div>
                    </div>
                </div>

                <a
                    href="{{ route('sak.landing') }}"
                    class="mt-8 inline-flex rounded-xl bg-[#012E34] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#01434B]"
                >
                    Masuk Program SAK
                </a>
            </div>

            <div class="flex min-h-64 items-center justify-center bg-slate-100 p-8">
                <div class="w-full max-w-xs rounded-2xl bg-[#012E34] p-6 shadow-xl">
                    <div class="text-xs font-semibold uppercase tracking-widest text-cyan-300">
                        CBT Module
                    </div>

                    <div class="mt-6 space-y-3">
                        @foreach ([1,2,3,4] as $number)
                            <div class="flex items-center gap-3 rounded-lg bg-white/10 px-4 py-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-md bg-cyan-400 text-xs font-bold text-[#012E34]">
                                    {{ $number }}
                                </div>

                                <div class="h-2 flex-1 rounded-full bg-white/20"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </article>

</section>

@endsection
