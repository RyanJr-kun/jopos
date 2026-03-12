<x-marketLayout>
    {{-- Breadcrumb --}}
    <div class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Section: Tentang Jo Computer -->
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <!-- Ganti 'src' dengan gambar toko atau tim Anda -->
                <img src="{{ asset('assets/img/toko.webp') }}" class="img-fluid rounded-3 shadow-lg"
                    alt="Toko Jo Computer">
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                <h2 class="fw-bolder display-5 mb-3">Selamat Datang di Jo Computer</h2>
                <p class="lead text-muted">Lebih dari sekadar toko, kami adalah partner teknologi Anda.</p>
                <p>Sejak berdiri, Jo Computer berkomitmen untuk menyediakan solusi teknologi terlengkap bagi para
                    antusias PC, gamer, dan profesional di Solo dan sekitarnya. Kami percaya bahwa setiap orang berhak
                    mendapatkan akses ke komponen komputer berkualitas dengan harga yang jujur dan layanan yang prima.
                    Dari perakitan PC impian hingga upgrade sederhana, tim kami siap membantu Anda.</p>
            </div>
        </div>
    </div>

    <!-- Section: Tentang Aplikasi Jo-POS -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2" data-aos="fade-up" data-aos-delay="600">
                    <!-- Ganti 'src' dengan screenshot atau logo aplikasi Jo-POS -->
                    <img src="https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=1931"
                        class="img-fluid rounded-3 shadow-lg" alt="Aplikasi Jo-POS">
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
    </div>

    <!-- Section: Lokasi & Kontak -->
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up" data-aos-delay="300">
            <h2 class="fw-bolder">Kunjungi & Hubungi Kami</h2>
            <p class="lead text-muted">Kami siap menyambut Anda di toko atau menjawab pertanyaan Anda secara online.</p>
        </div>
        <div class="row g-4">
            <!-- Google Maps -->
            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="400">
                <div class="ratio ratio-16x9 rounded-3 shadow-sm overflow-hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.1238542545925!2d110.75378237591431!3d-7.561472674674966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a14f83e78f24b%3A0x76f6f20de70e8d57!2sJO%20Computer!5e0!3m2!1sen!2sid!4v1760027885984!5m2!1sen!2sid"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        class="border-0"></iframe>
                </div>
            </div>
            <!-- Informasi Kontak -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="600">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-4">
                            <i class="bx bx-geo-alt-fill fs-4 text-info me-3"></i>
                            <div><strong class="d-block">Alamat</strong>{{ $profils->alamat ?? 'Alamat belum diatur.' }}
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-4">
                            <i class="bx bx-telephone-fill fs-4 text-info me-3"></i>
                            <div><strong class="d-block">Whatsapp</strong><a
                                    href="https://wa.me/{{ $profils->telepon ?? '' }}" target="_blank"
                                    class="text-decoration-none text-dark">{{ $profils->telepon ?? 'Nomor belum diatur.' }}</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="bx bx-envelope-fill fs-4 text-info me-3"></i>
                            <div><strong class="d-block">Email</strong><a href="mailto:{{ $profils->email ?? '' }}"
                                    class="text-decoration-none text-dark">{{ $profils->email ?? 'Email belum diatur.' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-marketLayout>
