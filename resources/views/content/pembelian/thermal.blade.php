<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Purchase {{ $pembelian->referensi }}</title>
    <style>
        body {
            /* Menggunakan font monospaced yang umum untuk printer kasir */
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            width: 58mm;
            /* Sesuaikan dengan lebar kertas thermal Anda */
            margin: 0;
            padding: 5px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header .store-name {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }

        .header .address,
        .header .contact {
            margin: 2px 0;
        }

        .content {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1.5px 0;
        }

        .items-table th,
        .items-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }

        .items-table thead tr {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .item-row td {
            padding-top: 5px;
        }

        .items-table .detail-row td {
            padding-bottom: 5px;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .summary-table {
            margin-top: 8px;
        }

        .summary-table td {
            padding: 1px 0;
        }

        .summary-table .label {
            text-align: left;
        }

        .summary-table .value {
            text-align: right;
        }

        .summary-table .total-row td {
            font-weight: bold;
            padding-top: 4px;
        }

        .footer {
            margin-top: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <div class="store-name">{{ $profilToko->name_toko ?? 'NAMA TOKO' }}</div>
        <div class="address">{{ $profilToko->alamat ?? 'Alamat Toko' }}</div>
        <div class="contact">Telp: {{ $profilToko->telepon ?? '-' }}</div>
    </div>

    <div class="content">
        <div class="separator"></div>
        <table class="info-table">
            <tr>
                <td>Ref:</td>
                <td>{{ $pembelian->referensi }}</td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td>Supplier:</td>
                <td>{{ $pembelian->pemasok->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kasir:</td>
                <td>{{ $pembelian->user->name ?? 'N/A' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembelian->details as $detail)
                    <tr class="item-row">
                        <td colspan="2">
                            {{ $detail->produk->name_product ?? 'Product Dihapus' }}
                        </td>
                    </tr>
                    <tr class="detail-row">
                        <td>{{ $detail->qty }} x @money($detail->harga_beli)</td>
                        <td class="text-right">@money($detail->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

        <table class="summary-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">@money($pembelian->subtotal)</td>
            </tr>
            <tr>
                <td class="label">PPN:</td>
                <td class="value">@money($pembelian->pajak)</td>
            </tr>
            <tr>
                <td class="label">Diskon:</td>
                <td class="value">@money($pembelian->diskon)</td>
            </tr>
            <tr>
                <td class="label">Ongkir:</td>
                <td class="value">@money($pembelian->ongkir)</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total Akhir:</td>
                <td class="value">@money($pembelian->total_akhir)</td>
            </tr>
            <tr>
                <td class="label">Dibayar:</td>
                <td class="value">@money($pembelian->jumlah_dibayar)</td>
            </tr>
            <tr>
                <td class="label">Sisa:</td>
                <td class="value">@money($pembelian->sisa_hutang)</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Terima kasih!</p>
    </div>

    <button class="no-print" onclick="window.print()"
        style="width: 100%; margin-top: 20px; padding: 8px; border: 1px solid #ccc; background-color: #f0f0f0; cursor: pointer;">Cetak
        Ulang</button>
</body>

</html>
