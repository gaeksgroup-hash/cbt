@extends('layouts.admin')

@section('title', 'Kelola '.$candidate->public_id)

@section('content')
<div class="document-page">
    <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('admin.dashboard') }}">User ID</a><span aria-hidden="true">/</span><span aria-current="page">Detail pengguna</span></nav>

    @if (session('success'))
        <div role="status" class="alert-success">{{ session('success') }}</div>
    @endif

    <header class="admin-candidate-head">
        <div class="min-w-0">
            <p class="document-kicker">Detail akses</p>
            <h1 class="mono-data mt-4 break-all text-2xl font-semibold text-[#062F33] sm:text-4xl">{{ $candidate->public_id }}</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $candidate->name ?: 'Nama belum diisi' }}{{ $candidate->email ? ' · '.$candidate->email : '' }}</p>
        </div>
        <form method="post" action="{{ route('admin.candidates.status', $candidate) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $candidate->status === 'active' ? 'suspended' : 'active' }}">
            <button class="{{ $candidate->status === 'active' ? 'button-danger' : 'button-primary' }}">{{ $candidate->status === 'active' ? 'Tangguhkan akses' : 'Aktifkan akses' }}</button>
        </form>
    </header>

    <dl class="summary-grid" aria-label="Ringkasan pengguna">
        <div class="summary-stat"><dt>Status</dt><dd class="!text-xl">{{ $candidate->status === 'active' ? 'Aktif' : 'Ditangguhkan' }}</dd></div>
        <div class="summary-stat"><dt>Akses</dt><dd class="!text-xl">{{ $candidate->entitlements->pluck('entitlement_key')->join(', ') ?: 'GDP' }}</dd></div>
        <div class="summary-stat"><dt>Total attempt</dt><dd>{{ $attempts->count() }}</dd></div>
        <div class="summary-stat"><dt>Aktivasi</dt><dd class="!text-base">{{ $candidate->activated_at?->timezone('Asia/Jakarta')->format('d M Y') ?: '-' }}</dd></div>
    </dl>

    <section aria-labelledby="quota-heading">
        <div class="section-rule">
            <span class="section-number">01</span>
            <div><h2 id="quota-heading" class="section-heading">Kuota per paket</h2><p class="section-summary">Reset memberi tiga kesempatan baru dan tetap menyimpan riwayat lama.</p></div>
        </div>
        <div class="admin-quota-list mt-6">
            @foreach ($exams as $exam)
                @php($usage = $examUsage[$exam->id])
                <article class="admin-quota-row">
                    <div>
                        <p class="mono-data text-xs font-semibold text-[#9A5D0B]">{{ $exam->code }}</p>
                        <h3>{{ $exam->title }}</h3>
                    </div>
                    <dl><div><dt>Dipakai</dt><dd>{{ $usage['used'] }}/{{ $usage['limit'] }}</dd></div><div><dt>Sisa</dt><dd>{{ $usage['remaining'] }}</dd></div></dl>
                    <button type="button" class="button-secondary" data-dialog-open="reset-{{ $exam->id }}">Reset kuota</button>
                </article>
                <dialog id="reset-{{ $exam->id }}" class="admin-dialog" aria-labelledby="reset-title-{{ $exam->id }}">
                    <form method="post" action="{{ route('admin.candidates.attempts.reset', [$candidate, $exam]) }}">
                        @csrf
                        <p class="mono-data text-xs font-semibold text-[#9A5D0B]">KONFIRMASI RESET</p>
                        <h2 id="reset-title-{{ $exam->id }}" class="mt-3 text-2xl font-bold text-[#062F33]">Berikan tiga attempt baru?</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Riwayat {{ $exam->code }} yang lama tetap tersimpan. Reset hanya membuka kuota baru untuk User ID ini.</p>
                        <div class="mt-6 flex flex-wrap justify-end gap-3"><button type="button" class="button-quiet" data-dialog-close>Batal</button><button type="submit" class="button-primary">Reset {{ $exam->code }}</button></div>
                    </form>
                </dialog>
            @endforeach
        </div>
    </section>

    <section aria-labelledby="history-heading">
        <div class="section-rule">
            <span class="section-number">02</span>
            <div><h2 id="history-heading" class="section-heading">Riwayat attempt</h2><p class="section-summary">Seluruh nilai tetap dapat ditelusuri setelah kuota direset.</p></div>
        </div>
        @if ($attempts->isEmpty())
            <div class="admin-empty-state mt-6"><h3>Belum ada attempt.</h3><p>User ini belum memulai paket ujian.</p></div>
        @else
            <div class="table-shell mt-6">
                <table class="data-table"><thead><tr><th>Paket</th><th>Attempt</th><th>Skor</th><th>Status</th><th>Waktu</th></tr></thead><tbody>
                    @foreach ($attempts as $attempt)
                        <tr><td>{{ $attempt->exam->code }}{{ $attempt->session_code ? ' / '.$attempt->session_code : '' }}</td><td class="mono-data">#{{ $attempt->attempt_no }}</td><td class="mono-data">{{ $attempt->score_final === null ? '-' : number_format((float) $attempt->score_final, 2) }}</td><td>{{ $attempt->status === 'in_progress' ? 'Sedang dikerjakan' : ($attempt->passed ? 'Target tercapai' : 'Perlu diulang') }}</td><td class="whitespace-nowrap">{{ ($attempt->submitted_at ?? $attempt->started_at)?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td></tr>
                    @endforeach
                </tbody></table>
            </div>
        @endif
    </section>
</div>
@endsection
