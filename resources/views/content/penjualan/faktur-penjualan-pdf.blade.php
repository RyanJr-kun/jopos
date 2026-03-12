<?php

use Carbon\Carbon;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Sale - {{ $penjualan->referensi }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding-top: 140px;
            padding-bottom: 50px;
        }

        .header,
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            padding-left: 25px;
            padding-right: 25px;
            width: 100%;
            color: #333;
        }

        .header {
            top: 0;
        }

        .footer {
            bottom: 0;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .align-top {
            vertical-align: top;
        }

        .summary {
            width: 45%;
            margin-left: 55%;
            margin-top: 20px;
        }

        .summary td {
            border: none;
            padding: 4px 8px;
        }

        .summary .total-akhir {
            font-weight: bold;
            background-color: #f2f2f2;
            border-bottom: 1px solid #ddd;
        }

        .notes {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 11px;
            border-radius: 5px;
        }

        .page-number:before {
            content: "Halaman " counter(page);
        }
    </style>
</head>

<body>

    <div class="footer">
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: left; width: 50%;">© {{ date('Y') }}
                    {{ $profilToko->name_toko ?? config('app.name') }}. All rights reserved.</td>
                <td style="border: none; text-align: right; width: 50%;" class="page-number"></td>
            </tr>
        </table>
    </div>

    <div class="container">
        <div style="text-align: center; padding: 8px 0; margin-bottom: 25px; border-radius: 5px;">
            <h3 style="margin: 0; font-size: 16px; font-weight: bold;">FAKTUR PENJUALAN</h3>
        </div>

        <table style="border: none; margin-bottom: 25px; margin-top: 0; width: 100%;">
            <tr class="align-top">
                <td style="border: none; width: 33%; padding-right: 10px;">
                    <p style="margin:0;"><strong>Dari:</strong></p>
                    <h4 style="margin: 2px 0;">{{ $profilToko->name_toko ?? 'Nama Toko Anda' }}</h4>
                    <p style="margin: 2px 0;">{{ $profilToko->alamat ?? 'Alamat toko belum diatur' }}</p>
                    <p style="margin: 2px 0;">Email: {{ $profilToko->email ?? '-' }}</p>
                    <p style="margin: 2px 0;">Telp: {{ $profilToko->telepon ?? '-' }}</p>
                </td>
                <td style="border: none; width: 33%; padding-left: 10px; padding-right: 10px;">
                    <p style="margin:0;"><strong>Kepada (Customer):</strong></p>
                    <h4 style="margin: 2px 0;">{{ $penjualan->pelanggan->name ?? 'Customer Umum' }}</h4>
                    <p style="margin: 2px 0;">{{ $penjualan->pelanggan->alamat ?? '' }}</p>
                    <p style="margin: 2px 0;">Kontak: {{ $penjualan->pelanggan->kontak ?? '-' }}</p>
                </td>
                <td style="border: none; width: 34%; padding-left: 10px;">
                    <p style="margin:0;"><strong>Info Transaksi:</strong></p>
                    <p style="margin: 2px 0;"><strong>Referensi:</strong> {{ $penjualan->referensi }}</p>
                    <p style="margin: 2px 0;"><strong>Tanggal:</strong>
                        {{ Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d F Y') }}</p>
                    <p style="margin: 2px 0;"><strong>Metode Bayar:</strong> {{ $penjualan->metode_pembayaran }}</p>
                    <p style="margin: 2px 0;"><strong>Status Bayar:</strong> {{ $penjualan->status_pembayaran }}</p>
                    <p style="margin: 2px 0;"><strong>Dibuat Oleh:</strong>
                        {{ $penjualan->user->name ?? 'User Dihapus' }}</p>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->items as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $item->produk->name_produk ?? 'Product Dihapus' }}
                            @if ($item->serialNumbers->isNotEmpty())
                                <div style="font-size: 9px; color: #555; margin-top: 4px;">
                                    <strong>SN:</strong> {{ $item->serialNumbers->pluck('nomor_seri')->join(', ') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-end">@money($item->harga_jual)</td>
                        <td class="text-end">@money($item->diskon_item)</td>
                        <td class="text-end">@money($item->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>Subtotal Keseluruhan:</td>
                <td class="text-end">@money($penjualan->subtotal)</td>
            </tr>
            <tr>
                <td>Diskon Tambahan:</td>
                <td class="text-end">@money($penjualan->diskon)</td>
            </tr>
            <tr>
                <td>Taxe:</td>
                <td class="text-end">@money($penjualan->pajak)</td>
            </tr>
            <tr>
                <td>Ongkos Kirim:</td>
                <td class="text-end">@money($penjualan->ongkir)</td>
            </tr>
            @if ($penjualan->service > 0)
                <tr>
                    <td>Biaya Servis:</td>
                    <td class="text-end">@money($penjualan->service)</td>
                </tr>
            @endif
            <tr class="total-akhir">
                <td>TOTAL AKHIR</td>
                <td class="text-end">@money($penjualan->total_akhir)</td>
            </tr>
            <tr>
                <td>Jumlah Dibayar:</td>
                <td class="text-end">@money($penjualan->jumlah_dibayar)</td>
            </tr>
            @if ($penjualan->kembalian >= 0)
                <tr>
                    <td style="font-weight: bold;">{{ $penjualan->kembalian > 0 ? 'Kembalian:' : 'Sisa Bayar:' }}</td>
                    <td class="text-end" style="font-weight: bold;">@money($penjualan->kembalian)</td>
                </tr>
            @else
                <tr>
                    <td style="font-weight: bold;">Sisa Bayar:</td>
                    <td class="text-end" style="font-weight: bold;">@money(abs($penjualan->kembalian))</td>
                </tr>
            @endif
        </table>

        @if ($penjualan->catatan)
            <div class="notes">
                <strong>Catatan:</strong><br>
                {!! $penjualan->catatan !!}
            </div>
        @endif

    </div>
</body>

</html>
