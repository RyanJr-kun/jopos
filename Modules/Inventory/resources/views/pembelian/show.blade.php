@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Purchase - ' . $pembelian->referensi)
@section('content')

    <div class="container-fluid p-0">
        <div class="card rounded-3 shadow-sm border-0 printable-area">

            <!-- HEADER -->
            <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-icon btn-outline-secondary btn-sm"
                            data-bs-toggle="tooltip" title="Kembali">
                            <i class="bx bx-arrow-back"></i>
                        </a>
                        <h5 class="mb-0 fw-bold">Detail Purchase <span
                                class="text-muted fs-6 ms-1">#{{ $pembelian->referensi }}</span></h5>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pembelian.thermal', $pembelian->referensi) }}" target="_blank"
                            class="btn btn-sm btn-dark flex-grow-1 flex-md-grow-0">
                            <i class="bx bx-receipt me-1"></i> Struk
                        </a>
                        <a href="{{ route('pembelian.pdf', $pembelian->referensi) }}" target="_blank"
                            class="btn btn-sm btn-outline-danger px-3" data-bs-toggle="tooltip" title="Download PDF">
                            <i class="bx bxs-file-pdf"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- INFO GRID SECTION -->
                <div class="row g-4 mb-5">

                    <!-- Info Toko -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border rounded-3 p-3 h-100 bg-label-light">
                            <span class="d-block text-muted text-xs fw-bold text-uppercase mb-2">Dari (Penerima)</span>
                            <h6 class="text-dark fw-bold mb-1"><i class="bx bx-store-alt text-muted me-1"></i>
                                {{ $profilToko->name_toko ?? 'Toko Saya' }}</h6>
                            <p class="text-sm mb-2 text-wrap">{{ $profilToko->alamat ?? 'Alamat toko belum diatur' }}</p>
                            <div class="text-sm d-flex align-items-center mb-1"><i
                                    class="bx bx-envelope text-muted me-2"></i> {{ $profilToko->email ?? '-' }}</div>
                            <div class="text-sm d-flex align-items-center"><i class="bx bx-phone text-muted me-2"></i>
                                {{ $profilToko->telepon ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Info Supplier -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border rounded-3 p-3 h-100 bg-white">
                            <span class="d-block text-muted text-xs fw-bold text-uppercase mb-2">Kepada (Supplier)</span>
                            <h6 class="text-dark fw-bold mb-1"><i class="bx bxs-truck text-primary me-1"></i>
                                {{ $pembelian->supplier->name ?? 'Supplier Dihapus' }}</h6>
                            <p class="text-sm mb-2 text-wrap">{{ $pembelian->supplier->alamat ?? 'Alamat tidak tersedia' }}
                            </p>
                            <div class="text-sm d-flex align-items-center mb-1"><i
                                    class="bx bx-envelope text-muted me-2"></i> {{ $pembelian->supplier->email ?? '-' }}
                            </div>
                            <div class="text-sm d-flex align-items-center"><i class="bx bx-phone text-muted me-2"></i>
                                {{ $pembelian->supplier->kontak ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Info Transaksi -->
                    <div class="col-12 col-lg-4">
                        <div class="border rounded-3 p-3 h-100 bg-white">
                            <span class="d-block text-muted text-xs fw-bold text-uppercase mb-3">Data Transaksi</span>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-muted">Tanggal:</span>
                                <span
                                    class="text-sm fw-semibold">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-muted">Dibuat Oleh:</span>
                                <span class="text-sm fw-semibold">{{ $pembelian->user->name ?? 'Sistem' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm text-muted">Status Barang:</span>
                                <span
                                    class="badge {{ $pembelian->status_barang == 'Diterima' ? 'bg-label-success' : ($pembelian->status_barang == 'Dibatalkan' ? 'bg-label-danger' : 'bg-label-warning') }}">{{ $pembelian->status_barang }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-sm text-muted">Status Bayar:</span>
                                <span
                                    class="badge {{ $pembelian->status_pembayaran == 'Lunas' ? 'bg-label-success' : ($pembelian->status_pembayaran == 'Dibatalkan' ? 'bg-label-danger' : 'bg-label-warning') }}">{{ $pembelian->status_pembayaran }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL ITEM -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Rincian Produk</h6>
                    <div class="table-responsive text-nowrap border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-secondary text-dark">
                                <tr>
                                    <th class="text-center text-xs fw-bold py-3" width="5%">No</th>
                                    <th class="text-xs fw-bold py-3">Nama Product</th>
                                    <th class="text-center text-xs fw-bold py-3" width="10%">Qty</th>
                                    <th class="text-end text-xs fw-bold py-3" width="15%">Harga Beli</th>
                                    <th class="text-end text-xs fw-bold py-3" width="15%">Diskon</th>
                                    <th class="text-end text-xs fw-bold py-3" width="15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($pembelian->details as $detail)
                                    <tr>
                                        <td class="text-center text-sm">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="text-sm fw-semibold text-dark">{{ $detail->produk->name_product ?? 'Produk Dihapus' }}</span>

                                                {{-- MENDUKUNG VARIAN: Jika punya varian, tampilkan di bawah nama --}}
                                                @if ($detail->product_variant_id && $detail->varian)
                                                    @php
                                                        // Asumsi ada relasi options untuk membentuk nama. Jika tidak, pakai SKU varian.
                                                        $variantOpts = [];
                                                        if ($detail->varian->relationLoaded('options')) {
                                                            foreach ($detail->varian->options as $opt) {
                                                                $variantOpts[] = $opt->value;
                                                            }
                                                        }
                                                        $namaVarian = !empty($variantOpts)
                                                            ? implode(' / ', $variantOpts)
                                                            : 'SKU: ' . $detail->varian->sku;
                                                    @endphp
                                                    <small class="text-muted"><i
                                                            class="bx bx-list-ul text-xs me-1"></i>{{ $namaVarian }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center text-sm">{{ $detail->qty }}</td>
                                        <td class="text-end text-sm">Rp
                                            {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                                        <td class="text-end text-sm text-danger">
                                            {{ $detail->diskon > 0 ? '- Rp ' . number_format($detail->diskon, 0, ',', '.') : 'Rp 0' }}
                                        </td>
                                        <td class="text-end text-sm fw-semibold">Rp
                                            {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- KALKULASI TOTAL -->
                <div class="row justify-content-end">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-label-light p-3 rounded-3 border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-muted">Subtotal Produk</span>
                                <span class="text-sm fw-semibold">Rp
                                    {{ number_format($pembelian->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-muted">Total PPN (Tax)</span>
                                <span class="text-sm fw-semibold">Rp
                                    {{ number_format($pembelian->pajak, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-muted">Diskon Tambahan</span>
                                <span class="text-sm fw-semibold text-danger">- Rp
                                    {{ number_format($pembelian->diskon, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-sm text-muted">Ongkos Kirim</span>
                                <span class="text-sm fw-semibold">Rp
                                    {{ number_format($pembelian->ongkir, 0, ',', '.') }}</span>
                            </div>

                            <hr class="border-secondary opacity-25 my-2">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-dark fw-bold text-uppercase">Total Akhir</span>
                                <span class="fw-bolder fs-5 text-primary">Rp
                                    {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-sm text-muted">Dibayar</span>
                                <span class="text-sm fw-semibold">Rp
                                    {{ number_format($pembelian->jumlah_dibayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-sm text-dark fw-bold">Sisa Tagihan</span>
                                @if ($pembelian->sisa_hutang > 0)
                                    <span class="text-sm fw-bold text-danger">Rp
                                        {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-sm fw-bold text-success">Lunas</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATATAN -->
                @if ($pembelian->catatan)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-secondary border-0 mb-0">
                                <h6 class="alert-heading fw-bold mb-1"><i class="bx bx-notepad me-1"></i> Catatan
                                    Transaksi</h6>
                                <div class="text-sm text-dark mb-0">
                                    {!! $pembelian->catatan !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
