{{-- Daftar Produk — PerfectScrollbar vertical --}}
<div class="pos-product-scroll" id="pos-product-scroll">
    <div id="product-list">

        @forelse ($products as $produk)
            @php
                $hargaDiskon = $produk->harga_diskon;
                $stokSimple = $produk->stocks_sum_qty ?? 0;
                $hasVariant = $produk->variants->isNotEmpty();
                $imgUrl =
                    $produk->primaryImage && $produk->primaryImage->path
                        ? Storage::url($produk->primaryImage->path)
                        : asset('assets/img/produk.png');
                $promoAktif = $produk->promotions->first(); // sudah eager-load

                // JSON variants dihitung sekali
                $variantsJson = $hasVariant
                    ? json_encode(
                        $produk->variants->map(
                            fn($v) => [
                                'id' => $v->id,
                                'name' => $v->options->pluck('value')->implode(' / ') ?: 'SKU: ' . $v->sku,
                                'stok' => $v->stocks->sum('qty'),
                            ],
                        ),
                    )
                    : '[]';

                // Stok total = simple + semua varian (jika ada)
                $stokTotal = $hasVariant ? $produk->variants->sum(fn($v) => $v->stocks->sum('qty')) : $stokSimple;
            @endphp

            <div class="product-card-wrap" data-product-category-id="{{ $produk->category_id }}">
                <div class="product-card-pos" data-id="{{ $produk->id }}" data-name="{{ e($produk->name_product) }}"
                    data-harga="{{ $hargaDiskon ?? $produk->harga_jual }}" data-harga-asli="{{ $produk->harga_jual }}"
                    data-img="{{ $imgUrl }}" data-stok="{{ $stokTotal }}"
                    data-wajib-seri="{{ $produk->wajib_seri ? 'true' : 'false' }}"
                    data-pajak-id="{{ $produk->taxe_id }}" data-pajak-rate="{{ $produk->pajak->rate ?? 0 }}"
                    data-has-variant="{{ $hasVariant ? 'true' : 'false' }}" data-variants="{{ $variantsJson }}"
                    data-disabled="{{ $stokTotal < 1 ? 'true' : 'false' }}" role="button">

                    {{-- Gambar — $imgUrl dipakai ulang, tidak hitung ulang --}}
                    <div class="product-img-wrap">
                        <img src="{{ $imgUrl }}" alt="Gambar {{ e($produk->name_product) }}" loading="lazy"
                            class="w-100 h-100 object-cover">
                    </div>

                    {{-- Badge --}}
                    @if ($stokTotal < 1)
                        <div class="product-badge">
                            <span class="badge bg-label-danger">Stok Habis</span>
                        </div>
                    @elseif ($promoAktif)
                        <div class="product-badge">
                            @if ($promoAktif->type == 'percentage')
                                <span class="badge bg-label-danger">{{ (int) $promoAktif->nilai_diskon }}% OFF</span>
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
                            @if ($hargaDiskon)
                                <span class="price-original">{{ $produk->harga_formatted }}</span>
                                <span class="price-main">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                            @else
                                <span class="price-main">{{ $produk->harga_formatted }}</span>
                            @endif
                            <span class="stock-info">
                                {{ $stokTotal }} {{ $produk->unit->singkat }}
                            </span>
                        </div>
                        @if ($stokTotal < 1)
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
