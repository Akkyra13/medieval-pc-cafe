@extends('layouts.app')

@section('title', 'Profil Member - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-user')

@php
    $rawUser = $user ?? null;
    if ($rawUser instanceof \Illuminate\Support\Collection) {
        $userObj = $rawUser->first();
    } elseif (is_array($rawUser) && array_is_list($rawUser)) {
        $userObj = $rawUser[0] ?? null;
    } else {
        $userObj = $rawUser;
    }

    $billingObj = $billing
        ?? (data_get($userObj, 'saldoWaktu')
        ?? (data_get($userObj, 'saldo_waktu')
        ?? (data_get($userObj, 'quotas')
        ?? (data_get($userObj, 'quota')
        ?? null))));

    $transactionsList = $transactions
        ?? ($riwayatTransaksi
        ?? ($userTransactions
        ?? ($transaksi
        ?? ($history
        ?? (data_get($userObj, 'transaksiPelanggan')
        ?? (data_get($userObj, 'transaksi_pelanggan')
        ?? (data_get($userObj, 'transactions')
        ?? (data_get($userObj, 'transaksi')
        ?? collect()))))))));

    $formatJamMenit = function ($menit) {
        $totalMenit = max(0, (int) ($menit ?? 0));
        $jam = intdiv($totalMenit, 60);
        $sisaMenit = $totalMenit % 60;
        return $jam . 'j ' . str_pad((string) $sisaMenit, 2, '0', STR_PAD_LEFT) . 'm';
    };

    $formatRupiah = function ($nominal) {
        $cleaned = is_numeric($nominal)
            ? (float) $nominal
            : (float) preg_replace('/[^\d.-]/', '', (string) ($nominal ?? 0));
        return 'Rp ' . number_format($cleaned, 0, ',', '.');
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
                $itemZonaId = data_get($item, 'id_zona', data_get($item, 'zona_id', data_get($item, 'zona.id_zona', data_get($item, 'zona.id', ''))));
                $itemZonaNama = strtolower((string) data_get(
                    $item,
                    'zona.nama_zona',
                    data_get($item, 'zona.name', data_get($item, 'zona.nama', data_get($item, 'zona', data_get($item, 'zone', data_get($item, 'nama_zona', ''))))))
                ));

                $matchNama = $itemZonaNama !== '' && in_array($itemZonaNama, array_map('strtolower', $zonaKeys), true);
                $matchId = (string) $itemZonaId !== '' && (string) $itemZonaId === (string) $idZona;

                if ($matchNama || $matchId) {
                    $val = data_get($item, 'sisa_menit')
                        ?? data_get($item, 'menit')
                        ?? data_get($item, 'kuota')
                        ?? data_get($item, 'saldo')
                        ?? data_get($item, 'balance')
                        ?? data_get($item, 'durasi_menit')
                        ?? 0;
                    return (int) $val;
                }
            }
        }

        foreach ($zonaKeys as $key) {
            $val = data_get($billing, $key);
            if (!is_null($val) && !is_array($val) && !is_object($val)) {
                return (int) $val;
            }
        }

        return (int) (data_get($billing, 'sisa_menit')
            ?? data_get($billing, 'menit')
            ?? data_get($billing, 'kuota')
            ?? data_get($billing, 'saldo')
            ?? 0);
    };

    $resolveZonaDurasi = function ($tx) {
        $directKeys = [
            'label',
            'zona_durasi',
            'zona_dan_durasi',
            'paket_durasi',
            'paket_dan_durasi',
            'keterangan',
            'deskripsi',
            'catatan',
            'item',
        ];

        foreach ($directKeys as $key) {
            $val = data_get($tx, $key);
            if (is_array($val) || is_object($val)) {
                $val = data_get($val, 'nama') ?? data_get($val, 'name') ?? data_get($val, 'label');
            }
            if (!is_null($val) && trim((string) $val) !== '' && trim((string) $val) !== '-') {
                return trim((string) $val);
            }
        }

        $zonaRaw = data_get($tx, 'nama_zona')
            ?? data_get($tx, 'zona')
            ?? data_get($tx, 'zone')
            ?? data_get($tx, 'zona.nama_zona')
            ?? data_get($tx, 'zona.name')
            ?? data_get($tx, 'zona.nama')
            ?? data_get($tx, 'paket.zona.nama_zona')
            ?? data_get($tx, 'paket.zona.name')
            ?? data_get($tx, 'paket.zona.nama')
            ?? data_get($tx, 'paketBilling.zona.nama_zona')
            ?? data_get($tx, 'paket_billing.zona.nama_zona')
            ?? data_get($tx, 'bill.zona.nama_zona')
            ?? data_get($tx, 'bill.zona.name')
            ?? '';

        if (is_array($zonaRaw) || is_object($zonaRaw)) {
            $zonaRaw = data_get($zonaRaw, 'nama_zona') ?? data_get($zonaRaw, 'name') ?? data_get($zonaRaw, 'nama') ?? '';
        }

        $paketRaw = data_get($tx, 'nama_paket')
            ?? data_get($tx, 'nama_bill')
            ?? data_get($tx, 'paket')
            ?? data_get($tx, 'bill')
            ?? data_get($tx, 'paket.nama_paket')
            ?? data_get($tx, 'paket.name')
            ?? data_get($tx, 'paket.nama')
            ?? data_get($tx, 'paketBilling.nama_paket')
            ?? data_get($tx, 'paket_billing.nama_paket')
            ?? data_get($tx, 'bill.nama_paket')
            ?? data_get($tx, 'bill.name')
            ?? data_get($tx, 'bill.nama')
            ?? '';

        if (is_array($paketRaw) || is_object($paketRaw)) {
            $paketRaw = data_get($paketRaw, 'nama_paket') ?? data_get($paketRaw, 'name') ?? data_get($paketRaw, 'nama') ?? '';
        }

        $durasiJam = data_get($tx, 'durasi_jam')
            ?? data_get($tx, 'durasi_paket')
            ?? data_get($tx, 'durasi')
            ?? data_get($tx, 'hours')
            ?? data_get($tx, 'jam')
            ?? data_get($tx, 'jumlah_jam')
            ?? data_get($tx, 'paket.durasi_jam')
            ?? data_get($tx, 'paket.durasi')
            ?? data_get($tx, 'paket.hours')
            ?? data_get($tx, 'paketBilling.durasi_jam')
            ?? data_get($tx, 'paket_billing.durasi_jam')
            ?? data_get($tx, 'bill.durasi_jam')
            ?? data_get($tx, 'bill.durasi')
            ?? '';

        $durasiMenit = data_get($tx, 'durasi_menit')
            ?? data_get($tx, 'menit')
            ?? data_get($tx, 'minutes')
            ?? data_get($tx, 'paket.durasi_menit')
            ?? '';

        $zonaStr = trim((string) $zonaRaw);
        $paketStr = trim((string) $paketRaw);

        $durText = '';
        if ($durasiJam !== '' && !is_null($durasiJam)) {
            $dStr = trim((string) $durasiJam);
            $durText = stripos($dStr, 'jam') !== false ? " ({$dStr})" : " ({$dStr} Jam)";
        } elseif ($durasiMenit !== '' && !is_null($durasiMenit)) {
            $mStr = trim((string) $durasiMenit);
            $durText = stripos($mStr, 'menit') !== false ? " ({$mStr})" : " ({$mStr} Menit)";
        }

        if ($zonaStr !== '' && $paketStr !== '') {
            return "{$zonaStr} - {$paketStr}{$durText}";
        }
        if ($paketStr !== '') {
            return "{$paketStr}{$durText}";
        }
        if ($zonaStr !== '') {
            return "{$zonaStr}{$durText}";
        }
        if ($durText !== '') {
            return trim($durText, " ()");
        }

        return '-';
    };

    $resolveActivePc = function ($user, $billing) {
        $sesi = data_get($user, 'sesiBermain')
            ?? data_get($user, 'sesi_bermain')
            ?? data_get($user, 'sesiAktif')
            ?? data_get($user, 'sesi_aktif');

        if ($sesi instanceof \Illuminate\Support\Collection || (is_array($sesi) && array_is_list($sesi))) {
            $sesi = collect($sesi)->first(function ($item) {
                $status = strtolower((string) data_get($item, 'status', ''));
                $endTime = data_get($item, 'waktu_selesai', data_get($item, 'selesai', data_get($item, 'end_at')));
                return in_array($status, ['aktif', 'berjalan', 'digunakan', 'terkunci', 'active'], true) || is_null($endTime);
            }) ?? collect($sesi)->last();
        }

        return data_get($sesi, 'id_pc')
            ?? data_get($sesi, 'pc_id')
            ?? data_get($sesi, 'pc.id_pc')
            ?? data_get($sesi, 'pc.id')
            ?? data_get($user, 'active_pc')
            ?? data_get($user, 'activePc')
            ?? data_get($user, 'id_pc')
            ?? data_get($billing, 'id_pc')
            ?? data_get($billing, 'pc_id');
    };

    $commonerMenit = $ambilKuotaZona($billingObj, ['commoner', 'sisa_commoner', 'quota_commoner'], 1);
    $nobleMenit    = $ambilKuotaZona($billingObj, ['noble', 'sisa_noble', 'quota_noble'], 2);
    $royaltyMenit  = $ambilKuotaZona($billingObj, ['royalty', 'sisa_royalty', 'quota_royalty'], 3);

    $userId    = data_get($userObj, 'id_akun', data_get($userObj, 'id', '-'));
    $username  = data_get($userObj, 'username', '-');
    $namaUser  = data_get($userObj, 'name', data_get($userObj, 'nama', '-'));
    $noTelp    = data_get($userObj, 'phone', data_get($userObj, 'no_hp', data_get($userObj, 'telepon', '-')));
    $activePc  = $resolveActivePc($userObj, $billingObj);
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
                <tbody id="user-history-body">
                    @foreach ($transactionsList as $tx)
                        @php
                            $txId = data_get($tx, 'id_transaksi', data_get($tx, 'kode_transaksi', data_get($tx, 'id', '-')));
                            $txTime = data_get($tx, 'waktu', data_get($tx, 'tanggal', data_get($tx, 'created_at', '-')));
                            $txZonaDurasi = $resolveZonaDurasi($tx);
                            $txMethod = data_get($tx, 'metode_pembayaran', data_get($tx, 'metode', data_get($tx, 'method', '-')));
                            $txAmount = data_get($tx, 'nominal', data_get($tx, 'total', data_get($tx, 'total_harga', data_get($tx, 'total_bayar', data_get($tx, 'amount', 0)))));
                        @endphp
                        <tr>
                            <td>{{ $txId }}</td>
                            <td>{{ $txTime }}</td>
                            <td>{{ $txZonaDurasi }}</td>
                            <td>{{ $txMethod }}</td>
                            <td>{{ $formatRupiah($txAmount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection