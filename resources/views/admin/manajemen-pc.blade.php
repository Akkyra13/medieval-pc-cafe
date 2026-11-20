@extends('layouts.app')

@section('title', 'Manajemen PC - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
<main class="container">
  <div class="section-header">
    <div>
      <div class="section-title">Manajemen PC</div>
      <div class="section-sub">Kelola status operasional, zona, IP address, dan unit komputer</div>
    </div>
    <button id="open-add-pc-btn" class="btn btn-primary" type="button">+ Tambah Komputer Baru</button>
  </div>

  <div id="admin-pc-stats" class="grid-4"></div>

  <div class="toolbar">
    <input id="admin-pc-search" class="form-control" style="max-width:260px;" type="text" placeholder="Cari No. PC, IP, atau User...">
    <select id="admin-pc-status" class="form-control" style="max-width:200px;">
      <option value="ALL">Semua Status</option>
      <option value="Tersedia">Tersedia</option>
      <option value="Digunakan">Digunakan</option>
      <option value="Maintenance">Maintenance</option>
    </select>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID PC</th>
            <th>Zona</th>
            <th>IP Address</th>
            <th>Status</th>
            <th>Pengguna</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="admin-pc-body"></tbody>
      </table>
    </div>
  </div>
</main>
@include('partials.modal')
@endsection