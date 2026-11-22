@extends('layouts.app')

@section('title', 'Edit Akun - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $userItem = $data ?? $user ?? $account ?? collect();

    $accId = data_get($userItem, 'id_akun', data_get($userItem, 'id'));
    $accUsername = data_get($userItem, 'username', '');
    $accName = data_get($userItem, 'name', data_get($userItem, 'nama', ''));
    $accPhone = data_get($userItem, 'phone', data_get($userItem, 'no_hp', data_get($userItem, 'telepon', '')));
    $accStatus = data_get($userItem, 'status', 'Aktif');
@endphp
<main class="container">
    <div class="section-header">
        <div>
            <div class="section-title">Edit Akun Member {{ $accUsername ? '@' . $accUsername : '' }}</div>
            <div class="section-sub">Perbarui data profil, nomor telepon, password, atau status akun member</div>
        </div>
        <a href="{{ route('admin.akun.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card mt-16">
        <form action="{{ route('admin.akun.update', $accId) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid-4">
                <div class="form-group">
                    <label for="edit-acc-username">Username</label>
                    <input id="edit-acc-username" name="username" class="form-control" type="text" value="{{ old('username', $accUsername) }}" required>
                </div>

                <div class="form-group">
                    <label for="edit-acc-name">Nama Lengkap</label>
                    <input id="edit-acc-name" name="name" class="form-control" type="text" value="{{ old('name', $accName) }}" required>
                </div>

                <div class="form-group">
                    <label for="edit-acc-phone">No. Telepon</label>
                    <input id="edit-acc-phone" name="phone" class="form-control" type="text" value="{{ old('phone', $accPhone) }}" required>
                </div>

                <div class="form-group">
                    <label for="edit-acc-status">Status Akun</label>
                    <select id="edit-acc-status" name="status" class="form-control">
                        <option value="Aktif" {{ old('status', $accStatus) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ old('status', $accStatus) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group mt-12" style="max-width:340px;">
                <label for="edit-acc-password">Password Baru (opsional)</label>
                <input id="edit-acc-password" name="password" class="form-control" type="password" placeholder="Kosongkan jika tidak diubah">
            </div>

            <div class="toolbar mt-16">
                <button class="btn btn-primary btn-sm" type="submit">Simpan Perubahan Akun</button>
                <a href="{{ route('admin.akun.index') }}" class="btn btn-secondary btn-sm">Batal</a>
            </div>
        </form>
    </div>
</main>
@endsection