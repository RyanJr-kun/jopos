@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Beranda - JO Computer')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')

    @yield('content')
    <x-marketHeader></x-marketHeader>

    {{-- Hero Section --}}
    @if ($mainBanners->isNotEmpty())
        <div class="container  py-4" data-aos="fade-up" data-aos-delay="200">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ($mainBanners as $key => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}"
                            class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"
                            aria-label="Slide {{ $key + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner rounded-2">
                    @foreach ($mainBanners as $banner)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-bs-interval="5000">
                            <a href="{{ $banner->url_tujuan ?? '#' }}">
                                <img src="{{ asset('storage/' . $banner->img_banner) }}" loading="eager"
                                    fetchpriority="high" decoding="async" class="d-block w-100 h-100"
                                    alt="{{ $banner->judul ?? 'Banner' }}">
                            </a>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    @else
        {{-- Tampilkan hero section default jika tidak ada banner --}}
        <div class="container col-xxl-8 px-4 py-5" data-aos="fade-up">
            <div class="row flex-lg-row-reverse align-items-center justify-content-center g-3">
                <div class="col-10 col-sm-8 col-lg-6">
                    <img src="https://images.pexels.com/photos/265087/pexels-photo-265087.jpeg"
                        class="d-block mx-lg-auto img-fluid rounded" alt="Bootstrap Themes" width="700" height="500"
                        loading="lazy">
                </div>
                <div class="col-lg-6">
                    <h1 class=" fw-bold lh-1 mb-3"data-aos="fade-up" data-aos-delay="200">Selamat Datang di JO-POS Market
                    </h1>
                    <p class="lead" data-aos="fade-up" data-aos-delay="400">Temukan berbagai macam komponen dan aksesoris
                        komputer berkualitas dengan harga terbaik. Kami menyediakan semua kebutuhan Anda, mulai dari
                        perakitan hingga upgrade.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="{{ route('market.produk') }}" type="button" class="btn btn-info btn-sm px-4 me-md-2">Lihat
                            Product</a>
                        <a href="https://wa.me/6281318000699" type="button"
                            class="btn btn-outline-secondary btn-sm px-4">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Browse by Category Section --}}
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up" data-aos-delay="200">
            <h4 class="fw-bolder mb-0">Jelajahi Kategori</h4>
        </div>
        <div class="position-relative category-scroll-wrapper">
            <div id="category-scroll-container" class="d-flex gap-3 overflow-hidden">
                @foreach ($kategoris as $kategori)
                    <div class="category-item" style="flex: 0 0 auto; width: 120px;" data-aos="fade-up"
                        data-aos-delay="400">
                        <a href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}"
                            class="text-decoration-none text-dark ">
                            <div class="card category-card bg-light border mx-2 mb-2 overflow-hidden">
                                <img src="{{ $kategori->img_kategori ? asset('storage/' . $kategori->img_kategori) : asset('assets/img/produk.png') }}"
                                    class="card-img-top" alt="{{ $kategori->name }}">
                            </div>
                            <div class="card-body p-2 text-center">
                                <h6 class="card-title fw-bold text-truncate mb-1" title="{{ $kategori->name }}">
                                    {{ $kategori->name }}</h6>
                                <p class="card-text text-muted small">{{ $kategori->products_count }} Product</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <button id="category-scroll-prev" class="btn btn-light rounded-circle category-scroll-btn prev"
                aria-label="Kategori Sebelumnya">
                <i class="bx bx-arrow-left"></i>
            </button>
            <button id="category-scroll-next" class="btn btn-light rounded-circle category-scroll-btn next"
                aria-label="Kategori Berikutnya">
                <i class="bx bx-arrow-right"></i>
            </button>
        </div>
    </div>

    {{-- Promotion Section --}}
    @if ($promoVertikalBanners->isNotEmpty())
        <div class="container px-4 py-5">
            <h2 class="text-center mb-4 fw-bold" data-aos="fade-up" data-aos-delay="300">Promotion Terbatas</h2>
            <div class="row g-4">
                <div class="col-lg-4 d-none d-lg-block">
                    <div id="promoVertikalCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-3">
                            @foreach ($promoVertikalBanners as $banner)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-bs-interval="7000"
                                    data-aos="fade-up" data-aos-delay="400">
                                    <a href="{{ $banner->url_tujuan ?? '#' }}">
                                        <img src="{{ asset('storage/' . $banner->img_banner) }}" loading="lazy"
                                            width="120px" height="720px" class="d-block w-100"
                                            alt="{{ $banner->judul ?? 'Promotion' }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom untuk 4 produk promo --}}
                <div class="col-lg-8">
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-3 g-3">
                        {{-- Loop untuk produk promo --}}
                        @forelse ($produkPromotion as $produk)
                            <div class="col" data-aos="fade-up" data-aos-delay="600">
                                <div class="card product-card overflow-hidden">
                                    <div class="product-card-img-container">
                                        <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                            <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                                loading="lazy" class="card-img-top" alt="{{ $produk->name_produk }}">
                                            {{-- Badge Promotion --}}
                                            @if ($produk->qty < 1)
                                                <div class="product-badge">
                                                    <span class="badge badge-danger">Stock Habis</span>
                                                </div>
                                            @elseif($produk->promotions->isNotEmpty() && ($promo = $produk->promotions->first()))
                                                <div class="product-badge">
                                                    @if ($promo->type == 'percentage')
                                                        <span class="badge badge-danger">{{ (int) $promo->nilai_diskon }}%
                                                            OFF</span>
                                                    @else
                                                        <span class="badge badge-info">PROMO</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </a>
                                        <div class="product-card-actions">
                                            @if ($produk->qty > 0)
                                                <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_produk }}"
                                                    target="_blank" class="btn btn-dark w-100">
                                                    <i class="bx bx-whatsapp me-1"></i> Pesan via WA
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-dark w-100">Stock Habis</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body py-2">
                                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                        class="text-decoration-none text-dark text-hover-primary">
                                        <p class="card-title fw-bold text-truncate" title="{{ $produk->name_produk }}">
                                            {{ $produk->name_produk }}</p>
                                    </a>
                                    @if ($produk->harga_diskon)
                                        <div class="d-md-flex">
                                            <p class="text-sm text-muted text-decoration-line-through mb-0">
                                                {{ $produk->harga_formatted }}</p>
                                            <p class="text-sm text-dark mb-0 ms-md-2">
                                                {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-dark mb-0">{{ $produk->harga_formatted }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12" data-aos="fade-up" data-aos-delay="300">
                                <p class="text-muted text-center">Saat ini belum ada produk promo.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Bestseller Section (Full Container) --}}
    @if ($bestsellerBanners->isNotEmpty())
        <div class="container px-4 py-5">
            <h2 class="text-center mb-4 fw-bold" data-aos="fade-up" data-aos-delay="200">Product Terlaris</h2>
            <div class="row">
                <div class="col-12">
                    <div id="bestsellerCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach ($bestsellerBanners as $key => $banner)
                                <button type="button" data-bs-target="#bestsellerCarousel"
                                    data-bs-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $key + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner rounded-3 shadow-sm" data-aos="fade-up" data-aos-delay="400">
                            @foreach ($bestsellerBanners as $banner)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <a href="{{ $banner->url_tujuan ?? '#' }}">
                                        <img src="{{ asset('storage/' . $banner->img_banner) }}" loading="lazy"
                                            class="d-block w-100 h-100"
                                            alt="{{ $banner->judul ?? 'Bestseller Banner' }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#bestsellerCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bestsellerCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
            {{-- Loop untuk 6 produk terlaris --}}
            @if ($produkTerlaris->isNotEmpty())
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 mt-4">
                    @foreach ($produkTerlaris as $produk)
                        <div class="col" data-aos="fade-up" data-aos-delay="300">
                            <div class="card product-card overflow-hidden">
                                <div class="product-card-img-container">
                                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                        <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                            loading="lazy" class="card-img-top" alt="{{ $produk->name_produk }}">
                                        {{-- Badge Populer --}}
                                        <div class="product-badge">
                                            <span class="badge badge-warning">Populer</span>
                                        </div>
                                    </a>
                                    <div class="product-card-actions">
                                        @if ($produk->qty > 0)
                                            <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_produk }}"
                                                target="_blank" class="btn btn-dark w-100">
                                                <i class="bx bx-whatsapp me-1"></i> Pesan via WA
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-dark w-100">Stock Habis</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                    class="text-decoration-none text-dark text-hover-primary">
                                    <p class="card-title fw-bold text-truncate" title="{{ $produk->name_produk }}">
                                        {{ $produk->name_produk }}</p>
                                </a>
                                @if ($produk->harga_diskon)
                                    <div class="d-md-flex">
                                        <p class="text-sm text-muted text-decoration-line-through mb-0">
                                            {{ $produk->harga_formatted }}</p>
                                        <p class="text-sm text-dark mb-0 ms-md-2">
                                            {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</p>
                                    </div>
                                @else
                                    <p class="text-sm text-dark mb-0">{{ $produk->harga_formatted }}</p>
                                @endif
                                <small class="text-muted">Terjual: {{ $produk->total_terjual }}
                                    {{ $produk->unit?->singkat }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endif

    {{-- Featured Products Section --}}
    <div class="album py-5 bg-light" data-aos="fade-up">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold" data-aos="fade-up" data-aos-delay="200">Product Unggulan</h2>
            <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                {{-- Loop untuk produk unggulan --}}
                @foreach ($products as $produk)
                    <div class="col" data-aos="fade-up" data-aos-delay="400">
                        <div class="card product-card overflow-hidden">
                            <div class="product-card-img-container">
                                <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                    <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                        loading="lazy" class="card-img-top" alt="{{ $produk->name_produk }}">
                                    {{-- Badge Promotion --}}
                                    @if ($produk->qty < 1)
                                        <div class="product-badge">
                                            <span class="badge badge-danger">Stock Habis</span>
                                        </div>
                                    @elseif($produk->promotions->isNotEmpty() && ($promo = $produk->promotions->first()))
                                        <div class="product-badge">
                                            @if ($promo->type == 'percentage')
                                                <span class="badge badge-danger">{{ (int) $promo->nilai_diskon }}%
                                                    OFF</span>
                                            @else
                                                <span class="badge badge-info">PROMO</span>
                                            @endif
                                        </div>
                                    @endif
                                </a>
                                <div class="product-card-actions">
                                    @if ($produk->qty > 0)
                                        <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_produk }}"
                                            target="_blank" class="btn btn-dark w-100">
                                            <i class="bx bx-whatsapp me-1"></i> Pesan via WA
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-dark w-100">Stock Habis</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                class="text-decoration-none text-dark text-hover-primary">
                                <p class="card-title fw-bold text-truncate" title="{{ $produk->name_produk }}">
                                    {{ $produk->name_produk }}</p>
                            </a>
                            @if ($produk->harga_diskon)
                                <div class="d-md-flex">
                                    <p class="text-sm text-muted text-decoration-line-through mb-0">
                                        {{ $produk->harga_formatted }}</p>
                                    <p class="text-sm text-dark mb-md-0 ms-md-2">
                                        {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</p>
                                </div>
                            @else
                                <p class="text-sm text-dark  mb-0">{{ $produk->harga_formatted }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
                {{-- Akhir loop produk --}}
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('market.produk') }}" class="btn btn-outline-dark">Lihat Semua Product</a>
            </div>
        </div>
    </div>

    {{-- Features Section --}}
    <div class="container px-4 py-5">
        <h2 class="pb-2 border-bottom text-center mb-5" data-aos="fade-up" data-aos-delay="200">Kenapa Memilih Kami?</h2>
        <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
            <div class="feature col text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-info bg-gradient fs-2 mb-3 rounded-3"
                    style="width: 3rem; height: 3rem;"><i class="bx bx-collection text-white"></i></div>
                <h3 class="fs-4">Product Lengkap</h3>
                <p>Kami menyediakan berbagai macam produk dari brand tername untuk memenuhi semua kebutuhan Anda.</p>
            </div>
            <div class="feature col text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-info bg-gradient fs-2 mb-3 rounded-3"
                    style="width: 3rem; height: 3rem;"><i class="bx bx-patch-check text-white"></i></div>
                <h3 class="fs-4">Kualitas Terjamin</h3>
                <p>Semua produk yang kami jual adalah original dan bergaransi resmi, menjamin kualitas terbaik.</p>
            </div>
            <div class="feature col text-center" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-info bg-gradient fs-2 mb-3 rounded-3"
                    style="width: 3rem; height: 3rem;"><i class="bx bx-truck text-white"></i></div>
                <h3 class="fs-4">Pengiriman Cepat</h3>
                <p>Nikmati layanan pengiriman yang cepat dan aman ke seluruh penjuru Indonesia.</p>
            </div>
        </div>
    </div>
    <x-marketFooter></x-marketFooter>
@endsection


@section('page-script')
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('category-scroll-container');
            const prevBtn = document.getElementById('category-scroll-prev');
            const nextBtn = document.getElementById('category-scroll-next');

            // Atur transisi scroll pada kontainer kategori untuk animasi yang halus
            container.style.scrollBehavior = 'smooth';

            // Scroll sejauh lebar satu item kategori
            const scrollAmount = () => {
                const firstItem = container.querySelector('.category-item');
                // Lebar item + gap
                return firstItem ? firstItem.offsetWidth + parseInt(getComputedStyle(container).gap) : 216;
            };

            let autoScrollInterval;

            const startAutoScroll = () => {
                autoScrollInterval = setInterval(() => {
                    // Jika sudah di ujung, kembali ke awal
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 1) {
                        container.scrollLeft = 0;
                    } else {
                        container.scrollLeft += scrollAmount();
                    }
                }, 3000); // Geser setiap 3 detik
            };

            const stopAutoScroll = () => clearInterval(autoScrollInterval);

            prevBtn.addEventListener('click', () => container.scrollLeft -= scrollAmount());
            nextBtn.addEventListener('click', () => container.scrollLeft += scrollAmount());

            // Mulai auto-scroll saat halaman dimuat
            startAutoScroll();
            // Hentikan auto-scroll saat mouse masuk ke area, dan mulai lagi saat keluar
            container.parentElement.addEventListener('mouseenter', stopAutoScroll);
            container.parentElement.addEventListener('mouseleave', startAutoScroll);
        });
    </script>
@endsection
