@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Beranda - JO Computer')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')

    @yield('content')
    <x-marketHeader></x-marketHeader>
    {{-- Breadcrumb --}}
    <div class="bg-white py-3 ms-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" id="breadcrumb-ol">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    @if (request('kategori') || request('search'))
                        <li class="breadcrumb-item"><a href="{{ route('market.produk') }}"
                                class="text-decoration-none">Product</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ request('kategori') ? \App\Models\Category::where('slug', request('kategori'))->first()->name ?? 'Filter' : 'Pencarian: ' . request('search') }}
                        </li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Product</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>

    {{-- Main Content --}}
    <section id="productShowCase" class="section-py bg-white">
        <div class="container">
            <div class="row">
                {{-- Sidebar untuk Filter --}}
                <div class="col-lg-3">
                    <div class="card shadow-none border mb-4">
                        <div class="card-body">
                            <form id="filter-form">
                                {{-- Filter Pencarian --}}
                                <div class="mb-4">
                                    <h6 class="filter-title">Cari Product</h6>
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
                                                        {{ $kategori->name }}
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

                {{-- Konten Product (akan diisi oleh AJAX) --}}
                <div class="col-lg-9">
                    <div id="product-list-container">
                        @include('content.market._produk_list', ['products' => $products])
                    </div>
                </div>
            </div>
        </div>
    </section>
    <x-marketFooter></x-marketFooter>
@endsection
@section('page-script')
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length >
                            0,
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
                        `<li class="breadcrumb-item"><a href="{{ route('market.produk') }}" class="text-decoration-none">Product</a></li>`;
                    breadcrumbHtml +=
                        `<li class="breadcrumb-item active" aria-current="page">${searchQuery ? `Pencarian: "${searchQuery}"` : kategoriNama}</li>`;
                } else {
                    breadcrumbHtml += `<li class="breadcrumb-item active" aria-current="page">Product</li>`;
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
