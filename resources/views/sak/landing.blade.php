@extends('layouts.app')

@section('title', 'SAK — Akses CBT')

@section('content')

<section class="bg-[#012E34]">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="max-w-3xl">

            <div class="mb-5 text-sm font-semibold text-cyan-300">
                GAEKS CBT / PROGRAM SAK
            </div>

            <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Sertifikasi Ahli Kepabeanan
            </h1>

            <p class="mt-4 max-w-2xl leading-7 text-slate-300">
                Masukkan User ID peserta dan Token Bab untuk membuka paket simulasi yang sesuai.
            </p>

        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

    <div class="grid gap-8 lg:grid-cols-[.85fr_1.15fr]">

        <aside class="order-2 lg:order-1">

            <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">
                    Sebelum masuk
                </p>

                <h2 class="mt-2 text-xl font-bold text-[#012E34]">
                    Tiga langkah sederhana
                </h2>

                <div class="mt-7 space-y-6">

                    <div class="flex gap-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#012E34] font-bold text-white">
                            1
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-800">
                                Masukkan User ID
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Gunakan ID peserta CBT Anda.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#012E34] font-bold text-white">
                            2
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-800">
                                Masukkan Token Bab
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Token akan mengarahkan Anda ke paket ujian yang sesuai.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#012E34] font-bold text-white">
                            3
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-800">
                                Baca petunjuk
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Pelajari durasi dan aturan sebelum memulai CBT.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-cyan-200 bg-cyan-50 p-5 text-sm leading-6 text-cyan-900">
                Token digunakan untuk menentukan paket ujian. Identitas peserta tetap berasal dari User ID.
            </div>

        </aside>

        <div class="order-1 lg:order-2">

            <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm sm:p-10">

                <div class="mb-8">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#012E34] text-lg font-bold text-white">
                        G
                    </div>

                    <h2 class="text-2xl font-bold text-[#012E34]">
                        Akses Paket CBT
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Masukkan kredensial pembelajaran Anda untuk melanjutkan.
                    </p>
                </div>

                @if ($errors->has('access'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first('access') }}
                    </div>
                @endif

                <form action="{{ route('sak.access') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="user_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            User ID
                        </label>

                        <input
                            id="user_id"
                            name="user_id"
                            type="text"
                            value="{{ old('user_id') }}"
                            maxlength="64"
                            autocomplete="off"
                            spellcheck="false"
                            required
                            placeholder="GSAK_CBT001"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div>
                        <label for="token" class="mb-2 block text-sm font-semibold text-slate-700">
                            Token Bab
                        </label>

                        <input
                            id="token"
                            name="token"
                            type="password"
                            maxlength="128"
                            autocomplete="off"
                            spellcheck="false"
                            required
                            placeholder="Masukkan Token Bab"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Token tidak akan ditampilkan kembali jika akses gagal.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-[#012E34] px-5 py-3.5 text-sm font-bold text-white transition hover:bg-[#01434B] focus:outline-none focus:ring-4 focus:ring-cyan-100"
                    >
                        Buka Paket Ujian
                    </button>

                </form>

            </div>
        </div>

    </div>
</section>

@endsection
