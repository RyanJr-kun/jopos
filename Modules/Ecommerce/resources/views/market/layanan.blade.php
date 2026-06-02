@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Layanan & Bantuan')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-layanan.scss'])
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
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Layanan Kami</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Section: Integritas Layanan -->
    <section id="layanan" class="section-py">
        <div class="container-market">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1558655146-d09347e92766?q=80&w=1964"
                        class="img-fluid rounded-3 shadow-lg" alt="Teknisi Profesional Jo Computer">
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                    <h2 class="fw-bolder display-5 mb-3">Integritas dan Keunggulan Layanan Kami</h2>
                    <p class="lead text-muted">Di Jo Computer, kami tidak hanya menjual produk, kami membangun kepercayaan.
                    </p>
                    <p>Setiap layanan yang kami tawarkan didasari oleh komitmen pada kejujuran, transparansi, dan kepuasan
                        Anda.
                        Kami memahami bahwa perangkat Anda adalah investasi penting. Oleh karena itu, tim teknisi
                        berpengalaman
                        kami menangani setiap tugas, mulai dari perakitan PC impian hingga perbaikan kompleks, dengan
                        ketelitian
                        dan standar kualitas tertinggi. Kami menjamin penggunaan komponen original dan memberikan penjelasan
                        mendetail di setiap langkahnya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Layanan Unggulan -->
    <section id="layanan-unggulan" class="section-py">
        <div class="bg-light py-5">
            <div class="container-market">
                <div class="text-center mb-5" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="fw-bolder">Solusi Teknologi Terpadu</h2>
                    <p class="lead text-muted">Dari perakitan hingga purna jual, kami siap membantu.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="card h-100 border-0 shadow-sm text-center p-4">
                            <div class="feature-icon-small d-inline-flex align-items-center justify-content-center bg-info bg-gradient fs-2 mb-3 mx-auto rounded-3"
                                style="width: 4rem; height: 4rem;">
                                <i class="bx bxl-windows text-white"></i>
                            </div>
                            <h4 class="fw-bold">Perakitan PC Custom</h4>
                            <p class="text-muted ">Wujudkan PC impian Anda sesuai kebutuhan dan budget. Kami bantu pilihkan
                                komponen terbaik dan merakitnya dengan presisi untuk performa maksimal.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="card h-100 border-0 shadow-sm text-center p-4">
                            <div class="feature-icon-small d-inline-flex align-items-center justify-content-center bg-info bg-gradient fs-2 mb-3 mx-auto rounded-3"
                                style="width: 4rem; height: 4rem;">
                                <i class="bx bx-tools text-white"></i>
                            </div>
                            <h4 class="fw-bold">Servis & Upgrade</h4>
                            <p class="text-muted">Performa PC menurun? Kami menyediakan layanan servis hardware, software,
                                dan
                                upgrade komponen untuk mengembalikan performa perangkat Anda.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="800">
                        <div class="card h-100 border-0 shadow-sm text-center p-4">
                            <div class="feature-icon-small d-inline-flex align-items-center justify-content-center bg-info bg-gradient fs-2 mb-3 mx-auto rounded-3"
                                style="width: 4rem; height: 4rem;">
                                <i class="bx bx-headset text-white"></i>
                            </div>
                            <h4 class="fw-bold">Konsultasi Profesional</h4>
                            <p class="text-muted">Bingung memilih spesifikasi? Tim ahli kami siap memberikan rekomendasi dan
                                konsultasi gratis untuk membantu Anda mengambil keputusan terbaik.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: FAQ -->
    <section id="faq" class="section-py">
        <div class="container-market">
            <div class="accordion-1" data-aos="fade-up">
                <div class="container-market">
                    <div class="row my-5">
                        <div class="col-md-6 mx-auto text-center">
                            <h2>Pertanyaan yang Sering Diajukan</h2>
                            <p>Semoga Informasi yang kami sediakan dapat membantu anda, jika anda ingin mengajukan
                                pertanyaan lain
                                silahkan hubungi cs kami.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 mx-auto">
                            <div class="accordion" id="accordionRental">
                                <div class="accordion-item mb-3">
                                    <h5 class="accordion-header" id="headingOne">
                                        <button class="accordion-button border-bottom font-weight-bold collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                            aria-expanded="false" aria-controls="collapseOne">
                                            Bagaimana Cara Memesan Product atau Jasa?
                                            <i class="collapse-close bx bx-plus text-xs pt-1 position-absolute end-0 me-3"
                                                aria-hidden="true"></i>
                                            <i class="collapse-open bx bx-minus text-xs pt-1 position-absolute end-0 me-3"
                                                aria-hidden="true"></i>
                                        </button>
                                    </h5>
                                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                        data-bs-parent="#accordionRental" style="">
                                        <div class="accordion-body text-sm opacity-8">
                                            Halaman ini sudah memuat detail terkait layanan yang tersedia di JO Computer,
                                            <br>apabila anda ingin melakukan pembelian produk atau jasa:
                                            <ol class="mt-3">
                                                <li>Anda bisa <b>memastikan produk</b> yang anda inginkan tersedia <b>di
                                                        Etalase</b>
                                                    Product JO Computer</li>
                                                <li>Lihat detail produk yang anda inginkan dan pastikan sesuai dengan yang
                                                    anda
                                                    harapkan</li>
                                                <li>Anda bisa menghubungi CS untuk pembelian online dengan media Whatsapp
                                                </li>
                                                <li>Pembayaran dilakukan sebelum barang dikirim atau diterima di tempat</li>
                                            </ol>
                                            Anda bisa memesan produk bersamaan dengan jasa sekaligus dalam satu transaksi.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-3">
                                    <h5 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button border-bottom font-weight-bold collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            Berapa lama proses servis atau perakitan PC?
                                            <i class="collapse-close bx bx-plus text-xs pt-1 position-absolute end-0 me-3"
                                                aria-hidden="true"></i>
                                            <i class="collapse-open bx bx-minus text-xs pt-1 position-absolute end-0 me-3"
                                                aria-hidden="true"></i>
                                        </button>
                                    </h5>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="headingTwo" data-bs-parent="#accordionRental">
                                        <div class="accordion-body text-sm opacity-8">
                                            Estimasi waktu pengerjaan sangat bervariasi tergantung pada type layanan:
                                            <ul>
                                                <li><b>Perakitan PC Custom:</b> Biasanya memakan waktu 1-2 hari kerja
                                                    setelah semua
                                                    komponen tersedia. Kami akan melakukan perakitan, manajemen kabel,
                                                    instalasi
                                                    sistem operasi, dan pengujian (stress test) untuk memastikan PC berjalan
                                                    stabil.
                                                </li>
                                                <li><b>Servis Hardware/Software:</b> Untuk masalah ringan seperti instalasi
                                                    ulang
                                                    atau pembersihan virus, biasanya bisa selesai dalam 1 hari kerja. Untuk
                                                    kerusakan hardware yang memerlukan diagnosis mendalam atau pemesanan
                                                    komponen
                                                    pengganti, waktu bisa lebih lama dan akan kami informasikan terlebih
                                                    dahulu.
                                                </li>
                                            </ul>
                                            Kami selalu mengutamakan kualitas dan ketelitian untuk hasil terbaik.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-3">
                                    <h5 class="accordion-header" id="headingThree">
                                        <button class="accordion-button border-bottom font-weight-bold collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            Bagaimana kebijakan garansi, komplain, dan pengembalian produk?
                                            <i class="collapse-close bx bx-plus text-xs pt-1 position-absolute end-0 me-3"
                                                aria-hidden="true"></i>
                                            <i class="collapse-open bx bx-minus text-xs pt-1 position-absolute end-0 me-3"
                                                aria-hidden="true"></i>
                                        </button>
                                    </h5>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionRental">
                                        <div class="accordion-body text-sm opacity-8">
                                            Ketentuan Komplain & Penukaran Product
                                            <ol>
                                                <li>Sangat disarankan bagi pembeli/penerima untuk men-disinfektan paket dan
                                                    kemudian
                                                    merekam video saat pembukaan paket</li>
                                                <li>Mohon perhatian dan pengertian bahwa video ini diperlukan sebagai bukti
                                                    saat ada
                                                    keluhan mengenai pesanan anda. Tanpa adanya bukti video ini, akan sangat
                                                    sulit
                                                    untuk kami bisa memproses keluhan/permintaan anda.</li>
                                                <li>Silakan memberikan rating di toko kami, setelah kendala terselesaikan.
                                                </li>
                                            </ol>
                                            Barang yang TIDAK dapat ditukar atau dikembalikan, apabila :
                                            <ol>
                                                <li>Warna : Pada waktu dipesan tidak info warna, sehingga yang dikirim
                                                    adalah
                                                    random.</li>
                                                <li>Model : Untuk beberapa barang tertentu yang diinfo akan dikirim
                                                    model/motif
                                                    random, barang tidak dapat ditukar.</li>
                                                <li>Rusak karena salah pakai: Rusak karena salah pakai dari sisi pembeli,
                                                    tidak
                                                    dapat ditukar.</li>
                                                <li>Berubah Pikiran : Maaf, apabila pembeli berubah pikiran, barang tidak
                                                    dapat
                                                    ditukar. Setiap penjualan adalah final dan tidak dapat dicancel/retur.
                                                </li>
                                            </ol>
                                            Barang yang DAPAT ditukar kembali :
                                            <ol>
                                                <li>Barang Tertukar. Karena kesalahan dari pihak ekspedisi atau kesalahan
                                                    dari kami,
                                                    barang dapat tertukar (human error), barang dapat dikembalikan dan
                                                    ditukar
                                                    dengan barang yang seharusnya. Ongkos kirim akan ditanggung oleh kami
                                                    atau pihak
                                                    ekspedisi.</li>
                                                <li>Barang RUSAK pada waktu pengiriman. Apabila diterima dalam kondisi rusak
                                                    harap
                                                    segera konfirmasi dalam 24 jam dengan mengirim foto paket dan barang
                                                    yang rusak
                                                    di pusat resolusi. Apabila tidak melampirkan bukti2 di atas, maka kami
                                                    tidak
                                                    dapat mengganti barang.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Call to Action -->
    <section id="actionCall" class="section-py">
        <div class="container-market" data-aos="fade-up">
            <div class="p-5 text-center bg-body-tertiary rounded-3">
                <!-- <div class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-success bg-gradient fs-2 rounded-3"
                                                                                                        style="width: 3rem; height: 3rem;">
                                                                                                        <i class="bx bxl-whatsapp icon-xl text-white"></i>
                                                                                                    </div> -->
                <h3 class="text-body-emphasis fw-bold">Punya Pertanyaan atau Butuh Bantuan?</h3>
                <p class="col-lg-8 mx-auto fs-5 text-muted">
                    Jangan ragu untuk menghubungi kami. Tim kami siap membantu Anda dengan solusi teknologi yang tepat.
                </p>
                <div class="d-inline-flex gap-2 mb-5">
                    <a href="https://wa.me/6281318000699" target="_blank"
                        class="btn btn-success btn-lg px-4 rounded-pill" type="button">
                        <i class="bx bxl-whatsapp me-2"></i> Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         Section: Ulasan Pelanggan
         ─────────────────────────────────────────────────────────────
         Ulasan bersumber dari data statis. Untuk menambah sumber baru
         (Tokopedia, Instagram, dll.), cukup tambahkan entri baru pada
         array $reviewSources di bawah dengan key source yang berbeda.
         ═══════════════════════════════════════════════════════════════ --}}
    @php
        // ── Data Ulasan ──────────────────────────────────────────────
        // Setiap item bisa memiliki:
        //   source   : 'google' | 'tokopedia' | 'instagram' | 'tiktok' | ...
        //   name     : nama reviewer
        //   avatar   : inisial atau URL foto (gunakan inisial jika tidak ada foto)
        //   rating   : 1–5
        //   date     : tanggal ulasan
        //   text     : isi ulasan
        //   verified : (opsional) apakah pembelian terverifikasi

        $reviewSources = [
            [
                'source' => 'google',
                'name' => 'Ardi Prasetyo',
                'avatar' => 'AP',
                'rating' => 5,
                'date' => '2 minggu lalu',
                'text' =>
                    'Pelayanannya ramah dan profesional. Proses servis laptop saya cepat, hasilnya memuaskan. Harga juga sangat transparan, tidak ada biaya tersembunyi. Sangat rekomendasikan!',
                'verified' => false,
            ],
            [
                'source' => 'google',
                'name' => 'Siti Rahmania',
                'avatar' => 'SR',
                'rating' => 5,
                'date' => '1 bulan lalu',
                'text' =>
                    'Beli RAM laptop di sini, langsung dipasangkan gratis sama teknisinya. Harga kompetitif, barang original bergaransi. Toko yang sangat amanah!',
                'verified' => false,
            ],
            [
                'source' => 'google',
                'name' => 'Dicky Firmansyah',
                'avatar' => 'DF',
                'rating' => 5,
                'date' => '3 minggu lalu',
                'text' =>
                    'Udah langganan di sini buat kebutuhan IT kantor. Stok lengkap, respon cepat via WhatsApp, pengiriman aman. Mantap jiwa!',
                'verified' => false,
            ],
            [
                'source' => 'google',
                'name' => 'Rizky Amalia',
                'avatar' => 'RA',
                'rating' => 5,
                'date' => '2 bulan lalu',
                'text' =>
                    'Cukup puas dengan pelayanannya. Laptopku yang bermasalah berhasil diperbaiki dalam waktu 2 hari. Tinggal tingkatkan kecepatan estimasi waktu agar lebih akurat.',
                'verified' => false,
            ],
            [
                'source' => 'google',
                'name' => 'Hendri Santoso',
                'avatar' => 'HS',
                'rating' => 5,
                'date' => '5 bulan lalu',
                'text' =>
                    'Tempat terpercaya untuk beli komponen PC. Toko bersih, staf berpengetahuan luas, bisa konsultasi dulu sebelum beli. Puas banget belanja di sini!',
                'verified' => false,
            ],
            [
                'source' => 'google',
                'name' => 'Dewi Lestari',
                'avatar' => 'DL',
                'rating' => 5,
                'date' => '1 minggu lalu',
                'text' =>
                    'Servis keyboard laptop saya selesai dalam sehari. Teknisinya ahli dan sabar menjelaskan masalah. Harga servis juga wajar. Jadi pelanggan tetap deh!',
                'verified' => false,
            ],
        ];

        // ── Statistik ringkasan (untuk badge Google-style) ──────────
        $totalReviews = count($reviewSources);
        $avgRating = round(array_sum(array_column($reviewSources, 'rating')) / $totalReviews, 1);
        $googleRating = 4.9; // Rating di Google Maps (statis)
        $googleTotal = 127; // Total ulasan di Google Maps (statis)
    @endphp

    <section id="reviews" class="review-section">
        <div class="container-market">

            {{-- ── Header ──────────────────────────────────────────── --}}
            <div class="review-section-header">
                <div>
                    <h3 class="review-section-title"><span class="tg-red-blue">ULASAN PELANGGAN</span></h3>
                    <p class="review-section-subtitle">Apa kata mereka tentang kami</p>
                </div>
                <a href="https://www.google.com/maps/search/JO+Computer" target="_blank" rel="noopener"
                    class="review-gmaps-badge">
                    <span class="review-gmaps-badge__logo">
                        <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                fill="#4285F4" />
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                fill="#34A853" />
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                fill="#FBBC05" />
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                fill="#EA4335" />
                        </svg>
                    </span>
                    <span class="review-gmaps-badge__info">
                        <span class="review-gmaps-badge__rating">{{ $googleRating }}</span>
                        <span class="review-gmaps-badge__stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bx bxs-star{{ $i <= floor($googleRating) ? '' : ($i - 0.5 <= $googleRating ? '-half' : '') }}"
                                    style="color:#FBBC05;font-size:11px;"></i>
                            @endfor
                        </span>
                        <span class="review-gmaps-badge__count">{{ $googleTotal }} ulasan</span>
                    </span>
                    <i class="bx bx-link-external review-gmaps-badge__arrow"></i>
                </a>
            </div>

            {{-- ── Swiper Carousel ──────────────────────────────────── --}}
            <div class="review-swiper-wrapper">
                <div class="swiper myReviewSwiper">
                    <div class="swiper-wrapper pb-3">
                        @foreach ($reviewSources as $review)
                            <div class="swiper-slide h-auto">
                                <div class="review-card">

                                    {{-- Source badge --}}
                                    <div class="review-card__source review-card__source--{{ $review['source'] }}">
                                        @if ($review['source'] === 'google')
                                            <svg width="14" height="14" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                                    fill="#4285F4" />
                                                <path
                                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                                    fill="#34A853" />
                                                <path
                                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                                    fill="#FBBC05" />
                                                <path
                                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                                    fill="#EA4335" />
                                            </svg>
                                            <span>Google</span>
                                        @elseif ($review['source'] === 'tokopedia')
                                            <i class="bx bx-store" style="color:#03AC0E;font-size:14px;"></i>
                                            <span>Tokopedia</span>
                                        @elseif ($review['source'] === 'instagram')
                                            <i class="bx bxl-instagram" style="font-size:14px;"></i>
                                            <span>Instagram</span>
                                        @else
                                            <i class="bx bx-chat" style="font-size:14px;"></i>
                                            <span>{{ ucfirst($review['source']) }}</span>
                                        @endif
                                    </div>

                                    {{-- Rating stars --}}
                                    <div class="review-card__stars">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <i class="bx bxs-star{{ $s <= $review['rating'] ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>

                                    {{-- Review text --}}
                                    <p class="review-card__text">"{{ $review['text'] }}"</p>

                                    {{-- Reviewer info --}}
                                    <div class="review-card__footer">
                                        <div class="review-card__avatar" data-initials="{{ $review['avatar'] }}">
                                            {{ $review['avatar'] }}
                                        </div>
                                        <div class="review-card__author">
                                            <span class="review-card__name">{{ $review['name'] }}</span>
                                            <span class="review-card__date">{{ $review['date'] }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Navigation --}}
                    <div class="review-swiper-prev"><i class="bx bx-chevron-left"></i></div>
                    <div class="review-swiper-next"><i class="bx bx-chevron-right"></i></div>
                    <div class="swiper-pagination review-pagination"></div>
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
            once: true,
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper === 'undefined') return;
            // ---------------------------------------------------------
            // 7. Review / Ulasan Pelanggan Swiper
            // ---------------------------------------------------------
            if (document.querySelector('.myReviewSwiper')) {
                new Swiper('.myReviewSwiper', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    grabCursor: true,
                    autoplay: {
                        delay: 4500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    navigation: {
                        nextEl: '.review-swiper-next',
                        prevEl: '.review-swiper-prev',
                    },
                    pagination: {
                        el: '.review-pagination',
                        clickable: true,
                    },
                    speed: 600,
                    breakpoints: {
                        576: {
                            slidesPerView: 2
                        },
                        992: {
                            slidesPerView: 3
                        },
                    },
                });
            }
        });
    </script>
@endsection
