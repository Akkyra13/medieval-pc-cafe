@extends('layouts.app')

@section('title', 'Manajemen Akun - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $usersList = $users ?? collect();
    $zonesList = $zones ?? collect();
@endphp
<main
    id="admin-account-root"
    class="container"
    data-users='@json($usersList)'
    data-zones='@json($zonesList)'
>
    <div class="section-header">
        <div>
            <div class="section-title">Manajemen Akun & Kuota Billing</div>
            <div class="section-sub">Registrasi member, update profil/status akun, tambah kuota billing, dan kelola akun</div>
        </div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">Registrasi Member Baru</h3>
        <form action="{{ route('admin.akun.store') }}" method="POST">
            @csrf
            <div class="grid-4">
                <div class="form-group">
                    <label for="acc-username">Username</label>
                    <input id="acc-username" name="username" class="form-control" type="text" value="{{ old('username') }}" required>
                </div>
                <div class="form-group">
                    <label for="acc-name">Nama Lengkap</label>
                    <input id="acc-name" name="name" class="form-control" type="text" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="acc-phone">No. Telepon</label>
                    <input id="acc-phone" name="phone" class="form-control" type="text" value="{{ old('phone') }}" required>
                </div>
                <div class="form-group">
                    <label for="acc-password">Password / PIN</label>
                    <input id="acc-password" name="password" class="form-control" type="password" required>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" type="submit">+ Tambah Akun</button>
        </form>
    </div>

    <div class="card mt-16">
        <div class="toolbar">
            <input id="admin-account-search" class="form-control" type="text" placeholder="Cari ID, nama, atau username...">
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID Akun</th>
                        <th>Member</th>
                        <th>No. Telepon</th>
                        <th>Commoner</th>
                        <th>Noble</th>
                        <th>Royalty</th>
                        <th>Status</th>
                        <th>Update Akun</th>
                        <th>Top-up & Hapus</th>
                    </tr>
                </thead>
                <tbody id="admin-account-body">
                    @foreach ($usersList as $acc)
                        @php
                            $accId = data_get($acc, 'id_akun', data_get($acc, 'id'));
                            $accUsername = data_get($acc, 'username', '-');
                            $accName = data_get($acc, 'name', data_get($acc, 'nama', '-'));
                            $accPhone = data_get($acc, 'phone', data_get($acc, 'no_hp', data_get($acc, 'telepon', '-')));
                            $accStatus = data_get($acc, 'status', 'Aktif');

                            $q1 = (int) data_get($acc, 'quota.1', data_get($acc, 'commoner', data_get($acc, 'sisa_commoner', 0)));
                            $q2 = (int) data_get($acc, 'quota.2', data_get($acc, 'noble', data_get($acc, 'sisa_noble', 0)));
                            $q3 = (int) data_get($acc, 'quota.3', data_get($acc, 'royalty', data_get($acc, 'sisa_royalty', 0)));
                        @endphp
                        <tr>
                            <td>{{ $accId }}</td>
                            <td>
                                <strong>{{ $accName }}</strong>
                                <div class="muted">{{ '@' . $accUsername }}</div>
                            </td>
                            <td>{{ $accPhone }}</td>
                            <td>{{ intdiv(max(0, $q1), 60) . 'j ' . str_pad((string) (max(0, $q1) % 60), 2, '0', STR_PAD_LEFT) . 'm' }}</td>
                            <td>{{ intdiv(max(0, $q2), 60) . 'j ' . str_pad((string) (max(0, $q2) % 60), 2, '0', STR_PAD_LEFT) . 'm' }}</td>
                            <td>{{ intdiv(max(0, $q3), 60) . 'j ' . str_pad((string) (max(0, $q3) % 60), 2, '0', STR_PAD_LEFT) . 'm' }}</td>
                            <td>{{ $accStatus }}</td>
                            <td>
                                <form action="{{ route('admin.akun.update', $accId) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    @csrf
                                    @method('PUT')
                                    <input name="name" class="form-control" type="text" value="{{ $accName }}" style="width:130px;" required>
                                    <input name="phone" class="form-control" type="text" value="{{ $accPhone }}" style="width:130px;" required>
                                    <select name="status" class="form-control" style="width:auto;">
                                        <option value="Aktif" {{ $accStatus === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Nonaktif" {{ $accStatus === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    <button class="btn btn-secondary btn-sm" type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.akun.topup', $accId) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    @csrf
                                    @method('PUT')
                                    <select name="id_zona" class="form-control" style="width:auto;">
                                        @if (count($zonesList) > 0)
                                            @foreach ($zonesList as $zone)
                                                @php
                                                    $zId = data_get($zone, 'id_zona', data_get($zone, 'id'));
                                                    $zName = data_get($zone, 'nama_zona', data_get($zone, 'name'));
                                                @endphp
                                                <option value="{{ $zId }}">{{ $zName }}</option>
                                            @endforeach
                                        @else
                                            <option value="1">Commoner</option>
                                            <option value="2">Noble</option>
                                            <option value="3">Royalty</option>
                                        @endif
                                    </select>
                                    <input name="hours" class="form-control" type="number" min="1" value="2" style="width:75px;" required>
                                    <button class="btn btn-primary btn-sm" type="submit">+ Billing</button>
                                </form>
                                <form action="{{ route('admin.akun.destroy', $accId) }}" method="POST" style="display:inline-block; margin-left:4px;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection