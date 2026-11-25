@extends('layouts.app')

@section('title', 'Manajemen Billing & Tarif - Sistem Billing Warnet')

@section('content')
@include('partials.navbar-admin')
@php
    $billsList = $bills ?? ($packages ?? collect());
    $zonesList = $zones ?? collect();
    $transactionsList = $transactions ?? ($riwayatTransaksi ?? collect());

    $extractPrice = function ($item, array $keys) {
        foreach ($keys as $key) {
            $val = data_get($item, $key);
            if (!is_null($val) && $val !== '') {
                if (is_numeric($val)) {
                    return (float) $val;
                }
                $cleaned = preg_replace('/[^\d.-]/', '', (string) $val);
                if ($cleaned !== '' && is_numeric($cleaned)) {
                    return (float) $cleaned;
                }
            }
        }
        return 0;
    };

    $formatRp = function ($num) {
        return 'Rp ' . number_format((float) ($num ?? 0), 0, ',', '.');
    };
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

    <div id="admin-zone-cards" class="grid-3">
        @foreach ($zonesList as $zone)
            @php
                $zName = data_get($zone, 'nama_zona', data_get($zone, 'name', 'Zona'));
                $zRate = $extractPrice($zone, [
                    'tarif_per_jam',
                    'harga_per_jam',
                    'tarif',
                    'harga',
                    'ratePerHour',
                    'rate_per_hour',
                    'price',
                    'biaya_per_jam',
                ]);
                $zSpec = trim(implode(' • ', array_filter([
                    data_get($zone, 'cpu', data_get($zone, 'spesifikasi_cpu')),
                    data_get($zone, 'gpu', data_get($zone, 'spesifikasi_gpu')),
                ])));
            @endphp
            <div class="card">
                <div class="row-between">
                    <strong>{{ $zName }}</strong>
                </div>
                <div style="font-size:22px; font-weight:bold; color:#22d3ee; margin-top:10px;">
                    {{ $formatRp($zRate) }} / jam
                </div>
                <div class="muted mt-12">{{ $zSpec !== '' ? $zSpec : '-' }}</div>
            </div>
        @endforeach
    </div>

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
                <tbody id="admin-package-body">
                    @foreach ($billsList as $pkg)
                        @php
                            $pName = data_get($pkg, 'nama_paket', data_get($pkg, 'nama_bill', data_get($pkg, 'name', data_get($pkg, 'paket.nama_paket', 'Paket'))));
                            $pZoneId = data_get($pkg, 'id_zona', data_get($pkg, 'zona_id', data_get($pkg, 'zoneId', data_get($pkg, 'zona.id_zona', data_get($pkg, 'zona.id')))));
                            $matchedZone = collect($zonesList)->first(function ($z) use ($pZoneId) {
                                return (string) data_get($z, 'id_zona', data_get($z, 'id')) === (string) $pZoneId;
                            });
                            $pZoneName = data_get($pkg, 'nama_zona', data_get($pkg, 'zona.nama_zona', data_get($pkg, 'zona.name', data_get($matchedZone, 'nama_zona', data_get($matchedZone, 'name', '-')))));
                            $pHours = data_get($pkg, 'durasi_jam', data_get($pkg, 'hours', data_get($pkg, 'durasi', data_get($pkg, 'paket.durasi_jam', 0))));
                            $pPrice = $extractPrice($pkg, [
                                'harga',
                                'harga_paket',
                                'harga_jual',
                                'harga_satuan',
                                'harga_per_jam',
                                'tarif_per_jam',
                                'nominal_harga',
                                'nominal_paket',
                                'price',
                                'tarif',
                                'tarif_paket',
                                'nominal',
                                'total_harga',
                                'total_biaya',
                                'biaya',
                                'paket.harga',
                                'paket.price',
                                'paket.harga_per_jam',
                                'paket.tarif_per_jam',
                            ]);
                            $pActiveRaw = data_get($pkg, 'active', data_get($pkg, 'is_active', data_get($pkg, 'aktif', true)));
                            $pActive = is_string($pActiveRaw)
                                ? !in_array(strtolower($pActiveRaw), ['0', 'false', 'nonaktif', 'inactive', 'tidak aktif'], true)
                                : (bool) $pActiveRaw;
                        @endphp
                        <tr>
                            <td>{{ $pName }}</td>
                            <td>{{ $pZoneName }}</td>
                            <td>{{ $pHours }} Jam</td>
                            <td>{{ $formatRp($pPrice) }}</td>
                            <td>{{ $pActive ? 'Aktif' : 'Nonaktif' }}</td>
                            <td>-</td>
                        </tr>
                    @endforeach
                </tbody>
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
                <tbody id="admin-tx-body">
                    @foreach ($transactionsList as $tx)
                        @php
                            $txId = data_get($tx, 'id_transaksi', data_get($tx, 'kode_transaksi', data_get($tx, 'no_transaksi', data_get($tx, 'id', '-'))));
                            $txMemberRaw = null;
                            foreach ([
                                'member',
                                'nama_member',
                                'member_name',
                                'username',
                                'nama_user',
                                'nama_akun',
                                'nama',
                                'name',
                                'akun.username',
                                'akun.nama',
                                'akun.name',
                                'user.username',
                                'user.nama',
                                'user.name',
                                'member.username',
                                'member.nama',
                                'member.name',
                                'id_akun',
                                'akun_id',
                                'user_id',
                            ] as $mKey) {
                                $val = data_get($tx, $mKey);
                                if (!is_null($val) && trim((string) $val) !== '' && trim((string) $val) !== '-') {
                                    $txMemberRaw = $val;
                                    break;
                                }
                            }
                            $txMember = is_array($txMemberRaw) || is_object($txMemberRaw)
                                ? (data_get($txMemberRaw, 'username') ?? data_get($txMemberRaw, 'nama') ?? data_get($txMemberRaw, 'name') ?? '-')
                                : ($txMemberRaw ?? '-');

                            $txTime = data_get($tx, 'waktu', data_get($tx, 'waktu_transaksi', data_get($tx, 'tgl_transaksi', data_get($tx, 'tanggal', data_get($tx, 'created_at', '-')))));

                            $txLabel = null;
                            foreach ([
                                'label',
                                'zona_durasi',
                                'paket_durasi',
                                'keterangan',
                                'deskripsi',
                                'catatan',
                                'item',
                            ] as $lKey) {
                                $val = data_get($tx, $lKey);
                                if (!is_null($val) && trim((string) $val) !== '' && trim((string) $val) !== '-') {
                                    $txLabel = $val;
                                    break;
                                }
                            }

                            if (is_null($txLabel)) {
                                $tZone = data_get($tx, 'nama_zona', data_get($tx, 'zona.nama_zona', data_get($tx, 'zona.name', data_get($tx, 'paket.zona.nama_zona', ''))));
                                $tPkg = data_get($tx, 'nama_paket', data_get($tx, 'nama_bill', data_get($tx, 'paket.nama_paket', data_get($tx, 'paket.name', ''))));
                                $tHours = data_get($tx, 'durasi_jam', data_get($tx, 'hours', data_get($tx, 'durasi', data_get($tx, 'paket.durasi_jam', data_get($tx, 'paket.hours', '')))));
                                $durPart = (!is_null($tHours) && trim((string) $tHours) !== '')
                                    ? (stripos((string) $tHours, 'jam') !== false ? ' (' . trim((string) $tHours) . ')' : ' (' . trim((string) $tHours) . ' Jam)')
                                    : '';

                                if ($tZone && $tPkg) {
                                    $txLabel = $tZone . ' - ' . $tPkg . $durPart;
                                } elseif ($tPkg) {
                                    $txLabel = $tPkg . $durPart;
                                } elseif ($tZone) {
                                    $txLabel = $tZone . $durPart;
                                } else {
                                    $txLabel = '-';
                                }
                            }

                            $txMethod = data_get($tx, 'metode_pembayaran', data_get($tx, 'metode', data_get($tx, 'method', data_get($tx, 'payment_method', '-'))));
                            $txAmount = $extractPrice($tx, [
                                'nominal',
                                'total',
                                'total_harga',
                                'total_bayar',
                                'amount',
                                'harga',
                                'biaya',
                            ]);
                        @endphp
                        <tr>
                            <td>{{ $txId }}</td>
                            <td>{{ $txMember }}</td>
                            <td>{{ $txTime }}</td>
                            <td>{{ $txLabel }}</td>
                            <td>{{ $txMethod }}</td>
                            <td>{{ $formatRp($txAmount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection