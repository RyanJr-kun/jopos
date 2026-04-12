{{-- Header Konten (Sorting & Info) --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- Menampilkan 0 jika tidak ada produk --}}
    <p class="mb-0 text-muted">Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari
        {{ $products->total() }} hasil</p>
    <div class="align-items-center">
        <label for="sort" class="form-label me-2 mb-0 text-nowrap">Urutkan:</label>
        {{-- Select sorting dipindahkan ke sini dari form utama agar tetap terlihat --}}
        <select name="sort" id="sort" class="form-select select2" data-placeholder="Urutkan">
            <option value="latest" @selected(request('sort') == 'latest' || !request('sort'))>Terbaru</option>
            <option value="harga_asc" @selected(request('sort') == 'harga_asc')>Harga Terendah</option>
            <option value="harga_desc" @selected(request('sort') == 'harga_desc')>Harga Tertinggi</option>
        </select>
    </div>
</div>

{{-- Grid Product --}}
<div class="row row-cols-2 row-cols-md-3 g-4">
    @forelse ($products as $produk)
        <div class="col">
            <div class="card product-card h-100 overflow-hidden">
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
                                    <span class="badge bg-label-danger">{{ (int) $promo->nilai_diskon }}% OFF</span>
                                @else
                                    <span class="badge bg-label-info">PROMO</span>
                                @endif
                            </div>
                        @endif

                    </a>
                    <div class="product-card-actions">
                        @if ($produk->qty > 0)
                            <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                                target="_blank" class="btn btn-dark w-100">
                                <i class="bx bxl-whatsapp me-1"></i> Pesan via WA
                            </a>
                        @else
                            <button type="button" class="btn btn-dark w-100">Stock Habis</button>
                        @endif
                    </div>
                </div>
                <div class="card-body py-2">
                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                        class="text-decoration-none text-dark text-hover-primary">
                        <p class="card-title fw-bold text-truncate" title="{{ $produk->name_product }}">
                            {{ $produk->name_product }}</p>
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

{{-- Paginasi --}}
<div class="d-flex justify-content-center mt-5">
    {{ $products->links() }}
</div>
