<footer class="footer-market text-white pt-5 pb-4">
    <div class="container-market pb-md-2">
        <div class="d-flex justify-content-center justify-content-md-start align-items-center mt-3">
            <a class="navbar-brand d-inline-block mb-3" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/LM-Putih.webp') }}" alt="Logo Jo Computer Putih"
                    class="img-fluid footer-logo">
            </a>
        </div>
        <div class="row">
            {{-- Kolom 1: Tentang Toko (Diberi porsi 5 kolom & padding kanan) --}}
            <div class="col-12 col-lg-5 mb-4 mb-lg-0 mt-lg-3 pe-lg-5">
                <p style="font-size: 0.9rem;" class="mb-4 text-white-50">
                    Toko komponen dan aksesoris komputer terpercaya. Kami menyediakan produk berkualitas dengan harga
                    terbaik untuk kebutuhan perakitan dan upgrade PC Anda.
                </p>
                <p class="small d-flex align-items-start mb-2">
                    <i class="bx bxs-map me-2 flex-shrink-0 mt-1 text-white"></i>
                    <span><strong>JO Computer</strong><br>Jl. Slamet Riyadi Somodinalan No.250,
                        Somodinatan, Ngadirejo, Kec. Kartasura, Kabupaten Sukoharjo, Jawa Tengah 57169</span>
                </p>
                <div class="d-flex flex-column gap-2 mb-4 mb-lg-0">
                    <a href="mailto:cs@jocomputer.com"
                        class="small text-white text-decoration-none email-hover d-inline-flex align-items-center"><i
                            class="bx bxs-envelope me-2 text-white"></i>cs@jocomputer.com</a>
                    <a href="tel:081318000699"
                        class="small text-white text-decoration-none phone-hover d-inline-flex align-items-center"><i
                            class="bx bxs-phone me-2 text-white"></i>081318000699</a>
                </div>
            </div>

            {{-- Kolom 2: Tautan Cepat (2 Kolom) --}}
            <div class="col-6 col-lg-2 mt-lg-3">
                <h6 class="text-uppercase mb-4 font-weight-bold text-white fs-6">Tautan Cepat</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ url('/') }}" class="link link-white small">Beranda</a>
                    <a href="{{ route('market.produk') }}" class="link link-white small">Product</a>
                    <a href="{{ route('market.layanan') }}" class="link link-white small">Layanan</a>
                    <a href="{{ route('market.tentang') }}" class="link link-white small">Tentang Kami</a>
                </div>
            </div>

            {{-- Kolom 3: Kategori (2 Kolom) --}}
            <div class="col-6 col-lg-2 mt-lg-3">
                <h6 class="text-uppercase mb-4 font-weight-bold text-white fs-6">Kategori</h6>
                <div class="d-flex flex-column gap-2">
                    @if (isset($bestSellingCategories) && $bestSellingCategories->isNotEmpty())
                        @foreach ($bestSellingCategories as $kategori)
                            <a href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}"
                                class="link link-white small">{{ $kategori->name }}</a>
                        @endforeach
                    @else
                        <span class="small text-muted">Kategori belum tersedia.</span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-3 ms-lg-auto mt-5 mt-lg-3 ">
                <h6 class="text-uppercase mb-4 font-weight-bold text-white fs-6">Ikuti Kami</h6>
                <div class="d-flex gap-3 mb-4 flex-wrap justify-content-start">
                    <a href="https://www.facebook.com/jo.comp.798/" target="_blank" rel="noopener noreferrer"
                        class="social-icon social-facebook" title="Facebook"><i class="bx bxl-facebook icon-md"></i></a>
                    <a href="https://www.instagram.com/jocompsolo?utm_source=ig_web_button_share_sheet&igsh=b3J2dXFxMmV5Zml1"
                        target="_blank" rel="noopener noreferrer" class="social-icon social-instagram"
                        title="Instagram"><i class="bx bxl-instagram icon-md"></i></a>
                    <a href="https://tokopedia.link/KTBDV7zZmXb" target="_blank" rel="noopener noreferrer"
                        class="social-icon social-tokopedia" title="Tokopedia"><i class="bx bx-store"></i></a>
                    <a href="https://www.tiktok.com/@jocomputer.official?is_from_webapp=1&sender_device=pc"
                        target="_blank" rel="noopener noreferrer" class="social-icon social-tiktok" title="TikTok"><i
                            class="bx bxl-tiktok i icon-md"></i></a>
                </div>

                <h6 class="text-uppercase mb-3 font-weight-bold text-white fs-6">Jam Operasional</h6>
                <div class="d-flex flex-column gap-2 align-items-start">
                    <p class="small d-flex align-items-start mb-0">
                        <i class="bx bxs-time me-2 mt-1 text-white"></i>
                        <span>Senin - Sabtu: 08.00 - 16.30 WIB</span>
                    </p>
                    <p class="small d-flex align-items-start mb-0">
                        <i class="bx bxs-time me-2 mt-1 text-white"></i>
                        <span>Minggu: 09.00 - 17.00 WIB</span>
                    </p>
                </div>
            </div>
        </div>

        <hr class="mb-4 mt-5 opacity-25">

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="copyright text-center text-md-start mb-2 mb-md-0">
                © {{ date('Y') }} <strong>JO Computer</strong>. All Rights Reserved.
            </div>
            <div class="credit text-center text-md-end text-muted fs-14px">
                Theme designed by <a href="https://themeselection.com" target="_blank"
                    class="text-decoration-none">ThemeSelection</a>
            </div>
        </div>
    </div>
</footer>
