@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="container-fluid p-3">
        {{-- Master Detail Card --}}
        <div class="card rounded-2 mb-4">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-n1">Detail Penyesuaian Stock</h6>
                        <p class="text-sm mb-0">Rincian penyesuaian untuk <span
                                class="fw-bold">{{ $penyesuaian->kode_penyesuaian }}</span></p>
                    </div>
                    <div class="ms-md-auto ">
                        <a href="{{ route('stok-penyesuaian.index') }}" class="btn btn-outline-secondary mb-0 d-flex mt-md-2">
                            <i class="bx bx-arrow-left mx-2"></i>
                            <span class="d-none d-md-block">Kembali ke Riwayat</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-4">
                        <p class="text-sm mb-1"><strong class="text-dark">Kode Penyesuaian:</strong>
                            {{ $penyesuaian->kode_penyesuaian }}</p>
                        <p class="text-sm mb-1"><strong class="text-dark">Tanggal:</strong>
                            {{ $penyesuaian->tanggal_penyesuaian->translatedFormat('l, d F Y H:i') }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-sm mb-1"><strong class="text-dark">Dilakukan oleh:</strong>
                            {{ $penyesuaian->user->username ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-sm mb-1"><strong class="text-dark">Catatan Umum:</strong></p>
                        <p class="text-sm">{{ $penyesuaian->catatan ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Table Card --}}
        <div class="card rounded-2">
            <div class="card-header pb-0">
                <h6>Item yang Disesuaikan</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table table-hover align-items-center mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Product</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Tipe</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Jumlah</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stock Sebelum
                                </th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stock Setelah
                                </th>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder">Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penyesuaian->details as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $detail->produk->img_produk ? asset('storage/' . $detail->produk->img_produk) : asset('assets/img/produk.png') }}"
                                                    class="avatar avatar-sm me-3" alt="product image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    {{ $detail->produk->name_product ?? 'Product Dihapus' }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $detail->produk->sku ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        {!! $detail->tipe_formatted !!}
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        {!! $detail->jumlah_formatted !!}
                                    </td>
                                    <td class="align-middle text-center text-sm"><span
                                            class="fw-bold">{{ $detail->stok_sebelum }}</span></td>
                                    <td class="align-middle text-center text-sm"><span
                                            class="fw-bold">{{ $detail->stok_setelah }}</span></td>
                                    <td class="align-middle text-sm">{{ $detail->alasan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada detail item untuk penyesuaian ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
