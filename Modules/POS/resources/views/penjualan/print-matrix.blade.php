<?php
use Carbon\Carbon;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur JOPOS - {{ $penjualan->referensi }}</title>
    <style>

        /* ============================================================
         * PENGATURAN KERTAS — Epson LX-300 Continuous Paper
         * Lebar: 241mm (9.5") — lebar fisik continuous paper
         * Tinggi: auto — mengikuti tinggi konten, bukan halaman penuh
         *
         * JANGAN set tinggi di @page untuk dot matrix continuous.
         * Kalau di-set (misal 279mm), browser selalu cetak 1 full
         * page meski konten cuma separuhnya = kertas terbuang.
         * ============================================================ */
        @page {
            size: 241mm auto;   /* lebar fixed, tinggi ikut konten */
            margin: 6mm 8mm;
        }

        /* ============================================================
         * BASE TYPOGRAPHY
         * Courier New wajib — dot matrix LX-300 paling cepat
         * dengan font monospace (mode teks, bukan mode grafis).
         * font-size 10pt ≈ 80 kolom di lebar 9.5".
         * ============================================================ */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #000;
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
        }

        /* ============================================================
         * JUDUL
         * Turun dari 16px ke 13pt agar muat di 80 kolom tanpa
         * wrap yang tidak diinginkan.
         * ============================================================ */
        .judul {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 4mm;
            text-decoration: underline;
            letter-spacing: 0.05em;
        }

        .nama-toko {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .tagline-toko {
            text-align: center;
            font-size: 9pt;
            margin-bottom: 4mm;
        }

        /* Garis pemisah header — lebih cepat dari border grafis */
        .divider {
            border: none;
            border-top: 1px solid #000;
            margin: 3mm 0;
        }

        .divider-dashed {
            border: none;
            border-top: 1px dashed #000;
            margin: 3mm 0;
        }

        /* ============================================================
         * HEADER 3 KOLOM
         * Gunakan table HTML — lebih stabil saat print daripada
         * flexbox/grid untuk multi-kolom di mode print browser.
         * ============================================================ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .header-table td {
            vertical-align: top;
            border: none;
            padding: 0;
            width: 33.33%;
            font-size: 9pt;
        }

        .header-table td:nth-child(1),
        .header-table td:nth-child(2) {
            padding-right: 4mm;
        }

        .header-table .col-label {
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 1mm;
            display: block;
        }

        .header-table .col-name {
            font-weight: bold;
            font-size: 10pt;
            margin: 0 0 1mm 0;
        }

        .header-table p {
            margin: 1mm 0;
            font-size: 9pt;
        }

        .header-table .info-row {
            display: flex;
            gap: 2mm;
        }

        .header-table .info-key {
            font-weight: bold;
            white-space: nowrap;
        }

        /* ============================================================
         * TABEL ITEM
         * border 0.5px lebih ringan untuk pita dot matrix.
         * padding dikurangi agar muat lebih banyak baris per halaman.
         * ============================================================ */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            table-layout: fixed; /* Wajib — mencegah kolom melar di print */
        }

        .items-table th {
            border: 0.5px solid #000;
            padding: 2mm 2mm;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
            /* TIDAK ADA background-color — hemat pita printer */
        }

        .items-table td {
            border: 0.5px solid #000;
            padding: 1.5mm 2mm;
            font-size: 9pt;
            vertical-align: top;
        }

        /* Kolom nomor */
        .col-no    { width: 5%; }
        /* Kolom deskripsi — lebih lebar untuk nama produk elektronik yang panjang */
        .col-desc  { width: 38%; }
        /* Kolom qty */
        .col-qty   { width: 7%; }
        /* Kolom harga */
        .col-harga { width: 18%; }
        /* Kolom diskon */
        .col-disc  { width: 12%; }
        /* Kolom subtotal */
        .col-sub   { width: 20%; }

        .items-table .product-name {
            font-weight: bold;
            display: block;
            margin-bottom: 0.5mm;
        }

        .items-table .product-variant {
            font-size: 8pt;
            display: block;
        }

        .items-table .serial-numbers {
            font-size: 8pt;
            margin-top: 1mm;
            display: block;
            line-height: 1.4;
            word-break: break-all;
        }

        /* Baris total di tbody (misal baris diskon baris) */
        .items-table .row-subtotal td {
            font-weight: bold;
            border-top: 0.5px solid #000;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }

        /* ============================================================
         * AREA SUMMARY + CATATAN
         * Gunakan table HTML 2-kolom untuk stabilitas print.
         * Float tidak reliable pada semua kombinasi browser + driver
         * printer saat window.print() dipanggil.
         * ============================================================ */
        .bottom-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .bottom-section > tbody > tr > td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .td-notes {
            width: 55%;
            padding-right: 4mm !important;
        }

        .td-summary {
            width: 45%;
        }

        /* Catatan */
        .notes-box {
            border: 0.5px dashed #000;
            padding: 2mm 3mm;
            font-size: 9pt;
            min-height: 15mm;
        }

        .notes-box strong {
            display: block;
            margin-bottom: 1mm;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Summary table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 1mm 2mm;
            font-size: 9pt;
            border: none;
        }

        .summary-table .td-label {
            text-align: left;
        }

        .summary-table .td-value {
            text-align: right;
            white-space: nowrap;
        }

        .summary-table tr.row-total-akhir td {
            font-weight: bold;
            font-size: 10pt;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 1.5mm 2mm;
        }

        .summary-table tr.row-dibayar td,
        .summary-table tr.row-kembalian td {
            font-size: 9pt;
        }

        .summary-table tr.row-kembalian td {
            font-weight: bold;
        }

        /* ============================================================
         * AREA TANDA TANGAN
         * Gunakan table HTML untuk 3 kolom tanda tangan — lebih
         * konsisten saat print daripada flexbox.
         * ============================================================ */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .signature-table td {
            text-align: center;
            border: none;
            width: 33.33%;
            padding: 0;
            font-size: 9pt;
        }

        .sign-space {
            height: 10mm; /* sedikit dikurangi agar muat di 1 halaman */
            display: block;
        }

        .sign-name {
            border-top: 0.5px solid #000;
            padding-top: 1mm;
            display: inline-block;
            min-width: 30mm;
        }

        /* ============================================================
         * FOOTER FAKTUR
         * ============================================================ */
        .footer-faktur {
            margin-top: 4mm;
            text-align: center;
            font-size: 8pt;
            color: #000;
        }

        /* ============================================================
         * PRINT RULES
         * ============================================================ */
        @media print {
            .no-print {
                display: none !important;
            }

            /* Hindari page break di tengah baris tabel item */
            .items-table tr {
                page-break-inside: avoid;
            }

            /* Biarkan area tanda tangan tetap di halaman yang sama */
            .signature-table {
                page-break-inside: avoid;
            }
        }

    </style>
</head>

<body>
    <div class="container">

        {{-- @if ($penjualan->status_pembayaran == 'Lunas')
            <div class="judul">KUITANSI PENJUALAN</div>
        @elseif ($penjualan->status_pembayaran == 'Piutang')
            <div class="judul">INVOICE PENJUALAN</div>
        @elseif ($penjualan->status_pembayaran == 'Batal')
            <div class="judul">TRANSAKSI BATAL</div>
        @endif --}}

        

        {{-- ============================================================
             3 KOLOM: DARI | KEPADA | INFO TRANSAKSI
             ============================================================ --}}
        <table class="header-table">
            <tr>
                <td>
                    <span class="col-label">Dari:</span>
                    <p class="col-name">{{ $profilToko->name_toko ?? 'JO COMPUTER' }}</p>
                    <p>{{ $profilToko->alamat ?? 'Alamat toko' }}</p>
                    <p>Telp &nbsp;: {{ $profilToko->telepon ?? '-' }}</p>
                    <p>Email : {{ $profilToko->email ?? '-' }}</p>
                </td>

                <td>
                    <span class="col-label">Kepada (Customer):</span>
                    <p class="col-name">{{ $penjualan->customer->name ?? 'Customer Umum' }}</p>
                    <p>{{ $penjualan->customer->alamat ?? '-' }}</p>
                    <p>Telp &nbsp;: {{ $penjualan->customer->kontak ?? '-' }}</p>
                    <p>Email : {{ $penjualan->customer->email ?? '-' }}</p>
                </td>

                <td>
                    <span class="col-label">Info Transaksi:</span>
                    <p><span class="info-key">No. Ref &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span> {{ $penjualan->referensi }}</p>
                    @if ($penjualan->status_pembayaran !== 'Lunas' && $penjualan->tanggal_jatuh_tempo)
                        <p><span class="info-key">Jatuh Tempo &nbsp;:</span> {{ \Carbon\Carbon::parse($penjualan->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</p>
                    @endif
                    <p><span class="info-key">Tanggal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span> {{ Carbon::parse($penjualan->tanggal_penjualan)->format('d/m/Y') }}</p>
                    <p><span class="info-key">Pembayaran&nbsp;&nbsp;&nbsp;:</span> {{ $penjualan->metode_pembayaran }} - {{ $penjualan->status_pembayaran }}</p>
                    <p><span class="info-key">Kasir&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span> {{ $penjualan->user->name ?? 'User' }}</p>
                </td>
            </tr>
        </table>
        <table class="items-table">
            <colgroup>
                <col class="col-no">
                <col class="col-desc">
                <col class="col-qty">
                <col class="col-harga">
                <col class="col-disc">
                <col class="col-sub">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th class="text-left">Deskripsi Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->items as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <span class="product-name">{{ $item->product->name_product ?? 'Produk Dihapus' }}
                                @if ($item->product_variant_id && $item->varian && $item->varian->label)                      
                                    - {{ $item->varian->label }}                       
                                @endif
                            </span>
                            @if ($item->serialNumbers->isNotEmpty())
                                <span class="serial-numbers">S/N: {{ $item->serialNumbers->pluck('nomor_seri')->join(', ') }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-right">@money($item->harga_jual)</td>
                        <td class="text-right">@money($item->diskon_item)</td>
                        <td class="text-right">@money($item->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ============================================================
             BAGIAN BAWAH: CATATAN (KIRI) + SUMMARY (KANAN)
             Menggunakan table HTML untuk stabilitas layout print.
             ============================================================ --}}
        <table class="bottom-section">
            <tbody>
                <tr>
                    {{-- KOLOM KIRI: Catatan --}}
                    <td class="td-notes">
                        @if ($penjualan->catatan)
                            <div class="notes-box">
                                <strong>Catatan:</strong>
                                {!! $penjualan->catatan !!}
                            </div>
                            @else
                            <div class="notes-box">
                                <strong><u>HARAP DICEK KEMBALI</u></strong>
                                <span>Barang yang sudah diterima dengan baik tidak dapat ditukar maupun dikembalikan. <br>
                                    garansi batal jika segel & nota hilang/cacat fisik.
                                </span>
                            </div>
                        @endif
                    </td>

                    {{-- KOLOM KANAN: Ringkasan Pembayaran --}}
                    <td class="td-summary">
                        <table class="summary-table">
                            <tr>
                                <td class="td-label">Subtotal</td>
                                <td class="td-value">@money($penjualan->subtotal)</td>
                            </tr>
                            @if ($penjualan->diskon > 0)
                            <tr>
                                <td class="td-label">Diskon Tambahan</td>
                                <td class="td-value">(@money($penjualan->diskon))</td>
                            </tr>
                            @endif
                            @if ($penjualan->pajak > 0)
                            <tr>
                                <td class="td-label">PPN / Pajak</td>
                                <td class="td-value">@money($penjualan->pajak)</td>
                            </tr>
                            @endif
                            @if ($penjualan->ongkir > 0)
                            <tr>
                                <td class="td-label">Ongkos Kirim</td>
                                <td class="td-value">@money($penjualan->ongkir)</td>
                            </tr>
                            @endif
                            @if ($penjualan->service > 0)
                            <tr>
                                <td class="td-label">Biaya Servis</td>
                                <td class="td-value">@money($penjualan->service)</td>
                            </tr>
                            @endif
                            <tr class="row-total-akhir">
                                <td class="td-label">TOTAL AKHIR</td>
                                <td class="td-value">@money($penjualan->total_akhir)</td>
                            </tr>
                            @if ($penjualan->status_pembayaran == 'Piutang')
                            <tr>
                                <td class="td-label">Jumlah Dibayar</td>
                                <td class="td-value">@money($penjualan->jumlah_dibayar)</td>
                            </tr>
                            <tr>
                                <td class="td-label">Sisa Piutang</td>
                                <td class="td-value">@money($penjualan->sisa_piutang)</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr class="divider-dashed" style="margin-top: 4mm;">

        {{-- ============================================================
             AREA TANDA TANGAN
             Menggunakan table HTML — 3 kolom, konsisten saat print.
             ============================================================ --}}
        <table class="signature-table">
            <tr>
                <td>
                    <p>Penerima / Customer</p>
                    <span class="sign-space"></span>
                    <br>
                    <span class="sign-name">( {{ $penjualan->customer->name ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' }} )</span>
                </td>
                <td>
                    {{-- Kolom tengah kosong intentional --}}
                </td>
                <td>
                    <p>Hormat Kami,</p>
                    <span class="sign-space"></span>
                    <br>
                    <span class="sign-name">( {{ $penjualan->user->name ?? 'Kasir' }} )</span>
                </td>
            </tr>
        </table>

        {{-- ============================================================
             FOOTER
             ============================================================ --}}
        <hr class="divider" style="margin-top: 3mm;">
        @if ($penjualan->status_pembayaran == 'Lunas')
        <div class="footer-faktur">
            Dokumen ini adalah kuitansi resmi dari {{ $profilToko->name_toko ?? 'JO COMPUTER' }}.
            Terima kasih atas kepercayaan Anda.
        </div>
        @elseif ($penjualan->status_pembayaran == 'Piutang')
        <div class="footer-faktur">
            Dokumen ini adalah invoice resmi dari {{ $profilToko->name_toko ?? 'JO COMPUTER' }}.
            Terima kasih atas kepercayaan Anda.
        </div>
        @elseif ($penjualan->status_pembayaran == 'Batal')
        <div class="footer-faktur">
            dokumen bukti transaksi telah dibatalkan.
        </div>
        @endif
        

    </div>

    {{-- Tombol cetak — tidak ikut tercetak --}}
    <div class="no-print" style="margin-top: 20px; padding: 0 10px;">
        <button
            onclick="window.print()"
            style="display:block; width:100%; padding:12px; border:1px solid #999; background:#f5f5f5; cursor:pointer; font-weight:bold; font-family:sans-serif; font-size:14px; border-radius:4px;">
            🖨️ Cetak Faktur — Epson LX-300
        </button>
    </div>
</body>
</html>