@extends('layouts.app')

@section('title', 'Manajemen Billing & Tarif - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $billsList = $bills ?? collect();
    $zonesList = $zones ?? collect();
    $transactionsList = $transactions ?? collect();
@endphp
<main
    id="admin-billing-root"
    class="container"
    data-bills='@json($billsList)'
    data-zones='@json($zonesList)'
    data-transactions='@json($transactionsList)'
>
    <div class="section-header">
        <div>
            <div class="section-title">Manajemen Zona, Tarif & Paket Billing</div>
            <div class="section-sub">Kelola tarif per jam tiap zona, paket durasi, dan pantau transaksi masuk</div>
        </div>
    </div>

    <div id="admin-zone-cards" class="grid-3"></div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">Daftar Paket Billing</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Zona</th>
                        <th>Durasi</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="admin-package-body"></tbody>
            </table>
        </div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">Riwayat Transaksi Billing</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Member</th>
                        <th>Waktu</th>
                        <th>Paket / Durasi</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody id="admin-tx-body"></tbody>
            </table>
        </div>
    </div>
</main>
@endsection