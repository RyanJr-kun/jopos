<?php
use Carbon\Carbon;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur pembelian - {{ $pembelian->referensi }}</title>
    <style>
        /* ══════════════════════════════════════════════════════════
         * PAGE SETUP — A4 Portrait
         * Margin kiri/kanan 20mm memberi "napas" agar konten
         * tidak terlalu mepet tepi kertas.
         * ══════════════════════════════════════════════════════════ */
        @page {
            size: A4 portrait;
            margin: 0;
            /* dihandle manual via padding container */
        }

        /* ══════════════════════════════════════════════════════════
         * RESET & BASE
         * ══════════════════════════════════════════════════════════ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #2d2d2d;
            background: #fff;
        }

        /* ══════════════════════════════════════════════════════════
         * FIXED HEADER — muncul di tiap halaman
         * Tinggi total ≈ 52px → padding-top container = 62px
         * ══════════════════════════════════════════════════════════ */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 40px;
            padding: 12px 20mm 10px;
            background: #fff;
        }

        .pdf-header-inner {
            display: table;
            width: 100%;
        }

        .pdf-header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 55%;
        }

        .pdf-header-logo img {
            height: 40px;
            width: auto;
        }

        .pdf-header-title {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 45%;
        }

        .pdf-header-title h1 {
            font-size: 15px;
            font-weight: bold;
            color: #1a1a2e;
            letter-spacing: 0.05em;
        }

        .pdf-header-title .ref-number {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
            letter-spacing: 0.02em;
        }

        /* ══════════════════════════════════════════════════════════
         * FIXED FOOTER — muncul di tiap halaman
         * ══════════════════════════════════════════════════════════ */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            padding: 7px 20mm;
            /* ikut margin 20mm */
            border-top: 1px solid #e8e8ee;
            background: #fff;
        }

        .pdf-footer table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .pdf-footer td {
            border: none;
            padding: 0;
            font-size: 8px;
            color: #aaa;
            vertical-align: middle;
        }

        .page-number::before {
            content: "Halaman " counter(page) " dari " counter(pages);
        }

        .container {
            padding-top: 64px;
            /* header 52px + gap 12px */
            padding-bottom: 40px;
            /* footer 28px + gap 12px */
            padding-left: 20mm;
            padding-right: 20mm;
        }

        .section-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #888;
            margin-bottom: 6px;
            margin-top: 18px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ebebf0;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-top: 16px;
            margin-bottom: 4px;
        }

        .info-grid-row {
            display: table-row;
        }

        .info-box {
            display: table-cell;
            width: 33.33%;
            border: 1px solid #e8e8ee;
            border-radius: 6px;
            padding: 10px 11px;
            vertical-align: top;
        }

        .info-box:first-child {
            margin-left: 0;
        }

        .info-box-shade {
            background-color: #f6f6fa;
        }

        .info-box .box-label {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #aaa;
            display: block;
            margin-bottom: 6px;
        }

        .info-box .box-name {
            font-size: 10.5px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .info-box p {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
            line-height: 1.5;
        }

        /* Baris key-value di box transaksi */
        .data-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .data-key {
            display: table-cell;
            font-size: 8.5px;
            color: #aaa;
            width: 42%;
            vertical-align: top;
        }

        .data-val {
            display: table-cell;
            font-size: 9px;
            font-weight: 600;
            color: #2d2d2d;
            text-align: right;
            vertical-align: top;
        }

        /* ── Badge status ── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.03em;
        }

        .badge-success {
            background: #dcf3e5;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ══════════════════════════════════════════════════════════
         * TABEL PRODUK
         * ══════════════════════════════════════════════════════════ */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e8e8ee;
            border-radius: 6px;
            overflow: hidden;
        }

        .items-table thead tr {
            background-color: #f0f0f6;
        }

        .items-table th {
            padding: 8px 9px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #666;
            border: none;
            border-bottom: 1px solid #e0e0ea;
        }

        .items-table td {
            padding: 8px 9px;
            font-size: 9.5px;
            border: none;
            border-bottom: 1px solid #f2f2f6;
            vertical-align: top;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Baris genap sedikit lebih terang — subtle zebra */
        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .product-name {
            font-weight: 600;
            color: #1a1a2e;
            display: block;
        }

        .product-variant {
            font-size: 8.5px;
            color: #888;
            margin-top: 2px;
            display: block;
        }


        .bottom-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .bottom-grid-row {
            display: table-row;
        }

        .bottom-left {
            display: table-cell;
            width: 54%;
            vertical-align: top;
            padding-right: 10px;
        }

        .bottom-right {
            display: table-cell;
            width: 46%;
            vertical-align: top;
        }

        /* Catatan */
        .notes-box {
            border: 1px solid #e8e8ee;
            border-radius: 6px;
            padding: 10px 12px;
            background: #fafafa;
        }

        .notes-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #aaa;
            display: block;
            margin-bottom: 5px;
        }

        .notes-box p {
            font-size: 9px;
            color: #555;
            line-height: 1.6;
        }

        /* Summary box */
        .summary-box {
            border: 1px solid #e8e8ee;
            border-radius: 6px;
            padding: 11px 14px;
            background: #f6f6fa;
        }

        .summary-line {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .summary-line td {
            display: table-cell;
            border: none;
            padding: 1.5px 0;
            font-size: 9px;
            color: #666;
            vertical-align: middle;
        }

        .summary-line .sl-val {
            text-align: right;
            font-weight: 600;
            color: #2d2d2d;
        }

        .summary-line .sl-val.is-discount {
            color: #991b1b;
        }

        .summary-divider {
            border: none;
            border-top: 1px solid #dcdcea;
            margin: 7px 0;
        }

        /* Baris Total Akhir — lebih besar dan menonjol */
        .summary-total-line {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }

        .summary-total-line td {
            display: table-cell;
            border: none;
            padding: 2px 0;
            vertical-align: middle;
        }

        .stl-label {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a1a2e;
            letter-spacing: 0.04em;
        }

        .stl-value {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #1a3a8f;
        }

        /* Dibayar / kembalian / sisa */
        .summary-sub-line {
            display: table;
            width: 100%;
            margin-top: 2px;
        }

        .summary-sub-line td {
            display: table-cell;
            border: none;
            padding: 1.5px 0;
            font-size: 9px;
            vertical-align: middle;
        }

        .ssl-val {
            text-align: right;
            font-weight: bold;
        }

        .ssl-val.is-green {
            color: #166534;
        }

        .ssl-val.is-red {
            color: #991b1b;
        }

        /* ══════════════════════════════════════════════════════════
         * TABEL RIWAYAT PEMBAYARAN
         * ══════════════════════════════════════════════════════════ */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e8e8ee;
            border-radius: 6px;
            overflow: hidden;
        }

        .payment-table thead tr {
            background-color: #f0f0f6;
        }

        .payment-table th {
            padding: 7px 9px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #666;
            border: none;
            border-bottom: 1px solid #e0e0ea;
        }

        .payment-table td {
            padding: 7px 9px;
            font-size: 9px;
            border: none;
            border-bottom: 1px solid #f2f2f6;
            vertical-align: middle;
        }

        .payment-table tbody tr:last-child td {
            border-bottom: none;
        }

        .payment-table tfoot tr {
            background-color: #f0f0f6;
        }

        .payment-table tfoot td {
            font-weight: bold;
            font-size: 9px;
            padding: 7px 9px;
            border: none;
            border-top: 1px solid #e0e0ea;
        }

        .pay-date-main {
            font-weight: 600;
            font-size: 9px;
        }

        .pay-date-time {
            font-size: 8px;
            color: #aaa;
            margin-top: 1px;
        }

        .pay-ref {
            font-weight: 600;
            color: #1a3a8f;
            margin-bottom: 2px;
            font-size: 8.5px;
        }

        .pay-note {
            color: #888;
            font-size: 8.5px;
        }

        /* ══════════════════════════════════════════════════════════
         * UTILITY CLASSES
         * ══════════════════════════════════════════════════════════ */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-green {
            color: #166534;
            font-weight: bold;
        }

        .text-blue {
            color: #1a3a8f;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .items-table tr {
                page-break-inside: avoid;
            }

            .payment-table tr {
                page-break-inside: avoid;
            }

            .bottom-grid {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    {{-- ══ HEADER TETAP ════════════════════════════════════════════ --}}
    <div class="pdf-header">
        <div class="pdf-header-inner">
            <div class="pdf-header-logo">
                <img src="{{ asset('assets/img/LM-Default.webp') }}" alt="{{ $profilToko->name_toko ?? 'JO COMPUTER' }}">
            </div>
            <div class="pdf-header-title">
                <h1>FAKTUR pembelian</h1>
                <div class="ref-number">No. {{ $pembelian->referensi }}</div>
            </div>
        </div>
    </div>

    {{-- ══ FOOTER TETAP ════════════════════════════════════════════ --}}
    <div class="pdf-footer">
        <table>
            <tr>
                <td>&copy; {{ date('Y') }} {{ $profilToko->name_toko ?? config('app.name') }}. All rights reserved.
                </td>
                <td class="text-right page-number"></td>
            </tr>
        </table>
    </div>

    {{-- ══ KONTEN UTAMA ════════════════════════════════════════════ --}}
    <div class="container">

        {{-- ── 3 INFO BOX ────────────────────────────────────────── --}}
        <div class="info-grid">
            <div class="info-grid-row">

                {{-- Dari Toko --}}
                <div class="info-box info-box-shade">
                    <span class="box-label">Dari (Penerima)</span>
                    <div class="box-name">{{ $profilToko->name_toko ?? 'JO COMPUTER' }}</div>
                    <p>{{ $profilToko->alamat ?? 'Alamat toko belum diatur' }}</p>
                    <p>{{ $profilToko->email ?? '-' }}</p>
                    <p>{{ $profilToko->telepon ?? '-' }}</p>
                </div>

                {{-- Kepada Customer --}}
                <div class="info-box">
                    <span class="box-label">Kepada (Customer)</span>
                    <div class="box-name"> {{ $pembelian->supplier->name ?? 'Supplier Dihapus' }}</div>
                    <p>{{ $pembelian->supplier->alamat ?? '-' }}</p>
                    <p>{{ $pembelian->supplier->email ?? '-' }}</p>
                    <p>{{ $pembelian->supplier->kontak ?? '-' }}</p>
                </div>

                {{-- Info Transaksi --}}
                <div class="info-box">
                    <span class="box-label">Data Transaksi</span>
                    <div class="data-row">
                        <span class="data-key">Tanggal</span>
                        <span
                            class="data-val">{{ Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}</span>
                    </div>
                    @if ($pembelian->status_pembayaran !== 'Lunas' && $pembelian->tanggal_jatuh_tempo)
                        <div class="data-row">
                            <span class="data-key">Jatuh Tempo</span>
                            <span
                                class="data-val">{{ Carbon::parse($pembelian->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</span>
                        </div>
                    @endif
                    <div class="data-row">
                        <span class="data-key">Dibuat Oleh</span>
                        <span class="data-val">{{ $pembelian->user->name ?? 'Sistem' }}</span>
                    </div>
                    <div class="data-row" style="margin-top: 5px;">
                        <span class="data-key">Status Bayar</span>
                        <span class="data-val">
                            @php
                                $statusClass = match ($pembelian->status_pembayaran) {
                                    'Lunas' => 'badge-success',
                                    'Dibatalkan' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $pembelian->status_pembayaran }}</span>
                        </span>
                    </div>
                    <div class="data-row" style="margin-top: 5px;">
                        <span class="data-key">Status Barang</span>
                        <span class="data-val">
                            @php
                                $statusClass = match ($pembelian->status_barang) {
                                    'Diterima' => 'badge-success',
                                    'Dibatalkan' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $pembelian->status_barang }}</span>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── TABEL PRODUK ───────────────────────────────────────── --}}
        <div class="section-title">Rincian Produk</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" width="4%">No.</th>
                    <th>Produk</th>
                    <th class="text-center" width="8%">Qty</th>
                    <th class="text-right" width="17%">Harga Jual</th>
                    <th class="text-right" width="13%">Diskon</th>
                    <th class="text-right" width="18%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembelian->details as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td>
                            <span class="product-name">
                                {{ $item->produk->name_product ?? 'Produk Dihapus' }}
                                @if ($item->product_variant_id && $item->varian && $item->varian->label)
                                    &mdash; {{ $item->varian->label }}
                                @endif
                            </span>
                        </td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-right">@money($item->harga_jual)</td>
                        <td class="text-right">@money($item->diskon_item)</td>
                        <td class="text-right fw-bold">@money($item->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── CATATAN + SUMMARY ──────────────────────────────────── --}}
        <div class="bottom-grid">
            <div class="bottom-grid-row">

                {{-- Catatan (kiri) --}}
                <div class="bottom-left">
                    <div class="section-title">Catatan</div>
                    @if ($pembelian->catatan)
                        <div class="notes-box">
                            <span class="notes-title">Catatan Transaksi</span>
                            <p>{!! $pembelian->catatan !!}</p>
                        </div>
                    @else
                        <p style="font-size:9px; color:#ccc; font-style:italic;">Tidak ada catatan.</p>
                    @endif
                </div>

                {{-- Summary (kanan) --}}
                <div class="bottom-right">
                    <div class="section-title">Ringkasan Pembayaran</div>
                    <div class="summary-box">

                        {{-- Baris subtotal, pajak, diskon, ongkir, service --}}
                        <div class="summary-line">
                            <table style="width:100%;border:none;margin:0;border-collapse:collapse;">
                                <tr>
                                    <td style="border:none;padding:2px 0;font-size:9px;color:#666;">Subtotal Produk</td>
                                    <td
                                        style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#2d2d2d;">
                                        @money($pembelian->subtotal)</td>
                                </tr>
                                @if ($pembelian->pajak > 0)
                                    <tr>
                                        <td style="border:none;padding:2px 0;font-size:9px;color:#666;">PPN / Pajak</td>
                                        <td
                                            style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#2d2d2d;">
                                            @money($pembelian->pajak)</td>
                                    </tr>
                                @endif
                                @if ($pembelian->diskon > 0)
                                    <tr>
                                        <td style="border:none;padding:2px 0;font-size:9px;color:#666;">Diskon Tambahan
                                        </td>
                                        <td
                                            style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#991b1b;">
                                            &#8722;&nbsp;@money($pembelian->diskon)</td>
                                    </tr>
                                @endif
                                @if ($pembelian->ongkir > 0)
                                    <tr>
                                        <td style="border:none;padding:2px 0;font-size:9px;color:#666;">Ongkos Kirim
                                        </td>
                                        <td
                                            style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#2d2d2d;">
                                            @money($pembelian->ongkir)</td>
                                    </tr>
                                @endif
                                @if ($pembelian->service > 0)
                                    <tr>
                                        <td style="border:none;padding:2px 0;font-size:9px;color:#666;">Biaya Servis
                                        </td>
                                        <td
                                            style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#2d2d2d;">
                                            @money($pembelian->service)</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <hr class="summary-divider">

                        {{-- Total Akhir --}}
                        <div class="summary-total-line">
                            <table style="width:100%;border:none;margin:0;border-collapse:collapse;">
                                <tr>
                                    <td style="border:none;padding:0;" class="stl-label">Total Akhir</td>
                                    <td style="border:none;padding:0;" class="stl-value text-right">@money($pembelian->total_akhir)
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <hr class="summary-divider">

                        {{-- Dibayar & sisa/kembalian --}}
                        <table style="width:100%;border:none;margin:0;border-collapse:collapse;">
                            @if ($pembelian->status_pembayaran === 'Hutang')
                                <tr>
                                    <td style="border:none;padding:2px 0;font-size:9px;color:#666;">Jumlah Dibayar</td>
                                    <td
                                        style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#2d2d2d;">
                                        @money($pembelian->jumlah_dibayar)</td>
                                </tr>
                                <tr>
                                    <td style="border:none;padding:2px 0;font-size:9px;font-weight:bold;color:#1a1a2e;">
                                        Sisa Hutang</td>
                                    <td
                                        style="border:none;padding:2px 0;font-size:9.5px;font-weight:bold;text-align:right;color:#991b1b;">
                                        @money($pembelian->sisa_hutang)</td>
                                </tr>
                            @elseif ($pembelian->status_pembayaran === 'Lunas')
                                <tr>
                                    <td style="border:none;padding:2px 0;font-size:9px;color:#666;">Dibayar</td>
                                    <td
                                        style="border:none;padding:2px 0;font-size:9px;font-weight:600;text-align:right;color:#2d2d2d;">
                                        @money($pembelian->jumlah_dibayar)</td>
                                </tr>
                                <tr>
                                    <td style="border:none;padding:2px 0;font-size:9px;font-weight:bold;color:#1a1a2e;">
                                        Kembalian</td>
                                    <td
                                        style="border:none;padding:2px 0;font-size:9.5px;font-weight:bold;text-align:right;color:#166534;">
                                        @money(abs($pembelian->kembalian))</td>
                                </tr>
                            @endif
                        </table>

                    </div>{{-- end summary-box --}}
                </div>

            </div>
        </div>

        {{-- ── RIWAYAT PEMBAYARAN ──────────────────────────────────── --}}
        @if ($pembelian->payments && $pembelian->payments->isNotEmpty())
            <div class="section-title" style="margin-top: 22px;">Riwayat Pembayaran</div>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th class="text-center" width="4%">No.</th>
                        <th class="text-left" width="18%">Tanggal Bayar</th>
                        <th class="text-left" width="14%">Kasir</th>
                        <th class="text-center" width="12%">Metode</th>
                        <th class="text-left">Referensi / Catatan</th>
                        <th class="text-right" width="18%">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelian->payments as $key => $payment)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}.</td>
                            <td>
                                <div class="pay-date-main">
                                    {{ Carbon::parse($payment->tanggal_bayar)->translatedFormat('d F Y') }}</div>
                                <div class="pay-date-time">{{ Carbon::parse($payment->tanggal_bayar)->format('H:i') }}
                                    WIB</div>
                            </td>
                            <td>{{ $payment->user->name ?? '-' }}</td>
                            <td class="text-center">
                                <span
                                    class="badge badge-{{ $payment->metode_pembayaran === 'TRANSFER' ? 'success' : 'warning' }}">
                                    {{ $payment->metode_pembayaran ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($payment->referensi_pembayaran)
                                    <div class="pay-ref">Ref: {{ $payment->referensi_pembayaran }}</div>
                                @endif
                                <div class="pay-note">{!! $payment->catatan ?? '-' !!}</div>
                            </td>
                            <td class="text-right text-green">@money($payment->jumlah_bayar)</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-left" colspan="5">Total Pembayaran</td>
                        <td class="text-right text-blue">@money($pembelian->payments->sum('jumlah_bayar'))</td>
                    </tr>
                </tfoot>
            </table>
        @endif

    </div>

</body>

</html>
