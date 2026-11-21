@extends('layouts.app')

@section('title', 'Denah PC - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')
<main
    id="denah-page-root"
    class="container"
    data-pcs='@json($pcs ?? [])'
    data-zones='@json($zones ?? [])'
>
    <div class="section-header">
        <div>
            <div class="section-title">Denah PC & Ketersediaan Komputer</div>
            <div class="section-sub">Pilih workstation yang siap digunakan untuk bermain</div>
        </div>
        <div id="denah-summary" class="nav-group"></div>
    </div>

    <div class="toolbar">
        <button class="btn btn-primary btn-sm" data-zone-filter="ALL" type="button">Semua</button>
        <button class="btn btn-secondary btn-sm" data-zone-filter="Commoner" type="button">Commoner</button>
        <button class="btn btn-secondary btn-sm" data-zone-filter="Noble" type="button">Noble</button>
        <button class="btn btn-secondary btn-sm" data-zone-filter="Royalty" type="button">Royalty</button>
        <label class="muted" style="margin-left:auto; display:flex; align-items:center; gap:6px; cursor:pointer;">
            <input id="only-available-check" type="checkbox"> Hanya PC Kosong
        </label>
    </div>

    <div id="denah-grid" class="pc-grid"></div>
</main>
@endsection