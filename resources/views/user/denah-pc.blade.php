@extends('layouts.app')

@section('title', 'Denah PC - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')
<main class="container">
  <div class="section-header">
    <div>
      <div class="section-title">Denah PC & Ketersediaan Komputer</div>
      <div class="section-sub">Pilih workstation yang siap digunakan untuk bermain</div>
    </div>
    <div id="denah-summary" class="nav-group"></div>
  </div>
  <div class="toolbar">
    <button class="btn btn-primary btn-sm" data-zone-filter="ALL">Semua</button>
    <button class="btn btn-secondary btn-sm" data-zone-filter="Commoner">Commoner</button>
    <button class="btn btn-secondary btn-sm" data-zone-filter="Noble">Noble</button>
    <button class="btn btn-secondary btn-sm" data-zone-filter="Royalty">Royalty</button>
    <label class="muted" style="margin-left:auto; display:flex; align-items:center; gap:6px; cursor:pointer;">
      <input id="only-available-check" type="checkbox"> Hanya PC Kosong
    </label>
  </div>
  <div id="denah-grid" class="pc-grid"></div>
</main>
@endsection