@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Beranda - JO Computer')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    {{-- Perfect Scrollbar --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/css/perfect-scrollbar.min.css">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    {{-- Perfect Scrollbar --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/perfect-scrollbar.min.js"></script>
@endsection

@section('layoutContent')

    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    {{-- Breadcrumb --}}
    <div class="bg-white py-3">
        <div class="container-market">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" id="breadcrumb-ol">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    @if (request('kategori') || request('search'))
                        <li class="breadcrumb-item"><a href="{{ route('market.produk') }}"
                                class="text-decoration-none">Product</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            @php
                                $bcSlug = request('kategori');
                                $bcLabel = 'Filter';
                                if ($bcSlug) {
                                    foreach ($kategoris as $_p) {
                                        if ($_p->slug === $bcSlug) {
                                            $bcLabel = $_p->name;
                                            break;
                                        }
                                        foreach ($_p->children as $_c) {
                                            if ($_c->slug === $bcSlug) {
                                                $bcLabel = $_c->name;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            {{ $bcSlug ? $bcLabel : 'Pencarian: ' . request('search') }}
                        </li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Product</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         MOBILE FLOATING FILTER BUTTON (hanya tampil di < lg)
    ════════════════════════════════════════════════════════ --}}
    <button class="mobile-filter-fab d-lg-none" id="mobileFilterFab" data-bs-toggle="offcanvas"
        data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
        <i class="bx bx-slider-alt me-1"></i>
        <span>Filter</span>
        <span class="fab-badge d-none" id="fabBadge">0</span>
    </button>

    {{-- ════════════════════════════════════════════════════════
         OFFCANVAS FILTER — Mobile Only
    ════════════════════════════════════════════════════════ --}}
    <div class="offcanvas offcanvas-start filter-offcanvas" tabindex="-1" id="filterOffcanvas"
        aria-labelledby="filterOffcanvasLabel" style="width: 300px; max-width: 85vw;">
        <div class="offcanvas-header border-bottom pb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="filter-offcanvas-icon">
                    <i class="bx bx-slider-alt"></i>
                </div>
                <h6 class="offcanvas-title mb-0 fw-bold" id="filterOffcanvasLabel">Filter Produk</h6>
            </div>
            {{-- <div class="d-flex align-items-center gap-2">
                <a href="{{ route('market.produk') }}" class="btn btn-sm btn-outline-secondary reset-all-btn">Reset</a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div> --}}
        </div>
        <div class="offcanvas-body p-0">
            {{-- Offcanvas inner form — syncs with desktop #filter-form --}}

            {{-- ── Pencarian ───────────────────────────────────── --}}
            <div class="filter-section border-bottom px-3 py-3">
                <h6 class="filter-section-title mb-2">
                    <i class="bx bx-search me-1 text-primary"></i> Cari Produk
                </h6>
                <input type="text" id="mobile-search-input" class="form-control form-control-sm"
                    placeholder="Nama atau SKU..." value="{{ request('search') }}">
            </div>

            {{-- ── Kategori ────────────────────────────────────── --}}
            <div class="filter-section border-bottom px-3 py-3">
                <h6 class="filter-section-title mb-2">
                    <i class="bx bx-category me-1 text-primary"></i> Kategori
                </h6>
                <ul class="filter-cat-tree list-unstyled mb-0">
                    {{-- Opsi "Semua" --}}
                    <li class="filter-cat-node">
                        <div class="filter-cat-row {{ !request('kategori') ? 'is-selected' : '' }}" data-slug="">
                            <button type="button"
                                class="filter-cat-label js-mobile-cat {{ !request('kategori') ? 'fw-semibold' : '' }}"
                                data-slug="">
                                <span class="filter-cat-dot {{ !request('kategori') ? 'dot-active' : '' }}"></span>
                                Semua Kategori
                            </button>
                        </div>
                    </li>
                    @forelse ($kategoris as $parent)
                        @php
                            $parentActive = request('kategori') === $parent->slug;
                            $childActive = $parent->children->contains('slug', request('kategori'));
                            $isOpen = $parentActive || $childActive;
                        @endphp
                        <li class="filter-cat-node {{ $isOpen ? 'is-open' : '' }}">
                            <div class="filter-cat-row {{ $parentActive ? 'is-selected' : '' }}"
                                data-slug="{{ $parent->slug }}">
                                <button type="button"
                                    class="filter-cat-label js-mobile-cat {{ $parentActive ? 'fw-semibold' : '' }}"
                                    data-slug="{{ $parent->slug }}">
                                    <span class="filter-cat-dot {{ $parentActive ? 'dot-active' : '' }}"></span>
                                    {{ $parent->name }}
                                </button>
                                @if ($parent->children->isNotEmpty())
                                    <button type="button" class="filter-cat-toggle {{ $isOpen ? 'rotate' : '' }}"
                                        aria-label="Toggle sub kategori">
                                        <i class="bx bx-chevron-right"></i>
                                    </button>
                                @endif
                            </div>
                            @if ($parent->children->isNotEmpty())
                                <ul class="filter-subcat-list list-unstyled mb-0 {{ $isOpen ? 'is-open' : '' }}">
                                    @foreach ($parent->children as $child)
                                        @php $childSel = request('kategori') === $child->slug; @endphp
                                        <li>
                                            <button type="button"
                                                class="filter-subcat-btn js-mobile-cat {{ $childSel ? 'is-selected' : '' }}"
                                                data-slug="{{ $child->slug }}">
                                                <span class="filter-cat-dot {{ $childSel ? 'dot-active' : '' }}"></span>
                                                {{ $child->name }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @empty
                        <li class="text-muted small px-2 py-2">Tidak ada kategori</li>
                    @endforelse
                </ul>
            </div>

            {{-- ── Brand (Checkbox, PerfectScroll, max 6 visible) ── --}}
            @if (isset($brands) && $brands->isNotEmpty())
                <div class="filter-section px-3 py-3">
                    <h6 class="filter-section-title mb-2">
                        <i class="bx bx-buildings me-1 text-primary"></i> Brand
                        <span class="brand-selected-count d-none ms-1 badge bg-primary rounded-pill"
                            id="brandCountBadge">0</span>
                    </h6>
                    <div class="brand-scroll-container ps-container" id="brandScrollContainer">
                        <ul class="list-unstyled mb-0 brand-list">
                            @foreach ($brands as $brand)
                                <li class="brand-check-item">
                                    <label class="brand-check-label d-flex align-items-center gap-2">
                                        <input type="checkbox" name="brand[]" value="{{ $brand->id }}"
                                            class="form-check-input mobile-brand-check mt-0 flex-shrink-0"
                                            {{ in_array($brand->id, (array) request('brand', [])) ? 'checked' : '' }}>
                                        @if ($brand->logo)
                                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
                                                class="brand-logo-thumb" onerror="this.style.display='none'">
                                        @else
                                            <span
                                                class="brand-initial-badge">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                        @endif
                                        <span class="brand-name small">{{ $brand->name }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @if ($brands->count() > 6)
                        <p class="text-muted small mt-1 mb-0 ps-1">
                            <i class="bx bx-mouse me-1"></i>Scroll untuk lihat semua brand
                        </p>
                    @endif
                </div>
            @endif

        </div>

        {{-- Footer Offcanvas: tombol Terapkan --}}
        <div class="offcanvas-footer border-top px-3 py-3">
            <a href="{{ route('market.produk') }}" class="btn btn-outline-secondary w-100 mb-2 py-2">
                <i class="bx bx-refresh me-1"></i> Reset Filter
            </a>

            <button type="button" class="btn btn-primary w-100 apply-filter-btn py-2" id="applyMobileFilter"
                data-bs-dismiss="offcanvas">
                <i class="bx bx-check me-1"></i> Terapkan Filter
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <section id="productShowCase" class="section-py bg-white">
        <div class="container-market">
            <div class="row">

                {{-- ════════════════════════════════════════════════════
                     DESKTOP SIDEBAR FILTER (d-none d-lg-block)
                ════════════════════════════════════════════════════ --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="card shadow-none border mb-4">
                        <div class="card-body">
                            <form id="filter-form">
                                {{-- Filter Pencarian --}}
                                <div class="mb-4">
                                    <h6 class="filter-title">Cari Product</h6>
                                    <input type="text" name="search" id="search-input" class="form-control"
                                        placeholder="Nama atau SKU..." value="{{ request('search') }}">
                                </div>

                                {{-- Filter Kategori: Multilevel Dropdown --}}
                                <div class="mb-4">
                                    <div class="filter-cat-header">
                                        <h6 class="filter-title mb-0">Kategori</h6>
                                        @php
                                            $activeSlug = request('kategori');
                                            $activeKatLabel = null;
                                            foreach ($kategoris as $p) {
                                                if ($p->slug === $activeSlug) {
                                                    $activeKatLabel = $p->name;
                                                    break;
                                                }
                                                foreach ($p->children as $c) {
                                                    if ($c->slug === $activeSlug) {
                                                        $activeKatLabel = $c->name;
                                                        break 2;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if ($activeKatLabel)
                                            <span class="filter-active-badge">{{ $activeKatLabel }}</span>
                                        @endif
                                    </div>

                                    {{-- Hidden radio --}}
                                    <div style="display:none">
                                        @foreach ($kategoris as $parent)
                                            <input class="filter-change" type="radio" name="kategori"
                                                value="{{ $parent->slug }}" id="cat-{{ $parent->slug }}"
                                                @checked(request('kategori') == $parent->slug)>
                                            @foreach ($parent->children as $child)
                                                <input class="filter-change" type="radio" name="kategori"
                                                    value="{{ $child->slug }}" id="cat-{{ $child->slug }}"
                                                    @checked(request('kategori') == $child->slug)>
                                            @endforeach
                                        @endforeach
                                    </div>

                                    {{-- Multilevel Category Tree --}}
                                    <ul class="filter-cat-tree list-unstyled mb-0">
                                        @forelse ($kategoris as $parent)
                                            @php
                                                $parentActive = request('kategori') === $parent->slug;
                                                $childActive = $parent->children->contains('slug', request('kategori'));
                                                $isOpen = $parentActive || $childActive;
                                            @endphp
                                            <li class="filter-cat-node {{ $isOpen ? 'is-open' : '' }}">
                                                <div class="filter-cat-row {{ $parentActive ? 'is-selected' : '' }}"
                                                    data-slug="{{ $parent->slug }}">
                                                    <button type="button"
                                                        class="filter-cat-label js-cat-select {{ $parentActive ? 'fw-semibold' : '' }}"
                                                        data-slug="{{ $parent->slug }}">
                                                        <span
                                                            class="filter-cat-dot {{ $parentActive ? 'dot-active' : '' }}"></span>
                                                        {{ $parent->name }}
                                                    </button>
                                                    @if ($parent->children->isNotEmpty())
                                                        <button type="button"
                                                            class="filter-cat-toggle {{ $isOpen ? 'rotate' : '' }}"
                                                            aria-label="Toggle sub kategori">
                                                            <i class="bx bx-chevron-right"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                @if ($parent->children->isNotEmpty())
                                                    <ul
                                                        class="filter-subcat-list list-unstyled mb-0 {{ $isOpen ? 'is-open' : '' }}">
                                                        @foreach ($parent->children as $child)
                                                            @php $childSel = request('kategori') === $child->slug; @endphp
                                                            <li>
                                                                <button type="button"
                                                                    class="filter-subcat-btn js-cat-select {{ $childSel ? 'is-selected' : '' }}"
                                                                    data-slug="{{ $child->slug }}">
                                                                    <span
                                                                        class="filter-cat-dot {{ $childSel ? 'dot-active' : '' }}"></span>
                                                                    {{ $child->name }}
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="text-muted small px-2 py-2">Tidak ada kategori</li>
                                        @endforelse
                                    </ul>
                                </div>

                                {{-- Filter Brand (Desktop) --}}
                                @if (isset($brands) && $brands->isNotEmpty())
                                    <div class="mb-4">
                                        <h6 class="filter-title">Brand</h6>
                                        <div class="brand-scroll-container ps-container" id="desktopBrandScroll">
                                            <ul class="list-unstyled mb-0 brand-list">
                                                @foreach ($brands as $brand)
                                                    <li class="brand-check-item">
                                                        <label class="brand-check-label d-flex align-items-center gap-2">
                                                            <input type="checkbox" name="brand[]"
                                                                value="{{ $brand->id }}"
                                                                class="form-check-input filter-change mt-0 flex-shrink-0"
                                                                {{ in_array($brand->id, (array) request('brand', [])) ? 'checked' : '' }}>
                                                            @if ($brand->logo)
                                                                <img src="{{ asset('storage/' . $brand->logo) }}"
                                                                    alt="{{ $brand->name }}" class="brand-logo-thumb"
                                                                    onerror="this.style.display='none'">
                                                            @else
                                                                <span
                                                                    class="brand-initial-badge">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                                            @endif
                                                            <span class="brand-name small">{{ $brand->name }}</span>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @if ($brands->count() > 6)
                                            <p class="text-muted" style="font-size:11px; margin-top:4px; margin-bottom:0">
                                                <i class="bx bx-mouse me-1"></i>Scroll untuk lihat semua
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                {{-- Tombol Reset --}}
                                <div class="d-grid">
                                    <a href="{{ route('market.produk') }}" class="btn btn-outline-secondary btn-sm">Reset
                                        Filter</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════
                     PRODUCT GRID
                ════════════════════════════════════════════════════ --}}
                <div class="col-lg-9">
                    <div id="product-list-container">
                        @include('content.market._produk_list', ['products' => $products])
                    </div>
                </div>

            </div>
        </div>
    </section>

    <x-market-footer></x-market-footer>

@endsection

@section('page-script')
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>

    {{-- Select2 Init --}}
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10
                    });
                });
            } else {
                setTimeout(initSelect2, 100);
            }
        };
        initSelect2();
    </script>

    {{-- Perfect Scrollbar Init --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Init perfect scrollbar on brand scroll containers
            const brandContainers = document.querySelectorAll('.brand-scroll-container');
            brandContainers.forEach(function(el) {
                if (typeof PerfectScrollbar !== 'undefined') {
                    new PerfectScrollbar(el, {
                        wheelSpeed: 1,
                        wheelPropagation: false,
                        minScrollbarLength: 20,
                        suppressScrollX: true
                    });
                }
            });
        });
    </script>

    {{-- AJAX Filter + Infinite Scroll --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let debounceTimer;
            let isLoading = false;
            let scrollObserver = null;
            const productContainer = document.getElementById('product-list-container');

            // ── Breadcrumb updater ────────────────────────────────────
            function updateBreadcrumb() {
                const breadcrumbContainer = document.getElementById('breadcrumb-ol');
                if (!breadcrumbContainer) return;
                const params = new URLSearchParams(window.location.search);
                const searchQuery = params.get('search');
                const kategoriSlug = params.get('kategori');
                let kategoriNama = 'Filter';
                if (kategoriSlug) {
                    const kategoriInput = document.querySelector(`input[name="kategori"][value="${kategoriSlug}"]`);
                    if (kategoriInput) {
                        const btn = kategoriInput.closest('div')?.querySelector(
                            `.js-cat-select[data-slug="${kategoriSlug}"]`);
                        if (btn) kategoriNama = btn.textContent.trim();
                    }
                }
                let breadcrumbHtml =
                    `<li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>`;
                if (searchQuery || kategoriSlug) {
                    breadcrumbHtml +=
                        `<li class="breadcrumb-item"><a href="{{ route('market.produk') }}" class="text-decoration-none">Product</a></li>`;
                    breadcrumbHtml +=
                        `<li class="breadcrumb-item active" aria-current="page">${searchQuery ? `Pencarian: "${searchQuery}"` : kategoriNama}</li>`;
                } else {
                    breadcrumbHtml += `<li class="breadcrumb-item active" aria-current="page">Product</li>`;
                }
                breadcrumbContainer.innerHTML = breadcrumbHtml;
            }

            // ── Sentinel observer ─────────────────────────────────────
            function observeSentinel() {
                if (scrollObserver) scrollObserver.disconnect();
                const sentinel = document.getElementById('infinite-scroll-sentinel');
                if (!sentinel) return;
                scrollObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting && !isLoading) {
                            const nextPage = sentinel.dataset.nextPage;
                            if (nextPage) loadMoreProducts(parseInt(nextPage));
                        }
                    });
                }, {
                    rootMargin: '200px'
                });
                scrollObserver.observe(sentinel);
            }

            // ── Fetch (reset) ─────────────────────────────────────────
            function fetchProducts(page) {
                if (isLoading) return;
                isLoading = true;

                const form = document.getElementById('filter-form');
                // Ambil data dari form desktop (brand & kategori hidden)
                const formData = new FormData(form);

                // PERBAIKAN: Pastikan element #sort diambil dengan benar meski berada di partial _produk_list
                const sortEl = document.getElementById('sort');
                if (sortEl) {
                    formData.append('sort', sortEl.value);
                }

                // Ambil nilai search dari input yang tersedia
                const searchInput = document.getElementById('search-input');
                if (searchInput && !formData.has('search')) {
                    formData.append('search', searchInput.value);
                }

                const params = new URLSearchParams(formData);
                const url = `{{ route('market.produk') }}?${params.toString()}&page=${page}`;

                productContainer.style.opacity = '0.5';

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        productContainer.innerHTML = html;
                        productContainer.style.opacity = '1';

                        // Push state agar URL di browser berubah
                        window.history.pushState({
                            path: url
                        }, '', url);

                        // Re-inisialisasi komponen
                        updateBreadcrumb();
                        observeSentinel();

                        // PENTING: Karena Select2 #sort ada di dalam partial yang baru di-load, 
                        // kita harus inisialisasi ulang Select2-nya.
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#sort').select2({
                                width: '100%'
                            });
                        }

                        isLoading = false;
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        productContainer.style.opacity = '1';
                        isLoading = false;
                    });
            }
            // ── Load more (append) ────────────────────────────────────
            function loadMoreProducts(page) {
                if (isLoading) return;
                isLoading = true;

                const form = document.getElementById('filter-form');
                const formData = new FormData(form);
                const sortEl = productContainer.querySelector('#sort');
                if (sortEl) formData.append('sort', sortEl.value);

                const params = new URLSearchParams(formData);
                const url = `{{ route('market.produk') }}?${params.toString()}&page=${page}`;

                const sentinel = document.getElementById('infinite-scroll-sentinel');
                if (sentinel) {
                    sentinel.innerHTML =
                        '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Memuat...</span></div>';
                }

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        const newCards = tempDiv.querySelectorAll('.product-col');
                        const grid = productContainer.querySelector('#product-grid');

                        if (sentinel) sentinel.remove();

                        newCards.forEach(function(card) {
                            grid.appendChild(card);
                        });

                        const newSentinel = tempDiv.querySelector('#infinite-scroll-sentinel');
                        const endMessage = tempDiv.querySelector('.text-center.py-4');
                        if (newSentinel) {
                            grid.insertAdjacentElement('afterend', newSentinel);
                        } else if (endMessage) {
                            grid.insertAdjacentElement('afterend', endMessage);
                        }

                        const newInfo = tempDiv.querySelector('.text-muted.small');
                        const currentInfo = productContainer.querySelector('.text-muted.small');
                        if (newInfo && currentInfo) currentInfo.textContent = newInfo.textContent;

                        observeSentinel();
                        isLoading = false;
                    })
                    .catch(err => {
                        console.error('Error loading more:', err);
                        isLoading = false;
                    });
            }

            // ── Event Listeners ───────────────────────────────────────
            $(document).on('change', '#sort', function() {
                fetchProducts(1);
            });

            // Tetap pertahankan vanilla JS untuk filter lainnya
            document.addEventListener('change', function(event) {
                if (event.target.matches('.filter-change')) {
                    fetchProducts(1);
                }
            });

            // Initial
            updateBreadcrumb();
            observeSentinel();
        });
    </script>

    {{-- Desktop Category Tree --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filter-cat-toggle').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const node = this.closest('.filter-cat-node');
                    const subList = node.querySelector('.filter-subcat-list');
                    const isOpen = node.classList.contains('is-open');

                    document.querySelectorAll('.filter-cat-node.is-open').forEach(function(n) {
                        if (n !== node) {
                            n.classList.remove('is-open');
                            n.querySelector('.filter-cat-toggle')?.classList.remove(
                                'rotate');
                            n.querySelector('.filter-subcat-list')?.classList.remove(
                                'is-open');
                        }
                    });

                    node.classList.toggle('is-open', !isOpen);
                    this.classList.toggle('rotate', !isOpen);
                    if (subList) subList.classList.toggle('is-open', !isOpen);
                });
            });

            document.querySelectorAll('.js-cat-select').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const slug = this.dataset.slug;
                    const radio = document.getElementById('cat-' + slug);
                    if (!radio) return;

                    document.querySelectorAll('.filter-cat-row, .filter-subcat-btn').forEach(el =>
                        el.classList.remove('is-selected'));
                    document.querySelectorAll('.filter-cat-label').forEach(el => el.classList
                        .remove('fw-semibold'));
                    document.querySelectorAll('.filter-cat-dot').forEach(el => el.classList.remove(
                        'dot-active'));

                    const row = this.closest('.filter-cat-row') || this;
                    row.classList.add('is-selected');
                    this.classList.add('fw-semibold');
                    this.querySelector('.filter-cat-dot')?.classList.add('dot-active');

                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                });
            });
        });
    </script>

    {{-- Mobile FAB & Offcanvas Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fab = document.getElementById('mobileFilterFab');
            const fabBadge = document.getElementById('fabBadge');
            const offcanvas = document.getElementById('filterOffcanvas');
            const applyBtn = document.getElementById('applyMobileFilter');
            const mobileSearch = document.getElementById('mobile-search-input');
            const desktopSearch = document.getElementById('search-input');
            const desktopForm = document.getElementById('filter-form');

            // ── FAB Scroll Behavior ───────────────────────────────────
            // FAB follows scroll direction: hide on scroll down, show on scroll up
            let lastScrollY = window.scrollY;
            let fabVisible = false;
            const SCROLL_THRESHOLD = 200; // px from top before FAB appears

            function updateFabVisibility() {
                const currentY = window.scrollY;
                const scrollingDown = currentY > lastScrollY;

                if (currentY < SCROLL_THRESHOLD) {
                    fab.classList.remove('fab-visible', 'fab-hidden');
                } else if (scrollingDown) {
                    fab.classList.add('fab-hidden');
                    fab.classList.remove('fab-visible');
                } else {
                    fab.classList.remove('fab-hidden');
                    fab.classList.add('fab-visible');
                }
                lastScrollY = currentY;
            }

            window.addEventListener('scroll', updateFabVisibility, {
                passive: true
            });

            // ── FAB Active Filter Badge ───────────────────────────────
            function countActiveFilters() {
                let count = 0;
                const kategori = desktopForm?.querySelector('input[name="kategori"]:checked');
                if (kategori && kategori.value) count++;

                const search = desktopSearch?.value?.trim();
                if (search) count++;

                const brands = desktopForm?.querySelectorAll('input[name="brand[]"]:checked');
                if (brands) count += brands.length;

                return count;
            }

            function updateFabBadge() {
                const count = countActiveFilters();
                if (count > 0) {
                    fabBadge.textContent = count;
                    fabBadge.classList.remove('d-none');
                } else {
                    fabBadge.classList.add('d-none');
                }
            }

            // Update badge whenever desktop filter changes
            document.addEventListener('change', function(e) {
                if (e.target.matches('.filter-change')) {
                    updateFabBadge();
                }
            });
            if (desktopSearch) {
                desktopSearch.addEventListener('input', updateFabBadge);
            }

            // ── Mobile category selection ─────────────────────────────
            offcanvas.querySelectorAll('.js-mobile-cat').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const slug = this.dataset.slug;

                    // Update visual active states in offcanvas
                    offcanvas.querySelectorAll('.filter-cat-row, .filter-subcat-btn').forEach(el =>
                        el.classList.remove('is-selected'));
                    offcanvas.querySelectorAll('.filter-cat-label, .filter-subcat-btn').forEach(
                        el => el.classList.remove('fw-semibold'));
                    offcanvas.querySelectorAll('.filter-cat-dot').forEach(el => el.classList.remove(
                        'dot-active'));

                    const row = this.closest('.filter-cat-row') || this;
                    row.classList.add('is-selected');
                    this.classList.add('fw-semibold');
                    this.querySelector('.filter-cat-dot')?.classList.add('dot-active');

                    // Sync with desktop hidden radio
                    if (slug) {
                        const radio = document.getElementById('cat-' + slug);
                        if (radio) {
                            radio.checked = true;
                        } else {
                            // Clear all radios if slug not found (shouldn't happen)
                            desktopForm?.querySelectorAll('input[name="kategori"]').forEach(r => r
                                .checked = false);
                        }
                    } else {
                        // "Semua" selected — uncheck all
                        desktopForm?.querySelectorAll('input[name="kategori"]').forEach(r => r
                            .checked = false);
                    }
                });
            });

            // ── Mobile category toggle (expand/collapse) ──────────────
            offcanvas.querySelectorAll('.filter-cat-toggle').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const node = this.closest('.filter-cat-node');
                    const subList = node.querySelector('.filter-subcat-list');
                    const isOpen = node.classList.contains('is-open');

                    offcanvas.querySelectorAll('.filter-cat-node.is-open').forEach(function(n) {
                        if (n !== node) {
                            n.classList.remove('is-open');
                            n.querySelector('.filter-cat-toggle')?.classList.remove(
                                'rotate');
                            n.querySelector('.filter-subcat-list')?.classList.remove(
                                'is-open');
                        }
                    });

                    node.classList.toggle('is-open', !isOpen);
                    this.classList.toggle('rotate', !isOpen);
                    if (subList) subList.classList.toggle('is-open', !isOpen);
                });
            });

            // ── Mobile search sync ────────────────────────────────────
            if (mobileSearch && desktopSearch) {
                // Sync mobile → desktop on input
                mobileSearch.addEventListener('input', function() {
                    desktopSearch.value = this.value;
                });
            }

            // ── Brand checkbox count badge ────────────────────────────
            const brandCountBadge = document.getElementById('brandCountBadge');

            function updateBrandBadge() {
                const checked = offcanvas.querySelectorAll('.mobile-brand-check:checked').length;
                if (brandCountBadge) {
                    if (checked > 0) {
                        brandCountBadge.textContent = checked;
                        brandCountBadge.classList.remove('d-none');
                    } else {
                        brandCountBadge.classList.add('d-none');
                    }
                }
            }

            offcanvas.querySelectorAll('.mobile-brand-check').forEach(function(cb) {
                cb.addEventListener('change', updateBrandBadge);
            });

            // ── Apply Filter Button ───────────────────────────────────
            if (applyBtn) {
                applyBtn.addEventListener('click', function() {
                    // Sync mobile search → desktop
                    if (mobileSearch && desktopSearch) {
                        desktopSearch.value = mobileSearch.value;
                    }

                    // Sync mobile brand checkboxes → desktop form
                    const mobileBrands = offcanvas.querySelectorAll('.mobile-brand-check');
                    mobileBrands.forEach(function(mobileCb) {
                        const desktopCb = desktopForm?.querySelector(
                            `input[name="brand[]"][value="${mobileCb.value}"]`);
                        if (desktopCb) desktopCb.checked = mobileCb.checked;
                    });

                    // Trigger fetch
                    if (typeof fetchProducts === 'function') {
                        fetchProducts(1);
                    } else {
                        // Fallback: dispatch change on first changed element
                        const radio = desktopForm?.querySelector('input[name="kategori"]:checked');
                        if (radio) radio.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                        else if (desktopSearch) desktopSearch.dispatchEvent(new Event('keyup', {
                            bubbles: true
                        }));
                    }

                    updateFabBadge();
                });
            }

            // Initial badge state
            updateFabBadge();
            updateBrandBadge();
        });
    </script>
@endsection
