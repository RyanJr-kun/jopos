@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', $produk->name_product . ' - JO Computer')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/swiper/swiper.scss')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* Modern Container & Typography */
        .container-market {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .product-title {
            font-size: clamp(1.5rem, 2.5vw, 2.2rem);
            line-height: 1.2;
            color: #2c3e50;
        }

        .product-price {
            font-size: clamp(1.4rem, 2vw, 1.8rem);
            color: #e74c3c;
        }

        /* Swiper Custom Styling */
        .swiper-main {
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
            border: 1px solid #eee;
        }

        .swiper-main .swiper-slide img {
            width: 100%;
            height: 450px;
            object-fit: contain;
            mix-blend-mode: multiply;
            /* Menghilangkan background putih gambar jika ada */
        }

        .swiper-thumbs {
            margin-top: 1rem;
            padding: 0.25rem;
        }

        .swiper-thumbs .swiper-slide {
            opacity: 0.5;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 8px;
            border: 2px solid transparent;
            height: 80px;
            background: #f8f9fa;
        }

        .swiper-thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border-color: #696cff;
            /* Primary color */
        }

        .swiper-thumbs .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }

        /* Responsive Swiper Height */
        @media (max-width: 991px) {
            .swiper-main .swiper-slide img {
                height: 350px;
            }
        }

        @media (max-width: 576px) {
            .swiper-main .swiper-slide img {
                height: 280px;
            }

            .swiper-thumbs .swiper-slide {
                height: 60px;
            }
        }

        /* Varian Selection */
        .variant-btn {
            border: 1px solid #ddd;
            background: #fff;
            color: #333;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .variant-btn:hover {
            border-color: #696cff;
            color: #696cff;
        }

        .variant-btn.active {
            background: #696cff;
            color: #fff;
            border-color: #696cff;
        }

        /* Description & Spec Tables */
        .product-description {
            color: #555;
            line-height: 1.8;
            font-size: 1rem;
        }

        .spec-table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            width: 35%;
        }

        /* WhatsApp Button Pulse Effect */
        .btn-wa {
            background-color: #25D366;
            color: white;
            border: none;
            transition: transform 0.2s;
        }

        .btn-wa:hover {
            background-color: #128C7E;
            transform: translateY(-2px);
            color: white;
        }
    </style>
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/swiper/swiper.js')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')
    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    {{-- Breadcrumb --}}
    <div class="bg-light py-2 border-bottom">
        <div class="container-market">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 fs-7">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"
                            class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('market.produk') }}"
                            class="text-decoration-none text-muted">Katalog</a></li>
                    @if ($produk->category)
                        <li class="breadcrumb-item"><a
                                href="{{ route('market.produk', ['kategori' => $produk->category->slug]) }}"
                                class="text-decoration-none text-muted">{{ $produk->category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active text-dark fw-medium text-truncate" aria-current="page"
                        style="max-width: 200px;">{{ $produk->name_product }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-market py-4 py-lg-5">
        <div class="row g-4 g-lg-5">
            {{-- Bagian Kiri: Galeri Gambar dengan Swiper --}}
            <div class="col-lg-6" data-aos="fade-right">
                @php
                    // 1. Ambil gambar galeri, urutkan yang primary di awal
                    $galleryImages = $produk->images->sortByDesc('is_primary')->map(function ($img) {
                        return (object) ['path' => $img->path];
                    });

                    // 2. Ambil gambar dari varian (yang tidak kosong)
                    $variantImages = $produk->variants
                        ->pluck('img_variant')
                        ->filter()
                        ->unique()
                        ->map(function ($path) {
                            return (object) ['path' => $path];
                        });

                    // 3. Gabungkan keduanya, lalu hapus duplikat path agar rapi
                    $allImages = $galleryImages->concat($variantImages)->unique('path')->values();
                @endphp

                <!-- Swiper Utama (Besar) -->
                <div class="swiper swiper-main shadow-sm">
                    <div class="swiper-wrapper">
                        @if ($allImages->count() > 0)
                            @foreach ($allImages as $img)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/' . $img->path) }}" alt="{{ $produk->name_product }}"
                                        loading="lazy">
                                </div>
                            @endforeach
                        @else
                            <div class="swiper-slide">
                                <img src="{{ asset('assets/img/produk.png') }}" alt="Default" loading="lazy">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Swiper Thumbnail (Kecil) -->
                @if ($allImages->count() > 1)
                    <div class="swiper swiper-thumbs">
                        <div class="swiper-wrapper">
                            @foreach ($allImages as $img)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/' . $img->path) }}" alt="Thumb">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Bagian Kanan: Detail & Info Produk --}}
            <div class="col-lg-6" data-aos="fade-left">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    @if ($produk->category)
                        <span class="badge bg-label-primary px-3 py-2 rounded-pill">{{ $produk->category->name }}</span>
                    @endif
                    @if ($produk->brand)
                        <span class="badge bg-label-secondary px-3 py-2 rounded-pill">{{ $produk->brand->name }}</span>
                    @endif
                </div>

                <h1 class="product-title fw-bolder mb-3">{{ $produk->name_product }}</h1>

                {{-- Harga --}}
                <div class="mb-4 bg-light p-3 rounded-3 border">
                    @if ($produk->harga_diskon)
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-danger">Diskon</span>
                            <span
                                class="text-muted text-decoration-line-through fs-6">{{ $produk->harga_formatted }}</span>
                        </div>
                        <div class="product-price fw-bolder">Rp {{ number_format($produk->harga_diskon, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="product-price fw-bolder" id="display-harga">{{ $produk->harga_formatted }}</div>
                    @endif
                </div>


                @if ($produk->variants && $produk->variants->count() > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">Pilih Varian:</h6>
                        <div class="d-flex flex-wrap gap-2" id="variant-container">
                            @foreach ($produk->variants as $index => $variant)
                                @php
                                    // Ambil gambar varian atau fallback ke gambar utama
                                    $imgVar = $variant->img_variant ? asset('storage/' . $variant->img_variant) : null;
                                @endphp
                                <button type="button" class="variant-btn {{ $index === 0 ? 'active' : '' }}"
                                    data-harga="Rp {{ number_format($variant->harga_jual, 0, ',', '.') }}"
                                    data-img="{{ $imgVar }}"
                                    data-stok="{{ $produk->stocks->where('product_variant_id', $variant->id)->sum('qty') ?? 0 }}"
                                    title="SKU: {{ $variant->sku }}">
                                    {{ $variant->options->pluck('value')->join(' / ') }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- Status Stok & Garansi --}}
                <div class="d-flex flex-column gap-2 mb-4 p-3 border rounded-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-package fs-5 me-2 text-primary"></i>
                        <span class="fw-medium me-2">Status Stok:</span>
                        @if ($produk->qty > 0)
                            <span class="badge bg-success">Tersedia ({{ $produk->qty }}
                                {{ $produk->unit->singkat ?? '' }})</span>
                        @else
                            <span class="badge bg-danger">Habis</span>
                        @endif
                    </div>
                    @if ($produk->garansi)
                        <div class="d-flex align-items-center">
                            <i class="bx bx-shield-check fs-5 me-2 text-primary"></i>
                            <span class="fw-medium me-2">Garansi:</span>
                            <span class="text-dark">{{ $produk->garansi->name }}
                                {{ $produk->garansi->formatted_duration }}</span>
                        </div>
                    @endif
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-grid gap-2">
                    @if ($produk->qty > 0)
                        <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                            target="_blank"
                            class="btn btn-wa btn-lg fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                            <i class="bx bxl-whatsapp fs-4"></i> Pesan via WhatsApp
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary btn-lg fw-bold" disabled>Stok Habis</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Row Bawah: Tabs Deskripsi & Spesifikasi --}}
        <div class="row mt-5" data-aos="fade-up">
            <div class="col-12">
                <div class="card shadow-none border">
                    <div class="card-header bg-transparent border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold px-4" data-bs-toggle="tab" data-bs-target="#desc-tab"
                                    role="tab">Deskripsi</button>
                            </li>
                            @if ($produk->specification)
                                <li class="nav-item">
                                    <button class="nav-link fw-bold px-4" data-bs-toggle="tab" data-bs-target="#spec-tab"
                                        role="tab">Spesifikasi</button>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="tab-content p-0">
                            {{-- Tab Deskripsi --}}
                            <div class="tab-pane fade show active product-description" id="desc-tab" role="tabpanel">
                                {!! $produk->description ?: '<p class="text-muted fst-italic">Tidak ada deskripsi untuk produk ini.</p>' !!}
                            </div>

                            {{-- Tab Spesifikasi --}}
                            @if ($produk->specification)
                                <div class="tab-pane fade" id="spec-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped spec-table mb-0">
                                            <tbody>
                                                @php $specs = json_decode($produk->specification, true); @endphp
                                                @if (is_array($specs))
                                                    @foreach ($specs as $spec)
                                                        <tr>
                                                            <th>{{ $spec['key'] ?? '-' }}</th>
                                                            <td>{{ $spec['value'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Similar Products Section (Styling Disesuaikan) --}}
    @if ($produkSerupa->isNotEmpty())
        <div class="py-5 bg-light mt-5">
            <div class="container-market">
                <h3 class="fw-bold mb-4 border-start border-primary border-4 ps-3" data-aos="fade-right">Produk Serupa
                </h3>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 g-lg-4" data-aos="fade-up"
                    data-aos-delay="100">
                    @foreach ($produkSerupa as $serupa)
                        <div class="col product-col">
                            <div class="card product-card h-100 border-0 shadow-sm transition-all"
                                style="border-radius: 12px; transition: transform 0.2s;">
                                <div class="position-relative overflow-hidden" style="border-radius: 12px 12px 0 0;">
                                    <a href="{{ route('market.produk.detail', ['slug' => $serupa->slug]) }}">
                                        <img src="{{ $serupa->primaryImage
                                            ? asset('storage/' . $serupa->primaryImage->path)
                                            : ($serupa->img_produk
                                                ? asset('storage/' . $serupa->img_produk)
                                                : asset('assets/img/produk.png')) }}"
                                            alt="{{ $serupa->name_product }}" loading="lazy" class="card-img-top w-100"
                                            style="height: 200px; object-fit: contain; background: #fff;">

                                        @if ($serupa->qty < 1)
                                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">Habis</span>
                                        @elseif($serupa->promotions->isNotEmpty() && ($promo = $serupa->promotions->first()))
                                            @if ($promo->type == 'percentage')
                                                <span
                                                    class="badge bg-danger position-absolute top-0 end-0 m-2">{{ (int) $promo->nilai_diskon }}%
                                                    OFF</span>
                                            @else
                                                <span class="badge bg-info position-absolute top-0 end-0 m-2">PROMO</span>
                                            @endif
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body p-3 d-flex flex-column">
                                    <a href="{{ route('market.produk.detail', ['slug' => $serupa->slug]) }}"
                                        class="text-decoration-none text-dark mb-auto">
                                        <h6 class="fw-semibold mb-2 text-truncate-2"
                                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $serupa->name_product }}</h6>
                                    </a>
                                    <div class="mt-2">
                                        @if ($serupa->harga_diskon)
                                            <div class="text-muted text-decoration-line-through"
                                                style="font-size: 0.8rem;">{{ $serupa->harga_formatted }}</div>
                                            <div class="fw-bold text-danger">Rp
                                                {{ number_format($serupa->harga_diskon, 0, ',', '.') }}</div>
                                        @else
                                            <div class="fw-bold text-dark">{{ $serupa->harga_formatted }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <x-market-footer></x-market-footer>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS Animation
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });

            // Initialize Swiper (jika ada class swiper-thumbs)
            if (document.querySelector('.swiper-thumbs')) {
                var swiperThumbs = new Swiper(".swiper-thumbs", {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        320: {
                            slidesPerView: 4
                        },
                        576: {
                            slidesPerView: 5
                        },
                        992: {
                            slidesPerView: 4
                        }
                    }
                });

                var swiperMain = new Swiper(".swiper-main", {
                    spaceBetween: 10,
                    effect: "fade", // Transisi fade yang elegan
                    thumbs: {
                        swiper: swiperThumbs,
                    },
                });
            } else if (document.querySelector('.swiper-main')) {
                // Fallback jika cuma ada 1 gambar (tanpa thumbs)
                var swiperMain = new Swiper(".swiper-main", {
                    spaceBetween: 10,
                });
            }

            // Simple Variant Button Toggle (Hanya untuk UI)
            // Logika Varian Dinamis
            const variantBtns = document.querySelectorAll('.variant-btn');
            const displayHarga = document.getElementById('display-harga');
            const swiperSlides = document.querySelectorAll('.swiper-main .swiper-slide img');

            variantBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // 1. Ubah tombol aktif
                    variantBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // 2. Update Harga
                    if (displayHarga) {
                        displayHarga.textContent = this.dataset.harga;
                    }

                    // 3. Update Gambar (jika varian punya gambar khusus)
                    const newImgSrc = this.dataset.img;
                    if (newImgSrc) {
                        // Jika Swiper tersedia
                        if (typeof swiperMain !== 'undefined') {
                            // Cek apakah gambar varian ini sudah ada di dalam slider
                            let foundIndex = -1;
                            swiperSlides.forEach((imgEl, i) => {
                                if (imgEl.src === newImgSrc) foundIndex = i;
                            });

                            if (foundIndex !== -1) {
                                // Jika gambar sudah ada di galeri, langsung geser (Slide) ke gambar tersebut
                                swiperMain.slideTo(foundIndex);
                            } else {
                                // Jika gambar tidak ada di galeri, ganti gambar slide yang sedang aktif secara instan
                                const activeSlideImg = document.querySelector(
                                    '.swiper-main .swiper-slide-active img');
                                if (activeSlideImg) activeSlideImg.src = newImgSrc;
                            }
                        } else {
                            // Fallback jika swiper gagal dimuat
                            const fallbackImg = document.getElementById('main-product-image');
                            if (fallbackImg) fallbackImg.src = newImgSrc;
                        }
                    }
                });
            });

            // Klik otomatis varian pertama saat halaman dimuat
            if (variantBtns.length > 0) {
                variantBtns[0].click();
            }
        });
    </script>
@endsection
