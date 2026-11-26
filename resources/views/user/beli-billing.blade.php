@extends('layouts.app')

@section('title', 'Beli Billing - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')
@php
    $billsList = $bills ?? ($packages ?? collect());
    $zonesList = $zones ?? collect();

    $fallbackRates = [
        1 => 7000,
        2 => 12000,
        3 => 25000,
    ];

    $extractNumeric = function ($item, array $keys, $default = 0) {
        foreach ($keys as $key) {
            $val = data_get($item, $key);
            if (!is_null($val) && $val !== '') {
                if (is_numeric($val)) {
                    return (float) $val;
                }
                $cleaned = preg_replace('/[^\d.-]/', '', (string) $val);
                if ($cleaned !== '' && is_numeric($cleaned)) {
                    return (float) $cleaned;
                }
            }
        }
        return (float) $default;
    };

    $formatRp = function ($num) {
        return 'Rp ' . number_format((float) ($num ?? 0), 0, ',', '.');
    };
@endphp
<main
    id="billing-page-root"
    class="container"
    data-bills='@json($billsList)'
    data-zones='@json($zonesList)'
>
    <div class="section-header">
        <div>
            <div class="section-title">Beli Billing & Top-up Kuota</div>
            <div class="section-sub">Pilih zona workstation, paket durasi atau jam kustom, dan metode pembayaran</div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom:12px;">1. Pilih Zona</h3>
        <div id="billing-zones" class="grid-3">
            @foreach ($zonesList as $idx => $zone)
                @php
                    $zId = data_get($zone, 'id_zona', data_get($zone, 'id', $idx + 1));
                    $zName = data_get($zone, 'nama_zona', data_get($zone, 'name', 'Zona'));
                    $zRate = $extractNumeric($zone, [
                        'tarif_per_jam',
                        'harga_per_jam',
                        'tarif',
                        'harga',
                        'ratePerHour',
                        'rate_per_hour',
                        'price',
                        'biaya_per_jam',
                    ], 0);
                    if ($zRate <= 0 && isset($fallbackRates[(int) $zId])) {
                        $zRate = $fallbackRates[(int) $zId];
                    }
                    $zCpu = data_get($zone, 'cpu', data_get($zone, 'spesifikasi_cpu', '-'));
                @endphp
                <div class="card selectable {{ $idx === 0 ? 'selected' : '' }}" data-select-zone="{{ $zId }}">
                    <div style="font-weight:bold;">{{ $zName }}</div>
                    <div style="color:#22d3ee; font-weight:bold; margin-top:6px;">{{ $formatRp($zRate) }} / jam</div>
                    <div class="muted mt-12">{{ $zCpu ?: '-' }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">2. Pilih Paket atau Jam Kustom</h3>
        <div id="billing-packages" class="grid-4">
            @foreach ($billsList as $idx => $pkg)
                @php
                    $pId = data_get($pkg, 'id_paket', data_get($pkg, 'id_bill', data_get($pkg, 'id', $idx + 1)));
                    $pName = data_get($pkg, 'nama_paket', data_get($pkg, 'nama_bill', data_get($pkg, 'name', 'Paket')));
                    $pHours = $extractNumeric($pkg, ['durasi_jam', 'hours', 'durasi', 'jam'], 1);
                    $pPrice = $extractNumeric($pkg, [
                        'harga',
                        'harga_paket',
                        'harga_jual',
                        'price',
                        'tarif',
                        'nominal',
                        'total_harga',
                        'biaya',
                    ], 0);
                    $pActiveRaw = data_get($pkg, 'active', data_get($pkg, 'is_active', data_get($pkg, 'aktif', true)));
                    $pActive = is_string($pActiveRaw)
                        ? !in_array(strtolower($pActiveRaw), ['0', 'false', 'nonaktif', 'inactive', 'tidak aktif'], true)
                        : (bool) $pActiveRaw;
                @endphp
                @if ($pActive)
                    <div class="card selectable {{ $idx === 1 ? 'selected' : '' }}" data-select-pkg="{{ $pId }}">
                        <div style="font-weight:bold;">{{ $pName }}</div>
                        <div class="muted mt-12">{{ $pHours }} Jam</div>
                        <div style="color:#34d399; font-weight:bold; margin-top:8px;">{{ $formatRp($pPrice) }}</div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="toolbar mt-16">
            <button id="custom-minus" class="btn btn-secondary btn-sm" type="button">-</button>
            <span id="custom-hours" style="font-weight:bold;">3 Jam</span>
            <button id="custom-plus" class="btn btn-secondary btn-sm" type="button">+</button>
            <button id="use-custom-btn" class="btn btn-secondary btn-sm" type="button">Pakai Jam Kustom</button>
        </div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">3. Ringkasan & Pembayaran</h3>
        <form action="{{ route('user.billing.store') }}" method="POST">
            @csrf
            <input type="hidden" id="billing-zone-id" name="id_zona" value="1">
            <input type="hidden" id="billing-package-id" name="id_paket" value="">
            <input type="hidden" id="billing-custom-hours" name="hours" value="3">
            <input type="hidden" id="billing-use-custom" name="use_custom" value="0">

            <div class="muted">Zona: <strong id="summary-zone">-</strong></div>
            <div class="muted mt-12">Item: <strong id="summary-package">-</strong></div>
            <div class="muted mt-12">Total: <strong id="summary-total">Rp 0</strong></div>

            <div class="form-group mt-16" style="max-width:300px;">
                <label for="payment-method">Metode Pembayaran</label>
                <select id="payment-method" name="payment_method" class="form-control">
                    <option value="QRIS Dinamis">QRIS Dinamis</option>
                    <option value="Virtual Account">Virtual Account</option>
                    <option value="Tunai Kasir">Tunai Kasir</option>
                </select>
            </div>

            <button id="pay-billing-btn" class="btn btn-primary" type="submit">Bayar Billing</button>
        </form>
    </div>
</main>
@endsection
