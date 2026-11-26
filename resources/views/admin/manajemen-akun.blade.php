@extends('layouts.app')

@section('title', 'Manajemen Akun - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $usersList = $users ?? collect();
    $zonesList = $zones ?? collect();

    $ambilKuotaAkun = function ($acc, $zonaKeys, $idZona) {
        $zonaLower = array_map('strtolower', $zonaKeys);

        foreach ($zonaKeys as $key) {
            $val = data_get($acc, $key);
            if (!is_null($val) && !is_array($val) && !is_object($val)) {
                return (int) $val;
            }
        }

        $containers = [
            data_get($acc, 'quota'),
            data_get($acc, 'quotas'),
            data_get($acc, 'saldo'),
            data_get($acc, 'billings'),
            data_get($acc, 'billing'),
            data_get($acc, 'kuota'),
        ];

        foreach ($containers as $container) {
            if (empty($container)) {
                continue;
            }

            foreach ($zonaKeys as $key) {
                $val = data_get($container, $key);
                if (!is_null($val) && !is_array($val) && !is_object($val)) {
                    return (int) $val;
                }
            }

            $byId = data_get($container, $idZona);
            if (!is_null($byId) && !is_array($byId) && !is_object($byId)) {
                return (int) $byId;
            }

            if ($container instanceof \Illuminate\Support\Collection || is_array($container)) {
                foreach ($container as $item) {
                    $itemZona = strtolower((string) data_get(
                        $item,
                        'zona',
                        data_get($item, 'zone', data_get($item, 'nama_zona', data_get($item, 'id_zona', '')))
                    ));

                    if (in_array($itemZona, $zonaLower, true) || (string) $itemZona === (string) $idZona) {
                        return (int) data_get(
                            $item,
                            'sisa_menit',
                            data_get(
                                $item,
                                'menit',
                                data_get(
                                    $item,
                                    'kuota',
                                    data_get(
                                        $item,
                                        'saldo',
                                        data_get($item, 'balance', data_get($item, 'durasi_menit', 0))
                                    )
                                )
                            )
                        );
                    }
                }
            }
        }

        return 0;
    };

    $formatDurasi = function ($menit) {
        $safe = max(0, (int) ($menit ?? 0));
        $jam = intdiv($safe, 60);
        $sisa = $safe % 60;
        return $jam . 'j ' . str_pad((string) $sisa, 2, '0', STR_PAD_LEFT) . 'm';
    };
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
            <div class="section-sub">Registrasi member, update akun, tambah kuota billing, dan kelola akun</div>
        </div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">Registrasi Member Baru</h3>
        <form action="{{ route('admin.akun.store') }}" method="POST">
            @csrf
            <div class="grid-4">
                <div class="form-group">
                    <label for="acc-username">Username</label>
                    <input id="acc-username" name="username" class="form-control" type="text" value="{{ old('username') }}" placeholder="Contoh: oguricap" required>
                </div>
                <div class="form-group">
                    <label for="acc-name">Nama Lengkap</label>
                    <input id="acc-name" name="name" class="form-control" type="text" value="{{ old('name') }}" placeholder="Contoh: Oguri Cap" required>
                </div>
                <div class="form-group">
                    <label for="acc-phone">No. Telepon</label>
                    <input id="acc-phone" name="phone" class="form-control" type="text" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" required>
                </div>
                <div class="form-group">
                    <label for="acc-password">Password / PIN</label>
                    <input id="acc-password" name="password" class="form-control" type="password" placeholder="Masukkan password / PIN" required>
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

                            $q1 = $ambilKuotaAkun($acc, ['commoner', 'sisa_commoner', 'quota_commoner', 'saldo_commoner'], 1);
                            $q2 = $ambilKuotaAkun($acc, ['noble', 'sisa_noble', 'quota_noble', 'saldo_noble'], 2);
                            $q3 = $ambilKuotaAkun($acc, ['royalty', 'sisa_royalty', 'quota_royalty', 'saldo_royalty'], 3);
                        @endphp
                        <tr>
                            <td>{{ $accId }}</td>
                            <td>
                                <strong>{{ $accName }}</strong>
                                <div class="muted">{{ '@' . $accUsername }}</div>
                            </td>
                            <td>{{ $accPhone }}</td>
                            <td>{{ $formatDurasi($q1) }}</td>
                            <td>{{ $formatDurasi($q2) }}</td>
                            <td>{{ $formatDurasi($q3) }}</td>
                            <td>
                                <form action="{{ route('admin.akun.update', $accId) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    @csrf
                                    @method('PUT')
                                    <input
                                        name="name"
                                        class="form-control"
                                        type="text"
                                        value="{{ $accName !== '-' ? $accName : '' }}"
                                        placeholder="Nama lengkap"
                                        aria-label="Nama lengkap"
                                        style="width:145px;"
                                        required
                                    >
                                    <input
                                        name="phone"
                                        class="form-control"
                                        type="text"
                                        value="{{ $accPhone !== '-' ? $accPhone : '' }}"
                                        placeholder="No. telepon"
                                        aria-label="No. telepon"
                                        style="width:145px;"
                                        required
                                    >
                                    <button class="btn btn-secondary btn-sm" type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.akun.topup', $accId) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    @csrf
                                    @method('PUT')
                                    <select name="id_zona" class="form-control" aria-label="Pilih zona top-up" style="width:auto;">
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
                                    <input
                                        name="hours"
                                        class="form-control"
                                        type="number"
                                        min="1"
                                        value="2"
                                        placeholder="Jam"
                                        aria-label="Durasi jam top-up"
                                        style="width:75px;"
                                        required
                                    >
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
