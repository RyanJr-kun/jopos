@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Beranda - JO Computer')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/swiper/swiper.scss')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/swiper/swiper.js')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')

    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    {{-- section : Hero --}}
    <section id="landingHero" class="py-3 bg-white">
        <div class="container-market">
            @if ($mainImg->isNotEmpty())
                <div class="hero-grid-layout">
                    <div class="hero-main-banner w-100 h-100 rounded-3 overflow-hidden shadow-sm">
                        <div class="swiper myHeroSwiper w-100 h-100">
                            <div class="swiper-wrapper">
                                @foreach ($mainImg as $b)
                                    <div class="swiper-slide w-100 h-100">
                                        <a href="{{ $b->url_tujuan ?? '#' }}" class="d-block w-100 h-100">
                                            <img src="{{ asset('storage/' . $b->img_banner) }}" class="w-100 h-100"
                                                style="object-fit: cover;" alt="Main Banner">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>

                    <div class="d-flex flex-lg-column flex-row gap-2 gap-lg-3 w-100 h-100">
                        <div class="hero-side-banner flex-fill rounded-3 overflow-hidden shadow-sm">
                            <div class="swiper myHeroSwiper w-100 h-100">
                                <div class="swiper-wrapper">
                                    @foreach ($main2Img as $b)
                                        <div class="swiper-slide w-100 h-100">
                                            <a href="{{ $b->url_tujuan ?? '#' }}" class="d-block w-100 h-100">
                                                <img src="{{ asset('storage/' . $b->img_banner) }}"
                                                    class="w-100 h-100 transition-all" style="object-fit: cover;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>

                        <div class="hero-side-banner flex-fill rounded-3 overflow-hidden shadow-sm">
                            <div class="swiper myHeroSwiper w-100 h-100">
                                <div class="swiper-wrapper">
                                    @foreach ($main3Img as $b)
                                        <div class="swiper-slide w-100 h-100">
                                            <a href="{{ $b->url_tujuan ?? '#' }}" class="d-block w-100 h-100">
                                                <img src="{{ asset('storage/' . $b->img_banner) }}"
                                                    class="w-100 h-100 transition-all" style="object-fit: cover;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row flex-lg-row-reverse align-items-center justify-content-center g-3" data-aos="fade-up"
                    data-aos-delay="200">
                    <div class="col-10 col-sm-8 col-lg-6">
                        <img src="https://images.pexels.com/photos/265087/pexels-photo-265087.jpeg"
                            class="d-block mx-lg-auto img-fluid rounded" alt="Fallback Image" width="700" height="500"
                            loading="lazy">
                    </div>
                    <div class="col-lg-6">
                        <h1 class="fw-bold lh-1 mb-3">Selamat Datang di JO-POS Market</h1>
                        <p class="lead">Temukan berbagai macam komponen dan aksesoris komputer berkualitas dengan harga
                            terbaik. Kami menyediakan semua kebutuhan Anda, mulai dari perakitan hingga upgrade.</p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="{{ route('market.produk') }}" type="button" class="btn btn-primary px-4 me-md-2">Lihat
                                Product</a>
                            <a href="https://wa.me/6281318000699" type="button"
                                class="btn btn-outline-secondary btn-sm px-4">Hubungi Kami</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- section : Features --}}
    <section id="landingFeatures" class="py-3 bg-white">
        <div class="container-market" data-aos="fade-up" data-aos-delay="200">
            <div class="text-center mb-4">
                <span class="badge bg-label-primary">Keunggulan Kami</span>
            </div>
            <h4 class="text-center mb-3 lh-1">
                <span class="position-relative fw-extrabold z-1">Kami menyediakan Solusi <br
                        class="d-block d-lg-none"></span>
                untuk kebutuhan teknologi anda
            </h4>
            <p class="text-center mb-12">Hadir sebagai teman konsultasi dan menyediakan produk berkualitas tinggi.</p>

            {{-- Desktop: grid biasa (lg ke atas) --}}
            <div class="features-icon-wrapper row gx-0 gy-6 g-sm-12 d-none d-lg-flex">
                <div class="col-lg-4 col-sm-6 text-center features-icon-box">
                    <div class="text-center mb-4 text-primary">
                        <svg width="64" height="65" viewBox="0 0 64 65" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.2"
                                d="M13.625 50.8413C11.325 48.5413 12.85 43.7163 11.675 40.8913C10.5 38.0663 6 35.5913 6 32.4663C6 29.3413 10.45 26.9663 11.675 24.0413C12.9 21.1163 11.325 16.3913 13.625 14.0913C15.925 11.7913 20.75 13.3163 23.575 12.1413C26.4 10.9663 28.875 6.46631 32 6.46631C35.125 6.46631 37.5 10.9163 40.425 12.1413C43.35 13.3663 48.075 11.7913 50.375 14.0913C52.675 16.3913 51.15 21.2163 52.325 24.0413C53.5 26.8663 58 29.3413 58 32.4663C58 35.5913 53.55 37.9663 52.325 40.8913C51.1 43.8163 52.675 48.5413 50.375 50.8413C48.075 53.1413 43.25 51.6163 40.425 52.7913C37.6 53.9663 35.125 58.4663 32 58.4663C28.875 58.4663 26.5 54.0163 23.575 52.7913C20.65 51.5663 15.925 53.1413 13.625 50.8413Z"
                                fill="currentColor"></path>
                            <path
                                d="M43 26.4663L28.325 40.4663L21 33.4663M13.625 50.8413C11.325 48.5413 12.85 43.7163 11.675 40.8913C10.5 38.0663 6 35.5913 6 32.4663C6 29.3413 10.45 26.9663 11.675 24.0413C12.9 21.1163 11.325 16.3913 13.625 14.0913C15.925 11.7913 20.75 13.3163 23.575 12.1413C26.4 10.9663 28.875 6.46631 32 6.46631C35.125 6.46631 37.5 10.9163 40.425 12.1413C43.35 13.3663 48.075 11.7913 50.375 14.0913C52.675 16.3913 51.15 21.2163 52.325 24.0413C53.5 26.8663 58 29.3413 58 32.4663C58 35.5913 53.55 37.9663 52.325 40.8913C51.1 43.8163 52.675 48.5413 50.375 50.8413C48.075 53.1413 43.25 51.6163 40.425 52.7913C37.6 53.9663 35.125 58.4663 32 58.4663C28.875 58.4663 26.5 54.0163 23.575 52.7913C20.65 51.5663 15.925 53.1413 13.625 50.8413Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </div>
                    <h5 class="mb-2">JO Care</h5>
                    <p class="features-icon-description">Semua produk yang kami jual original dan bergaransi resmi, menjamin
                        kualitas terbaik</p>
                </div>
                <div class="col-lg-4 col-sm-6 text-center features-icon-box">
                    <div class="text-center mb-4 text-primary">
                        <svg width="64" height="65" viewBox="0 0 64 65" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.2"
                                d="M31.9999 8.46631C27.1437 8.46489 22.4012 9.93672 18.399 12.6874C14.3969 15.438 11.3233 19.3381 9.58436 23.8723C7.84542 28.4066 7.52291 33.3617 8.65945 38.0831C9.79598 42.8045 12.3381 47.0701 15.9499 50.3163C17.4549 47.3526 19.7511 44.8636 22.5841 43.125C25.417 41.3864 28.676 40.4662 31.9999 40.4663C30.0221 40.4663 28.0887 39.8798 26.4442 38.781C24.7997 37.6822 23.518 36.1204 22.7611 34.2931C22.0043 32.4659 21.8062 30.4552 22.1921 28.5154C22.5779 26.5756 23.5303 24.7938 24.9289 23.3952C26.3274 21.9967 28.1092 21.0443 30.049 20.6585C31.9888 20.2726 33.9995 20.4706 35.8268 21.2275C37.654 21.9844 39.2158 23.2661 40.3146 24.9106C41.4135 26.5551 41.9999 28.4885 41.9999 30.4663C41.9999 33.1185 40.9464 35.662 39.071 37.5374C37.1956 39.4127 34.6521 40.4663 31.9999 40.4663C35.3238 40.4662 38.5829 41.3864 41.4158 43.125C44.2487 44.8636 46.545 47.3526 48.0499 50.3163C51.6618 47.0701 54.2039 42.8045 55.3404 38.0831C56.477 33.3617 56.1545 28.4066 54.4155 23.8723C52.6766 19.3381 49.603 15.438 45.6008 12.6874C41.5987 9.93672 36.8562 8.46489 31.9999 8.46631Z"
                                fill="currentColor"></path>
                            <path
                                d="M32 40.4663C37.5228 40.4663 42 35.9892 42 30.4663C42 24.9435 37.5228 20.4663 32 20.4663C26.4772 20.4663 22 24.9435 22 30.4663C22 35.9892 26.4772 40.4663 32 40.4663ZM32 40.4663C28.6759 40.4663 25.4168 41.3852 22.5839 43.1241C19.7509 44.863 17.4548 47.3524 15.95 50.3163M32 40.4663C35.3241 40.4663 38.5832 41.3852 41.4161 43.1241C44.2491 44.863 46.5452 47.3524 48.05 50.3163M56 32.4663C56 45.7211 45.2548 56.4663 32 56.4663C18.7452 56.4663 8 45.7211 8 32.4663C8 19.2115 18.7452 8.46631 32 8.46631C45.2548 8.46631 56 19.2115 56 32.4663Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </div>
                    <h5 class="mb-2">Pelayanan Terbaik</h5>
                    <p class="features-icon-description">Kami menyediakan pelayanan yang profesional dan terbaik untuk
                        kepuasan pelanggan.</p>
                </div>
                <div class="col-lg-4 col-sm-6 text-center features-icon-box">
                    <div class="text-center mb-4 text-primary">
                        <svg width="64" height="65" viewBox="0 0 64 65" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.2"
                                d="M52.575 9.44123L5.97499 22.5662C5.57831 22.6747 5.2247 22.9028 4.96234 23.2195C4.69997 23.5361 4.54161 23.926 4.50881 24.3359C4.47602 24.7459 4.57039 25.1559 4.77907 25.5103C4.98775 25.8647 5.3006 26.1461 5.67499 26.3162L27.075 36.4412C27.4942 36.6354 27.8309 36.972 28.025 37.3912L38.15 58.7912C38.3201 59.1656 38.6016 59.4785 38.9559 59.6872C39.3103 59.8958 39.7204 59.9902 40.1303 59.9574C40.5402 59.9246 40.9301 59.7662 41.2468 59.5039C41.5634 59.2415 41.7915 58.8879 41.9 58.4912L55.025 11.8912C55.1245 11.5512 55.1306 11.1906 55.0428 10.8474C54.955 10.5041 54.7765 10.1908 54.5259 9.94028C54.2754 9.68975 53.9621 9.51123 53.6189 9.42342C53.2756 9.33562 52.9151 9.34177 52.575 9.44123Z"
                                fill="currentColor"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M53.8666 8.45462C53.3513 8.32282 52.8102 8.33156 52.2995 8.47988L52.2942 8.48144L5.71115 21.6016L5.70701 21.6028C5.11366 21.7659 4.5848 22.1076 4.19216 22.5815C3.79862 23.0565 3.56107 23.6413 3.51188 24.2562C3.46268 24.8711 3.60424 25.4862 3.91726 26.0177C4.22884 26.5468 4.69522 26.9675 5.25338 27.2231L26.6472 37.3452L26.6472 37.3452L26.6546 37.3486C26.8589 37.4432 27.0229 37.6072 27.1175 37.8115L27.1174 37.8115L27.1209 37.8189L37.243 59.2126C37.4985 59.7708 37.9192 60.2372 38.4484 60.5488C38.9799 60.8619 39.595 61.0034 40.2099 60.9542C40.8248 60.905 41.4096 60.6675 41.8846 60.2739C42.3586 59.8813 42.7002 59.3524 42.8634 58.759L42.8645 58.755L55.9847 12.1719L55.9862 12.1668C56.1346 11.656 56.1433 11.1149 56.0115 10.5996C55.8792 10.0825 55.6103 9.61055 55.2329 9.23317C54.8556 8.85579 54.3836 8.58688 53.8666 8.45462ZM52.846 10.4038L52.5749 9.44123L52.8556 10.401C53.0235 10.3519 53.2015 10.3489 53.3709 10.3922C53.5404 10.4356 53.695 10.5237 53.8187 10.6474C53.9424 10.7711 54.0305 10.9257 54.0739 11.0952C54.1172 11.2646 54.1142 11.4426 54.0651 11.6105L54.065 11.6105L54.0623 11.6201L40.9373 58.2201L40.9353 58.2275C40.8811 58.4258 40.767 58.6026 40.6087 58.7338C40.4503 58.865 40.2554 58.9442 40.0504 58.9606C39.8455 58.977 39.6404 58.9298 39.4632 58.8255C39.2861 58.7211 39.1454 58.5647 39.0603 58.3775L39.0538 58.3635L28.9323 36.971L28.9303 36.9667C28.9285 36.9629 28.9268 36.9591 28.925 36.9553L39.732 26.1483C40.1225 25.7578 40.1225 25.1246 39.732 24.7341C39.3415 24.3436 38.7083 24.3436 38.3178 24.7341L27.5108 35.5411C27.5069 35.5393 27.503 35.5375 27.4991 35.5357L6.10255 25.4123L6.0886 25.4058C5.9014 25.3208 5.74498 25.18 5.64064 25.0029C5.53629 24.8257 5.48911 24.6206 5.50551 24.4157C5.5219 24.2107 5.60109 24.0158 5.73227 23.8574C5.86345 23.6991 6.04025 23.5851 6.2386 23.5308L6.2386 23.5309L6.24598 23.5288L52.846 10.4038Z"
                                fill="currentColor"></path>
                        </svg>
                    </div>
                    <h5 class="mb-2">Proses Cepat</h5>
                    <p class="features-icon-description">Kemudahan Berbelanja, pembayaran yang fleksibel, dan pengiriman
                        cepat.</p>
                </div>
            </div>

            {{-- Mobile & Tablet: Swiper 1 slide (< lg) --}}
            <div class="d-lg-none">
                <div class="swiper myFeaturesSwiper pb-4">
                    <div class="swiper-wrapper">

                        {{-- Slide 1: JO Care --}}
                        <div class="swiper-slide text-center features-icon-box px-3">
                            <div class="text-center mb-4 text-primary">
                                <svg width="64" height="65" viewBox="0 0 64 65" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.2"
                                        d="M13.625 50.8413C11.325 48.5413 12.85 43.7163 11.675 40.8913C10.5 38.0663 6 35.5913 6 32.4663C6 29.3413 10.45 26.9663 11.675 24.0413C12.9 21.1163 11.325 16.3913 13.625 14.0913C15.925 11.7913 20.75 13.3163 23.575 12.1413C26.4 10.9663 28.875 6.46631 32 6.46631C35.125 6.46631 37.5 10.9163 40.425 12.1413C43.35 13.3663 48.075 11.7913 50.375 14.0913C52.675 16.3913 51.15 21.2163 52.325 24.0413C53.5 26.8663 58 29.3413 58 32.4663C58 35.5913 53.55 37.9663 52.325 40.8913C51.1 43.8163 52.675 48.5413 50.375 50.8413C48.075 53.1413 43.25 51.6163 40.425 52.7913C37.6 53.9663 35.125 58.4663 32 58.4663C28.875 58.4663 26.5 54.0163 23.575 52.7913C20.65 51.5663 15.925 53.1413 13.625 50.8413Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M43 26.4663L28.325 40.4663L21 33.4663M13.625 50.8413C11.325 48.5413 12.85 43.7163 11.675 40.8913C10.5 38.0663 6 35.5913 6 32.4663C6 29.3413 10.45 26.9663 11.675 24.0413C12.9 21.1163 11.325 16.3913 13.625 14.0913C15.925 11.7913 20.75 13.3163 23.575 12.1413C26.4 10.9663 28.875 6.46631 32 6.46631C35.125 6.46631 37.5 10.9163 40.425 12.1413C43.35 13.3663 48.075 11.7913 50.375 14.0913C52.675 16.3913 51.15 21.2163 52.325 24.0413C53.5 26.8663 58 29.3413 58 32.4663C58 35.5913 53.55 37.9663 52.325 40.8913C51.1 43.8163 52.675 48.5413 50.375 50.8413C48.075 53.1413 43.25 51.6163 40.425 52.7913C37.6 53.9663 35.125 58.4663 32 58.4663C28.875 58.4663 26.5 54.0163 23.575 52.7913C20.65 51.5663 15.925 53.1413 13.625 50.8413Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <h5 class="mb-2">JO Care</h5>
                            <p class="features-icon-description">Semua produk yang kami jual original dan bergaransi resmi,
                                menjamin kualitas terbaik</p>
                        </div>

                        {{-- Slide 2: Pelayanan Terbaik --}}
                        <div class="swiper-slide text-center features-icon-box px-3">
                            <div class="text-center mb-4 text-primary">
                                <svg width="64" height="65" viewBox="0 0 64 65" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.2"
                                        d="M31.9999 8.46631C27.1437 8.46489 22.4012 9.93672 18.399 12.6874C14.3969 15.438 11.3233 19.3381 9.58436 23.8723C7.84542 28.4066 7.52291 33.3617 8.65945 38.0831C9.79598 42.8045 12.3381 47.0701 15.9499 50.3163C17.4549 47.3526 19.7511 44.8636 22.5841 43.125C25.417 41.3864 28.676 40.4662 31.9999 40.4663C30.0221 40.4663 28.0887 39.8798 26.4442 38.781C24.7997 37.6822 23.518 36.1204 22.7611 34.2931C22.0043 32.4659 21.8062 30.4552 22.1921 28.5154C22.5779 26.5756 23.5303 24.7938 24.9289 23.3952C26.3274 21.9967 28.1092 21.0443 30.049 20.6585C31.9888 20.2726 33.9995 20.4706 35.8268 21.2275C37.654 21.9844 39.2158 23.2661 40.3146 24.9106C41.4135 26.5551 41.9999 28.4885 41.9999 30.4663C41.9999 33.1185 40.9464 35.662 39.071 37.5374C37.1956 39.4127 34.6521 40.4663 31.9999 40.4663C35.3238 40.4662 38.5829 41.3864 41.4158 43.125C44.2487 44.8636 46.545 47.3526 48.0499 50.3163C51.6618 47.0701 54.2039 42.8045 55.3404 38.0831C56.477 33.3617 56.1545 28.4066 54.4155 23.8723C52.6766 19.3381 49.603 15.438 45.6008 12.6874C41.5987 9.93672 36.8562 8.46489 31.9999 8.46631Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M32 40.4663C37.5228 40.4663 42 35.9892 42 30.4663C42 24.9435 37.5228 20.4663 32 20.4663C26.4772 20.4663 22 24.9435 22 30.4663C22 35.9892 26.4772 40.4663 32 40.4663ZM32 40.4663C28.6759 40.4663 25.4168 41.3852 22.5839 43.1241C19.7509 44.863 17.4548 47.3524 15.95 50.3163M32 40.4663C35.3241 40.4663 38.5832 41.3852 41.4161 43.1241C44.2491 44.863 46.5452 47.3524 48.05 50.3163M56 32.4663C56 45.7211 45.2548 56.4663 32 56.4663C18.7452 56.4663 8 45.7211 8 32.4663C8 19.2115 18.7452 8.46631 32 8.46631C45.2548 8.46631 56 19.2115 56 32.4663Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <h5 class="mb-2">Pelayanan Terbaik</h5>
                            <p class="features-icon-description">Kami menyediakan pelayanan yang profesional dan terbaik
                                untuk kepuasan pelanggan.</p>
                        </div>

                        {{-- Slide 3: Proses Cepat --}}
                        <div class="swiper-slide text-center features-icon-box px-3">
                            <div class="text-center mb-4 text-primary">
                                <svg width="64" height="65" viewBox="0 0 64 65" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.2"
                                        d="M52.575 9.44123L5.97499 22.5662C5.57831 22.6747 5.2247 22.9028 4.96234 23.2195C4.69997 23.5361 4.54161 23.926 4.50881 24.3359C4.47602 24.7459 4.57039 25.1559 4.77907 25.5103C4.98775 25.8647 5.3006 26.1461 5.67499 26.3162L27.075 36.4412C27.4942 36.6354 27.8309 36.972 28.025 37.3912L38.15 58.7912C38.3201 59.1656 38.6016 59.4785 38.9559 59.6872C39.3103 59.8958 39.7204 59.9902 40.1303 59.9574C40.5402 59.9246 40.9301 59.7662 41.2468 59.5039C41.5634 59.2415 41.7915 58.8879 41.9 58.4912L55.025 11.8912C55.1245 11.5512 55.1306 11.1906 55.0428 10.8474C54.955 10.5041 54.7765 10.1908 54.5259 9.94028C54.2754 9.68975 53.9621 9.51123 53.6189 9.42342C53.2756 9.33562 52.9151 9.34177 52.575 9.44123Z"
                                        fill="currentColor"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M53.8666 8.45462C53.3513 8.32282 52.8102 8.33156 52.2995 8.47988L52.2942 8.48144L5.71115 21.6016L5.70701 21.6028C5.11366 21.7659 4.5848 22.1076 4.19216 22.5815C3.79862 23.0565 3.56107 23.6413 3.51188 24.2562C3.46268 24.8711 3.60424 25.4862 3.91726 26.0177C4.22884 26.5468 4.69522 26.9675 5.25338 27.2231L26.6472 37.3452L26.6546 37.3486C26.8589 37.4432 27.0229 37.6072 27.1175 37.8115L27.1209 37.8189L37.243 59.2126C37.4985 59.7708 37.9192 60.2372 38.4484 60.5488C38.9799 60.8619 39.595 61.0034 40.2099 60.9542C40.8248 60.905 41.4096 60.6675 41.8846 60.2739C42.3586 59.8813 42.7002 59.3524 42.8634 58.759L42.8645 58.755L55.9847 12.1719L55.9862 12.1668C56.1346 11.656 56.1433 11.1149 56.0115 10.5996C55.8792 10.0825 55.6103 9.61055 55.2329 9.23317C54.8556 8.85579 54.3836 8.58688 53.8666 8.45462ZM52.846 10.4038L52.5749 9.44123L52.8556 10.401C53.0235 10.3519 53.2015 10.3489 53.3709 10.3922C53.5404 10.4356 53.695 10.5237 53.8187 10.6474C53.9424 10.7711 54.0305 10.9257 54.0739 11.0952C54.1172 11.2646 54.1142 11.4426 54.0651 11.6105L54.0623 11.6201L40.9373 58.2201L40.9353 58.2275C40.8811 58.4258 40.767 58.6026 40.6087 58.7338C40.4503 58.865 40.2554 58.9442 40.0504 58.9606C39.8455 58.977 39.6404 58.9298 39.4632 58.8255C39.2861 58.7211 39.1454 58.5647 39.0603 58.3775L39.0538 58.3635L28.9323 36.971L28.9303 36.9667L27.5108 35.5411C27.5069 35.5393 27.503 35.5375 27.4991 35.5357L6.10255 25.4123L6.0886 25.4058C5.9014 25.3208 5.74498 25.18 5.64064 25.0029C5.53629 24.8257 5.48911 24.6206 5.50551 24.4157C5.5219 24.2107 5.60109 24.0158 5.73227 23.8574C5.86345 23.6991 6.04025 23.5851 6.2386 23.5308L6.24598 23.5288L52.846 10.4038Z"
                                        fill="currentColor"></path>
                                </svg>
                            </div>
                            <h5 class="mb-2">Proses Cepat</h5>
                            <p class="features-icon-description">Kemudahan Berbelanja, pembayaran yang fleksibel, dan
                                pengiriman cepat.</p>
                        </div>

                    </div>
                    {{-- Pagination dots --}}
                    <div class="swiper-pagination features-pagination"></div>
                </div>
            </div>

        </div>
    </section>

    {{-- section : Category --}}
    <section id="category" class="section-py bg-white">
        <div class="container-market">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bolder tg-blue-v2 mb-0">JELAJAHI KATEGORI</h3>
                <a href="{{ route('market.produk') }}" class="btn btn-outline-secondary px-2"> <i
                        class="bx bx-category"></i>
                    <span class="d-none d-lg-block ms-2">Lihat Semua</span>
                </a>
            </div>
            <div class="position-relative category-scroll-wrapper">
                <div class="swiper myCategorySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($kategoris as $kategori)
                            <div class="swiper-slide">
                                <a href="{{ route('market.produk', ['kategori' => $kategori->slug]) }}"
                                    class="text-decoration-none text-dark">
                                    <div class="card category-card overflow-hidden">
                                        <img src="{{ $kategori->img_kategori ? asset('storage/' . $kategori->img_kategori) : asset('assets/img/produk.png') }}"
                                            class="card-img-top" alt="{{ $kategori->name }}">
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title fw-bold text-truncate mb-1" title="{{ $kategori->name }}">
                                            {{ $kategori->name }}</h6>
                                        {{-- <p class="card-text text-muted small">{{ $kategori->products_count }} Product</p> --}}
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tombol navigasi --}}
                    <div class="swiper-button-prev category-nav-btn"></div>
                    <div class="swiper-button-next category-nav-btn"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- section : Promo --}}
    @if ($promoImg->isNotEmpty() || $produkPromotion->isNotEmpty())
        <section id="promotions" class="py-3">
            <div class="container-market">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bolder  mb-0"><span class="tg-red-blue">PROMO TERBATAS</span></h3>
                    <a href="{{ route('market.produk') }}" class="btn btn-outline-secondary px-2">
                        <i class="bx bx-category"></i>
                        <span class="d-none d-lg-block ms-2">Lihat Semua</span>
                    </a>
                </div>
                <div class="row g-4 align-items-center">
                    @if ($promoImg->isNotEmpty())
                        <div class="col-lg-3 d-none d-lg-flex">
                            <div class="swiper myPromoSwiper promo-card rounded-3 overflow-hidden">
                                <div class="swiper-wrapper">
                                    @foreach ($promoImg as $banner)
                                        <div class="swiper-slide">
                                            <a href="{{ $banner->url_tujuan ?? '#' }}" class="d-block w-100 h-100">
                                                <img src="{{ asset('storage/' . $banner->img_banner) }}"
                                                    class="d-block w-100 h-100" style="object-fit: cover;"
                                                    alt="{{ $banner->judul ?? 'Promotion' }}">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="{{ $promoImg->isNotEmpty() ? 'col-lg-9' : 'col-11' }}">
                        @if ($produkPromotion->isNotEmpty())
                            <div class="swiper myPromoProductSwiper"
                                data-lg-slides="{{ $promoImg->isNotEmpty() ? 3 : 5 }}">
                                <div class="swiper-wrapper py-2">
                                    @foreach ($produkPromotion as $produk)
                                        <div class="swiper-slide h-auto">
                                            {{-- Cukup 1 Card Container di sini --}}
                                            <div class="card product-card overflow-hidden h-100 d-flex flex-column">

                                                <div class="promo-produk promo-produk-lg">
                                                    <a
                                                        href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                                        <img src="{{ $produk->primaryImage
                                                            ? asset('storage/' . $produk->primaryImage->path)
                                                            : ($produk->img_produk
                                                                ? asset('storage/' . $produk->img_produk)
                                                                : asset('assets/img/produk.png')) }}"
                                                            alt="{{ $produk->name_product }}" loading="eager"
                                                            class="card-img-top" alt="{{ $produk->name_product }}">

                                                        @if ($produk->qty < 1)
                                                            <div class="product-badge">
                                                                <span class="badge bg-danger">Habis</span>
                                                            </div>
                                                        @elseif($produk->active_promotion)
                                                            @php $promo = $produk->active_promotion @endphp
                                                            <div class="product-badge">
                                                                @if ($promo->type == 'percentage')
                                                                    <span
                                                                        class="badge bg-danger">{{ (int) $promo->nilai_diskon }}%
                                                                        OFF</span>
                                                                @else
                                                                    <span class="badge bg-info">PROMO</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="product-badge">
                                                                <span
                                                                    class="badge bg-warning fw-bolder rounded-4">Populer</span>
                                                            </div>
                                                        @endif
                                                    </a>

                                                    <div class="product-card-actions">
                                                        @if ($produk->qty > 0)
                                                            <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                                                                target="_blank" class="btn btn-dark w-100">
                                                                <i class="bx bxl-whatsapp me-1"></i> Pesan via WA
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-dark w-100">Stock
                                                                Habis</button>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="card-body border-top py-2">
                                                    <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                                        class="text-decoration-none text-dark text-hover">
                                                        <p class="card-title fw-bold text-truncate"
                                                            title="{{ $produk->name_product }}">
                                                            {{ $produk->name_product }}
                                                        </p>
                                                    </a>
                                                    <div class="mt-auto">
                                                        @if ($produk->harga_diskon)
                                                            <div>
                                                                <span
                                                                    class="text-muted text-decoration-line-through product-price-old">
                                                                    {{ $produk->harga_formatted }}</span>
                                                                <span class="fw-bold product-price-current text-hover">
                                                                    {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</span>
                                                            </div>
                                                        @else
                                                            <div>
                                                                <span
                                                                    class="fw-bold mb-0 product-price-current text-hover">
                                                                    {{ $produk->harga_formatted }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div> {{-- Penutup card --}}
                                        </div> {{-- Penutup swiper-slide --}}
                                    @endforeach
                                </div>

                                {{-- Tombol Navigasi Swiper --}}
                                {{-- <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div> --}}
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <p class="text-muted text-center">Saat ini belum ada produk promo.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- section : Bestseller --}}
    @if (
        $bestsellerImg->isNotEmpty() ||
            (isset($bestsellerMobileImg) && $bestsellerMobileImg->isNotEmpty()) ||
            $produkTerlaris->isNotEmpty())
        <section id="best-seller" class="py-3">
            <div class="container-market">

                {{-- Banner Bestseller (Swiper) --}}
                <div class="row mb-4">
                    <div class="col-12">

                        {{-- 1. Banner Desktop (Tampil di Desktop, Sembunyi di Tab & Mobile) --}}
                        @if ($bestsellerImg->isNotEmpty())
                            <div class="swiper myBestsellerSwiper rounded-3 shadow-sm d-none d-lg-block">
                                <div class="swiper-wrapper">
                                    @foreach ($bestsellerImg as $banner)
                                        <div class="swiper-slide">
                                            <a href="{{ $banner->url_tujuan ?? route('market.produk') }}">
                                                <img src="{{ asset('storage/' . $banner->img_banner) }}" loading="lazy"
                                                    class="d-block w-100 h-100" style="object-fit: cover;"
                                                    alt="{{ $banner->judul ?? 'Bestseller Banner' }}">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                            </div>
                        @endif

                        {{-- 2. Banner Tablet & Mobile (Tampil di Tab & Mobile, Sembunyi di Desktop) --}}
                        @if (isset($bestsellerMobileImg) && $bestsellerMobileImg->isNotEmpty())
                            <div class="swiper myBestsellerSwiperMobile rounded-3 shadow-sm d-block d-lg-none">
                                <div class="swiper-wrapper">
                                    @foreach ($bestsellerMobileImg as $bannerMobile)
                                        <div class="swiper-slide">
                                            <a href="{{ $bannerMobile->url_tujuan ?? route('market.produk') }}">
                                                <img src="{{ asset('storage/' . $bannerMobile->img_banner) }}"
                                                    loading="lazy" class="d-block w-100 h-100" style="object-fit: cover;"
                                                    alt="{{ $bannerMobile->judul ?? 'Bestseller Banner Mobile' }}">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Grid Produk Terlaris (Swiper) — tampil jika ada produk --}}
                @if ($produkTerlaris->isNotEmpty())
                    <div class="swiper myBestsellerProductSwiper {{ $bestsellerImg->isNotEmpty() ? 'mt-0' : 'mt-2' }}">
                        <div class="swiper-wrapper py-2">
                            @foreach ($produkTerlaris as $produk)
                                <div class="swiper-slide h-auto">
                                    <div class="card product-card overflow-hidden h-100 d-flex flex-column">
                                        <div class="product-card-img-container">
                                            <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                                <img src="{{ $produk->primaryImage
                                                    ? asset('storage/' . $produk->primaryImage->path)
                                                    : ($produk->img_produk
                                                        ? asset('storage/' . $produk->img_produk)
                                                        : asset('assets/img/produk.png')) }}"
                                                    loading="eager" class="card-img-top"
                                                    alt="{{ $produk->name_product }}">

                                                @if ($produk->qty < 1)
                                                    <div class="product-badge">
                                                        <span class="badge bg-danger fw-bolder rounded-4">Habis</span>
                                                    </div>
                                                @elseif($produk->active_promotion)
                                                    @php $promo = $produk->active_promotion @endphp
                                                    <div class="product-badge">
                                                        @if ($promo->type == 'percentage')
                                                            <span
                                                                class="badge bg-danger">{{ (int) $promo->nilai_diskon }}%
                                                                OFF</span>
                                                        @else
                                                            <span class="badge bg-info">PROMO</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="product-badge">
                                                        <span class="badge bg-warning fw-bolder rounded-4">Populer</span>
                                                    </div>
                                                @endif
                                            </a>
                                            <div class="product-card-actions">
                                                @if ($produk->qty > 0)
                                                    <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                                                        target="_blank" class="btn btn-dark w-100">
                                                        <i class="bx bxl-whatsapp me-1"></i> Pesan via WA
                                                    </a>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-light fw-bold w-100">Habis</button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-body border-top py-2">
                                            <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                                class="text-dark text-decoration-none">
                                                <p class="fw-bold product-title" title="{{ $produk->name_product }}">
                                                    {{ $produk->name_product }}
                                                </p>
                                            </a>

                                            <div class="mt-auto">
                                                @if ($produk->harga_diskon)
                                                    <div>
                                                        <span
                                                            class="text-muted text-decoration-line-through product-price-old">
                                                            {{ $produk->harga_formatted }}</span>
                                                        <span class="fw-bold product-price-current text-hover">
                                                            {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</span>
                                                    </div>
                                                @else
                                                    <div>
                                                        <span class="fw-bold mb-0 product-price-current text-hover">
                                                            {{ $produk->harga_formatted }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- Penutup swiper-slide --}}
                            @endforeach
                        </div>

                        {{-- Tombol Navigasi Swiper --}}
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                @endif

            </div>
        </section>
    @endif

    {{-- section : Produk Terbaru --}}
    <section id="product" class="section-py">
        <div class="container-market">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bolder tg-red mb-0">PRODUK TERBARU</h3>
                <a href="{{ route('market.produk') }}" class="btn btn-outline-secondary px-2"> <i
                        class="bx bx-category"></i>
                    <span class="d-none d-lg-block ms-2">Lihat Semua</span></a>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                @foreach ($products as $produk)
                    <div class="col">
                        <div class="card product-card overflow-hidden h-100 d-flex flex-column">
                            <div class="product-card-img-container">
                                <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}">
                                    <img src="{{ $produk->primaryImage
                                        ? asset('storage/' . $produk->primaryImage->path)
                                        : ($produk->img_produk
                                            ? asset('storage/' . $produk->img_produk)
                                            : asset('assets/img/produk.png')) }}"
                                        alt="{{ $produk->name_product }}" loading="eager" class="card-img-top"
                                        alt="{{ $produk->name_product }}">
                                    @if ($produk->qty < 1)
                                        <div class="product-badge">
                                            <span class="badge bg-danger fw-bold rounded-4">Habis</span>
                                        </div>
                                    @elseif($produk->active_promotion)
                                        @php $promo = $produk->active_promotion @endphp
                                        <div class="product-badge">
                                            @if ($promo->type == 'percentage')
                                                <span class="badge bg-danger">{{ (int) $promo->nilai_diskon }}% OFF</span>
                                            @else
                                                <span class="badge bg-info">PROMO</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="product-badge">
                                            <span class="badge bg-primary fw-bold rounded-4">Baru</span>
                                        </div>
                                    @endif
                                </a>
                                <div class="product-card-actions">
                                    @if ($produk->qty > 0)
                                        <a href="https://wa.me/6281318000699?text=Halo, saya tertarik dengan produk: {{ $produk->name_product }}"
                                            target="_blank" class="btn btn-dark w-100">
                                            <i class="bx bxl-whatsapp me-1"></i> Pesan via WA
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-dark w-100">Stock Habis</button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body border-top py-2">
                                <a href="{{ route('market.produk.detail', ['slug' => $produk->slug]) }}"
                                    class="text-decoration-none text-dark">
                                    <p class="product-title fw-bold" title="{{ $produk->name_product }}">
                                        {{ $produk->name_product }}</p>
                                </a>
                                @if ($produk->harga_diskon)
                                    <div>
                                        <span class="text-muted text-decoration-line-through product-price-old">
                                            {{ $produk->harga_formatted }}</span>
                                        <span class="fw-bold product-price-current text-hover">
                                            {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <div>
                                        <span class="fw-bold mb-0 product-price-current text-hover">
                                            {{ $produk->harga_formatted }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                {{-- Akhir loop produk --}}
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
    {{-- Tambahkan script ini di @section('page-script') --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper === 'undefined') return;

            // ---------------------------------------------------------
            // 1. Hero Swiper (sudah ada sebelumnya)
            // ---------------------------------------------------------
            if (document.querySelector('.myHeroSwiper')) {
                new Swiper('.myHeroSwiper', {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: '.myHeroSwiper .swiper-pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.myHeroSwiper .swiper-button-next',
                        prevEl: '.myHeroSwiper .swiper-button-prev',
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                });
            }

            // ---------------------------------------------------------
            // 2. Kategori Swiper
            // ---------------------------------------------------------
            if (document.querySelector('.myCategorySwiper')) {
                new Swiper('.myCategorySwiper', {
                    slidesPerView: 2,
                    spaceBetween: 0,
                    loop: true,
                    autoplay: {
                        delay: 2500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    navigation: {
                        nextEl: '.myCategorySwiper .swiper-button-next',
                        prevEl: '.myCategorySwiper .swiper-button-prev',
                    },
                    speed: 600,
                    grabCursor: true,
                    breakpoints: {
                        480: {
                            slidesPerView: 3
                        },
                        768: {
                            slidesPerView: 4
                        },
                        992: {
                            slidesPerView: 6
                        },
                        1200: {
                            slidesPerView: 7
                        },
                    },
                });
            }

            // ---------------------------------------------------------
            // 3. Promo  Swiper — autoplay, no nav, loop
            // ---------------------------------------------------------
            if (document.querySelector('.myPromoSwiper')) {
                new Swiper('.myPromoSwiper', {
                    loop: true,
                    direction: 'vertical', // geser vertikal agar kesan berbeda
                    autoplay: {
                        delay: 7000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    speed: 800,
                });
            }

            // ---------------------------------------------------------
            // 5. Promo Produk Slider (1 Baris Horizontal)
            // ---------------------------------------------------------
            const promoProductContainer = document.querySelector('.myPromoProductSwiper');
            if (promoProductContainer) {
                // Ambil jumlah slide untuk layar besar dari atribut data HTML
                let lgSlides = promoProductContainer.getAttribute('data-lg-slides') || 5;

                new Swiper('.myPromoProductSwiper', {
                    slidesPerView: 1, // Di HP tampil 1.5 card agar user tahu bisa di-swipe
                    spaceBetween: 16, // Jarak antar card (setara dengan class g-3 di Bootstrap)
                    grabCursor: true,
                    navigation: {
                        nextEl: '.myPromoProductSwiper .swiper-button-next',
                        prevEl: '.myPromoProductSwiper .swiper-button-prev',
                    },
                    breakpoints: {
                        576: {
                            slidesPerView: 2, // Layar sm
                        },
                        768: {
                            slidesPerView: 3, // Layar md
                        },
                        992: {
                            slidesPerView: parseInt(lgSlides), // Layar lg (3 atau 5 tergantung banner)
                        }
                    }
                });
            }

            // ---------------------------------------------------------
            // 6. Bestseller Produk Slider (1 Baris Horizontal)
            // ---------------------------------------------------------
            if (document.querySelector('.myBestsellerProductSwiper')) {
                new Swiper('.myBestsellerProductSwiper', {
                    slidesPerView: 1, // Tampilan default di layar HP terkecil
                    spaceBetween: 16,
                    grabCursor: true,
                    navigation: {
                        nextEl: '.myBestsellerProductSwiper .swiper-button-next',
                        prevEl: '.myBestsellerProductSwiper .swiper-button-prev',
                    },
                    breakpoints: {
                        576: {
                            slidesPerView: 2, // Layar smartphone agak besar (sm)
                        },
                        768: {
                            slidesPerView: 3, // Layar tablet (md)
                        },
                        992: {
                            slidesPerView: 5, // Layar laptop/PC (lg) - selalu 5 kolom
                        }
                    }
                });
            }
            // ---------------------------------------------------------
            // 4. Bestseller Banner Swiper — autoplay, nav + pagination
            // ---------------------------------------------------------
            // ---------------------------------------------------------
            // 4. Bestseller Banner Swiper — autoplay, nav + pagination
            // ---------------------------------------------------------
            // Versi Desktop
            if (document.querySelector('.myBestsellerSwiper')) {
                new Swiper('.myBestsellerSwiper', {
                    loop: true,
                    autoplay: {
                        delay: 6000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: '.myBestsellerSwiper .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.myBestsellerSwiper .swiper-button-next',
                        prevEl: '.myBestsellerSwiper .swiper-button-prev',
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    speed: 700,
                });
            }

            // Versi Mobile / Tablet (TAMBAHKAN KODE INI)
            if (document.querySelector('.myBestsellerSwiperMobile')) {
                new Swiper('.myBestsellerSwiperMobile', {
                    loop: true,
                    autoplay: {
                        delay: 6000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: '.myBestsellerSwiperMobile .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.myBestsellerSwiperMobile .swiper-button-next',
                        prevEl: '.myBestsellerSwiperMobile .swiper-button-prev',
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    speed: 700,
                });
            }



        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper === 'undefined') return;

            // Hanya inisialisasi jika layar < lg (992px)
            // Swiper tetap di-init tapi hanya aktif di mobile
            if (document.querySelector('.myFeaturesSwiper')) {
                new Swiper('.myFeaturesSwiper', {
                    slidesPerView: 1,
                    loop: true,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: '.myFeaturesSwiper .features-pagination',
                        clickable: true,
                    },
                    speed: 600,
                    grabCursor: true,
                    // Nonaktifkan di desktop — biarkan CSS d-lg-none yang handle visibilitas
                    breakpoints: {
                        992: {
                            // Di lg ke atas, matikan autoplay & loop
                            // (wrapper sudah d-none via CSS jadi tidak terlihat)
                            autoplay: false,
                            loop: false,
                        }
                    }
                });
            }
        });
    </script>
@endsection
