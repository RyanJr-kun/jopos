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
<div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3" id="product-grid">
    @forelse ($products as $produk)
        <div class="col product-col">
            <div class="card product-card product-card-compact h-100 overflow-hidden">
                <div class="product-card-img-container">
                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                        <img src="{{ $produk->primaryImage
                            ? Storage::url($produk->primaryImage->path)
                            : ($produk->img_produk
                                ? Storage::url($produk->img_produk)
                                : asset('assets/img/produk.png')) }}"
                            alt="{{ $produk->name_product }}" loading="lazy" class="card-img-top"
                            alt="{{ $produk->name_product }}">
                        {{-- Badge Promotion --}}
                        @if ($produk->stocks->sum('qty') < 1)
                            <div class="product-badge">
                                <span class="badge bg-danger badge-sm">Stok Habis</span>
                            </div>
                        @elseif($produk->promotions->isNotEmpty() && ($promo = $produk->promotions->first()))
                            <div class="product-badge">
                                @if ($promo->type == 'percentage')
                                    <span class="badge bg-danger badge-sm">{{ (int) $promo->nilai_diskon }}% OFF</span>
                                @else
                                    <span class="badge bg-info badge-sm">PROMO</span>
                                @endif
                            </div>
                        @endif
                    </a>
                    <div class="product-card-actions">
                        @if ($produk->stocks->sum('qty') > 0)
                            <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                                target="_blank" class="btn btn-blue btn-sm w-100">
                                <i class="bx bxl-whatsapp me-1"></i> Pesan via WA
                            </a>
                        @else
                            <button type="button" class="btn btn-blue btn-sm w-100">Stok Habis</button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-2">
                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                        class="text-decoration-none text-dark">
                        <p class="product-title-compact fw-semibold mb-1" title="{{ $produk->name_product }}">
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
