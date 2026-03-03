<footer class="footer-market bg-dark text-white pt-5 pb-4">
    <div class="container text-md-left">
        <div class="row text-md-left">

            {{-- Kolom 1: Tentang Toko --}}
            <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/logomiring_putih.svg') }}" alt="Logo Jo Computer Putih"
                        style="height: 80px;">
                </a>
                <p class="mt-3" style="font-size: 0.9rem;">
                    Toko komponen dan aksesoris komputer terpercaya. Kami menyediakan produk berkualitas dengan harga
                    terbaik untuk kebutuhan perakitan dan upgrade PC Anda.
                </p>
                {{-- Alamat Pusat Solo --}}
                <p class="small d-flex align-items-start mb-2">
                    <i class="bx bx-geo-alt-fill me-2 flex-shrink-0"></i>
                    <span><strong>JO Computer Solo</strong> (Pusat)<br>Jl. Slamet Riyadi Somodinalan No.250,
                        Somodinatan, Ngadirejo, Kec. Kartasura, Kabupaten Sukoharjo</span>
                </p>
                {{-- Alamat Cabang Baturetno --}}
                <p class="small d-flex align-items-start">
                    <i class="bx bx-geo-alt-fill me-2 flex-shrink-0"></i>
                    <span><strong>JO Computer Baturetno</strong> (Cabang)<br>Jl. Raya Baturetno-Batuwarno, Batu Lor,
                        Baturetno, Kec. Baturetno, Kabupaten Wonogiri</span>
                </p>
                <p class="small"><i class="bx bx-envelope-fill me-2"></i>cs@jocomputer.com</p>
                <p class="small"><i class="bx bx-telephone-fill me-2"></i>081318000699</p>
            </div>

            {{-- Kolom 2: Tautan Cepat --}}
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold text-info">Tautan Cepat</h6>
                <p><a href="{{ url('/') }}" class="footer-link">Beranda</a></p>
                <p><a href="{{ route('market.produk') }}" class="footer-link">Produk</a></p>
                <p><a href="{{ route('market.layanan') }}" class="footer-link">Tentang Kami</a></p>
                <p><a href="{{ route('market.tentang') }}" class="footer-link">Kontak</a></p>
            </div>

            {{-- Kolom 3: Kategori --}}
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold text-info">Kategori</h6>
                @if (isset($bestSellingCategories) && $bestSellingCategories->isNotEmpty())
                    @foreach ($bestSellingCategories as $kategori)
                        <p><a href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}"
                                class="footer-link">{{ $kategori->nama }}</a></p>
                    @endforeach
                @else
                    <p class="small text-muted">Kategori belum tersedia.</p>
                @endif
            </div>

            {{-- Kolom 4: Media Sosial --}}
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold text-info">Ikuti Kami</h6>
                <div class="d-flex mb-4">
                    <a href="https://www.facebook.com/jo.comp.798/" class="social-icon social-facebook me-3"
                        title="Facebook"><i class="bx bx-facebook"></i></a>
                    <a href="https://www.instagram.com/jocompsolo?utm_source=ig_web_button_share_sheet&igsh=b3J2dXFxMmV5Zml1"
                        class="social-icon social-instagram me-3" title="Instagram"><i class="bx bx-instagram"></i></a>
                    <a href="https://tokopedia.link/KTBDV7zZmXb" class="social-icon social-tokopedia me-3"
                        title="Tokopedia"><i class="bx bx-shop"></i></a>
                    <a href="https://www.tiktok.com/@jocomputer.official?is_from_webapp=1&sender_device=pc"
                        class="social-icon social-tiktok" title="TikTok"><i class="bx bx-tiktok"></i></a>
                </div>
                <h6 class="text-uppercase mb-3 font-weight-bold text-info">Jam Operasional</h6>
                <p class="small mb-1">Senin - Sabtu: <br><i class="bx bx-clock-fill me-2"></i>08.00 - 16.30 WIB</p>
                <p class="small mb-1">Minggu: <br><i class="bx bx-clock-fill me-2"></i>09.00 - 17.00 WIB</p>
            </div>
        </div>

        <hr class="mb-4 mt-2 opacity-50">

        <div class="row align-items-center justify-content-center">
            <div class="col-12">
                <p class="text-center mb-3 mb-md-0 small">
                    © {{ date('Y') }} Copyright
                    <a href="{{ url('/') }}" class="text-info fw-bold text-decoration-none">JO Computer</a>.
                    All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
