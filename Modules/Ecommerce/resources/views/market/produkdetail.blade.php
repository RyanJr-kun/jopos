@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', $produk->name_product . ' - JO Computer')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/swiper/swiper.scss')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-produk-detail.scss'])
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/swiper/swiper.js')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')
    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    {{-- Breadcrumb --}}
    <div class="bg-white py-2 border-bottom">
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

    <section id="Produk-detail" class="section-py bg-white">
        <div class="container-market">
            <div class="row g-4 g-lg-5">
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
                    <div class="product-gallery-wrapper">
                        <div class="swiper swiper-main" style="order:1">
                            <div class="swiper-wrapper">
                                @if ($allImages->count() > 0)
                                    @foreach ($allImages as $img)
                                        <div class="swiper-slide">
                                            <img src="{{ Storage::url($img->path) }}" alt="{{ $produk->name_product }}"
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
                        @if ($allImages->count() > 1)
                            <div class="swiper swiper-thumbs" style="order:2">
                                <div class="swiper-wrapper">
                                    @foreach ($allImages as $img)
                                        <div class="swiper-slide">
                                            <img src="{{ Storage::url($img->path) }}" alt="Thumb">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>{{-- end .product-gallery-wrapper --}}
                </div>

                <div class="col-lg-6 " data-aos="fade-left">
                    <div class="d-flex flex-wrap align-items-center gap-2 my-3">
                        @if ($produk->category)
                            <span class="badge bg-label-blue px-3 py-2">{{ $produk->category->name }}</span>
                        @endif
                        @if ($produk->brand)
                            <span class="badge bg-label-danger px-3 py-2">{{ $produk->brand->name }}</span>
                        @endif
                    </div>

                    <h3 class="text-product-title fw-bolder mb-3">{{ $produk->name_product }}</h3>

                    {{-- Harga --}}
                    <div class="mb-4">
                        @if ($produk->harga_diskon)
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-danger">Diskon</span>
                                <span
                                    class="text-muted text-decoration-line-through text-discount">{{ $produk->harga_formatted }}</span>
                            </div>
                            <div class="fw-bolder text-price">Rp {{ number_format($produk->harga_diskon, 0, ',', '.') }}
                            </div>
                        @else
                            <div class="fw-bolder text-price" id="display-harga">{{ $produk->harga_formatted }}</div>
                        @endif
                    </div>

                    @if ($produk->variants && $produk->variants->count() > 0)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Pilih Varian:</h6>
                            <div class="d-flex flex-wrap gap-2" id="variant-container">
                                @foreach ($produk->variants as $index => $variant)
                                    @php
                                        $imgVar = $variant->img_variant ? Storage::url($variant->img_variant) : null;
                                    @endphp
                                    <button type="button"
                                        class="variant-btn {{ $index === 0 ? 'active' : '' }} btn btn-outline-dark btn-sm"
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
                    <div class="d-flex flex-column gap-2 mb-4">
                        @if ($produk->garansi)
                            <div class="d-flex align-items-center">
                                <i class="bx bx-shield-check fs-5 me-2 text-primary"></i>
                                <span class="fw-medium me-2">Garansi:</span>
                                <span class="text-dark">{{ $produk->garansi->name }}
                                    {{ $produk->garansi->formatted_duration }}</span>
                            </div>
                        @endif
                        <div class="d-flex align-items-center">
                            @if ($produk->stocks->sum('qty') > 0)
                                <div class="d-flex rounded-pill align-items-center badge bg-label-success ">
                                    <i class="bx bx-package fs-5 me-2"></i>
                                    <span class="">In Stock</span>
                                </div>
                            @else
                                <div class="d-flex rounded-pill align-items-center badge bg-label-danger ">
                                    <i class="bx bx-package-x fs-5 me-2"></i>
                                    <span>Out of Stock</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        $stokTotal = $produk->stocks->sum('qty');
                        $stockReady = $stokTotal > 0;
                        $waMsg = urlencode('Halo, saya tertarik dengan produk: ' . $produk->name_product);
                        $waUrl = 'https://wa.me/6281318000699?text=' . $waMsg;
                    @endphp

                    <div class="product-action-section">

                        {{-- ── Stok Habis Banner ─────────────────────────────── --}}
                        @unless ($stockReady)
                            <div class="out-of-stock-banner mb-3">
                                <i class="bx bx-sad" aria-hidden="true"></i>
                                <div>
                                    <div class="fw-semibold">Stok sedang habis</div>
                                    <div style="font-size:12px;margin-top:1px;color:#9c2935">
                                        Hubungi kami untuk info ketersediaan
                                    </div>
                                </div>
                            </div>
                        @endunless

                        {{-- ── Qty Control ───────────────────────────────────── --}}
                        @if ($stockReady)
                            <div class="product-qty-row">
                                <span class="product-qty-label">Jumlah</span>
                                <div class="qty-ctrl">
                                    <button class="qty-ctrl__btn" id="btn-minus" onclick="changeQty(-1)"
                                        aria-label="Kurangi jumlah" disabled>
                                        <i class="bx bx-minus" aria-hidden="true"></i>
                                    </button>
                                    <div class="qty-ctrl__val" id="qty-val" aria-live="polite">1</div>
                                    <button class="qty-ctrl__btn" id="btn-plus" onclick="changeQty(1)"
                                        aria-label="Tambah jumlah">
                                        <i class="bx bx-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <span style="font-size:12px;color:#adb5bd" id="qty-info">
                                    Maks. {{ $stokTotal }} {{ $produk->unit->singkat ?? '' }}
                                </span>
                            </div>
                        @endif

                        <hr class="my-3" style="opacity:.1">

                        {{-- ── Primary CTA Buttons ───────────────────────────── --}}
                        <div class="d-grid gap-2 d-flex">

                            {{-- Tambah ke Keranjang --}}
                            @if ($stockReady)
                                <button type="button" class="btn-add-cart w-100" id="btn-add-cart"
                                    onclick="handleAddToCart(this)">
                                    <i class="bx bx-cart-add fs-5" aria-hidden="true"></i>
                                    <span id="cart-label">Tambah ke keranjang</span>
                                </button>
                            @else
                                <button type="button" class="btn-add-cart w-100" disabled
                                    style="background:#adb5bd;cursor:not-allowed;opacity:.7">
                                    <i class="bx bx-cart-add fs-5" aria-hidden="true"></i>
                                    Tambah ke keranjang
                                </button>
                            @endif

                            {{-- Beli Sekarang (Checkout) --}}
                            @if ($stockReady)
                                <button type="button" class="btn-checkout w-100" onclick="handleCheckout()">
                                    <i class="bx bx-zap fs-5" aria-hidden="true"></i>
                                    Beli sekarang
                                </button>
                            @else
                                <button type="button" class="btn-checkout w-100" disabled
                                    style="background:#adb5bd;cursor:not-allowed;opacity:.7">
                                    <i class="bx bx-zap fs-5" aria-hidden="true"></i>
                                    Beli sekarang
                                </button>
                            @endif

                        </div>
                        <hr class="my-3" style="opacity:.1">

                        {{-- ── Secondary Icon Actions ─────────────────────────── --}}
                        <div class="d-flex justify-content-center flex-wrap">

                            {{-- Wishlist --}}
                            <button type="button" class="btn-icon-action" id="btn-wish" onclick="toggleWishlist(this)"
                                aria-label="Tambah ke wishlist" data-product-id="{{ $produk->id }}">
                                <i class="bx bx-heart" aria-hidden="true"></i>
                                <span>Wishlist</span>
                            </button>

                            {{-- Bagikan --}}
                            <button type="button" class="btn-icon-action mx-3" onclick="shareProduct(this)"
                                aria-label="Bagikan produk ini">
                                <i class="bx bx-share-alt" aria-hidden="true"></i>
                                <span>Bagikan</span>
                            </button>

                            {{-- Bandingkan --}}
                            <button type="button" class="btn-icon-action" onclick="addToCompare(this)"
                                aria-label="Bandingkan produk ini" data-product-id="{{ $produk->id }}"
                                data-product-name="{{ $produk->name_product }}">
                                <i class="bx bx-transfer-alt" aria-hidden="true"></i>
                                <span>Bandingkan</span>
                            </button>

                        </div>
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
                                    <button class="nav-link active fw-bold px-4" data-bs-toggle="tab"
                                        data-bs-target="#desc-tab" role="tab">Deskripsi</button>
                                </li>
                                @if ($produk->specification)
                                    <li class="nav-item">
                                        <button class="nav-link fw-bold px-4" data-bs-toggle="tab"
                                            data-bs-target="#spec-tab" role="tab">Spesifikasi</button>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body p-4 p-lg-5">
                            <div class="tab-content p-0">
                                {{-- Tab Deskripsi --}}
                                <div class="tab-pane fade show active product-description" id="desc-tab"
                                    role="tabpanel">
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
    </section>

    @if ($produkSerupa->isNotEmpty())
        <section id="produk-serupa" class="section-py bg-white">
            <div class="container-market">
                <div class="d-flex align-items-center justify-content-start mb-4">
                    <h3 class="fw-bold mb-4 ps-3 border-start border-primary border-4" data-aos="fade-right">Produk Serupa
                    </h3>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 g-lg-4" data-aos="fade-up"
                    data-aos-delay="100">
                    @foreach ($produkSerupa as $serupa)
                        <div class="col">
                            <div class="card product-card overflow-hidden h-100 d-flex flex-column">
                                <div class="product-card-img-container">
                                    <a href="{{ route('market.produk.detail', ['slug' => $serupa->slug]) }}">
                                        <img src="{{ $serupa->image_url }}" alt="{{ $serupa->name_product }}"
                                            loading="eager" class="card-img-top">

                                        @if ($serupa->stocks->sum('qty') < 1)
                                            <div class="product-badge">
                                                <span class="badge bg-danger fw-bold rounded-4">Habis</span>
                                            </div>
                                        @elseif($serupa->active_promotion)
                                            @php $promo = $serupa->active_promotion @endphp
                                            <div class="product-badge">
                                                @if ($promo->type == 'percentage')
                                                    <span class="badge bg-danger">{{ (int) $promo->nilai_diskon }}%
                                                        OFF</span>
                                                @else
                                                    <span class="badge bg-info">PROMO</span>
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                    <div class="product-card-actions d-flex gap-2 mt-2 align-items-center">
                                        @if ($produk->stocks->sum('qty') > 0)
                                            <button type="button"
                                                class="btn btn-sm btn-dark flex-grow-1 d-flex align-items-center justify-content-center rounded-2">
                                                <i class="bx bx-shopping-bag me-2 fs-6"></i> Add Cart
                                            </button>
                                        @else
                                            <button type="button"
                                                class="btn btn-sm btn-danger text-muted flex-grow-1 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-minus-circle me-2 fs-6"></i> HABIS
                                            </button>
                                        @endif

                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center rounded-2 px-2 "
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Wishlist">
                                            <i class="bx bx-heart fs-5"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body border-top py-2">
                                    <a href="{{ route('market.produk.detail', ['slug' => $serupa->slug]) }}"
                                        class="text-decoration-none text-dark">
                                        <p class="product-title fw-bold" title="{{ $serupa->name_product }}">
                                            {{ $serupa->name_product }}</p>
                                    </a>
                                    @if ($serupa->harga_diskon)
                                        <div>
                                            <span class="text-muted text-decoration-line-through product-price-old">
                                                {{ $serupa->harga_formatted }}</span>
                                            <span class="fw-bold product-price-current text-hover">
                                                {{ 'Rp ' . number_format($serupa->harga_diskon, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div>
                                            <span class="fw-bold mb-0 product-price-current text-hover">
                                                {{ $serupa->harga_formatted }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Similar Products Section (Styling Disesuaikan) --}}


    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold" id="shareModalLabel">Bagikan Produk</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <p class="text-muted fs-7 mb-3">Pilih platform untuk membagikan produk ini</p>

                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <a href="https://wa.me/?text={{ urlencode('Cek produk ini: ' . $produk->name_product . ' di JO Computer. ' . url()->current()) }}"
                            target="_blank"
                            class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bx bxl-whatsapp fs-3"></i>
                        </a>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                            target="_blank"
                            class="social-icon social-facebook rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bx bxl-facebook icon-lg"></i>
                        </a>

                        <a href="https://twitter.com/intent/tweet?text={{ urlencode('Cek produk keren ini: ' . $produk->name_product) }}&url={{ urlencode(url()->current()) }}"
                            target="_blank"
                            class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bx bxl-twitter fs-3"></i>
                        </a>

                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode('Cek produk ini: ' . $produk->name_product) }}"
                            target="_blank"
                            class="btn btn-info text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bx bxl-telegram fs-3"></i>
                        </a>
                    </div>

                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="shareUrlInput" value="{{ url()->current() }}"
                            readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyShareUrl(this)">
                            <i class="bx bx-copy"></i> Salin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-market-footer></x-market-footer>
@endsection

@section('page-script')
    <script>
        (function() {
            // ── State ──────────────────────────────────────────────
            let qty = 1;
            const maxQty = {{ $stokTotal }};

            // ── Qty Control ───────────────────────────────────────
            window.changeQty = function(delta) {
                qty = Math.min(maxQty, Math.max(1, qty + delta));

                const valEl = document.getElementById('qty-val');
                const minBtn = document.getElementById('btn-minus');
                const maxBtn = document.getElementById('btn-plus');

                if (valEl) valEl.textContent = qty;
                if (minBtn) minBtn.disabled = qty <= 1;
                if (maxBtn) maxBtn.disabled = qty >= maxQty;
            };

            // ── Tambah Keranjang ──────────────────────────────────
            window.handleAddToCart = function(btn) {
                const label = document.getElementById('cart-label');

                // TODO: kirim AJAX ke route keranjang Anda
                // fetch('/keranjang', { method:'POST', body: JSON.stringify({ product_id: {{ $produk->id }}, qty }) ... })

                // Feedback visual
                btn.classList.add('added');
                if (label) label.textContent = '✓ Ditambahkan (' + qty + ')';
                btn.disabled = true;

                setTimeout(() => {
                    btn.classList.remove('added');
                    if (label) label.textContent = 'Tambah ke keranjang';
                    btn.disabled = false;
                }, 2500);
            };

            // ── Beli Sekarang ─────────────────────────────────────
            window.handleCheckout = function() {
                // TODO: arahkan ke halaman checkout dengan produk & qty
                // window.location.href = '/checkout?product={{ $produk->id }}&qty=' + qty;
                alert('Redirect ke checkout – qty: ' + qty);
            };

            // ── Wishlist Toggle ───────────────────────────────────
            window.toggleWishlist = function(btn) {
                const wished = btn.classList.toggle('wish-active');
                const icon = btn.querySelector('i');
                const label = btn.querySelector('span');

                if (icon) icon.className = wished ? 'bx bxs-heart' : 'bx bx-heart';
                if (label) label.textContent = wished ? 'Tersimpan' : 'Wishlist';

                // TODO: kirim AJAX ke route wishlist Anda
                // fetch('/wishlist/toggle', { method:'POST', body: JSON.stringify({ product_id: btn.dataset.productId }) ... })
            };

            // ── Share ─────────────────────────────────────────────
            window.shareProduct = function(btn) {
                const url = window.location.href;
                const title = '{{ $produk->name_product }}';
                const text = 'Cek produk ini di JO Computer!';

                // Deteksi jika browser mendukung Web Share API (Biasanya Mobile)
                if (navigator.share) {
                    navigator.share({
                        title: title,
                        text: text,
                        url: url
                    }).catch((error) => {
                        console.log('Error sharing:', error);
                        // Jika user cancel share, biarkan saja
                    });
                } else {
                    // Fallback untuk Desktop: Tampilkan Bootstrap Modal
                    const shareModalEl = document.getElementById('shareModal');
                    // Pastikan script bootstrap sudah ter-load di layout utama
                    const shareModal = new bootstrap.Modal(shareModalEl);
                    shareModal.show();
                }
            };

            // ── Fungsi Copy URL di dalam Modal ────────────────────
            window.copyShareUrl = function(btn) {
                const copyText = document.getElementById("shareUrlInput");

                // Proses salin teks
                navigator.clipboard.writeText(copyText.value).then(() => {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="bx bx-check"></i> Tersalin';
                    btn.classList.replace('btn-outline-primary', 'btn-success');
                    btn.classList.add('text-white');

                    // Kembalikan tombol seperti semula setelah 2 detik
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.replace('btn-success', 'btn-outline-primary');
                        btn.classList.remove('text-white');
                    }, 2000);
                });
            };

            // ── Bandingkan ────────────────────────────────────────
            window.addToCompare = function(btn) {
                const label = btn.querySelector('span');
                // TODO: simpan ke localStorage / sesi perbandingan
                // const compares = JSON.parse(localStorage.getItem('compares') || '[]');
                // if (!compares.includes(btn.dataset.productId)) compares.push(btn.dataset.productId);
                // localStorage.setItem('compares', JSON.stringify(compares));

                if (label) label.textContent = 'Ditambahkan';
                btn.classList.add('wish-active');
                setTimeout(() => {
                    if (label) label.textContent = 'Bandingkan';
                    btn.classList.remove('wish-active');
                }, 2000);
            };
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── AOS ────────────────────────────────────────────────────
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });

            // ── SWIPER GALLERY ─────────────────────────────────────────
            // Referensi global agar bisa diakses oleh handler varian di bawah
            let swiperMain = null;
            let swiperThumbs = null;

            const DESKTOP_BP = 992; // sama dengan lg Bootstrap

            /**
             * Apakah viewport saat ini termasuk ukuran desktop/laptop?
             */
            function isDesktop() {
                return window.innerWidth >= DESKTOP_BP;
            }

            /**
             * Hancurkan instance Swiper yang ada (untuk re-init saat resize)
             */
            function destroySwipers() {
                if (swiperThumbs) {
                    swiperThumbs.destroy(true, true);
                    swiperThumbs = null;
                }
                if (swiperMain) {
                    swiperMain.destroy(true, true);
                    swiperMain = null;
                }
            }

            /**
             * Setelah main swiper dirender, samakan tinggi thumbs container
             * (diperlukan oleh Swiper mode vertikal)
             */
            function syncThumbsHeight() {
                const mainEl = document.querySelector('.swiper-main');
                const thumbsEl = document.querySelector('.swiper-thumbs');
                if (!mainEl || !thumbsEl) return;

                if (isDesktop()) {
                    // Tinggi thumbs = tinggi main image (yang sudah 1:1 via CSS)
                    thumbsEl.style.height = mainEl.offsetHeight + 'px';
                    thumbsEl.style.width = ''; // biarkan CSS yang atur (84px)
                } else {
                    // Mobile: kembalikan ke auto agar CSS yang mengatur
                    thumbsEl.style.height = '';
                    thumbsEl.style.width = '';
                }
            }

            /**
             * Inisialisasi Swiper sesuai kondisi layar
             */
            function initGallerySwipers() {
                const mainEl = document.querySelector('.swiper-main');
                const thumbsEl = document.querySelector('.swiper-thumbs');

                if (!mainEl) return; // halaman tanpa galeri

                const desktop = isDesktop();

                // ── Thumbs: ada lebih dari 1 gambar ───────────────────
                if (thumbsEl) {
                    // Atur ulang order via style (desktop: thumbs kiri, main kanan)
                    if (desktop) {
                        thumbsEl.style.order = '1';
                        mainEl.style.order = '2';
                    } else {
                        thumbsEl.style.order = '2'; // di bawah
                        mainEl.style.order = '1'; // di atas
                    }

                    // Sync tinggi sebelum init (butuh setTimeout agar layout settle)
                    setTimeout(syncThumbsHeight, 0);

                    swiperThumbs = new Swiper('.swiper-thumbs', {
                        spaceBetween: 8,
                        direction: desktop ? 'vertical' : 'horizontal',
                        slidesPerView: desktop ? 'auto' : 4,
                        freeMode: true,
                        watchSlidesProgress: true,
                        // Breakpoint horizontal (mobile saja)
                        ...(desktop ? {} : {
                            breakpoints: {
                                576: {
                                    slidesPerView: 5
                                },
                                768: {
                                    slidesPerView: 6
                                },
                            }
                        }),
                    });

                    swiperMain = new Swiper('.swiper-main', {
                        spaceBetween: 10,
                        effect: 'fade',
                        thumbs: {
                            swiper: swiperThumbs
                        },
                    });

                } else {
                    // Hanya 1 gambar → tidak ada thumbs
                    swiperMain = new Swiper('.swiper-main', {
                        spaceBetween: 10,
                    });
                }
            }

            // Jalankan pertama kali
            initGallerySwipers();

            // ── Re-init saat resize melintas breakpoint ─────────────
            let lastDesktopState = isDesktop();
            let resizeTimer;

            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const nowDesktop = isDesktop();

                    if (nowDesktop !== lastDesktopState) {
                        // Pindah breakpoint → hancurkan & init ulang
                        lastDesktopState = nowDesktop;
                        destroySwipers();
                        initGallerySwipers();
                    } else if (nowDesktop) {
                        // Masih desktop, cukup sinkronkan tinggi
                        syncThumbsHeight();
                        if (swiperThumbs) swiperThumbs.update();
                    }
                }, 200);
            });

            // ── VARIANT BUTTON LOGIC ───────────────────────────────────
            const variantBtns = document.querySelectorAll('.variant-btn');
            const displayHarga = document.getElementById('display-harga');
            const swiperSlides = document.querySelectorAll('.swiper-main .swiper-slide img');

            variantBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // 1. Toggle tombol aktif
                    variantBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // 2. Update harga
                    if (displayHarga) {
                        displayHarga.textContent = this.dataset.harga;
                    }

                    // 3. Update gambar sesuai varian
                    const newImgSrc = this.dataset.img;
                    if (newImgSrc && swiperMain) {
                        let foundIndex = -1;
                        swiperSlides.forEach((imgEl, i) => {
                            if (imgEl.src === newImgSrc) foundIndex = i;
                        });

                        if (foundIndex !== -1) {
                            swiperMain.slideTo(foundIndex);
                        } else {
                            const activeSlideImg = document.querySelector(
                                '.swiper-main .swiper-slide-active img'
                            );
                            if (activeSlideImg) activeSlideImg.src = newImgSrc;
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
