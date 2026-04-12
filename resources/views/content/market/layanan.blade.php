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
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Layanan Kami</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Section: Integritas Layanan -->
    <section id="layanan" class="section-py">
        <div class="container">
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
            <div class="container">
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
        <div class="container">
            <div class="accordion-1" data-aos="fade-up">
                <div class="container">
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
        <div class="container" data-aos="fade-up">
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

    <x-marketFooter></x-marketFooter>
@endsection

@section('page-script')
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
@endsection
