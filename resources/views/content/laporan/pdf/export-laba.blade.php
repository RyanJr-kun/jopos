<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-logo {
            width: 80px;
            height: auto;
        }

        .shop-info h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .shop-info p {
            margin: 2px 0;
            font-size: 11px;
        }

        .report-title {
            text-align: right;
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
            margin-top: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .main-table td:last-child {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .net-profit-row {
            font-weight: bold;
            font-size: 14px;
        }

        .profit {
            color: #1E9E63;
            /* Green */
        }

        .loss {
            color: #D32F2F;
            /* Red */
        }

        .footer {
            position: fixed;
            bottom: -20px;
            /* Adjust as needed */
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

        .footer-right {
            text-align: right;
        }

        .footer-left {
            text-align: left;
        }
    </style>
</head>

<body>
    {{-- Memanggil komponen header yang sudah ada --}}
    @include('content.laporan.pdf._header')

    <main>
        <table class="main-table">
            <tbody>
                <tr>
                    <td>Pendapatan dari Sale</td>
                    <td>@money($totalRevenue)</td>
                </tr>
                <tr>
                    <td>Harga Pokok Sale (HPP)</td>
                    <td>(@money($cogs))</td> {{-- Angka negatif dalam kurung --}}
                </tr>
                <tr class="total-row">
                    <td>Laba Kotor</td>
                    <td>@money($grossProfit)</td>
                </tr>
                <tr>
                    <td>Pendapatan Lain-lain</td>
                    <td class="profit">@money($totalOtherIncome)</td>
                </tr>
                <tr>
                    <td>Beban Operasional</td>
                    <td>(@money($totalExpenses))</td> {{-- Angka negatif dalam kurung --}}
                </tr>
                <tr class="net-profit-row {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                    <td>Laba Bersih</td>
                    <td>@money($netProfit)</td>
                </tr>
            </tbody>
        </table>
    </main>

    {{-- Memanggil komponen footer yang sudah ada --}}
    @include('content.laporan.pdf._footer')

</body>

</html>
