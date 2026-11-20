@extends('layouts.app')

@section('title', 'Profile - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')
<main class="container">
  <div class="section-header">
    <div>
      <div class="section-title">Profil Member & Sisa Billing</div>
      <div class="section-sub">Informasi akun, kuota per zona, sesi aktif, dan riwayat transaksi</div>
    </div>
    <a href="{{ route('user.billing') }}" class="btn btn-primary">+ Beli Billing</a>
  </div>

  <div class="grid-4">
    <div class="card">
      <div class="muted">Identitas Akun</div>
      <div id="profile-name" class="stat-val" style="font-size:20px;">-</div>
      <div id="profile-meta" class="muted mt-12">-</div>
    </div>
    <div class="card">
      <div class="muted">Sisa Billing Commoner</div>
      <div id="quota-commoner" class="stat-val">0j 0m</div>
    </div>
    <div class="card">
      <div class="muted">Sisa Billing Noble</div>
      <div id="quota-noble" class="stat-val">0j 0m</div>
    </div>
    <div class="card">
      <div class="muted">Sisa Billing Royalty</div>
      <div id="quota-royalty" class="stat-val">0j 0m</div>
    </div>
  </div>

  <div class="card mt-16">
    <div class="row-between">
      <div>
        <h3>Sesi PC Aktif</h3>
        <div id="active-session-text" class="muted mt-12">Tidak ada sesi aktif</div>
      </div>
      <button id="remote-logout-btn" class="btn btn-danger btn-sm" type="button">Logout Sesi Jarak Jauh</button>
    </div>
  </div>

  <div class="card mt-16">
    <h3 style="margin-bottom:12px;">Riwayat Transaksi Billing</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID Transaksi</th>
            <th>Waktu</th>
            <th>Zona & Durasi</th>
            <th>Metode</th>
            <th>Nominal</th>
          </tr>
        </thead>
        <tbody id="user-history-body"></tbody>
      </table>
    </div>
  </div>
</main>
@endsection