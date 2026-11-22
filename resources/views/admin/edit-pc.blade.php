@extends('layouts.app')

@section('title', 'Edit PC - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $pcItem = $data ?? $pc ?? collect();
    $zonesList = $zones ?? data_get($data, 'zones', collect());
    $statusOptions = $pcStatuses ?? ['Tersedia', 'Digunakan', 'Terkunci', 'Maintenance'];

    $pcId = data_get($pcItem, 'id_pc', data_get($pcItem, 'id'));
    $pcZoneId = data_get($pcItem, 'id_zona', data_get($pcItem, 'zoneId', 1));
    $pcIp = data_get($pcItem, 'ip_address', data_get($pcItem, 'ip', ''));
    $pcStatus = data_get($pcItem, 'status', data_get($pcItem, 'status_pc', 'Tersedia'));
@endphp
<main class="container">
    <div class="section-header">
        <div>
            <div class="section-title">Edit Komputer {{ $pcId }}</div>
            <div class="section-sub">Perbarui zona layanan, IP address, dan status operasional unit PC</div>
        </div>
        <a href="{{ route('admin.pc.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card mt-16">
        <form action="{{ route('admin.pc.update', $pcId) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid-4">
                <div class="form-group">
                    <label for="edit-pc-id">ID PC</label>
                    <input id="edit-pc-id" name="id_pc" class="form-control" type="text" value="{{ old('id_pc', $pcId) }}" required>
                </div>

                <div class="form-group">
                    <label for="edit-pc-zone">Zona</label>
                    <select id="edit-pc-zone" name="id_zona" class="form-control">
                        @if (count($zonesList) > 0)
                            @foreach ($zonesList as $zone)
                                @php
                                    $zId = data_get($zone, 'id_zona', data_get($zone, 'id'));
                                    $zName = data_get($zone, 'nama_zona', data_get($zone, 'name'));
                                    $selectedZone = (string) old('id_zona', $pcZoneId) === (string) $zId ? 'selected' : '';
                                @endphp
                                <option value="{{ $zId }}" {{ $selectedZone }}>{{ $zName }}</option>
                            @endforeach
                        @else
                            <option value="1" {{ (string) old('id_zona', $pcZoneId) === '1' ? 'selected' : '' }}>Commoner</option>
                            <option value="2" {{ (string) old('id_zona', $pcZoneId) === '2' ? 'selected' : '' }}>Noble</option>
                            <option value="3" {{ (string) old('id_zona', $pcZoneId) === '3' ? 'selected' : '' }}>Royalty</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-pc-ip">IP Address</label>
                    <input id="edit-pc-ip" name="ip_address" class="form-control" type="text" value="{{ old('ip_address', $pcIp) }}" required>
                </div>

                <div class="form-group">
                    <label for="edit-pc-status">Status</label>
                    <select id="edit-pc-status" name="status" class="form-control">
                        @foreach ($statusOptions as $stVal)
                            <option value="{{ $stVal }}" {{ (string) old('status', $pcStatus) === (string) $stVal ? 'selected' : '' }}>{{ $stVal }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="toolbar mt-16">
                <button class="btn btn-primary btn-sm" type="submit">Simpan Perubahan PC</button>
                <a href="{{ route('admin.pc.index') }}" class="btn btn-secondary btn-sm">Batal</a>
            </div>
        </form>
    </div>
</main>
@endsection