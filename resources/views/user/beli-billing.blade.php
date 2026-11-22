@extends('layouts.app')

@section('title', 'Beli Billing - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')
@php
    $billsList = $bills ?? collect();
    $zonesList = $zones ?? collect();
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
        <div id="billing-zones" class="grid-3"></div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">2. Pilih Paket atau Jam Kustom</h3>
        <div id="billing-packages" class="grid-4"></div>

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