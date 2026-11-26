@extends('layouts.app')

@section('title', 'Manajemen PC - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $pcsList = $pcs ?? collect();
    $zonesList = $zones ?? collect();
    $usersList = $users ?? ($accounts ?? collect());
    $statusOptions = $pcStatuses ?? ['Tersedia', 'Digunakan', 'Terkunci', 'Maintenance'];

    $extractUserLabel = function ($val) {
        if (is_null($val) || $val === false) return null;

        if (is_array($val) || is_object($val)) {
            if ($val instanceof \Illuminate\Support\Collection || (is_array($val) && array_is_list($val))) {
                $activeItem = collect($val)->first(function ($item) {
                    $status = strtolower((string) data_get($item, 'status', data_get($item, 'status_sesi', '')));
                    $endTime = data_get($item, 'waktu_selesai', data_get($item, 'selesai', data_get($item, 'end_at')));
                    return in_array($status, ['aktif', 'berjalan', 'digunakan', 'terkunci', 'active'], true) || is_null($endTime);
                }) ?? collect($val)->last();

                return $activeItem ? call_user_func(function ($item) {
                    $nested = data_get($item, 'akun.username')
                        ?? data_get($item, 'akun.nama')
                        ?? data_get($item, 'akun.name')
                        ?? data_get($item, 'user.username')
                        ?? data_get($item, 'user.nama')
                        ?? data_get($item, 'user.name')
                        ?? data_get($item, 'member.username')
                        ?? data_get($item, 'member.nama')
                        ?? data_get($item, 'username')
                        ?? data_get($item, 'nama')
                        ?? data_get($item, 'name');
                    return (!is_null($nested) && trim((string) $nested) !== '' && trim((string) $nested) !== '-')
                        ? (string) $nested
                        : null;
                }, $activeItem) : null;
            }

            $nested = data_get($val, 'username')
                ?? data_get($val, 'nama')
                ?? data_get($val, 'name')
                ?? data_get($val, 'akun.username')
                ?? data_get($val, 'akun.nama')
                ?? data_get($val, 'akun.name')
                ?? data_get($val, 'user.username')
                ?? data_get($val, 'user.nama')
                ?? data_get($val, 'user.name');

            return (!is_null($nested) && trim((string) $nested) !== '' && trim((string) $nested) !== '-')
                ? (string) $nested
                : null;
        }

        $str = trim((string) $val);
        return ($str !== '' && $str !== '-') ? $str : null;
    };

    $resolvePcUser = function ($pc, $pcId, $pcStatus) use ($usersList, $extractUserLabel) {
        if (strtolower((string) $pcStatus) === 'tersedia') {
            return '-';
        }

        $candidates = [
            data_get($pc, 'user_aktif'),
            data_get($pc, 'active_user'),
            data_get($pc, 'activeUser'),
            data_get($pc, 'sesiBermain'),
            data_get($pc, 'sesi_bermain'),
            data_get($pc, 'sesiAktif'),
            data_get($pc, 'sesi_aktif'),
            data_get($pc, 'sesi'),
            data_get($pc, 'akun'),
            data_get($pc, 'user'),
            data_get($pc, 'member'),
            data_get($pc, 'username'),
        ];

        foreach ($candidates as $cand) {
            $found = $extractUserLabel($cand);
            if (!is_null($found)) {
                return $found;
            }
        }

        if (!empty($pcId) && ($usersList instanceof \Illuminate\Support\Collection || is_array($usersList))) {
            foreach ($usersList as $u) {
                $uPc = data_get($u, 'active_pc')
                    ?? data_get($u, 'activePc')
                    ?? data_get($u, 'id_pc')
                    ?? data_get($u, 'pc_id')
                    ?? data_get($u, 'sesiBermain.id_pc')
                    ?? data_get($u, 'sesi_bermain.id_pc');

                if (!is_null($uPc) && (string) $uPc === (string) $pcId) {
                    $uName = data_get($u, 'username') ?? data_get($u, 'nama') ?? data_get($u, 'name');
                    if (!is_null($uName) && trim((string) $uName) !== '') {
                        return (string) $uName;
                    }
                }
            }
        }

        return '-';
    };
@endphp
<main
    id="admin-pc-root"
    class="container"
    data-pcs='@json($pcsList)'
    data-zones='@json($zonesList)'
>
    <div class="section-header">
        <div>
            <div class="section-title">Manajemen Komputer & Status Sesi</div>
            <div class="section-sub">Kelola unit workstation, zona layanan, IP address, dan status operasional</div>
        </div>
    </div>

    <div id="admin-pc-stats" class="grid-4"></div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">Tambah Komputer Baru</h3>
        <form action="{{ route('admin.pc.store') }}" method="POST">
            @csrf
            <div class="grid-4">
                <div class="form-group">
                    <label for="pc-id-input">ID PC</label>
                    <input id="pc-id-input" name="id_pc" class="form-control" type="text" placeholder="PC-07" value="{{ old('id_pc') }}" required>
                    @error('id_pc') <small>{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="pc-zone-input">Zona</label>
                    <select id="pc-zone-input" name="id_zona" class="form-control">
                        @if (count($zonesList) > 0)
                            @foreach ($zonesList as $zone)
                                @php
                                    $zId = data_get($zone, 'id_zona', data_get($zone, 'id'));
                                    $zName = data_get($zone, 'nama_zona', data_get($zone, 'name'));
                                @endphp
                                <option value="{{ $zId }}" {{ (string) old('id_zona') === (string) $zId ? 'selected' : '' }}>{{ $zName }}</option>
                            @endforeach
                        @else
                            <option value="1" {{ old('id_zona') == '1' ? 'selected' : '' }}>Commoner</option>
                            <option value="2" {{ old('id_zona') == '2' ? 'selected' : '' }}>Noble</option>
                            <option value="3" {{ old('id_zona') == '3' ? 'selected' : '' }}>Royalty</option>
                        @endif
                    </select>
                    @error('id_zona') <small>{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="pc-ip-input">IP Address</label>
                    <input id="pc-ip-input" name="ip_address" class="form-control" type="text" placeholder="192.168.1.107" value="{{ old('ip_address') }}" required>
                    @error('ip_address') <small>{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="pc-status-input">Status</label>
                    <select id="pc-status-input" name="status" class="form-control">
                        @foreach ($statusOptions as $stVal)
                            <option value="{{ $stVal }}" {{ (string) old('status') === (string) $stVal ? 'selected' : '' }}>{{ $stVal }}</option>
                        @endforeach
                    </select>
                    @error('status_pc') <small>{{ $message }}</small> @enderror
                </div>
            </div>
            <button class="btn btn-primary btn-sm" type="submit">+ Simpan PC</button>
        </form>
    </div>

    <div class="card mt-16">
        <div class="toolbar">
            <input id="admin-pc-search" class="form-control" type="text" placeholder="Cari ID PC, IP, atau user...">
            <select id="admin-pc-status" class="form-control" style="max-width:220px;">
                <option value="ALL">Semua Status</option>
                @foreach ($statusOptions as $stVal)
                    <option value="{{ $stVal }}">{{ $stVal }}</option>
                @endforeach
            </select>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID PC</th>
                        <th>Zona</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>User Aktif</th>
                        <th>Update & Hapus</th>
                    </tr>
                </thead>
                <tbody id="admin-pc-body">
                    @foreach ($pcsList as $pc)
                        @php
                            $pcId = data_get($pc, 'id_pc', data_get($pc, 'id'));
                            $pcZoneName = data_get($pc, 'nama_zona', data_get($pc, 'zone.nama_zona', data_get($pc, 'zone.name', data_get($pc, 'id_zona', data_get($pc, 'zoneId', 1)))));
                            $pcIp = data_get($pc, 'ip_address', data_get($pc, 'ip', '-'));
                            $pcStatus = data_get($pc, 'status', data_get($pc, 'status_pc', 'Tersedia'));
                            $pcUser = $resolvePcUser($pc, $pcId, $pcStatus);
                        @endphp
                        <tr>
                            <td><strong>{{ $pcId }}</strong></td>
                            <td>{{ $pcZoneName }}</td>
                            <td>{{ $pcIp }}</td>
                            <td>{{ $pcStatus }}</td>
                            <td>{{ $pcUser }}</td>
                            <td>
                                <form action="{{ route('admin.pc.update', $pcId) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-control" style="width:auto;">
                                        @foreach ($statusOptions as $stVal)
                                            <option value="{{ $stVal }}" {{ (string) $pcStatus === (string) $stVal ? 'selected' : '' }}>{{ $stVal }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary btn-sm" type="submit">Update</button>
                                </form>
                                <form action="{{ route('admin.pc.destroy', $pcId) }}" method="POST" style="display:inline-block; margin-left:4px;">
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