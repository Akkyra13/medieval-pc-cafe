@extends('layouts.app')

@section('title', 'Login - Sistem Billing Warnet')

@section('content')
<div class="login-wrapper">
    <div class="login-card">
        <h1>Login Member</h1>
        <p>Masukkan akun warnet Anda</p>
        <form id="login-form" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="login-username">Username atau ID Member</label>
                <input id="login-username" name="username" class="form-control" type="text" placeholder="Contoh: capuri atau admin" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password / PIN</label>
                <input id="login-password" name="password" class="form-control" type="password" placeholder="Masukkan password atau PIN" required>
            </div>
            @if ($errors->any())
                <div class="muted mt-12">{{ $errors->first() }}</div>
            @endif
            <button class="btn btn-primary" style="width:100%" type="submit">Masuk</button>
        </form>
    </div>
</div>
@endsection
