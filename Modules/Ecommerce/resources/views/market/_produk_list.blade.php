{{-- Header Konten (Sorting & Info) --}}
<div class="row d-flex justify-content-between align-items-center mb-3">
    <div class="col-md-8">
        <p class="mb-2 text-muted small">
            Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}
            dari {{ $products->total() }} hasil
        </p>
    </div>
    <div class="col-md-4">
        <div class="d-flex align-items-center">
            <label for="sort" class="form-label me-2 mb-0 text-nowrap small">Urutkan:</label>
            <select name="sort" id="sort" class="form-select select2" data-placeholder="Urutkan">
                <option value="nama_asc" @selected(request('sort') == 'nama_asc')>Nama A-Z</option>
                <option value="nama_desc" @selected(request('sort') == 'nama_desc')>Nama Z-A</option>
                <option value="latest" @selected(request('sort') == 'latest' || !request('sort'))>Produk Terbaru</option>
                <option value="terpopuler" @selected(request('sort') == 'terpopuler')>Terpopuler</option>
                <option value="harga_asc" @selected(request('sort') == 'harga_asc')>Harga Terendah</option>
                <option value="harga_desc" @selected(request('sort') == 'harga_desc')>Harga Tertinggi</option>
            </select>
        </div>
    </div>
</div>

{{-- Grid Product: 2 kolom mobile, 3 tablet, 5 desktop --}}
<div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3" id="product-grid">
    @forelse ($products as $produk)
        @php
            $hargaDiskon = $produk->harga_diskon;
            $stokQty = $produk->stocks->sum('qty') ?? 0;
            $promoAktif = $produk->promotions->first();
            $imgUrl =
                $produk->primaryImage && $produk->primaryImage->path
                    ? Storage::url($produk->primaryImage->path)
                    : asset('assets/img/produk.png');
        @endphp
        <div class="col product-col">
            <div class="card product-card-pos product-card-compact h-100 overflow-hidden">
                <div class="product-card-img-container">
                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                        <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $produk->name_product }}"
                            loading="lazy">
                        <div class="product-badge">
                            @if ($stokQty < 1)
                                <span class="badge bg-danger">Habis</span>
                            @elseif ($promoAktif)
                                @if ($promoAktif->type == 'percentage')
                                    <span class="badge bg-danger">{{ (int) $promoAktif->nilai_diskon }}%
                                        OFF</span>
                                @else
                                    <span class="badge bg-info">PROMO</span>
                                @endif
                            @else
                                <span class="badge bg-warning fw-bolder rounded-4">Baru</span>
                            @endif
                        </div>
                    </a>
                    <div class="product-card-actions d-flex gap-2 mt-2 align-items-center">
                        @if ($stokQty > 0)
                            {{-- <button type="button"
                                class="btn btn-sm btn-dark flex-grow-1 d-flex align-items-center justify-content-center">
                                <i class="bx bx-shopping-bag me-2 fs-6"></i> Add Cart
                            </button> --}}
                            <a href="https://wa.me/6281318000699?text={{ urlencode('Halo, saya ingin membeli produk ' . $produk->name_product) }}"
                                target="_blank" type="button"
                                class="btn btn-sm btn-success flex-grow-1 d-flex align-items-center justify-content-center rounded-2">
                                <i class="bx bx-cart me-2 fs-5"></i> Beli
                            </a>
                        @else
                            <button type="button"
                                class="btn btn-sm btn-danger text-muted flex-grow-1 d-flex align-items-center justify-content-center">
                                <i class="bx bx-minus-circle me-2 fs-6"></i> HABIS
                            </button>
                        @endif

                        {{-- <button type="button"
                            class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center rounded-2 px-1 "
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Wishlist">
                            <i class="bx bx-heart fs-5"></i>
                        </button> --}}
                    </div>
                </div>
                <div class="card-body p-2">
                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }} class"
                        class="text-decoration-none text-dark">
                        <p class="product-title-compact fw-semibold mb-1" title="{{ $produk->name_product }}">
                            {{ $produk->name_product }}</p>
                    </a>
                    @if ($hargaDiskon)
                        <div class="d-flex flex-column align-items-start">
                            <span class="text-muted text-decoration-line-through product-price-old">
                                {{ $produk->harga_formatted }}</span>
                            <span class="fw-bold product-price-current text-hover">
                                {{ 'Rp ' . number_format($hargaDiskon, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div>
                            <span class="fw-bold mb-0 product-price-current text-hover">
                                {{ $produk->harga_formatted }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center border rounded-3 py-5">
            <i class="bx bx-book-heart fs-1 text-muted mb-3"></i>
            <h4 class="fw-bold">Oops! Product tidak ditemukan.</h4>
            <p class="text-muted">Coba gunakan kata kunci atau filter yang berbeda.</p>
            <a href="{{ route('market.produk') }}" class="btn btn-info mt-2">Lihat Semua Product</a>
        </div>
    @endforelse
</div>

{{-- Sentinel untuk infinite scroll — JS IntersectionObserver akan mengawasi ini --}}
@if ($products->hasMorePages())
    <div id="infinite-scroll-sentinel" data-next-page="{{ $products->currentPage() + 1 }}"
        class="d-flex justify-content-center py-4">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
    </div>
@else
    @if ($products->total() > 0)
        <div class="text-center py-4">
            <p class="text-muted small mb-0"><i class="bx bx-check-circle me-1"></i>Semua produk telah ditampilkan</p>
        </div>
    @endif
@endif
