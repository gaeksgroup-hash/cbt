@extends('layouts.app')

@section('title', 'Masuk Admin')

@section('content')
<div class="document-page max-w-xl">
    <section class="content-sheet content-sheet--white" aria-labelledby="admin-login-title">
        <p class="document-kicker">Area administrator</p>
        <h1 id="admin-login-title" class="mt-4 text-3xl font-bold tracking-tight text-[#062F33] sm:text-4xl">Masuk ke panel CBT</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600">Kelola User ID, periksa riwayat, dan reset kuota attempt dari satu tempat.</p>

        @if ($errors->any())
            <div role="alert" class="alert-error mt-6">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('admin.login.store') }}" class="mt-7">
            @csrf
            <div class="form-field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" required autofocus class="form-control" value="{{ old('username') }}">
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="form-control">
            </div>
            <button type="submit" class="button-primary mt-6 w-full">Masuk ke panel admin</button>
        </form>
    </section>
</div>
@endsection
