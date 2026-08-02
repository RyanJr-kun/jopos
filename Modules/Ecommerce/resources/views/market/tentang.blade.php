@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Tentang Kami')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')

    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>
    {{-- Breadcrumb --}}
    <div class="bg-white py-3">
        <div class="container-market">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Section: Tentang Jo Computer -->
    <section id="tentangJoComputer" class="section-py bg-white">
        <div class="container-market">
            <div class="row align-items-start g-5">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <img src="{{ asset('assets/img/toko.webp') }}" class="img-fluid rounded-3 shadow-lg"
                        alt="Toko Jo Computer" width="800" height="533" loading="eager" fetchpriority="high">
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                    <h2 class="mb-3">Selamat Datang di <span class="tg-red-blue fw-bolder">JO Computer</span></h1>
                        <p class="lead text-muted">Lebih dari sekadar toko, kami adalah partner teknologi Anda.</p>
                        <p>Sejak berdiri, Jo Computer berkomitmen untuk menyediakan solusi teknologi terlengkap bagi para
                            antusias PC, gamer, dan profesional di Solo dan sekitarnya. Kami percaya bahwa setiap orang
                            berhak
                            mendapatkan akses ke komponen komputer berkualitas dengan harga yang jujur dan layanan yang
                            prima.
                            Dari perakitan PC impian hingga upgrade sederhana, tim kami siap membantu Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Tentang Aplikasi Jo-POS -->
    <section id="supportSistem" class="section-py bg-white">
        <div class="container-market">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2" data-aos="fade-up" data-aos-delay="600">
                    <!-- Ganti 'src' dengan screenshot atau logo aplikasi Jo-POS -->
                    <img src="https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=1931"
                        class="img-fluid rounded-3 shadow-lg" alt="Aplikasi Jo-POS" width="800" height="533" loading="lazy">
                </div>
                <div class="col-lg-6 order-lg-1" data-aos="fade-up" data-aos-delay="800">
                    <h2 class="fw-bolder display-5 mb-3">Didukung oleh JO-POS</h2>
                    <p class="lead text-muted">Inovasi di balik layar untuk pengalaman belanja terbaik.</p>
                    <p>Website yang sedang Anda jelajahi ini dibangun di atas <strong>JO-POS</strong>, sebuah sistem
                        Point of Sale dan manajemen inventaris yang kami kembangkan sendiri. Aplikasi ini adalah wujud
                        dari dedikasi kami pada efisiensi dan teknologi, memungkinkan kami untuk mengelola stok secara
                        akurat, memproses transaksi dengan cepat, dan memberikan Anda pengalaman belanja online yang
                        lancar dan terintegrasi.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="location" class="section-py">
        <div class="container-market">
            <div class="text-start mb-5" data-aos="fade-up" data-aos-delay="300">
                <h4 class="fw-bolder mb-0">Lokasi Toko Kami</h4>
                <p class="lead text-muted">Temukan toko jocomputer terdekat dengan lokasi anda.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-5" data-aos="fade-up" data-aos-delay="600">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-transparent p-4 border-bottom">
                            <form action="" method="GET" class="mb-0" id="form-pencarian-toko">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-sm fw-bold text-muted">Cari Lokasi Toko</h6>
                                    <div>
                                        <span class="badge bg-label-danger fw-bolder" id="badge-daerah">Semua Kota</span>
                                        <span class="badge bg-label-danger fw-bolder"><span
                                                id="toko-count">{{ count($stores) }}</span> Toko</span>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-8 col-12">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                                            <input type="search" id="search-toko" name="search"
                                                class="form-control js-search-input" placeholder="Cari nama toko..."
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <select id="filter-daerah" name="daerah" class="form-select select2"
                                            data-placeholder="Kota">
                                            <option value="" selected>Semua Kota</option>
                                            @foreach ($stores as $d)
                                                <option value="{{ $d->kecamatan }}">{{ $d->kecamatan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <div id="loading-indicator" class="text-center mt-3" style="display: none;">
                                <small class="text-muted"><i class="bx bx-loader-alt bx-spin"></i> Memuat data...</small>
                            </div>
                        </div>

                        <div class="list-group list-group-flush overflow-auto" id="wadah-toko" style="max-height: 500px;">
                            @include('ecommerce::market._list_toko', ['stores' => $stores])
                        </div>
                    </div>
                </div>

                {{-- Ganti bagian ini di tentang.blade.php --}}
                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="400">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm h-100">
                        @php
                            // Ambil toko pertama untuk default view
                            $firstStore = $stores->first();
                            $defaultSrc = 'https://www.google.com/maps/embed?pb=...'; // Fallback jika tidak ada data sama sekali

                            if ($firstStore) {
                                $defaultSrc = $firstStore->map_url;
                                // Pastikan URL memiliki parameter output=embed agar bisa tampil di iframe
                                if (!str_contains($defaultSrc, 'output=embed')) {
                                    $defaultSrc .= (str_contains($defaultSrc, '?') ? '&' : '?') . 'output=embed';
                                }
                            }
                        @endphp
                        <iframe id="map-iframe" src="{{ $defaultSrc }}" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" class="border-0">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-market-footer></x-market-footer>
@endsection

@section('page-script')
    <script>
        function updateMapByCoords(el) {
            const mapUrl = el.getAttribute('data-map-url');
            const lat = el.getAttribute('data-lat');
            const lng = el.getAttribute('data-lng');
            const iframe = document.getElementById('map-iframe');

            // 1. Prioritas Utama: Gunakan map_url yang sudah ada di database
            if (mapUrl && mapUrl !== '' && mapUrl !==
                'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.1238542545925!2d110.75378237591431!3d-7.561472674674966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a14f83e78f24b%3A0x76f6f20de70e8d57!2sJO%20Computer!5e0!3m2!1sen!2sid!4v1760027885984!5m2!1sen!2sid'
            ) {
                let embedUrl = mapUrl;
                // Pastikan formatnya adalah embed
                if (!embedUrl.includes('output=embed')) {
                    embedUrl += (embedUrl.includes('?') ? '&' : '?') + 'output=embed';
                }
                iframe.src = embedUrl;
            }
            // 2. Fallback: Gunakan koordinat jika map_url tidak valid
            else if (lat && lng && lat !== '' && lng !== '') {
                const coordUrl = `https://maps.google.com/maps?q=${lat},${lng}&hl=id&z=15&output=embed`;
                iframe.src = coordUrl;
            }
        }
        // --- 2. Fungsi AJAX ---
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-toko');
            const wadahToko = document.getElementById('wadah-toko');
            const countDisplay = document.getElementById('toko-count');
            const loadingIndicator = document.getElementById('loading-indicator');
            const badgeDaerah = document.getElementById('badge-daerah');

            let timeoutId;

            function debounceAjax() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    fetchData();
                }, 300);
            }

            async function fetchData() {
                const searchValue = searchInput.value;
                const daerahValue = $('#filter-daerah').val(); // Pakai jQuery karena select2

                loadingIndicator.style.display = 'block';
                wadahToko.style.opacity = '0.5';

                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', searchValue);
                    url.searchParams.set('daerah', daerahValue);

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Jaringan bermasalah');

                    const data = await response.json();
                    wadahToko.innerHTML = data.html;
                    countDisplay.textContent = data.count;

                    const firstItem = wadahToko.querySelector('.toko-item');
                    if (firstItem) {
                        updateMapByCoords(firstItem);
                    }

                } catch (error) {
                    console.error('Error fetching data:', error);
                } finally {
                    loadingIndicator.style.display = 'none';
                    wadahToko.style.opacity = '1';
                }
            }

            // Event Listener Input Text
            searchInput.addEventListener('input', debounceAjax);

            // Event Listener Select2 (Harus via jQuery on change)
            $('#filter-daerah').on('change', function() {
                // Update text Badge sesuai opsi yang dipilih
                const selectedText = $(this).find("option:selected").text();
                badgeDaerah.textContent = selectedText;

                fetchData(); // Panggil AJAX
            });
        });
    </script>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>

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
@endsection
