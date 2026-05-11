{{-- resources/views/dashboard/all.blade.php --}}
@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="row">
        {{-- Notifikasi Butuh Nomor Seri --}}
        <div class="col-12 mb-4">
            <div class="card rounded-2">
                <div class="card-header pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Butuh Pendaftaran Nomor Seri</h6>
                            <p class="text-sm mb-0">Product wajib seri yang jumlah stoknya belum sesuai dengan nomor seri
                                terdaftar.</p>
                        </div>
                        <span class="badge bg-label-warning">{{ $productsNeedingSerials->count() }} Item</span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="list-group list-group-flush">
                        @forelse ($productsNeedingSerials as $produk)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $produk->img_produk ? Storage::url($produk->img_produk) : asset('assets/img/produk.png') }}"
                                        class="avatar avatar-lg me-3" alt="product image">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-1 text-sm">{{ $produk->name_product }}</h6>
                                        <div class="d-md-flex text-xs text-secondary">
                                            <div class="me-3">Stock Fisik: <span
                                                    class="text-dark font-weight-bold">{{ $produk->qty }}</span></div>
                                            <div class="d-md-flex text-xs text-secondary">
                                                <div class="me-3 my-md-0 my-1">Total SN Tercatat: <span
                                                        class="text-dark font-weight-bold">{{ $produk->sn_tercatat_count }}</span>
                                                </div>
                                                <div>
                                                    <strong class="text-danger">Butuh:
                                                        {{ $produk->qty - $produk->sn_tercatat_count }} SN</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('serialNumber.index', ['produk_slug' => $produk->slug]) }}"
                                    class="btn btn-outline-primary btn-sm px-3 mb-0 d-inline-flex align-items-center justify-content-center">
                                    <i class="bx bx-upc-scan me-md-2"></i><span class="d-none d-md-inline">Kelola
                                        SN</span>
                                </a>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0 fw-bolder text-sm">🎉 Hebat! Semua produk sudah memiliki nomor
                                    seri yang sesuai.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifikasi Stock Rendah --}}
        <div class="col-12">
            <div class="card rounded-2">
                <div class="card-header pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Stock Rendah</h6>
                            <p class="text-sm mb-0">Product yang stoknya di bawah atau sama dengan batas minimum.</p>
                        </div>
                        <span class="badge bg-label-danger">{{ $lowStockProducts->count() }} Item</span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="list-group list-group-flush">
                        @forelse ($lowStockProducts as $produk)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $produk->img_produk ? Storage::url($produk->img_produk) : asset('assets/img/produk.png') }}"
                                        class="avatar avatar-lg me-3" alt="product image">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $produk->name_product }}</h6>
                                        <div class="d-md-flex text-xs text-secondary">
                                            <div class="me-3">Stock Minimum: <span
                                                    class="text-dark font-weight-bold">{{ $produk->stok_minimum }}</span>
                                            </div>
                                            <div>Sisa Stock: <strong class="text-danger">{{ $produk->qty }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Mengarahkan ke halaman edit produk untuk mengelola stok secara langsung --}}
                                <a href="{{ route('produk.edit', $produk->slug) }}"
                                    class="btn btn-outline-info btn-sm px-3 mb-0 d-inline-flex align-items-center justify-content-center">
                                    <i class="bx bx-edit me-md-2"></i><span class="d-none d-md-inline">Kelola
                                        Stock</span>
                                </a>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0 fw-bolder text-sm">👍 Bagus! Tidak ada produk dengan stok
                                    rendah saat ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
