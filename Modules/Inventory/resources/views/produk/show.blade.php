@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Produk - ' . $produk->name_product)

@section('vendor-style')
    @vite('resources/assets/vendor/libs/swiper/swiper.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/swiper/swiper.js')
@endsection

@section('content')
    <div class="p-0">

        {{-- Header Navigation --}}
        <div class="d-flex justify-content-start align-items-center mb-4 gap-3">
            <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary px-2" title="Kembali"
                data-bs-toggle="tooltip" data-bs-target="up">
                <i class="bx bx-arrow-back"></i>
                <span class="text-muted ms-2 d-none d-md-block">Kembali</span>
            </a>
            @can('edit-produk')
                <a href="{{ route('produk.edit', $produk->slug) }}" class="btn btn-primary px-2" title="Edit Produk"
                    data-bs-toggle="tooltip" data-bs-target="up">
                    <i class="bx bx-edit-alt"></i>
                    <span class="text-muted ms-2 d-none d-md-block">Edit Produk</span>
                </a>
            @endcan
        </div>

        <div class="row">
            {{-- Bagian Kiri: Gambar & Galeri (Dengan Swiper) --}}
            <div class="col-xl-5 col-lg-5 col-md-5 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3">

                        @php
                            $sortedImages = $produk->images->sortByDesc('is_primary')->values();

                            $variantImages = $produk->variants
                                ->filter(fn($v) => !empty($v->img_variant))
                                ->map(fn($v) => (object) ['path' => $v->img_variant]);

                            $sortedImages = $sortedImages->concat($variantImages)->unique('path')->values();
                        @endphp

                        {{-- 1. Main Slider (Gambar Besar) --}}
                        <div class="swiper mySwiper2 mb-3">
                            <div class="swiper-wrapper">
                                @if ($sortedImages->count() > 0)
                                    @foreach ($sortedImages as $img)
                                        <div class="swiper-slide text-center">
                                            <img src="{{ Storage::url($img->path) }}" class="img-fluid rounded shadow-sm"
                                                style="height: 500px; width: 500px; object-fit: contain; background-color: #f8f9fa;"
                                                alt="{{ $produk->name_product }}">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide text-center">
                                        <img src="{{ asset('assets/img/produk.png') }}" class="img-fluid rounded shadow-sm"
                                            style="height: 300px; width: 100%; object-fit: contain; background-color: #f8f9fa;"
                                            alt="Default">
                                    </div>
                                @endif
                            </div>

                            {{-- Tampilkan tombol Next/Prev hanya jika gambar > 1 --}}
                            @if ($sortedImages->count() > 1)
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            @endif
                        </div>

                        {{-- 2. Thumbnail Slider (Gambar Kecil di bawah) --}}
                        @if ($sortedImages->count() > 1)
                            <div class="swiper mySwiper">
                                <div class="swiper-wrapper">
                                    @foreach ($sortedImages as $img)
                                        <div class="swiper-slide">
                                            <img src="{{ Storage::url($img->path) }}" class="rounded border mx-0"
                                                style="width: 80px; height: 80px; object-fit: cover;" alt="Thumbnail">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Informasi Utama --}}
            <div class="col-xl-7 col-lg-7 col-md-7 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">{{ $produk->name_product }}</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row mb-3">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <small class="text-muted text-uppercase d-block mb-1">SKU</small>
                                <span class="fw-bold text-dark">
                                    @if ($produk->sku)
                                        {{-- Jika produk utama punya SKU (Produk Simple) --}}
                                        {{ Str::limit(strip_tags($produk->sku), 20) }}
                                    @elseif ($produk->variants && $produk->variants->count() > 0)
                                        {{-- Jika tidak punya SKU utama, ambil SKU dari varian pertama --}}
                                        {{ Str::limit(strip_tags($produk->variants->first()->sku), 15) }} &nbsp;

                                        {{-- Tambahkan indikator jika variannya lebih dari 1 --}}
                                        @if ($produk->variants->count() > 1)
                                            <span class="badge bg-label-secondary px-1" style="font-size: 0.6rem;">
                                                +{{ $produk->variants->count() - 1 }} Varian
                                            </span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted text-uppercase d-block mb-1">Barcode</small>
                                <span class="fw-bold text-dark">{{ $produk->barcode ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="row mb-3 bg-light p-3 rounded">
                            <div class="col-sm-4 mb-2 mb-sm-0">
                                <small class="text-muted d-block mb-1">Harga Beli Dasar</small>
                                <span class="fw-bold text-danger">Rp
                                    {{ number_format($produk->harga_beli, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-sm-4 mb-2 mb-sm-0">
                                <small class="text-muted d-block mb-1">Harga Jual Dasar</small>
                                <span class="fw-bold text-success fs-5">Rp
                                    {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <small class="text-muted d-block mb-1">Total Stok Tersedia</small>
                                @php $totalStok = $produk->stocks->sum('qty') ?? 0; @endphp
                                <span
                                    class="badge {{ $totalStok <= $produk->stok_minimum ? 'bg-danger' : 'bg-primary' }} fs-6">
                                    {{ $totalStok }} {{ $produk->unit->singkat ?? 'Unit' }}
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td class="text-muted w-25">Kategori</td>
                                        <td class="fw-bold">: {{ $produk->category->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Brand</td>
                                        <td class="fw-bold">: {{ $produk->brand->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pajak</td>
                                        <td class="fw-bold">: {{ $produk->pajak->name_taxe ?? 'Tidak ada pajak' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Garansi</td>
                                        <td class="fw-bold">: {{ $produk->garansi->name ?? 'Tidak bergaransi' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Batas Stok Min.</td>
                                        <td class="fw-bold">: <span class="text-warning">{{ $produk->stok_minimum }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Baris Bawah: Deskripsi, Spesifikasi & Variasi --}}
        <div class="row">
            {{-- Kolom Kiri Bawah: Deskripsi & Spek --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h6 class="card-title mb-0">Deskripsi & Spesifikasi</h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-4">
                            <small class="text-muted text-uppercase fw-bold">Deskripsi Produk</small>
                            <div class="mt-2 text-dark" style="font-size: 0.9rem;">
                                {!! $produk->description ?? '<em>Tidak ada deskripsi.</em>' !!}
                            </div>
                        </div>

                        @php
                            $specs = $produk->specification ? json_decode($produk->specification, true) : null;
                        @endphp

                        @if ($specs && is_array($specs) && count($specs) > 0)
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Spesifikasi Teknis</small>
                                <table class="table table-sm table-bordered mt-2">
                                    <tbody>
                                        @foreach ($specs as $spec)
                                            <tr>
                                                <td class="bg-light text-muted" style="width: 35%;">{{ $spec['key'] }}
                                                </td>
                                                <td class="fw-bold">{{ $spec['value'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan Bawah: Tabel Varian Produk (Jika Ada) --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h6 class="card-title mb-0">Variasi Produk</h6>
                    </div>
                    <div class="card-body pt-3 p-0">
                        @if ($produk->variants->count() > 0)
                            <div class="row g-3">
                                @foreach ($produk->variants as $variant)
                                    @php $varStok = $produk->stocks->where('product_variant_id', $variant->id)->sum('qty') ?? 0; @endphp
                                    <div class="col-12">
                                        <div class="card mx-3 border h-100 variant-card">
                                            <div class="card-body d-flex gap-3">
                                                <div class="flex-shrink-0">
                                                    @if ($variant->img_variant && Storage::disk('r2')->exists($variant->img_variant))
                                                        <img src="{{ Storage::url($variant->img_variant) }}"
                                                            class="rounded"
                                                            style="width: 106px; height: 106px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded bg-label-secondary d-flex align-items-center justify-content-center"
                                                            style="width: 106px; height: 106px;">
                                                            <i class="bx bx-image text-muted"
                                                                style="font-size: 1.5rem;"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex-grow-1 overflow-hidden">
                                                    <span class="badge bg-label-info mb-2">
                                                        {{ $variant->options->pluck('value')->join(' / ') }}
                                                    </span>

                                                    <div class="d-flex justify-content-between align-items-start mt-1">
                                                        <small class="text-muted d-block text-truncate"
                                                            style="max-width: 60%;">
                                                            {{ $variant->sku }}
                                                        </small>
                                                        <span
                                                            class="badge {{ $varStok > 0 ? 'bg-secondary' : 'bg-label-danger' }}">
                                                            Stok: {{ $varStok }}
                                                        </span>
                                                    </div>

                                                    <p class="text-success fw-bold mb-0 mt-2">
                                                        Rp {{ number_format($variant->harga_jual, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-layer text-muted mb-2" style="font-size: 2.5rem;"></i>
                                <p class="text-muted mb-0">Produk ini tidak memiliki variasi.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {

                // 1. Inisialisasi Thumbnail Slider (Bawah)
                var swiperThumbs = new Swiper(".mySwiper", {
                    spaceBetween: 10,
                    slidesPerView: 4, // Tampilkan 4 kotak gambar kecil
                    freeMode: true,
                    watchSlidesProgress: true,
                });

                // 2. Inisialisasi Main Slider (Atas)
                var swiperMain = new Swiper(".mySwiper2", {
                    spaceBetween: 10,
                    loop: true, // Biar bisa digeser (swipe) terus berputar
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    thumbs: {
                        swiper: swiperThumbs, // Menghubungkan klik thumbnail ke gambar utama
                    },
                });

            } else {
                console.warn('Swiper.js gagal dimuat.');
            }
        });
    </script>
@endsection
