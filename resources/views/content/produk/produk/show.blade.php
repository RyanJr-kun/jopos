@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('breadcrumb')
    @php
        // Definisikan item breadcrumb dalam bentuk array
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Manajemen Product', 'url' => route('produk.index')],
            ['name' => 'Detail Product', 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection
<div class="container-fluid p-3">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-md-flex d-block justify-content-between align-items-center">
                <h5 class="mb-3">Detail Product: {{ $produk->name_produk }}</h5>
                <div>
                    <a href="{{ route('produk.edit', $produk->slug) }}" class="btn btn-sm btn-info mb-0">Edit</a>
                    <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-secondary mb-0">Kembali</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Kolom Kiri: Detail Product dalam Tabel --}}
                <div class="col-lg-7">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-bold" style="width: 30%;">Nama Product</td>
                                    <td>: {{ $produk->name_produk }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">SKU (Stock Keeping Unit)</td>
                                    <td>: {{ $produk->sku }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Barcode</td>
                                    <td>: {{ $produk->barcode ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kategori</td>
                                    <td>: {{ $produk->category->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Brand</td>
                                    <td>: {{ $produk->brand->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Unit</td>
                                    <td>: {{ $produk->unit->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Harga Jual</td>
                                    <td>: Rp.{{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Harga Beli</td>
                                    <td>: Rp.{{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Stock Saat Ini</td>
                                    <td>: {{ $produk->qty }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Stock Minimum</td>
                                    <td>: {{ $produk->stok_minimum }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Warrantie</td>
                                    <td>: {{ $produk->garansi?->name ?? 'Tidak ada garansi' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Taxe</td>
                                    <td>: {{ $produk->pajak?->name_taxe ?? 'Tidak ada' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Dibuat oleh</td>
                                    <td>: {{ $produk->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold align-text-top">Description</td>
                                    <td class="align-text-top">:<div
                                            style="white-space: normal; word-wrap: break-word;"> {!! $produk->description ?? '-' !!}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Kanan: Gambar Product --}}
                <div class="col-lg-5 mt-4 mt-lg-0">
                    <h5 class="ms-2">Gambar Product :</h5>
                    <div class="text-start ms-2 my-3">
                        @if ($produk->img_produk && Storage::disk('public')->exists($produk->img_produk))
                            <img src="{{ asset('storage/' . $produk->img_produk) }}"
                                class="img-fluid border-radius-lg shadow-lg" style="max-height: 500px;"
                                alt="Gambar Product {{ $produk->name_produk }}">
                        @else
                            <img src="{{ asset('assets/img/produk.webp') }}" class="img-fluid border-radius-lg"
                                alt="Gambar produk default">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
