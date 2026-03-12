@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <style>
        .difference-positive {
            color: #2dce89;
            font-weight: bold;
        }

        .difference-negative {
            color: #f5365c;
            font-weight: bold;
        }

        .difference-zero {
            color: #8898aa;
        }
    </style>
@endsection
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Stock Opname', 'url' => route('stok-opname.index')],
            ['name' => 'Riwayat', 'url' => route('stok-opname.history')],
            ['name' => 'Detail ' . $stokOpname->kode_opname, 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2 mb-4">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Detail Stock Opname</h6>
                    <p class="text-sm mb-0">Rincian penyesuaian stok untuk <span
                            class="fw-bold">{{ $stokOpname->kode_opname }}</span></p>
                </div>
                <div class="ms-md-auto mt-2">
                    <a href="{{ route('stok-opname.history') }}" class="btn btn-outline-secondary mb-0">
                        <i class="bx bx-arrow-left me-2"></i>Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-4">
                    <p class="text-sm mb-1"><strong class="text-dark">Kode Opname:</strong>
                        {{ $stokOpname->kode_opname }}</p>
                    <p class="text-sm mb-1"><strong class="text-dark">Tanggal:</strong>
                        {{ $stokOpname->tanggal_opname->translatedFormat('l, d F Y H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <p class="text-sm mb-1"><strong class="text-dark">Dilakukan oleh:</strong>
                        {{ $stokOpname->user->username ?? 'N/A' }}</p>
                    <p class="text-sm mb-1"><strong class="text-dark">Status:</strong> <span
                            class="badge badge-sm badge-success">{{ $stokOpname->status }}</span></p>
                </div>
                <div class="col-md-4">
                    <p class="text-sm mb-1"><strong class="text-dark">Catatan Umum:</strong></p>
                    <p class="text-sm">{{ $stokOpname->catatan ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

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
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stock Sistem
                            </th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Stock Fisik</th>
                            <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Selisih</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stokOpname->details as $detail)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="{{ $detail->produk->img_produk ? asset('storage/' . $detail->produk->img_produk) : asset('assets/img/produk.webp') }}"
                                                class="avatar avatar-sm me-3" alt="product image">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">
                                                {{ $detail->produk->name_produk ?? 'Product Dihapus' }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $detail->produk->sku ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm"><span
                                        class="fw-bold">{{ $detail->stok_sistem }}</span></td>
                                <td class="align-middle text-center text-sm"><span
                                        class="fw-bold">{{ $detail->stok_fisik }}</span></td>
                                <td class="align-middle text-center text-sm">
                                    @php $class = $detail->selisih == 0 ? 'difference-zero' : ($detail->selisih > 0 ? 'difference-positive' : 'difference-negative'); @endphp
                                    <span
                                        class="{{ $class }}">{{ $detail->selisih > 0 ? '+' . $detail->selisih : $detail->selisih }}</span>
                                </td>
                                <td class="align-middle text-sm">{{ $detail->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada detail item untuk opname ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
