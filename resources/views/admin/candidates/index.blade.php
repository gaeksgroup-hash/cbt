@extends('layouts.admin')

@section('title', 'Kelola User ID')

@section('content')
<div class="document-page">
    <header class="admin-page-head">
        <div>
            <p class="document-kicker">Kontrol akses CBT</p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-[#062F33] sm:text-5xl">User ID dan kuota attempt</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Cari satu pengguna untuk memeriksa paket, skor, serta sisa attempt. User manual yang dibuat di sini mendapat akses SAK tanpa transaksi GDP.</p>
        </div>
        <dl class="admin-ledger" aria-label="Ringkasan data CBT">
            <div><dt>Seluruh User ID</dt><dd>{{ number_format($candidateCount) }}</dd></div>
            <div><dt>Aktif</dt><dd>{{ number_format($activeCandidateCount) }}</dd></div>
            <div><dt>Attempt tercatat</dt><dd>{{ number_format($attemptCount) }}</dd></div>
        </dl>
    </header>

    @if (session('success'))
        <div role="status" class="alert-success mt-6">{{ session('success') }}</div>
    @endif

    <div class="admin-workspace mt-8">
        <section class="admin-create-panel" aria-labelledby="create-user-title">
            <p class="mono-data text-xs font-semibold text-[#9A5D0B]">USER MANUAL</p>
            <h2 id="create-user-title" class="mt-2 text-2xl font-bold text-[#062F33]">Buat User ID</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Kosongkan User ID jika ingin sistem membuat kode CBT secara otomatis.</p>

            @if ($errors->any())
                <div role="alert" class="alert-error mt-5">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ route('admin.candidates.store') }}" class="mt-6">
                @csrf
                <div class="form-field">
                    <label for="public_id">User ID <small>Opsional</small></label>
                    <input id="public_id" name="public_id" class="form-control mono-data" value="{{ old('public_id') }}" placeholder="CBT-PELATIHSK01">
                    <p class="field-note">Gunakan awalan CBT-. Akses yang dibuat: program SAK.</p>
                </div>
                <div class="form-field">
                    <label for="name">Nama <small>Opsional</small></label>
                    <input id="name" name="name" class="form-control" value="{{ old('name') }}" autocomplete="name">
                </div>
                <div class="form-field">
                    <label for="email">Email <small>Opsional</small></label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" autocomplete="email">
                </div>
                <button class="button-primary mt-6 w-full">Buat akses SAK</button>
            </form>
        </section>

        <section aria-labelledby="user-list-title" class="min-w-0">
            <div class="admin-list-head">
                <div>
                    <h2 id="user-list-title" class="text-2xl font-bold text-[#062F33]">Daftar User ID</h2>
                    <p class="mt-1 text-sm text-slate-600">Hasil terbaru ditampilkan lebih dulu.</p>
                </div>
                <form method="get" action="{{ route('admin.dashboard') }}" class="admin-search" role="search">
                    <label for="user-search" class="sr-only">Cari User ID, nama, atau email</label>
                    <input id="user-search" name="q" class="form-control" value="{{ $search }}" placeholder="Cari User ID, nama, atau email">
                    <button class="button-secondary">Cari pengguna</button>
                </form>
            </div>

            @if ($candidates->isEmpty())
                <div class="admin-empty-state">
                    <h3>{{ $search === '' ? 'Belum ada User ID.' : 'Tidak ada hasil yang cocok.' }}</h3>
                    <p>{{ $search === '' ? 'Buat User ID manual atau tunggu pembeli pertama masuk dari Order ID GDP.' : 'Periksa ejaan atau cari dengan bagian lain dari User ID.' }}</p>
                    @if ($search !== '')<a href="{{ route('admin.dashboard') }}" class="button-secondary mt-4">Hapus pencarian</a>@endif
                </div>
            @else
                <div class="table-shell mt-5">
                    <table class="data-table admin-user-table">
                        <thead><tr><th>User ID</th><th>Identitas</th><th>Status</th><th>Attempt</th><th>Diperbarui</th><th><span class="sr-only">Aksi</span></th></tr></thead>
                        <tbody>
                            @foreach ($candidates as $candidate)
                                <tr>
                                    <td class="mono-data font-semibold text-[#062F33]">{{ $candidate->public_id }}</td>
                                    <td><strong>{{ $candidate->name ?: 'Nama belum diisi' }}</strong><br><span class="text-slate-600">{{ $candidate->email ?: 'Email belum diisi' }}</span></td>
                                    <td><span class="admin-status admin-status--{{ $candidate->status }}">{{ $candidate->status === 'active' ? 'Aktif' : ($candidate->status === 'suspended' ? 'Ditangguhkan' : 'Belum aktif') }}</span></td>
                                    <td class="mono-data">{{ $candidate->attempts_count }}</td>
                                    <td class="whitespace-nowrap">{{ $candidate->updated_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                                    <td><a class="admin-row-link" href="{{ route('admin.candidates.show', $candidate) }}">Kelola</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">{{ $candidates->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection
