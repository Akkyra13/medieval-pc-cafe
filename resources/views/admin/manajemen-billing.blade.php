@extends('layouts.app')

@section('title', 'Manajemen Billing - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
<main class="container">
  <div class="section-header">
    <div>
      <div class="section-title">Manajemen Billing (Tarif, Zona & Paket)</div>
      <div class="section-sub">Atur tarif per jam zona, paket billing, dan pantau transaksi</div>
    </div>
    <div class="nav-group">
      <button id="open-add-zone-btn" class="btn btn-primary" type="button">+ Tambah Zona</button>
      <button id="open-add-package-btn" class="btn btn-secondary" type="button">+ Tambah Paket</button>
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
    <h3 style="margin-bottom:12px;">Daftar Transaksi Billing</h3>
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
@include('partials.modal')
@endsection