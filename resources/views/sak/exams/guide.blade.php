@extends('layouts.app')

@section('title', $exam->title . ' — Petunjuk')

@section('content')

<section class="bg-[#012E34]">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        <div class="text-xs font-semibold uppercase tracking-widest text-cyan-300">
            Petunjuk Ujian
        </div>

        <h1 class="mt-3 max-w-4xl text-2xl font-bold leading-tight text-white sm:text-3xl">
            {{ $exam->title }}
        </h1>

    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

    <div class="grid gap-7 lg:grid-cols-[1fr_.42fr]">

        <div class="space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">

                <h2 class="text-xl font-bold text-[#012E34]">
                    Informasi Ujian
                </h2>

                @if ($exam->code === 'SAK-BAB12')

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Sesi 1
                            </div>
                            <div class="mt-2 font-bold text-[#012E34]">
                                Uraian & Hitungan
                            </div>
                            <div class="mt-1 text-sm text-slate-500">
                                2 soal kasus • 75 menit
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Sesi 2
                            </div>
                            <div class="mt-2 font-bold text-[#012E34]">
                                Pilihan Ganda
                            </div>
                            <div class="mt-1 text-sm text-slate-500">
                                30 soal • 75 menit
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                        Nilai gabungan minimal <strong>60</strong> dan nilai setiap sesi tidak boleh berada di bawah <strong>40</strong>.
                    </div>

                @else

                    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Jumlah soal</div>
                            <div class="mt-1 text-lg font-bold text-[#012E34]">10</div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Durasi</div>
                            <div class="mt-1 text-lg font-bold text-[#012E34]">
                                {{ (int) ($exam->duration_seconds / 60) }} menit
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Target</div>
                            <div class="mt-1 text-lg font-bold text-[#012E34]">
                                ≥ {{ number_format((float) $exam->pass_score, 0) }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">Attempt</div>
                            <div class="mt-1 text-sm font-bold text-[#012E34]">
                                Dapat diulang
                            </div>
                        </div>

                    </div>

                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">

                <h2 class="text-xl font-bold text-[#012E34]">
                    Sebelum Memulai
                </h2>

                <div class="mt-6 space-y-4">

                    @foreach ([
                        'Timer dimulai setelah ujian benar-benar dimulai.',
                        'Jawaban akan disimpan secara berkala ketika exam engine aktif.',
                        'Periksa kembali soal yang belum dijawab sebelum mengirim hasil.',
                        'Gunakan fitur Tandai untuk menandai soal yang ingin diperiksa kembali.'
                    ] as $rule)

                        <div class="flex gap-3">
                            <div class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold text-cyan-800">
                                ✓
                            </div>

                            <p class="text-sm leading-6 text-slate-600">
                                {{ $rule }}
                            </p>
                        </div>

                    @endforeach
                </div>
            </div>

        </div>

        <aside class="space-y-4">

            <a
                href="{{ route('sak.exam.demo', ['exam' => $exam->slug]) }}"
                class="block rounded-2xl border border-cyan-200 bg-cyan-50 p-6 transition hover:border-cyan-300"
            >
                <div class="text-xs font-semibold uppercase tracking-wider text-cyan-700">
                    Tutorial
                </div>

                <div class="mt-2 text-lg font-bold text-[#012E34]">
                    Lihat Contoh CBT
                </div>

                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Pelajari navigasi sebelum mengikuti simulasi yang sebenarnya.
                </p>
            </a>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <button
                    disabled
                    class="w-full cursor-not-allowed rounded-xl bg-slate-200 px-5 py-3 text-sm font-bold text-slate-500"
                >
                    Mulai Ujian
                </button>

                <p class="mt-3 text-center text-xs leading-5 text-slate-500">
                    Mesin ujian akan diaktifkan pada tahap berikutnya.
                </p>
            </div>

            <form action="{{ route('sak.logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="w-full rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Keluar dari Akses CBT
                </button>
            </form>

        </aside>

    </div>
</section>

@endsection
