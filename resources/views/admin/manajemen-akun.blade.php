@extends('layouts.app')

@section('title', 'Manajemen Akun - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
<main class="container">
  <div class="section-header">
    <div>
      <div class="section-title">Manajemen Akun</div>
      <div class="section-sub">Kelola akun member, kuota billing, dan status pengguna</div>
    </div>
    <button id="open-add-account-btn" class="btn btn-primary" type="button">+ Registrasi Member</button>
  </div>

  <div class="toolbar">
    <input id="admin-account-search" class="form-control" style="max-width:280px;" type="text" placeholder="Cari username, nama, atau ID...">
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username & Nama</th>
            <th>No. Telp</th>
            <th>Commoner</th>
            <th>Noble</th>
            <th>Royalty</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="admin-account-body"></tbody>
      </table>
    </div>
  </div>
</main>
@include('partials.modal')
@endsection