<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Purchase - {{ $pembelian->referensi }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            /* Space for header */
            padding-top: 140px;
            padding-bottom: 50px;
            /* Space for footer */
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

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            /* Menghilangkan border dari sel header */
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

        .w-50 {
            width: 50%;
        }

        .align-top {
            vertical-align: top;
        }

        .info-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-size: 11px;
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
            <h3 style="margin: 0; font-size: 16px; font-weight: bold;">FAKTUR PEMBELIAN</h3>
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
                    <p style="margin:0;"><strong>Kepada (Supplier):</strong></p>
                    <h4 style="margin: 2px 0;">{{ $pembelian->pemasok->name ?? 'Supplier Dihapus' }}</h4>
                    <p style="margin: 2px 0;">{{ $pembelian->pemasok->alamat ?? 'Alamat tidak tersedia' }}</p>
                    <p style="margin: 2px 0;">Email: {{ $pembelian->pemasok->email ?? '-' }}</p>
                    <p style="margin: 2px 0;">Kontak: {{ $pembelian->pemasok->kontak ?? '-' }}</p>
                </td>
                <td style="border: none; width: 34%; padding-left: 10px;">
                    <p style="margin:0;"><strong>Info Transaksi:</strong></p>
                    <p style="margin: 2px 0;"><strong>Referensi:</strong> {{ $pembelian->referensi }}</p>
                    <p style="margin: 2px 0;"><strong>Tanggal:</strong>
                        {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}</p>
                    <p style="margin: 2px 0;"><strong>Status Barang:</strong> {{ $pembelian->status_barang }}</p>
                    <p style="margin: 2px 0;"><strong>Status Bayar:</strong> {{ $pembelian->status_pembayaran }}</p>
                    <p style="margin: 2px 0;"><strong>Dibuat Oleh:</strong>
                        {{ $pembelian->user->name ?? 'User Dihapus' }}</p>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembelian->details as $detail)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $detail->produk->name_produk ?? 'Product Dihapus' }}
                        </td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-end">@money($detail->harga_beli)</td>
                        <td class="text-end">@money($detail->diskon)</td>
                        <td class="text-end">@money($detail->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td style="font-weight: bold;">Subtotal Keseluruhan:</td>
                <td class="text-end" style="font-weight: bold;">@money($pembelian->subtotal)</td>
            </tr>
            <tr>
                <td>Diskon Tambahan:</td>
                <td class="text-end">@money($pembelian->diskon)</td>
            </tr>
            <tr>
                <td>PPN:</td>
                <td class="text-end">@money($pembelian->pajak)</td>
            </tr>
            <tr>
                <td>Ongkos Kirim:</td>
                <td class="text-end">@money($pembelian->ongkir)</td>
            </tr>
            <tr class="total-akhir">
                <td>TOTAL AKHIR</td>
                <td class="text-end">@money($pembelian->total_akhir)</td>
            </tr>
            <tr>
                <td>Jumlah Dibayar:</td>
                <td class="text-end">@money($pembelian->jumlah_dibayar)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Sisa Hutang:</td>
                <td class="text-end" style="font-weight: bold;">@money($pembelian->sisa_hutang)</td>
            </tr>
        </table>

        @if ($pembelian->catatan)
            <div class="notes">
                <strong>Catatan:</strong><br>
                {!! $pembelian->catatan !!}
            </div>
        @endif

    </div>
</body>

</html>
