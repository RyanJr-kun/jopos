<header id="main-header" class="glass-header-active sticky-top shadow-none transition-all duration-300">
    <div class="main-header container-market border-bottom">

        {{-- ================================================================================
             TOP BAR: LOGO, SEARCH, & ACTION ICONS
             ================================================================================ --}}
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 pb-lg-3 border-top">

            {{-- 1. Logo --}}
            <a class="navbar-brand d-none d-lg-block" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/LM-Default.png') }}" alt="gambar logo" style="height: 40px;">
            </a>
            <a class="navbar-brand d-lg-none" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/LM-Default.png') }}" alt="gambar logo" style="height: 25px;">
            </a>

            {{-- 2. Desktop Search --}}
            <div class="d-none d-lg-block w-50 position-relative search-wrapper">
                <form action="{{ route('market.produk') }}" method="GET">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="search" name="search" class="form-control js-search-input"
                            placeholder="Cari produk, kategori, atau brand..." autocomplete="off">
                    </div>
                </form>
                <div class="js-search-results position-absolute w-100 bg-white border rounded-2 shadow-lg mt-1"
                    style="display:none; z-index:1050;"></div>
            </div>

            {{-- 3. Action Icons --}}
            <div class="d-flex align-items-center justify-content-end">
                <div class="dropdown d-flex align-items-center">

                    {{-- Dark Mode Toggle (Desktop) --}}
                    <button type="button" class="theme-toggle-btn border-none d-lg-inline-flex" id="themeToggleDesktop"
                        aria-label="Toggle dark mode" title="Toggle dark mode">
                        <span class="theme-toggle-icon">
                            <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5" />
                                <line x1="12" y1="1" x2="12" y2="3" />
                                <line x1="12" y1="21" x2="12" y2="23" />
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                                <line x1="1" y1="12" x2="3" y2="12" />
                                <line x1="21" y1="12" x2="23" y2="12" />
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                            </svg>
                            <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                            </svg>
                        </span>
                    </button>

                    <a class="d-flex text-success rounded-2 px-2 align-items-center" href="https://wa.me/6281318000699"
                        target="_blank" rel="noopener noreferrer">
                        <i class="bx bx-bxl-whatsapp icon-md text-success"></i>
                        <div class="d-none d-lg-block ms-2">
                            <p class="mb-n1 fw-bold"><small>Bantuan & Layanan</small></p>
                            <small class="mb-0 text-muted">0813-1800-0699</small>
                        </div>
                    </a>

                    {{-- 3. Action Icons (Lanjutan baris 81)    --}}
                    @auth
                        <a href="javascript:void(0);" class="nav-link dropdown-toggle hide-arrow p-0 ms-3"
                            data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                @if (auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile"
                                        class="w-px-40 h-auto rounded-circle">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr(Auth::user()->name, 0, 2) }}
                                    </span>
                                @endif
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">

                            {{-- PENGECEKAN ROLE/PROFIL --}}
                            {{-- Jika user adalah karyawan (punya relasi employee) --}}
                            @if (auth()->user()->employee)
                                <li>
                                    {{-- Menggunakan subdomain dinamis --}}
                                    @php $adminDomain = 'http://jopos.' . env('APP_DOMAIN', 'jocomputer.test'); @endphp
                                    <a class="dropdown-item" href="{{ $adminDomain }}/dashboard">
                                        <i class="bx bx-home-smile me-2"></i>Dashboard Admin
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ $adminDomain }}/penjualan/create">
                                        <i class="bx bx-tv me-2"></i>Point Of Sales
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('employee.logout') }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-power-off me-2"></i>Log Out</button>
                                    </form>
                                </li>

                                {{-- Jika user adalah pelanggan biasa --}}
                            @else
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bx bx-user me-2"></i>Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bx bx-cart me-2"></i>Pesanan Saya
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('customer.logout') }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-power-off me-2"></i>Log Out</button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    @else
                        {{-- Jika Belum Login, Arahkan ke Login Customer --}}
                        <a href="{{ route('login') }}"
                            class="nav-link badge bg-label-primary fw-bold d-flex align-items-center ms-3">
                            <i class="bx bx-user icon-md"></i>
                            <span class="d-none d-lg-block ms-2">Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- 4. Mobile Search --}}
        <div class="d-flex d-lg-none align-items-center border-top py-2 gap-2 search-wrapper">
            <button class="navbar-toggler align-items-center justify-content-center" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav">
                <i class="bx bx-menu fs-3"></i>
            </button>
            <div class="flex-grow-1 position-relative">
                <form action="{{ route('market.produk') }}" method="GET" class="mb-0">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text "><i class="bx bx-search"></i></span>
                        <input type="search" name="search" class="form-control js-search-input"
                            placeholder="Cari produk..." autocomplete="off">
                    </div>
                </form>
                <div class="js-search-results position-absolute w-100 bg-white border rounded-2 shadow-lg mt-1"
                    style="display:none; z-index:1050; max-height:60vh; overflow-y:auto;"></div>
            </div>
        </div>
    </div>

    {{-- ================================================================================
         DESKTOP NAVIGATION BAR
         ================================================================================ --}}
    <nav class="navbar navbar-expand-lg navbar-light d-none d-lg-block shadow-none">
        <div class="container-market">
            <ul class="navbar-nav">
                <li class="nav-item me-4">
                    <a class="nav-link text-sm nav-link-animated active" href="{{ url('/') }}">Beranda</a>
                </li>

                {{-- ─── Dropdown Kategori Produk (Desktop) ─────────────────── --}}
                <li class="nav-item me-4 dropdown-custom-click" id="desktopCategoryDropdown">
                    <a class="nav-link text-sm d-flex align-items-center gap-1" href="#" role="button"
                        id="desktopCategoryToggle">
                        Produk
                        <i class="bx bx-chevron-down" style="font-size:0.85rem; transition: transform 0.2s;"
                            id="categoryChevron"></i>
                    </a>

                    {{-- Dropdown panel --}}
                    <div class="dropdown-multilevel-container" id="desktopCategoryMenu"
                        aria-labelledby="desktopCategoryToggle">
                        <div class="d-flex" style="height:100%">

                            {{-- Kolom kiri: parent kategori --}}
                            <div class="category-sidebar" id="categoryScrollbar">
                                <ul class="list-unstyled mb-0">
                                    @forelse ($kategoris as $parent)
                                        <li class="category-item" data-slug="{{ $parent->slug }}"
                                            data-image="{{ $parent->img_kategori ? asset('storage/' . $parent->img_kategori) : asset('assets/img/produk.png') }}">

                                            {{-- Link parent kategori --}}
                                            <a class="dropdown-item"
                                                href="{{ route('market.produk', ['kategori' => $parent->slug]) }}">
                                                <span class="cat-name">{{ $parent->name }}</span>
                                                @if ($parent->children->isNotEmpty())
                                                    <i class="bx bx-chevron-right chevron-icon"></i>
                                                @endif
                                            </a>

                                            {{-- Sub kategori panel (muncul saat hover) --}}
                                            @if ($parent->children->isNotEmpty())
                                                <ul class="submenu-panel list-unstyled mb-0">
                                                    <li class="submenu-header">
                                                        {{ $parent->name }}
                                                    </li>
                                                    @foreach ($parent->children as $child)
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('market.produk', ['kategori' => $child->slug]) }}"
                                                                data-image="{{ $child->img_kategori ? asset('storage/' . $child->img_kategori) : asset('assets/img/produk.png') }}">
                                                                {{ $child->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="px-3 py-4 text-center text-muted" style="font-size:0.82rem">
                                            Kategori tidak tersedia
                                        </li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- Kolom kanan: preview gambar --}}
                            <div
                                class="category-preview  w-full bg-white h-full align-items-center justify-content-center">
                                <div class="preview-wrapper p-4 rounded-3">
                                    <div class="preview-placeholder" id="catPreviewPlaceholder">
                                        <i class="bx bx-image-alt"></i>
                                        <span>Arahkan ke kategori</span>
                                    </div>
                                    <img src="{{ asset('assets/img/banner/1.png') }}" id="catPreviewImg"
                                        alt="Preview kategori" class="rounded-3"
                                        style="display:block; width:100%; height:100%; object-fit:cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                {{-- ─── End Dropdown Kategori ───────────────────────────────── --}}

                <li class="nav-item me-4">
                    <a class="nav-link text-sm nav-link-animated" href="{{ route('market.produk') }}">Explore</a>
                </li>
                <li class="nav-item me-4">
                    <a class="nav-link text-sm nav-link-animated" href="{{ route('market.layanan') }}">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-sm nav-link-animated" href="{{ route('market.tentang') }}">Tentang
                        Kami</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

{{-- ================================================================================
     MOBILE OFFCANVAS — dengan accordion multilevel
     ================================================================================ --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="offcanvasNavLabel">
            <span class="text-primary me-1">JO</span>Computer
        </h5>

        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <div class="d-flex align-items-center gap-2">
        </div>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav">

            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">Beranda</a>
            </li>

            {{-- ─── Accordion Kategori Produk (Mobile) ─────────────────── --}}
            <li class="nav-item">
                <button class="category-accordion-btn" type="button" id="mobileCategoryBtn" aria-expanded="false"
                    aria-controls="mobileCategoryBody">
                    <span>Produk</span>
                    <i class="bx bx-chevron-down accordion-chevron"></i>
                </button>

                <div class="category-accordion-body" id="mobileCategoryBody">
                    @forelse ($kategoris as $parent)
                        <div>
                            <button class="parent-cat-btn" type="button" aria-expanded="false"
                                aria-controls="sub-{{ $parent->slug }}">
                                <span class="cat-name">{{ $parent->name }}</span>
                                <i class="bx bx-chevron-right sub-chevron"></i>
                            </button>
                            <ul class="sub-cat-list list-unstyled mb-0" id="sub-{{ $parent->slug }}">
                                {{-- Link langsung ke parent --}}
                                <li>
                                    <a href="{{ route('market.produk', ['kategori' => $parent->slug]) }}">
                                        Semua {{ $parent->name }}
                                    </a>
                                </li>
                                @foreach ($parent->children as $child)
                                    <li>
                                        <a href="{{ route('market.produk', ['kategori' => $child->slug]) }}">
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="px-4 py-3 text-muted mb-0" style="font-size:0.82rem">
                            Kategori tidak tersedia
                        </p>
                    @endforelse
                </div>
            </li>
            {{-- ─── End Accordion Kategori ──────────────────────────────── --}}

            <li class="nav-item">
                <a class="nav-link" href="{{ route('market.produk') }}">Explore</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('market.layanan') }}">Layanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('market.tentang') }}">Tentang Kami</a>
            </li>
        </ul>
    </div>
</div>

{{-- ================================================================================
     PAGE SCRIPTS
     ================================================================================ --}}
@push('page-script')
    {{-- A. LIVE SEARCH SCRIPT (tidak berubah) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchWrappers = document.querySelectorAll('.search-wrapper');
            const formatCurrency = (n) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(n);

            searchWrappers.forEach(wrapper => {
                const searchInput = wrapper.querySelector('.js-search-input');
                const resultsContainer = wrapper.querySelector('.js-search-results');
                let debounceTimer;
                if (!searchInput || !resultsContainer) return;

                searchInput.addEventListener('keyup', function() {
                    clearTimeout(debounceTimer);
                    const query = searchInput.value.trim();
                    if (query.length < 3) {
                        resultsContainer.style.display = 'none';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch(`{{ route('market.liveSearch') }}?query=${query}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(r => r.json())
                            .then(data => {
                                resultsContainer.innerHTML = '';
                                if (data.products && data.products.length > 0) {
                                    let html =
                                        `<div class="p-3 border-bottom"><p class="mb-0 text-sm text-muted">Menampilkan ${data.total} dari hasil teratas...</p></div><div class="list-group list-group-flush">`;
                                    data.products.forEach(produk => {
                                        const detailUrl =
                                            `{{ url('market/produk') }}/${produk.slug}`;
                                        const imageUrl = produk.img_produk ?
                                            `{{ asset('storage') }}/${produk.img_produk}` :
                                            `{{ asset('assets/img/produk.png') }}`;
                                        const harga = produk.harga_diskon ?
                                            formatCurrency(produk
                                                .harga_diskon) :
                                            formatCurrency(produk.harga_jual);
                                        html += `<a href="${detailUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <img src="${imageUrl}" alt="${produk.name_product}" class="avatar avatar-md rounded me-3">
                                        <div class="flex-grow-1">
                                            <p class="fw-bold mb-0 text-dark text-sm">${produk.name_product}</p>
                                            <span class="badge bg-label-primary me-1 px-2 py-1">${produk.brand ? produk.brand.name : ''}</span><span class="badge bg-label-danger px-2 py-1">${produk.category.name}</span>
                                            <p class="fw-bolder mb-0 text-sm">${harga}</p>
                                        </div>
                                    </a>`;
                                    });
                                    html += `</div><div class="p-2 m-2 rounded text-start text-sm bg-label-light">
                                    <a href="{{ route('market.produk') }}?search=${encodeURIComponent(query)}" class="d-flex justify-content-center align-items-center">Lihat semua hasil <i class="bx bxs-chevron-right ms-1"></i></a>
                                </div>`;
                                    resultsContainer.innerHTML = html;
                                    resultsContainer.style.display = 'block';
                                } else {
                                    resultsContainer.innerHTML =
                                        `<div class="p-3 text-center text-muted">Tidak ada produk untuk "${query}".</div>`;
                                    resultsContainer.style.display = 'block';
                                }
                            })
                            .catch(() => {
                                resultsContainer.style.display = 'none';
                            });
                    }, 300);
                });

                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target)) resultsContainer.style.display = 'none';
                });
            });
        });
    </script>

    {{-- B. THEME TOGGLE & SCROLL EFFECTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const htmlEl = document.documentElement;
            const toggleButtons = document.querySelectorAll('.theme-toggle-btn');

            function getCurrentTheme() {
                return htmlEl.getAttribute('data-bs-theme') || 'light';
            }

            function updateToggleIcons(theme) {
                toggleButtons.forEach(btn => {
                    if (theme === 'dark') {
                        btn.classList.add('is-dark');
                    } else {
                        btn.classList.remove('is-dark');
                    }
                });
            }

            function toggleTheme() {
                const current = getCurrentTheme();
                const next = current === 'dark' ? 'light' : 'dark';

                // Add transition class to body for smooth color change
                document.body.classList.add('theme-transitioning');

                htmlEl.setAttribute('data-bs-theme', next);
                localStorage.setItem('templateCustomizer-theme', next);
                updateToggleIcons(next);

                // Remove transition class after animation completes
                setTimeout(() => document.body.classList.remove('theme-transitioning'), 400);
            }

            // Initialize icons based on current theme
            updateToggleIcons(getCurrentTheme());

            // Bind click to all toggle buttons (desktop + mobile)
            toggleButtons.forEach(btn => btn.addEventListener('click', toggleTheme));

            // Scroll effect for header glassmorphism
            const navbar = document.querySelector('#main-header');
            window.addEventListener('scroll', function() {
                if (!navbar) return;
                const isDark = getCurrentTheme() === 'dark';
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-active', 'shadow-sm');
                    navbar.classList.remove('shadow-none');
                    if (!isDark) navbar.classList.add('bg-white');
                } else {
                    navbar.classList.remove('navbar-active', 'shadow-sm', 'bg-white');
                    navbar.classList.add('shadow-none');
                }
            });
        });
    </script>

    {{-- C. DESKTOP DROPDOWN MULTILEVEL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Referensi elemen ──────────────────────────────────────
            const dropWrap = document.getElementById('desktopCategoryDropdown');
            const toggleBtn = document.getElementById('desktopCategoryToggle');
            const menu = document.getElementById('desktopCategoryMenu');
            const chevron = document.getElementById('categoryChevron');
            const previewImg = document.getElementById('catPreviewImg');
            const previewPlaceholder = document.getElementById('catPreviewPlaceholder');

            if (!dropWrap || !menu) return;

            // ── 1. Toggle buka/tutup (sticky click) ───────────────────
            function openMenu() {
                menu.classList.add('show');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }

            function closeMenu() {
                menu.classList.remove('show');
                if (chevron) chevron.style.transform = '';
            }

            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                menu.classList.contains('show') ? closeMenu() : openMenu();
            });

            // Tutup saat klik di luar
            document.addEventListener('click', function(e) {
                if (!dropWrap.contains(e.target)) closeMenu();
            });

            // Tutup dengan tombol Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeMenu();
            });

            // ── 2. Preview gambar saat hover kategori ─────────────────
            function setPreview(src) {
                if (!src) return;
                previewImg.classList.add('is-loading');
                previewImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';

                const tmp = new Image();
                tmp.onload = () => {
                    previewImg.src = src;
                    previewImg.classList.remove('is-loading');
                };
                tmp.onerror = () => {
                    previewImg.style.display = 'none';
                    previewPlaceholder.style.display = 'flex';
                };
                tmp.src = src;
            }

            // Hover pada parent kategori (di sidebar kiri)
            document.querySelectorAll('.category-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const imgSrc = this.getAttribute('data-image');
                    setPreview(imgSrc);
                });
            });

            // Hover pada sub kategori (di kolom tengah)
            document.querySelectorAll('.submenu-panel .dropdown-item').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    const imgSrc = this.getAttribute('data-image');
                    if (imgSrc) setPreview(imgSrc);
                });
            });
        });
    </script>

    {{-- D. MOBILE ACCORDION (offcanvas) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Accordion level 1: toggle seluruh daftar kategori ─────
            const mobileCategoryBtn = document.getElementById('mobileCategoryBtn');
            const mobileCategoryBody = document.getElementById('mobileCategoryBody');

            if (mobileCategoryBtn && mobileCategoryBody) {
                mobileCategoryBtn.addEventListener('click', function() {
                    const isOpen = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', String(!isOpen));
                    mobileCategoryBody.classList.toggle('show', !isOpen);
                });
            }

            // ── Accordion level 2: toggle sub kategori per parent ─────
            document.querySelectorAll('.parent-cat-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const isOpen = this.getAttribute('aria-expanded') === 'true';
                    const targetId = this.getAttribute('aria-controls');
                    const subList = document.getElementById(targetId);

                    // Tutup semua sub list lain (accordion behavior)
                    document.querySelectorAll('.parent-cat-btn').forEach(b => {
                        if (b !== this) {
                            b.setAttribute('aria-expanded', 'false');
                            const id = b.getAttribute('aria-controls');
                            if (id) document.getElementById(id)?.classList.remove('show');
                        }
                    });

                    this.setAttribute('aria-expanded', String(!isOpen));
                    if (subList) subList.classList.toggle('show', !isOpen);
                });
            });

            // Reset state accordion saat offcanvas ditutup
            const offcanvas = document.getElementById('offcanvasNav');
            if (offcanvas) {
                offcanvas.addEventListener('hidden.bs.offcanvas', function() {
                    // Reset level 1
                    if (mobileCategoryBtn) {
                        mobileCategoryBtn.setAttribute('aria-expanded', 'false');
                        mobileCategoryBody?.classList.remove('show');
                    }
                    // Reset level 2
                    document.querySelectorAll('.parent-cat-btn').forEach(b => {
                        b.setAttribute('aria-expanded', 'false');
                        const id = b.getAttribute('aria-controls');
                        if (id) document.getElementById(id)?.classList.remove('show');
                    });
                });
            }
        });
    </script>
@endpush
