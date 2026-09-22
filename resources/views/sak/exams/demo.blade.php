@extends('layouts.app')

@section('title', 'Demo CBT — ' . $exam->title)

@section('content')

<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                DEMO — TIDAK DINILAI
            </div>

            <h1 class="mt-3 text-xl font-bold text-[#012E34] sm:text-2xl">
                {{ $exam->title }}
            </h1>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm shadow-sm">
            <div class="text-xs text-slate-500">
                Contoh posisi timer
            </div>
            <div class="mt-1 font-mono text-lg font-bold text-[#012E34]">
                19:59
            </div>
        </div>

    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_240px]">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <div class="text-sm font-semibold text-cyan-700">
                Contoh Soal 1
            </div>

            <h2 class="mt-4 text-lg font-semibold leading-7 text-slate-800">
                Ini adalah contoh pertanyaan untuk mempelajari cara menggunakan antarmuka CBT.
            </h2>

            <div class="mt-7 space-y-3">

                @foreach (['A', 'B', 'C', 'D'] as $option)
                    <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-cyan-400 hover:bg-cyan-50">

                        <input
                            type="radio"
                            name="demo_answer"
                            class="h-4 w-4 accent-cyan-700"
                        >

                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-[#012E34]">
                            {{ $option }}
                        </div>

                        <span class="text-sm text-slate-700">
                            Pilihan {{ $option }}
                        </span>

                    </label>
                @endforeach

            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-3">

                <button
                    type="button"
                    class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-600"
                >
                    Sebelumnya
                </button>

                <button
                    type="button"
                    class="rounded-xl border border-amber-300 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-800"
                >
                    Tandai
                </button>

                <button
                    type="button"
                    class="rounded-xl bg-[#012E34] px-5 py-3 text-sm font-semibold text-white"
                >
                    Berikutnya
                </button>

            </div>
        </div>

        <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <h3 class="text-sm font-bold text-[#012E34]">
                Navigasi Soal
            </h3>

            <div class="mt-5 grid grid-cols-4 gap-2 lg:grid-cols-3">

                @foreach ([1,2,3,4,5,6] as $number)
                    <button
                        type="button"
                        class="flex h-10 items-center justify-center rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:border-cyan-400"
                    >
                        {{ $number }}
                    </button>
                @endforeach

            </div>

            <div class="mt-6 space-y-3 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-[#012E34]"></span>
                    Terjawab
                </div>

                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded border border-slate-300 bg-white"></span>
                    Belum dijawab
                </div>

                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-amber-400"></span>
                    Ditandai
                </div>
            </div>

            <a
                href="{{ route('sak.exam.guide', ['exam' => $exam->slug]) }}"
                class="mt-7 block rounded-xl border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-[#012E34] transition hover:bg-slate-50"
            >
                Kembali ke Petunjuk
            </a>

        </aside>

    </div>

    <div class="mt-6 rounded-xl border border-cyan-200 bg-cyan-50 px-5 py-4 text-sm text-cyan-900">
        Demo ini tidak membuat attempt, tidak menyimpan jawaban, tidak menjalankan timer, dan tidak menghasilkan nilai.
    </div>

</section>

@endsection
