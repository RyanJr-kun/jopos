{{-- Daftar Produk — PerfectScrollbar vertical --}}
<div class="pos-product-scroll" id="pos-product-scroll">
    <div id="product-list">

        @forelse ($products as $produk)
            {{-- Wrapper untuk animasi + filter kategori --}}
            <div class="product-card-wrap" data-product-category-id="{{ $produk->category_id }}">
                <div class="product-card-pos" data-id="{{ $produk->id }}" data-name="{{ e($produk->name_product) }}"
                    data-harga="{{ $produk->harga_diskon ?? $produk->harga_jual }}"
                    data-harga-asli="{{ $produk->harga_jual }}"
                    data-img="{{ $produk->primaryImage ? Storage::url($produk->primaryImage->path) : asset('assets/img/produk.png') }}"
                    data-stok="{{ $produk->stocks->sum('qty') }}"
                    data-wajib-seri="{{ $produk->wajib_seri ? 'true' : 'false' }}"
                    data-pajak-id="{{ $produk->taxe_id }}" data-pajak-rate="{{ $produk->pajak->rate ?? 0 }}"
                    data-disabled="{{ $produk->stocks->sum('qty') < 1 ? 'true' : 'false' }}" role="button"
                    aria-label="Tambah {{ e($produk->name_product) }} ke keranjang"
                    tabindex="{{ $produk->stocks->sum('qty') < 1 ? '-1' : '0' }}">

                    {{-- Gambar --}}
                    <div class="product-img-wrap">
                        <img src="{{ $produk->primaryImage ? Storage::url($produk->primaryImage->path) : asset('assets/img/produk.png') }}"
                            alt="Gambar {{ e($produk->name_product) }}" loading="lazy"
                            class="w-100 h-100 object-cover">
                    </div>

                    {{-- Badge --}}
                    @if ($produk->stocks->sum('qty') < 1)
                        <div class="product-badge">
                            <span class="badge bg-label-danger">Stok Habis</span>
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

                    {{-- Info Teks --}}
                    <div class="product-info">
                        <p class="product-category">{{ $produk->category->name }}</p>
                        <p class="product-name">{{ $produk->name_product }}</p>
                        <div class="d-flex flex-column">
                            @if ($produk->harga_diskon)
                                <span class="price-original">{{ $produk->harga_formatted }}</span>
                                <span class="price-main text-hover">
                                    {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="price-main text-hover">{{ $produk->harga_formatted }}</span>
                            @endif
                            <span class="stock-info">
                                {{ $produk->stocks->sum('qty') }} {{ $produk->unit->singkat }}
                            </span>
                        </div>
                        @if ($produk->stocks->sum('qty') < 1)
                            <p class="text-danger text-center fw-bold mb-0" style="font-size:.65rem; margin-top:4px;">
                                Stok Habis
                            </p>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1;" class="d-flex flex-column align-items-center justify-content-center py-5">
                <i class="bx bx-package fs-1 text-muted d-block mb-2" aria-hidden="true"></i>
                <p class="text-muted">Tidak ada produk yang tersedia.</p>
            </div>
        @endforelse

    </div>
</div>
