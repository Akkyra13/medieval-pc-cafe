@extends('layouts.app')

@section('title', 'Profil Member - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')

@php
    $userObj = $user ?? null;
    $billingObj = $billing ?? null;
    $transactionsList = $transactions ?? ($riwayatTransaksi ?? ($userTransactions ?? collect()));

    $formatJamMenit = function ($menit) {
        $totalMenit = max(0, (int) ($menit ?? 0));
        $jam = intdiv($totalMenit, 60);
        $sisaMenit = $totalMenit % 60;
        return $jam . 'j ' . str_pad((string) $sisaMenit, 2, '0', STR_PAD_LEFT) . 'm';
    };

    $ambilKuotaZona = function ($billing, $zonaKeys, $idZona) {
        if (empty($billing)) return 0;

        if ($billing instanceof \Illuminate\Support\Collection || is_array($billing)) {
            foreach ($zonaKeys as $key) {
                $val = data_get($billing, $key);
                if (!is_null($val) && !is_array($val) && !is_object($val)) {
                    return (int) $val;
                }
            }
            $byId = data_get($billing, $idZona);
            if (!is_null($byId) && !is_array($byId) && !is_object($byId)) {
                return (int) $byId;
            }

            foreach ($billing as $item) {
                $itemZona = strtolower((string) data_get($item, 'zona', data_get($item, 'zone', data_get($item, 'nama_zona', data_get($item, 'id_zona', '')))));
                if (in_array($itemZona, array_map('strtolower', $zonaKeys), true) || (string) $itemZona === (string) $idZona) {
                    return (int) data_get($item, 'sisa_menit', data_get($item, 'menit', data_get($item, 'kuota', data_get($item, 'saldo', 0))));
                }
            }
        }

        foreach ($zonaKeys as $key) {
            $val = data_get($billing, $key);
            if (!is_null($val)) {
                return (int) $val;
            }
        }

        return (int) data_get($billing, 'sisa_menit', data_get($billing, 'menit', data_get($billing, 'kuota', 0)));
    };

    $commonerMenit = $ambilKuotaZona($billingObj, ['commoner', 'sisa_commoner', 'quota_commoner'], 1);
    $nobleMenit    = $ambilKuotaZona($billingObj, ['noble', 'sisa_noble', 'quota_noble'], 2);
    $royaltyMenit  = $ambilKuotaZona($billingObj, ['royalty', 'sisa_royalty', 'quota_royalty'], 3);

    $userId    = data_get($userObj, 'id_akun', data_get($userObj, 'id', '-'));
    $username  = data_get($userObj, 'username', '-');
    $namaUser  = data_get($userObj, 'name', data_get($userObj, 'nama', '-'));
    $noTelp    = data_get($userObj, 'phone', data_get($userObj, 'no_hp', data_get($userObj, 'telepon', '-')));
    $activePc  = data_get($userObj, 'active_pc', data_get($userObj, 'activePc', data_get($billingObj, 'id_pc', data_get($billingObj, 'pc_id'))));
@endphp

<main
    id="profile-page-root"
    class="container"
    data-user='@json($userObj)'
    data-billing='@json($billingObj)'
    data-transactions='@json($transactionsList)'
>
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
            <div id="profile-name" class="stat-val" style="font-size:20px;">{{ $namaUser }}</div>
            <div id="profile-meta" class="muted mt-12">{{ '@' . $username }} • ID: {{ $userId }} • {{ $noTelp }}</div>
        </div>
        <div class="card">
            <div class="muted">Sisa Billing Commoner</div>
            <div id="quota-commoner" class="stat-val">{{ $formatJamMenit($commonerMenit) }}</div>
        </div>
        <div class="card">
            <div class="muted">Sisa Billing Noble</div>
            <div id="quota-noble" class="stat-val">{{ $formatJamMenit($nobleMenit) }}</div>
        </div>
        <div class="card">
            <div class="muted">Sisa Billing Royalty</div>
            <div id="quota-royalty" class="stat-val">{{ $formatJamMenit($royaltyMenit) }}</div>
        </div>
    </div>

    <div class="card mt-16">
        <div class="row-between">
            <div>
                <h3>Sesi PC Aktif</h3>
                <div id="active-session-text" class="muted mt-12">
                    {{ $activePc ? 'Sedang terhubung di unit ' . $activePc : 'Tidak ada sesi aktif' }}
                </div>
                <div class="muted mt-12">Billing akan tetap terkuras saat PC terkunci</div>
            </div>
            <button id="remote-logout-btn" class="btn btn-danger btn-sm" type="button">Lock</button>
        </div>
    </div>

    <div class="card mt-16">
        <h3 style="margin-bottom:12px;">Ganti Password</h3>
        <form action="{{ route('user.password.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="current-password">Password Lama</label>
                <input id="current-password" name="current_password" class="form-control" type="password" required>
            </div>
            <div class="form-group">
                <label for="new-password">Password Baru</label>
                <input id="new-password" name="password" class="form-control" type="password" required>
            </div>
            <div class="form-group">
                <label for="new-password-confirmation">Konfirmasi Password Baru</label>
                <input id="new-password-confirmation" name="password_confirmation" class="form-control" type="password" required>
            </div>
            <button class="btn btn-primary btn-sm" type="submit">Simpan Password</button>
        </form>
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