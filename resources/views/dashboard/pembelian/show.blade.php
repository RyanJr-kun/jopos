@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Daftar Invoice Pembelian', 'url' => route('pembelian.index')],
            ['name' => 'Detail Pembelian', 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid py-4">
    <div class="card rounded-2 printable-area">
        <div class="card-header d-md-flex justify-content-between align-items-center pb-0">
            <h5 class="mb-2 fw-bolder mb-md-0">Detail Pembelian</h5>
            <div>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary mb-0">
                    <i class="bx bx-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('pembelian.thermal', $pembelian->referensi) }}" target="_blank"
                    class="btn btn-sm btn-dark mx-2 mb-0">
                    <i class="bx bx-receipt me-1"></i> Struk
                </a>
                <a href="{{ route('pembelian.pdf', $pembelian->referensi) }}" target="_blank"
                    class="btn btn-sm btn-outline-danger px-2 mb-0" data-bs-toggle="tooltip" title="Export PDF">
                    <img src="{{ asset('assets/img/pdf.png') }}" alt="Download PDF" width="20" height="20">
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4 gx-4">
                {{-- Kolom Kiri: Info Toko & Pemasok --}}
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Dari:</h6>
                            <h5 class="text-dark text-uppercase fw-bolder mb-1">
                                {{ $profilToko->nama_toko ?? 'Nama Toko Anda' }}</h5>
                            <p class="text-sm mb-1">{{ $profilToko->alamat ?? 'Alamat toko belum diatur' }}</p>
                            <p class="text-sm mb-0">Email: {{ $profilToko->email ?? '-' }}</p>
                            <p class="text-sm mb-0">Telp: {{ $profilToko->telepon ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0">
                            <h6 class="mb-2">Kepada (Pemasok):</h6>
                            <h5 class="text-dark fw-bolder mb-1">{{ $pembelian->pemasok->nama ?? 'Pemasok Dihapus' }}
                            </h5>
                            <p class="text-sm mb-1">{{ $pembelian->pemasok->alamat ?? 'Alamat tidak tersedia' }}</p>
                            <p class="text-sm mb-0">Email: {{ $pembelian->pemasok->email ?? '-' }}</p>
                            <p class="text-sm mb-0">Kontak: {{ $pembelian->pemasok->kontak ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                {{-- Kolom Kanan: Info Transaksi --}}
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <h6 class="mb-2">Informasi Transaksi</h6>
                    <div class="border rounded-2 p-3">
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Referensi:</strong>
                            <span>{{ $pembelian->referensi }}</span>
                        </p>
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Tanggal:</strong>
                            <span>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}</span>
                        </p>
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Status Barang:</strong> <span
                                class="badge badge-sm {{ $pembelian->status_barang == 'Diterima' ? 'badge-success' : 'badge-warning' }}">{{ $pembelian->status_barang }}</span>
                        </p>
                        <p class="text-sm mb-2 d-flex justify-content-between"><strong>Status Bayar:</strong> <span
                                class="badge badge-sm {{ $pembelian->status_pembayaran == 'Lunas' ? 'badge-success' : ($pembelian->status_pembayaran == 'Lunas Sebagian' ? 'badge-warning' : 'badge-danger') }}">{{ $pembelian->status_pembayaran }}</span>
                        </p>
                        <hr class="horizontal dark my-2">
                        <p class="text-sm mb-0 d-flex justify-content-between"><strong>Dibuat Oleh:</strong>
                            <span>{{ $pembelian->user->nama ?? 'User Dihapus' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <p class="mb-1 fw-bolder">Ringkasan Pembelian:</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center text-dark text-xs font-weight-bolder">No.</th>
                            <th class="text-dark text-xs font-weight-bolder ps-2">Produk</th>
                            <th class="text-center text-dark text-xs font-weight-bolder">Qty</th>
                            <th class="text-end text-dark text-xs font-weight-bolder pe-2">Harga Beli</th>
                            <th class="text-end text-dark text-xs font-weight-bolder pe-2">Diskon</th>
                            <th class="text-end text-dark text-xs font-weight-bolder ">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian->details as $detail)
                            <tr>
                                <td class="text-center text-sm">{{ $loop->iteration }}</td>
                                <td class="text-sm">{{ $detail->produk->nama_produk }}</td>
                                <td class="text-center text-sm">{{ $detail->qty }}</td>
                                <td class="text-end text-sm">@money($detail->harga_beli)</td>
                                <td class="text-end text-sm">@money($detail->diskon)</td>
                                <td class="text-end text-sm">@money($detail->subtotal)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 justify-content-end">
                <div class="col-md-5">
                    <div class="d-flex justify-content-between">
                        <p class="text-sm fw-bold mb-2">Subtotal Keseluruhan:</p>
                        <p class="text-sm fw-bold mb-2">@money($pembelian->subtotal)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm mb-2">Diskon Tambahan:</p>
                        <p class="text-sm mb-2">@money($pembelian->diskon)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm mb-2">PPN:</p>
                        <p class="text-sm mb-2">@money($pembelian->pajak)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm mb-2">Ongkos Kirim:</p>
                        <p class="text-sm mb-2">@money($pembelian->ongkir)</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 my-2">
                        <h6 class="fw-bolder mb-0">TOTAL AKHIR</h6>
                        <h6 class="fw-bolder mb-0">@money($pembelian->total_akhir)</h6>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <p class="text-sm mb-2">Jumlah Dibayar:</p>
                        <p class="text-sm mb-2">@money($pembelian->jumlah_dibayar)</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-sm fw-bold mb-0">Sisa Hutang:</p>
                        <p class="text-sm fw-bold text-danger mb-0">@money($pembelian->sisa_hutang)</p>
                    </div>
                </div>
            </div>

            @if ($pembelian->catatan)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Catatan:</h6>
                        <div class="p-3 border rounded text-sm">
                            {!! $pembelian->catatan !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
