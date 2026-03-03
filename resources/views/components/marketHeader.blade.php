<header class="bg-white shadow-0">
    {{-- 2. MAIN HEADER (Logo, Search, Icons) --}}
    <div class="main-header container-fluid container-xl border-bottom ">
        <div class="d-flex justify-content-between align-items-center py-3 border-top">
            {{-- Desktop: Logo --}}
            <a class="navbar-brand d-none d-lg-block" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logomiring.svg') }}" alt="gambar logo" style="height: 50px;">
            </a>
            {{-- Mobile: Logo --}}
            <a class="navbar-brand d-lg-none" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logomiring.svg') }}" alt="gambar logo" style="height: 45px;">
            </a>

            {{-- Desktop: Search Form --}}
            <div class="d-none d-lg-block w-50 position-relative">
                <form action="{{ route('market.produk') }}" method="GET">
                    <div class="input-group">
                        <input type="search" id="global-search-input" name="search" class="form-control"
                            placeholder="Cari produk, kategori, atau brand..." aria-label="Cari produk"
                            autocomplete="off">
                    </div>
                </form>
                {{-- Container untuk hasil live search --}}
                <div id="search-results-container"
                    class="position-absolute w-100 bg-white border rounded-2 shadow-lg mt-1"
                    style="display: none; z-index: 1050;">
                    {{-- Hasil akan di-inject oleh JavaScript di sini --}}
                </div>
            </div>



            {{-- All Devices: Action Icons --}}
            <div class="d-flex align-items-center justify-content-end">
                <div class="dropdown d-flex align-items-center">
                    @auth
                        <a class="text-dark d-flex align-items-center me-3" href="https://wa.me/6281318000699"
                            target="_blank" rel="noopener noreferrer">
                            <i class="bx bx-bxl-whatsapp bi-lg me-lg-2 text-success"></i>
                            <div class="d-none d-lg-block">
                                <p class="mb-0 fw-bold" style="font-size: 0.8rem; line-height: 1.2;">Bantuan & Layanan</p>
                                <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.2;">0813-1800-0699</p>
                            </div>
                        </a>

                        <a href="#" class="text-dark d-lg-none" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasSearch" aria-controls="offcanvasSearch">
                            <i class="bx bx-search bi-lg"></i>
                        </a>

                        {{-- Logged In View --}}
                        <a href="#" class="nav-link text-dark p-0 mx-3" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            @if (auth()->user()->img_user)
                                <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                    class="avatar avatar-sm rounded-2">
                            @else
                                <i class="bx bx-person-circle fs-4"></i>
                            @endif
                        </a>
                        <button class="navbar-toggler border-0 p-0 d-lg-none" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
                            <i class="bx bx-list bi-lg"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2 mt-2">
                            <li class="text-start d-flex m-2">
                                @if (auth()->user()->img_user)
                                    <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                        class="avatar avatar-sm rounded-circle cursor-pointer">
                                @else
                                    <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                        alt="Gambar user default">
                                @endif
                                <div class="ms-2">
                                    <p class="mb-0 text-xs fw-bolder">{{ auth()->user()->username }}</p>
                                    <p class="text-xs text-secondary">{{ auth()->user()->role->nama }}</p>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item border-radius-md" href="{{ route('dashboard') }}"><i
                                        class="bx bx-tv me-2"></i> Dashboard</a>
                            </li>
                            <li>
                                <a class="dropdown-item border-radius-md" href="{{ route('penjualan.create') }}"><i
                                        class="bx bx-person-fill me-2"></i> Point Of Sales</a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="dropdown-item border-radius-md w-100 text-danger"
                                        style="text-align: left;"><i class="bx bx-box-arrow-right me-2"></i>Log Out</button>
                                </form>
                            </li>
                        </ul>
                    @else
                        <div class="d-flex align-items-center">

                            {{-- Tautan Bantuan WhatsApp --}}
                            <a class="text-dark d-flex align-items-center me-3" href="https://wa.me/6281318000699"
                                target="_blank" rel="noopener noreferrer">
                                <i class="bx bx-whatsapp bi-lg me-lg-2 text-success"></i>
                                <div class="d-none d-lg-block">
                                    <p class="mb-0 fw-bold" style="font-size: 0.8rem; line-height: 1.2;">Bantuan & Layanan
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.2;">0813-1800-0699
                                    </p>
                                </div>
                            </a>
                            <a href="#" class="text-dark d-lg-none" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasSearch" aria-controls="offcanvasSearch">
                                <i class="bx bx-search bi-lg"></i>
                            </a>

                            {{-- Guest View: Login Icon --}}
                            <a href="{{ route('login') }}" class="nav-link mx-3 text-dark p-0" title="Login/Register">
                                <i class="bx bx-bxs-user bx-md"></i>
                            </a>

                            <button class="navbar-toggler d-lg-none border-0 p-0" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
                                <i class="bx bx-list bi-xl"></i>
                            </button>


                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- 3. NAVIGATION BAR (Desktop) --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white d-none d-lg-block shadow-none">
        <div class="container-fluid container-xl border-md-top">
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item me-4">
                        <a class="nav-link text-sm nav-link-animated active" aria-current="page"
                            href="{{ url('/') }}">Beranda</a>
                    </li>
                    <li class="nav-item me-4">
                        <div class="dropdown dropdown-hover-market">
                            <a class="nav-link text-sm dropdown-toggle" href="#" id="produkDrop"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">Produk</a>
                            <ul class="dropdown-menu p-2 mt-2 rounded-2" aria-labelledby="produkDrop">
                                @forelse ($kategoris as $kategori)
                                    <li>
                                        {{-- Arahkan ke halaman produk dengan filter kategori --}}
                                        <a class="dropdown-item border-radius-md"
                                            href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}">{{ $kategori->nama }}</a>
                                    </li>
                                @empty
                                    <li><a class="dropdown-item border-radius-md text-muted" href="#">Kategori
                                            tidak tersedia</a></li>
                                @endforelse
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-sm nav-link-animated" href="{{ route('market.produk') }}">Explore</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-sm nav-link-animated"
                            href="{{ route('market.layanan') }}">Layanan</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-sm nav-link-animated" href="{{ route('market.tentang') }}">Tentang
                            Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Mobile Offcanvas Navigation --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavLabel">Menu</h5>
            <i class="bx bx-x-lg p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-xl-none"
                data-bs-dismiss="offcanvas" aria-label="Close"></i>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link fs-5" href="{{ url('/') }}">Beranda</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fs-5" href="#" id="offcanvasKategoriDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">Produk</a>
                    <ul class="dropdown-menu border-0" aria-labelledby="offcanvasKategoriDropdown">
                        @forelse ($kategoris as $kategori)
                            <li>
                                {{-- Arahkan ke halaman produk dengan filter kategori --}}
                                <a class="dropdown-item"
                                    href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}">{{ $kategori->nama }}</a>
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

    {{-- Mobile Offcanvas Search --}}
    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasSearch" aria-labelledby="offcanvasSearchLabel"
        style="height: auto;">
        <div class="offcanvas-body">
            <div class="container d-flex align-items-center">
                <form action="{{ route('market.produk') }}" method="GET">
                    <div class="input-group">
                        <input type="search" id="global-search-input" name="search" class="form-control"
                            placeholder="Cari produk, kategori, atau brand..." aria-label="Cari produk"
                            autocomplete="off">
                    </div>
                </form>
                {{-- Container untuk hasil live search --}}
                <div id="search-results-container"
                    class="position-absolute w-100 bg-white border rounded-2 shadow-lg mt-1"
                    style="display: none; z-index: 1050;">
                    {{-- Hasil akan di-inject oleh JavaScript di sini --}}
                </div>
                <i class="bx bx-x-lg ms-3 cursor-pointer" data-bs-dismiss="offcanvas" aria-label="Close"></i>
            </div>
        </div>
    </div>
</header>

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('global-search-input');
            const resultsContainer = document.getElementById('search-results-container');
            let debounceTimer;

            // Fungsi untuk format mata uang
            const formatCurrency = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

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
                            resultsContainer.innerHTML = ''; // Kosongkan hasil sebelumnya
                            if (data.produks && data.produks.length > 0) {
                                let productsHtml =
                                    `<div class="p-3 border-bottom"><p class="mb-0 text-sm text-muted">Menampilkan ${data.total} dari hasil teratas...</p></div><div class="list-group list-group-flush">`;

                                data.produks.forEach(produk => {
                                    const detailUrl =
                                        `{{ url('produk') }}/${produk.slug}`;
                                    const imageUrl = produk.img_produk ?
                                        `{{ asset('storage') }}/${produk.img_produk}` :
                                        `{{ asset('assets/img/produk.png') }}`;
                                    const harga = produk.harga_diskon ? formatCurrency(
                                        produk.harga_diskon) : formatCurrency(produk
                                        .harga_jual);

                                    productsHtml += `
                            <a href="${detailUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
                                <img src="${imageUrl}" alt="${produk.nama_produk}" class="avatar avatar-md rounded me-3">
                                <div class="flex-grow-1">
                                    <p class="fw-bold mb-0 text-dark text-sm">${produk.nama_produk}</p>
                                    <small class="text-muted d-block">${produk.kategori_produk.nama} / ${produk.brand ? produk.brand.nama : ''}</small>
                                    <p class="fw-bolder text-info mb-0 text-sm">${harga}</p>
                                </div>
                            </a>
                        `;
                                });

                                productsHtml += `</div>`;
                                productsHtml += `
                        <div class="p-2 rounded-2 text-start text-sm bg-light">
                            <a href="{{ route('market.produk') }}?search=${encodeURIComponent(query)}" class="">Lihat semua hasil pencarian <i class="bx bx-chevron-double-right"></i></a>
                        </div>
                    `;
                                resultsContainer.innerHTML = productsHtml;
                                resultsContainer.style.display = 'block';
                            } else {
                                resultsContainer.innerHTML =
                                    `<div class="p-3 text-center text-muted">Tidak ada produk ditemukan untuk "${query}".</div>`;
                                resultsContainer.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                            resultsContainer.style.display = 'none';
                        });
                }, 300); // Waktu tunda 300ms
            });

            // Sembunyikan hasil pencarian saat mengklik di luar area
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
                    resultsContainer.style.display = 'none';
                }
            });
        });
    </script>
@endsection
