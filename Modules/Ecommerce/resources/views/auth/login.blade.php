@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Login')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-log-reg.scss'])
@endsection

@section('layoutContent')
    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    <section class="auth-page my-0">
        <div class="auth-card" data-aos="fade-up" data-aos-duration="700">

            {{-- ── Left Decorative Panel ──────────────────────────── --}}
            <div class="auth-panel">
                <div class="auth-panel__inner-circle"></div>

                {{-- Logo --}}
                <div class="auth-panel__logo">
                    JO Computer
                </div>

                {{-- Main content --}}
                <div class="auth-panel__content">
                    <div class="auth-panel__eyebrow">
                        <span>Selamat Datang</span>
                    </div>
                    <h2 class="auth-panel__heading">
                        Masuk dan<br>Mulai Belanja
                    </h2>
                    <p class="auth-panel__tagline">
                        Akses Produk &amp; Layanan kami dengan pilihan harga terbaik.
                    </p>
                </div>
            </div>

            {{-- ── Form Side ──────────────────────────────────────── --}}
            <div class="auth-form-side">

                {{-- Header --}}
                <div class="auth-header">
                    <h1 class="auth-header__title">Login</h1>
                    <p class="auth-header__sub">Masukkan kredensial akun Anda untuk melanjutkan.</p>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="auth-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('customer.login.post') }}" method="POST" class="auth-form">
                    @csrf

                    {{-- Email --}}
                    <div class="auth-field">
                        <label for="email" class="auth-field__label">Alamat Email</label>
                        <input type="email" id="email" name="email"
                            class="auth-input @error('email') auth-input--error @enderror" placeholder="nama@email.com"
                            value="{{ old('email') }}" required autofocus>
                    </div>

                    {{-- Password --}}
                    <div class="auth-field">
                        <div class="auth-field__label-row">
                            <label for="password" class="auth-field__label">Password</label>
                            <a href="{{ route('customer.password.request') }}" class="auth-field__forgot">
                                Lupa password?
                            </a>
                        </div>
                        <div class="auth-field__input-wrap auth-field__input-wrap--group">
                            <input type="password" id="password" name="password"
                                class="auth-input @error('password') auth-input--error @enderror" placeholder="••••••••"
                                required>
                            <button type="button" class="auth-field__toggle" id="togglePassword"
                                aria-label="Tampilkan password">
                                <i class="bx bx-hide" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="auth-field" style="margin-top: 0.25rem;">
                        <label class="auth-check">
                            <input type="checkbox" name="remember" id="remember-me">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="auth-btn">
                        Masuk ke Akun
                    </button>
                </form>

                {{-- Divider --}}
                <div class="auth-divider">
                    <span>atau lanjutkan dengan</span>
                </div>

                {{-- Google OAuth --}}
                <a href="{{ route('customer.google.login') }}" class="auth-btn-google">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                    Masuk dengan Google
                </a>

                {{-- Register Link --}}
                <p class="auth-footer-link">
                    Belum punya akun?
                    <a href="{{ route('customer.register') }}">Daftar sekarang</a>
                </p>

            </div>
        </div>
    </section>

    <x-market-footer></x-market-footer>
@endsection

@section('page-script')
    <script>
        AOS.init({
            duration: 700,
            once: true,
            easing: 'ease-out-cubic'
        });

        // Password toggle
        const toggleBtn = document.getElementById('togglePassword');
        const passwordEl = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const isHidden = passwordEl.type === 'password';
                passwordEl.type = isHidden ? 'text' : 'password';
                toggleIcon.className = isHidden ? 'bx bx-show' : 'bx bx-hide';
            });
        }
    </script>
@endsection
