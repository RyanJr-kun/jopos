<x-marketLayout>
    {{-- Breadcrumb --}}
    <div class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                {{-- Beri ID agar mudah dimanipulasi oleh JS --}}
                <ol class="breadcrumb mb-0" id="breadcrumb-ol">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    @if (request('kategori') || request('search'))
                        <li class="breadcrumb-item"><a href="{{ route('market.produk') }}"
                                class="text-decoration-none">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ request('kategori') ? \App\Models\KategoriProduk::where('slug', request('kategori'))->first()->nama ?? 'Filter' : 'Pencarian: ' . request('search') }}
                        </li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Produk</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container py-5">
        <div class="row">
            {{-- Sidebar untuk Filter --}}
            <div class="col-lg-3">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form id="filter-form">
                            {{-- Filter Pencarian --}}
                            <div class="mb-4">
                                <h6 class="filter-title">Cari Produk</h6>
                                <input type="text" name="search" id="search-input" class="form-control"
                                    placeholder="Nama atau SKU..." value="{{ request('search') }}">
                            </div>

                            {{-- Filter Kategori --}}
                            <div class="mb-4">
                                <h6 class="filter-title">Kategori</h6>
                                <ul class="list-unstyled">
                                    @foreach ($kategorisForFilter as $kategori)
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input filter-change" type="radio"
                                                    name="kategori" value="{{ $kategori->slug }}"
                                                    id="cat-{{ $kategori->slug }}" @checked(request('kategori') == $kategori->slug)>
                                                <label class="form-check-label" for="cat-{{ $kategori->slug }}">
                                                    {{ $kategori->nama }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-grid">
                                <a href="{{ route('market.produk') }}" class="btn btn-outline-secondary btn-sm">Reset
                                    Filter</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Konten Produk (akan diisi oleh AJAX) --}}
            <div class="col-lg-9">
                <div id="product-list-container">
                    @include('market._produk_list', ['produks' => $produks])
                </div>
            </div>
        </div>
    </div>

    @section('page-script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let debounceTimer;
                const productContainer = document.getElementById('product-list-container');

                function updateBreadcrumb() {
                    const breadcrumbContainer = document.getElementById('breadcrumb-ol');
                    if (!breadcrumbContainer) return;

                    const params = new URLSearchParams(window.location.search);
                    const searchQuery = params.get('search');
                    const kategoriSlug = params.get('kategori');

                    // Teks default untuk kategori jika tidak ditemukan
                    let kategoriNama = 'Filter';
                    if (kategoriSlug) {
                        const kategoriInput = document.querySelector(`input[name="kategori"][value="${kategoriSlug}"]`);
                        if (kategoriInput) {
                            kategoriNama = kategoriInput.nextElementSibling.textContent.trim();
                        }
                    }

                    let breadcrumbHtml =
                        `<li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>`;
                    if (searchQuery || kategoriSlug) {
                        breadcrumbHtml +=
                            `<li class="breadcrumb-item"><a href="{{ route('market.produk') }}" class="text-decoration-none">Produk</a></li>`;
                        breadcrumbHtml +=
                            `<li class="breadcrumb-item active" aria-current="page">${searchQuery ? `Pencarian: "${searchQuery}"` : kategoriNama}</li>`;
                    } else {
                        breadcrumbHtml += `<li class="breadcrumb-item active" aria-current="page">Produk</li>`;
                    }
                    breadcrumbContainer.innerHTML = breadcrumbHtml;
                }

                function fetchProducts(page = 1) {
                    const form = document.getElementById('filter-form');
                    const formData = new FormData(form);
                    // Ambil nilai sort dari select di dalam container produk
                    const sortValue = productContainer.querySelector('#sort')?.value || 'latest';
                    formData.append('sort', sortValue);

                    const params = new URLSearchParams(formData);
                    const url = `{{ route('market.produk') }}?${params.toString()}&page=${page}`;

                    // Tampilkan indikator loading
                    productContainer.style.opacity = '0.5';

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            productContainer.innerHTML = html;
                            productContainer.style.opacity = '1';
                            // Update URL di browser tanpa reload
                            window.history.pushState({
                                path: url
                            }, '', url);
                            // Panggil fungsi untuk update breadcrumb setelah konten baru dimuat
                            updateBreadcrumb();
                        })
                        .catch(error => {
                            console.error('Error fetching products:', error);
                            productContainer.style.opacity = '1'; // Kembalikan opacity jika error
                        });
                }

                // Event listener untuk input pencarian dengan debounce
                document.getElementById('search-input').addEventListener('keyup', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetchProducts(1); // Selalu kembali ke halaman 1 saat pencarian baru
                    }, 500); // Tunggu 500ms setelah user berhenti mengetik
                });

                // Event listener untuk filter radio kategori dan select sort
                document.addEventListener('change', function(event) {
                    if (event.target.matches('.filter-change') || event.target.matches('#sort')) {
                        fetchProducts(1); // Selalu kembali ke halaman 1 saat filter berubah
                    }
                });

                // Event listener untuk klik paginasi
                document.addEventListener('click', function(event) {
                    // Cek apakah yang diklik adalah link di dalam elemen paginasi
                    if (event.target.closest('.pagination a')) {
                        event.preventDefault();
                        const link = event.target.closest('.pagination a');
                        const url = new URL(link.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            fetchProducts(page);
                        }
                    }
                });

                // Panggil sekali saat halaman dimuat untuk menyesuaikan breadcrumb dengan URL awal
                updateBreadcrumb();
            });
        </script>
    @endsection
</x-marketLayout>
