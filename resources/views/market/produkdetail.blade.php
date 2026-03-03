<x-marketLayout>
    {{-- Breadcrumb --}}
    <div class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('market.produk') }}"
                            class="text-decoration-none">Produk</a></li>
                    @if ($produk->kategori_produk)
                        <li class="breadcrumb-item"><a
                                href="{{ route('market.produk', ['kategori' => $produk->kategori_produk->slug]) }}"
                                class="text-decoration-none">{{ $produk->kategori_produk->nama }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_produk }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="mb-3">
                    <img id="main-product-image"
                        src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                        class="img-fluid rounded-3 w-100" alt="{{ $produk->nama_produk }}"
                        style="max-height: 500px; object-fit: contain;">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if ($produk->kategori_produk)
                        <a href="{{ route('market.produk', ['kategori' => $produk->kategori_produk->slug]) }}"
                            class="badge badge-primary">{{ $produk->kategori_produk->nama }}</a>
                    @endif
                    @if ($produk->brand)
                        <span class="badge badge-primary">{{ $produk->brand->nama }}</span>
                    @endif
                </div>
                <h2 class="fw-bold display-6">{{ $produk->nama_produk }}</h2>
                <div class="fs-3 my-3">
                    @if ($produk->harga_diskon)
                        <span
                            class="text-muted text-decoration-line-through me-2">{{ $produk->harga_formatted }}</span>
                        <span
                            class="fw-bold text-danger">{{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</span>
                    @else
                        <span class="fw-bold text-dark">{{ $produk->harga_formatted }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center mb-2">
                    @if ($produk->qty > 0)
                        <span class="badge badge-success rounded-pill me-3"><i class="bx bx-check-circle me-1"></i> Stok
                            Tersedia</span>
                    @else
                        <span class="badge badge-danger rounded-pill me-3"><i class="bx bx-x-circle me-1"></i> Stok
                            Habis</span>
                    @endif
                    <span class="text-muted small">( Tersedia: {{ $produk->qty ?? '-' }}
                        {{ $produk->unit->singkat ?? '-' }} )</span>
                </div>
                @if ($produk->garansi)
                    <div class="mt-2 d-flex align-items-center text-muted small">
                        <i class="bx bx-shield-check me-2"></i> Garansi: {{ $produk->garansi->nama }}
                        {{ $produk->garansi->formatted_duration }}
                    </div>
                @endif
                <div class="d-flex align-items-center gap-3 mt-4 pt-2 border-top">
                    @if ($produk->qty > 0)
                        <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->nama_produk }}"
                            target="_blank" class="btn btn-primary" {{ $produk->qty <= 0 ? 'disabled' : '' }}>
                            <i class="bx bx-whatsapp me-1"></i> Belanja Sekarang
                        </a>
                    @else
                        <button type="button" class="btn btn-dark">Stok Habis</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Product Description & Specifications --}}
        <div class="row mt-2 pt-4">
            <div class="col-12">
                <h3 class="fw-bold border-bottom pb-2 mb-3">Deskripsi Produk</h3>
                <div class="product-description">
                    {!! $produk->deskripsi !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Similar Products Section --}}
    @if ($produkSerupa->isNotEmpty())
        <div class="album py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5 fw-bold">Anda Mungkin Juga Suka</h2>
                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                    @foreach ($produkSerupa as $item)
                        <div class="col">
                            <div class="card product-card h-100 overflow-hidden">
                                <div class="product-card-img-container">
                                    <a href="{{ route('market.produk.detail', ['slug' => $item->slug]) }}">
                                        <img src="{{ $item->img_produk ? asset('storage/' . $item->img_produk) : asset('assets/img/produk.png') }}"
                                            loading="lazy" class="card-img-top" alt="{{ $item->nama_produk }}">
                                    </a>
                                    <div class="product-card-actions">
                                        @if ($item->qty > 0)
                                            <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->nama_produk }}"
                                                target="_blank" class="btn btn-dark w-100 "
                                                {{ $produk->qty <= 0 ? 'disabled' : '' }}>
                                                <i class="bx bx-whatsapp me-1"></i> Pesan via WA
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-dark w-100">Stok Habis</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body py-2">
                                    <a href="{{ route('market.produk.detail', ['slug' => $item->slug]) }}"
                                        class="text-decoration-none text-dark text-hover-primary">
                                        <p class="card-title fw-bold text-truncate mb-1"
                                            title="{{ $item->nama_produk }}">{{ $item->nama_produk }}</p>
                                    </a>
                                    @if ($item->harga_diskon)
                                        <div class="d-flex flex-wrap">
                                            <p class="small text-muted text-decoration-line-through mb-0 me-2">
                                                {{ $item->harga_formatted }}</p>
                                            <p class="small text-danger fw-bold mb-0">
                                                {{ 'Rp ' . number_format($item->harga_diskon, 0, ',', '.') }}</p>
                                        </div>
                                    @else
                                        <p class="small text-dark fw-bold mb-0">{{ $item->harga_formatted }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-marketLayout>
