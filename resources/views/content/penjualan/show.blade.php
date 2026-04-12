@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .printable-area,
            .printable-area * {
                visibility: visible;
            }

            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
@endsection

<div class="container-fluid py-4">
    <div class="card rounded-2 printable-area">
        <div class="card-header d-flex justify-content-between align-items-center pb-0">
            <h5 class="mb-2 fw-bolder mb-md-0">Detail Sale</h5>
            <div class="no-print">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary mb-0">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
                {{-- Tambahkan route ke thermal print jika ada --}}
                <a href="{{ route('penjualan.thermal', $penjualan->referensi) }}" target="_blank"
                    class="btn btn-sm btn-dark mx-2 mb-0">
                    <i class="bx bx-receipt me-1"></i> Struk
                </a>
                <a href="{{ route('penjualan.pdf', $penjualan->referensi) }}" target="_blank"
                    class="btn btn-sm btn-outline-danger px-2 mb-0" data-bs-toggle="tooltip" title="Export PDF">
                    <img src="{{ asset('assets/img/pdf.png') }}" alt="Download PDF" width="20" height="20">
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4 gx-4">
                {{-- Kolom Kiri: Info Toko & Customer --}}
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Dari:</h6>
                            <h5 class="text-dark text-uppercase fw-bolder mb-1">
                                {{ $profilToko->name_toko ?? 'Nama Toko Anda' }}</h5>
                            <p class="text-sm mb-1">{{ $profilToko->alamat ?? 'Alamat toko belum diatur' }}</p>
                            <p class="text-sm mb-0">Email: {{ $profilToko->email ?? '-' }}</p>
                            <p class="text-sm mb-0">Telp: {{ $profilToko->telepon ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0">
                            <h6 class="mb-2">Kepada (Customer):</h6>
                            <h5 class="text-dark fw-bolder mb-1">{{ $penjualan->pelanggan->name ?? 'Customer Umum' }}
                            </h5>
                            <p class="text-sm mb-1">{{ $penjualan->pelanggan->alamat ?? 'Alamat tidak tersedia' }}</p>
                            <p class="text-sm mb-0">Email: {{ $penjualan->pelanggan->email ?? '-' }}</p>
                            <p class="text-sm mb-0">Kontak: {{ $penjualan->pelanggan->kontak ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                {{-- Kolom Kanan: Info Transaksi --}}
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <h6 class="mb-2">Informasi Transaksi</h6>
                    <div class="border rounded-2 p-3">
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Referensi:</strong>
                            <span>{{ $penjualan->referensi }}</span>
                        </p>
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Tanggal:</strong>
                            <span>{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d F Y, H:i') }}</span>
                        </p>
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Metode Bayar:</strong>
                            <span>{{ $penjualan->metode_pembayaran }}</span>
                        </p>
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Status Bayar:</strong> <span
                                class="badge badge-sm {{ $penjualan->status_pembayaran == 'Lunas' ? 'bg-label-success' : ($penjualan->status_pembayaran == 'Belum Lunas' ? 'bg-label-warning' : 'bg-label-danger') }}">{{ $penjualan->status_pembayaran }}</span>
                        </p>
                        <hr class="horizontal dark my-2">
                        <p class="text-sm mb-0 d-flex justify-content-between"><strong>Dibuat Oleh:</strong>
                            <span>{{ $penjualan->user->name ?? 'User Dihapus' }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div>
                <p class="mb-1 fw-bolder">Ringkasan Sale:</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center text-dark text-xs font-weight-bolder">No.</th>
                            <th class="text-dark text-xs font-weight-bolder ps-2">Product</th>
                            <th class="text-center text-dark text-xs font-weight-bolder">Qty</th>
                            <th class="text-end text-dark text-xs font-weight-bolder pe-2">Harga Jual</th>
                            <th class="text-end text-dark text-xs font-weight-bolder pe-2">Diskon</th>
                            <th class="text-end text-dark text-xs font-weight-bolder ">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan->items as $item)
                            <tr>
                                <td class="text-center text-sm">{{ $loop->iteration }}</td>
                                <td class="text-sm">
                                    {{ $item->product->name_product ?? 'Product Dihapus' }}
                                    @if ($item->serialNumbers->isNotEmpty())
                                        <div class="text-muted text-sm">
                                            <strong>SN:</strong>
                                            {{ $item->serialNumbers->pluck('nomor_seri')->join(', ') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center text-sm">{{ $item->jumlah }}</td>
                                <td class="text-end text-sm">@money($item->harga_jual)</td>
                                <td class="text-end text-sm">@money($item->diskon_item)</td>
                                <td class="text-end text-sm">@money($item->subtotal)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 justify-content-end">
                <div class="col-md-5">
                    <div class="d-flex justify-content-between">
                        <p class="text-sm fw-bold mb-2">Subtotal Keseluruhan:</p>
                        <p class="text-sm fw-bold mb-2">@money($penjualan->subtotal)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm mb-2">Diskon Tambahan:</p>
                        <p class="text-sm mb-2">@money($penjualan->diskon)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm mb-2">PPN:</p>
                        <p class="text-sm mb-2">@money($penjualan->pajak)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm mb-2">Ongkos Kirim:</p>
                        <p class="text-sm mb-2">@money($penjualan->ongkir)</p>
                    </div>
                    @if ($penjualan->service > 0)
                        <div class="d-flex justify-content-between">
                            <p class="text-sm mb-2">Biaya Servis:</p>
                            <p class="text-sm mb-2">@money($penjualan->service)</p>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 my-2">
                        <h6 class="fw-bolder mb-0">TOTAL AKHIR</h6>
                        <h6 class="fw-bolder mb-0">@money($penjualan->total_akhir)</h6>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <p class="text-sm mb-2">Jumlah Dibayar:</p>
                        <p class="text-sm mb-2">@money($penjualan->jumlah_dibayar)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        @if ($penjualan->kembalian > 0)
                            <p class="text-sm fw-bold mb-0">Kembalian:</p>
                            <p class="text-sm fw-bold text-success mb-0">@money($penjualan->kembalian)</p>
                        @else
                            <p class="text-sm fw-bold mb-0">Sisa Bayar:</p>
                            <p class="text-sm fw-bold text-danger mb-0">@money(abs($penjualan->kembalian))</p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($penjualan->catatan)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Catatan:</h6>
                        <div class="p-3 border rounded text-sm">
                            {!! $penjualan->catatan !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
