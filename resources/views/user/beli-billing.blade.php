@extends('layouts.app')

@section('title', 'Beli Billing - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')
<main class="container">
  <div class="section-header">
    <div>
      <div class="section-title">Beli Billing Online</div>
      <div class="section-sub">Pilih zona, paket jam atau top-up fleksibel, lalu bayar langsung</div>
    </div>
  </div>
  <div class="grid-2-1">
    <div>
      <div class="card">
        <h3 style="margin-bottom:12px;">1. Pilih Zona Bermain</h3>
        <div id="billing-zones" class="grid-3" style="margin-bottom:0;"></div>
      </div>

      <div class="card mt-16">
        <h3 style="margin-bottom:12px;">2. Pilih Paket Billing</h3>
        <div id="billing-packages" class="grid-3" style="margin-bottom:16px;"></div>
        <div class="row-between" style="background:#0b1017; padding:12px; border-radius:8px; border:1px solid #1f2937;">
          <div>
            <div style="font-weight:bold; font-size:14px;">Top-up Jam Fleksibel</div>
            <div class="muted">Atur durasi jam sesuai kebutuhan</div>
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <button id="custom-minus" class="btn btn-secondary btn-sm" type="button">-</button>
            <span id="custom-hours" style="min-width:52px; text-align:center; font-weight:bold;">3 Jam</span>
            <button id="custom-plus" class="btn btn-secondary btn-sm" type="button">+</button>
            <button id="use-custom-btn" class="btn btn-primary btn-sm" type="button">Pilih</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <h3 style="margin-bottom:12px;">Ringkasan Order</h3>
      <div class="row-between mt-12">
        <span class="muted">Zona Terpilih</span>
        <strong id="summary-zone">-</strong>
      </div>
      <div class="row-between mt-12">
        <span class="muted">Paket / Durasi</span>
        <strong id="summary-package">-</strong>
      </div>
      <div class="row-between mt-12">
        <span class="muted">Biaya Layanan</span>
        <strong style="color:#34d399;">GRATIS</strong>
      </div>
      <div class="total-box">
        <div class="muted">Total Tagihan</div>
        <div id="summary-total" style="font-size:24px; font-weight:bold; color:#22d3ee; margin-top:4px;">Rp 0</div>
      </div>
      <div class="form-group">
        <label for="payment-method">Metode Pembayaran</label>
        <select id="payment-method" class="form-control">
          <option value="QRIS Dinamis">QRIS Dinamis</option>
          <option value="Virtual Account">Virtual Account</option>
          <option value="Saldo Deposit">Saldo Deposit</option>
        </select>
      </div>
      <button id="pay-billing-btn" class="btn btn-primary" style="width:100%" type="button">Bayar Sekarang & Aktifkan Billing</button>
    </div>
  </div>
</main>
@endsection