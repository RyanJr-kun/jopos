<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 0;
            vertical-align: top;
        }

        .header-info h1 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header-info p {
            margin: 0;
            font-size: 11px;
        }

        .report-title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .report-title p {
            margin: 2px 0;
            font-size: 11px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .main-table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-success {
            color: #1E9E63;
        }

        .text-danger {
            color: #D32F2F;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 9px;
            color: #777;
            text-align: center;
            line-height: 35px;
        }

        .page-number:before {
            content: "Halaman " counter(page);
        }
    </style>
</head>

<body>
    {{-- Memanggil komponen header --}}
    @include('content.laporan.pdf._header')

    <main>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th>Product</th>
                    <th style="width: 10%;">Tipe</th>
                    <th style="width: 13%;">Referensi</th>
                    <th class="text-center" style="width: 7%;">Masuk</th>
                    <th class="text-center" style="width: 7%;">Keluar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pergerakan as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                        <td>{{ $item->name_product ?? 'Product Dihapus' }} <br><small>SKU:
                                {{ $item->sku ?? '-' }}</small>
                        </td>
                        <td>{{ $item->tipe_gerakan }}</td>
                        <td>{{ $item->referensi }}</td>
                        <td class="text-center font-weight-bold text-success">
                            {{ $item->jumlah_masuk > 0 ? '+' . number_format($item->jumlah_masuk) : '-' }}</td>
                        <td class="text-center font-weight-bold text-danger">
                            {{ $item->jumlah_keluar > 0 ? '-' . number_format($item->jumlah_keluar) : '-' }}</td>
                        <td>{{ $item->keterangan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pergerakan inventaris yang ditemukan untuk
                            periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

    {{-- Memanggil komponen footer --}}
    @include('content.laporan.pdf._footer')
</body>

</html>
