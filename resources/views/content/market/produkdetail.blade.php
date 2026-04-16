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
    <x-market-header :kategoris="$kategoris"></x-market-header>
    {{-- Breadcrumb --}}
    <div class="bg-white py-3">
        <div class="container-market">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('market.produk') }}"
                            class="text-decoration-none">Product</a>
                    </li>
                    @if ($produk->category)
                        <li class="breadcrumb-item"><a
                                href="{{ route('market.produk', ['kategori' => $produk->category->slug]) }}"
                                class="text-decoration-none">{{ $produk->category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $produk->name_product }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-market py-5">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="mb-3">
                    <img id="main-product-image"
                        src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                        class="img-fluid rounded-3 w-100" alt="{{ $produk->name_product }}"
                        style="max-height: 500px; object-fit: contain;">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if ($produk->category)
                        <a href="{{ route('market.produk', ['kategori' => $produk->category->slug]) }}"
                            class="badge bg-label-primary">{{ $produk->category->name }}</a>
                    @endif
                    @if ($produk->brand)
                        <span class="badge bg-label-primary">{{ $produk->brand->name }}</span>
                    @endif
                </div>
                <h2 class="fw-bold display-6">{{ $produk->name_product }}</h2>
                <div class="fs-3 my-3">
                    @if ($produk->harga_diskon)
                        <span class="text-muted text-decoration-line-through me-2">{{ $produk->harga_formatted }}</span>
                        <span
                            class="fw-bold text-danger">{{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</span>
                    @else
                        <span class="fw-bold text-dark">{{ $produk->harga_formatted }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center mb-2">
                    @if ($produk->qty > 0)
                        <span class="badge bg-label-success rounded-pill me-3"><i class="bx bx-check-circle me-1"></i>
                            Stock
                            Tersedia</span>
                    @else
                        <span class="badge bg-label-danger rounded-pill me-3"><i class="bx bx-x-circle me-1"></i> Stock
                            Habis</span>
                    @endif
                    <span class="text-muted small">( Tersedia: {{ $produk->qty ?? '-' }}
                        {{ $produk->unit->singkat ?? '-' }} )</span>
                </div>
                @if ($produk->garansi)
                    <div class="mt-2 d-flex align-items-center text-muted small">
                        <i class="bx bx-shield-check me-2"></i> Warrantie: {{ $produk->garansi->name }}
                        {{ $produk->garansi->formatted_duration }}
                    </div>
                @endif
                <div class="d-flex align-items-center gap-3 mt-4 pt-2 border-top">
                    @if ($produk->qty > 0)
                        <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                            target="_blank" class="btn btn-primary" {{ $produk->qty <= 0 ? 'disabled' : '' }}>
                            <i class="bx bx-whatsapp me-1"></i> Belanja Sekarang
                        </a>
                    @else
                        <button type="button" class="btn btn-dark">Stock Habis</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Product Description & Specifications --}}
        <div class="row mt-2 pt-4">
            <div class="col-12">
                <h3 class="fw-bold border-bottom pb-2 mb-3">Description Product</h3>
                <div class="product-description">
                    {!! $produk->description !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Similar Products Section --}}
    @if ($produkSerupa->isNotEmpty())
        <div class="album py-5 bg-light">
            <div class="container-market">
                <h2 class="text-center mb-5 fw-bold">Anda Mungkin Juga Suka</h2>
                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                    @foreach ($produkSerupa as $produk)
                        <div class="col product-col">
                            <div class="card product-card product-card-compact h-100 overflow-hidden">
                                <div class="product-card-img-container">
                                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                        <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                            loading="lazy" class="card-img-top" alt="{{ $produk->name_product }}">
                                        {{-- Badge Promotion --}}
                                        @if ($produk->qty < 1)
                                            <div class="product-badge">
                                                <span class="badge bg-label-danger">Stock Habis</span>
                                            </div>
                                        @elseif($produk->promotions->isNotEmpty() && ($promo = $produk->promotions->first()))
                                            <div class="product-badge">
                                                @if ($promo->type == 'percentage')
                                                    <span class="badge bg-label-danger">{{ (int) $promo->nilai_diskon }}%
                                                        OFF</span>
                                                @else
                                                    <span class="badge bg-label-info">PROMO</span>
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                    <div class="product-card-actions">
                                        @if ($produk->qty > 0)
                                            <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                                                target="_blank" class="btn btn-dark btn-sm w-100">
                                                <i class="bx bxl-whatsapp me-1"></i> Pesan via WA
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-dark btn-sm w-100">Stock Habis</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                        class="text-decoration-none text-dark">
                                        <p class="product-title-compact fw-semibold mb-1"
                                            title="{{ $produk->name_product }}">
                                            {{ $produk->name_product }}</p>
                                    </a>
                                    @if ($produk->harga_diskon)
                                        <div>
                                            <span class="text-muted text-decoration-line-through product-price-old">
                                                {{ $produk->harga_formatted }}</span>
                                            <span class="fw-bold product-price-current text-hover">
                                                {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div>
                                            <p class="fw-bold mb-0 product-price-current text-hover">
                                                {{ $produk->harga_formatted }}</p>
                                        </div>
                                    @endif
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
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
@endsection
