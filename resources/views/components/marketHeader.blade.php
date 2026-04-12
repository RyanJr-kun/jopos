<header id="main-header" class="glass-header-active sticky-top shadow-none transition-all duration-300">
    <div class="main-header container-fluid container-xl border-bottom">

        {{-- ================================================================================
             TOP BAR: LOGO, SEARCH, & ACTION ICONS
             ================================================================================ --}}
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 pb-lg-3 border-top">

            {{-- 1. Logo Area --}}
            <a class="navbar-brand d-none d-lg-block" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/LM-Default.png') }}" alt="gambar logo" style="height: 40px;">
            </a>
            <a class="navbar-brand d-lg-none" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/LM-Default.png') }}" alt="gambar logo" style="height: 25px;">
            </a>

            {{-- 2. Desktop Search Bar --}}
            <div class="d-none d-lg-block w-50 position-relative search-wrapper">
                <form action="{{ route('market.produk') }}" method="GET">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="search" name="search" class="form-control js-search-input"
                            placeholder="Cari produk, kategori, atau brand..." autocomplete="off">
                    </div>
                </form>
                {{-- Live Search Results Container --}}
                <div class="js-search-results position-absolute w-100 bg-white border rounded-2 shadow-lg mt-1"
                    style="display: none; z-index: 1050;"></div>
            </div>

            {{-- 3. Action Icons (Contact & Auth Profile) --}}
            <div class="d-flex align-items-center justify-content-end">
                <div class="dropdown d-flex align-items-center">

                    {{-- Contact/WhatsApp Button --}}
                    <a class="d-flex text-success rounded-2 px-2 align-items-center" href="https://wa.me/6281318000699"
                        target="_blank" rel="noopener noreferrer">
                        <i class="bx bx-bxl-whatsapp icon-md text-success"></i>
                        <div class="d-none d-lg-block ms-2">
                            <p class="mb-n1 fw-bold"><small>Bantuan & Layanan</small></p>
                            <small class="mb-0 text-muted">0813-1800-0699</small>
                        </div>
                    </a>

                    {{-- User Profile / Login Button --}}
                    @auth
                        <a href="javascript:void(0);" class="nav-link dropdown-toggle hide-arrow p-0 ms-3"
                            data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                @if (auth()->user()->img_user)
                                    <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                        class="w-px-40 h-auto rounded-circle">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr(Auth::user()->name, 0, 2) }}
                                    </span>
                                @endif
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i
                                        class="bx bx-home-smile me-2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('penjualan.create') }}"><i
                                        class="bx bx-tv me-2"></i> Point Of Sales</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i
                                            class="bx bx-power-off me-2"></i> Log Out</button>
                                </form>
                            </li>
                        </ul>
                    @else
                        <a href="{{ route('login') }}"
                            class="nav-link badge bg-label-primary fw-bold d-flex align-items-center ms-3"
                            title="Login/Register">
                            <i class="bx bx-user icon-md"></i>
                            <span class="d-none d-lg-block ms-2">Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- 4. Mobile Search Bar --}}
        <div class="d-flex d-lg-none align-items-center border-top py-2 gap-2 search-wrapper">
            <button class="navbar-toggler align-items-center justify-content-center" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav">
                <i class="bx bx-menu fs-3"></i>
            </button>
            <div class="flex-grow-1 position-relative">
                <form action="{{ route('market.produk') }}" method="GET" class="mb-0">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="search" name="search" class="form-control js-search-input"
                            placeholder="Cari produk..." autocomplete="off">
                    </div>
                </form>
                {{-- Live Search Results Container (Mobile) --}}
                <div class="js-search-results position-absolute w-100 bg-white border rounded-2 shadow-lg mt-1"
                    style="display: none; z-index: 1050; max-height: 60vh; overflow-y: auto;"></div>
            </div>
        </div>
    </div>


    {{-- ================================================================================
         DESKTOP MAIN NAVIGATION BAR
         ================================================================================ --}}
    <nav class="navbar navbar-expand-lg navbar-light d-none d-lg-block shadow-none">
        <div class="container-fluid container-xl border-md-top">
            <ul class="navbar-nav">
                <li class="nav-item me-4"><a class="nav-link text-sm nav-link-animated active"
                        href="{{ url('/') }}">Beranda</a></li>
                <li class="nav-item me-4 dropdown dropdown-hover-market">
                    <a class="nav-link text-sm dropdown-toggle" href="#" data-bs-toggle="dropdown">Produk</a>
                    <ul class="dropdown-menu p-2 mt-2 rounded-2">
                        @forelse ($kategoris as $kategori)
                            <li><a class="dropdown-item border-radius-md"
                                    href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}">{{ $kategori->name }}</a>
                            </li>
                        @empty
                            <li><a class="dropdown-item border-radius-md text-muted" href="#">Kategori tidak
                                    tersedia</a></li>
                        @endforelse
                    </ul>
                </li>
                <li class="nav-item me-4"><a class="nav-link text-sm nav-link-animated"
                        href="{{ route('market.produk') }}">Explore</a></li>
                <li class="nav-item me-4"><a class="nav-link text-sm nav-link-animated"
                        href="{{ route('market.layanan') }}">Layanan</a></li>
                <li class="nav-item"><a class="nav-link text-sm nav-link-animated"
                        href="{{ route('market.tentang') }}">Tentang Kami</a></li>
            </ul>
        </div>
    </nav>
</header>

{{-- ================================================================================
     MOBILE OFFCANVAS MENU
     ================================================================================ --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNav">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold"><span class="text-primary me-1">JO</span>Computer</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link fs-5" href="{{ url('/') }}">Beranda</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle fs-5" href="#" data-bs-toggle="dropdown">Produk</a>
                <ul class="dropdown-menu border-0 shadow-none ps-3">
                    @forelse ($kategoris as $kategori)
                        <li><a class="dropdown-item"
                                href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}">{{ $kategori->name }}</a>
                        </li>
                    @empty
                        <li><a class="dropdown-item text-muted" href="#">Tidak ada kategori</a></li>
                    @endforelse
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link fs-5" href="{{ route('market.produk') }}">Explore</a></li>
            <li class="nav-item"><a class="nav-link fs-5" href="{{ route('market.layanan') }}">Layanan</a></li>
            <li class="nav-item"><a class="nav-link fs-5" href="{{ route('market.tentang') }}">Tentang Kami</a>
            </li>
        </ul>
    </div>
</div>

{{-- ================================================================================
     PAGE SCRIPTS
     ================================================================================ --}}
@push('page-script')
    {{-- A. LIVE SEARCH SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua elemen pembungkus search (ada 2: Desktop dan Mobile)
            const searchWrappers = document.querySelectorAll('.search-wrapper');

            const formatCurrency = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

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
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                resultsContainer.innerHTML = '';
                                if (data.products && data.products.length > 0) {
                                    let productsHtml =
                                        `<div class="p-3 border-bottom"><p class="mb-0 text-sm text-muted">Menampilkan ${data.total} dari hasil teratas...</p></div><div class="list-group list-group-flush">`;

                                    data.products.forEach(produk => {
                                        const detailUrl =
                                            `{{ url('market/produk') }}/${produk.slug}`;
                                        const imageUrl = produk.img_produk ?
                                            `{{ asset('storage') }}/${produk.img_produk}` :
                                            `{{ asset('assets/img/produk.png') }}`;
                                        const harga = produk.harga_diskon ?
                                            formatCurrency(produk
                                                .harga_diskon) : formatCurrency(
                                                produk.harga_jual);

                                        productsHtml += `
                                        <a href="${detailUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
                                            <img src="${imageUrl}" alt="${produk.name_product}" class="avatar avatar-md rounded me-3">
                                            <div class="flex-grow-1">
                                                <p class="fw-bold mb-0 text-dark text-sm">${produk.name_product}</p>
                                                <small class="text-muted d-block">${produk.category.name} / ${produk.brand ? produk.brand.name : ''}</small>
                                                <p class="fw-bolder mb-0 text-sm">${harga}</p>
                                            </div>
                                        </a>`;
                                    });

                                    productsHtml += `</div>
                                    <div class="p-2 m-2 rounded text-start text-sm bg-label-light">
                                        <a href="{{ route('market.produk') }}?search=${encodeURIComponent(query)}" class="align-items-center d-flex justify-content-center">Lihat semua hasil pencarian <i class="bx bxs-chevron-right"></i></a>
                                    </div>`;
                                    resultsContainer.innerHTML = productsHtml;
                                    resultsContainer.style.display = 'block';
                                } else {
                                    resultsContainer.innerHTML =
                                        `<div class="p-3 text-center text-muted">Tidak ada produk ditemukan untuk "${query}".</div>`;
                                    resultsContainer.style.display = 'block';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                resultsContainer.style.display = 'none';
                            });
                    }, 300);
                });

                // Sembunyikan dropdown search jika klik di luar
                document.addEventListener('click', function(event) {
                    if (!wrapper.contains(event.target)) {
                        resultsContainer.style.display = 'none';
                    }
                });
            });
        });
    </script>

    {{-- B. THEME & HEADER SCROLL EFFETCS SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================================================
            // B.1 THEME CHANGER
            // =========================================================
            const themeButtons = document.querySelectorAll('[data-bs-theme-value]');
            const themeIcon = document.querySelector('.theme-icon-active');
            const htmlElement = document.documentElement; // Tag <html>

            // Ambil tema dari localStorage, default ke 'system' jika belum ada
            let currentTheme = localStorage.getItem('templateCustomizer-theme') || 'system';

            // Fungsi untuk menerapkan tema
            function applyTheme(theme) {
                let activeTheme = theme;

                // Jika pilih 'system', cek preferensi OS pengguna
                if (theme === 'system') {
                    activeTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                // Setel atribut ke tag html (Bootstrap 5.3+ standard)
                htmlElement.setAttribute('data-bs-theme', activeTheme);

                // Ubah Icon utama di navbar
                if (themeIcon) {
                    themeIcon.classList.remove('bx-moon', 'bx-sun', 'bx-desktop');
                    if (theme === 'dark') {
                        themeIcon.classList.add('bx-moon');
                    } else if (theme === 'light') {
                        themeIcon.classList.add('bx-sun');
                    } else {
                        themeIcon.classList.add('bx-desktop');
                    }
                }

                // Tandai tombol dropdown yang sedang aktif
                themeButtons.forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.getAttribute('data-bs-theme-value') === theme) {
                        btn.classList.add('active');
                    }
                });
            }

            // Jalankan fungsi saat halaman pertama kali dimuat
            applyTheme(currentTheme);

            // Event listener untuk klik tombol pergantian tema
            themeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const selectedTheme = btn.getAttribute('data-bs-theme-value');
                    localStorage.setItem('templateCustomizer-theme',
                        selectedTheme); // Simpan pilihan
                    applyTheme(selectedTheme);
                });
            });

            // Otomatis ganti tema jika OS pengguna berganti (saat mode 'system')
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (localStorage.getItem('templateCustomizer-theme') === 'system') {
                    applyTheme('system');
                }
            });


            // =========================================================
            // B.2 STICKY NAVBAR (SCROLL EFFECT)
            // =========================================================
            const navbar = document.querySelector('.layout-navbar');

            window.addEventListener('scroll', function() {
                if (!navbar) return;

                if (window.scrollY > 50) {
                    // Saat di-scroll ke bawah: Tambahkan background, shadow, dan class aktif
                    navbar.classList.add('navbar-active', 'bg-white', 'shadow-sm');
                    navbar.classList.remove('shadow-none');
                } else {
                    // Saat di posisi paling atas: Hapus background & kembalikan transparan
                    navbar.classList.remove('navbar-active', 'bg-white', 'shadow-sm');
                    navbar.classList.add('shadow-none');
                }
            });
        });
    </script>
@endpush
