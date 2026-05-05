@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Daftar Akun - JO Computer')

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
        <div class="auth-card auth-card--register" data-aos="fade-up" data-aos-duration="700">

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
                        <span>Bergabung Sekarang</span>
                    </div>
                    <h2 class="auth-panel__heading">
                        Daftar dan<br>
                        Nikmati Promo
                    </h2>
                    <p class="auth-panel__tagline">
                        Buat akun gratis dan dapatkan akses ke penawaran eksklusif serta pelacakan pesanan real-time.
                    </p>
                </div>
            </div>

            {{-- ── Form Side ──────────────────────────────────────── --}}
            <div class="auth-form-side">

                {{-- Header --}}
                <div class="auth-header">
                    <h1 class="auth-header__title">Buat Akun</h1>
                    <p class="auth-header__sub">Isi data di bawah ini untuk membuat akun baru Anda.</p>
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
                <form action="{{ route('customer.register.post') }}" method="POST" class="auth-form">
                    @csrf

                    {{-- Full Name --}}
                    <div class="auth-field">
                        <label for="name" class="auth-field__label">Nama Lengkap</label>
                        <input type="text" id="name" name="name"
                            class="auth-input @error('name') auth-input--error @enderror" placeholder="Nama lengkap Anda"
                            value="{{ old('name') }}" required autofocus>
                    </div>

                    {{-- Email --}}
                    <div class="auth-field">
                        <label for="email" class="auth-field__label">Alamat Email</label>
                        <input type="email" id="email" name="email"
                            class="auth-input @error('email') auth-input--error @enderror" placeholder="nama@email.com"
                            value="{{ old('email') }}" required>
                    </div>

                    {{-- Password --}}
                    <div class="auth-field">
                        <label for="password" class="auth-field__label">Password</label>
                        <div class="auth-field__input-wrap auth-field__input-wrap--group">
                            <input type="password" id="password" name="password"
                                class="auth-input @error('password') auth-input--error @enderror"
                                placeholder="Minimal 8 karakter" required>
                            <button type="button" class="auth-field__toggle" id="togglePassword"
                                aria-label="Tampilkan password">
                                <i class="bx bx-hide" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Password Confirmation --}}
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-field__label">Konfirmasi Password</label>
                        <div class="auth-field__input-wrap auth-field__input-wrap--group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="auth-input" placeholder="Ulangi password Anda" required>
                            <button type="button" class="auth-field__toggle" id="toggleConfirm"
                                aria-label="Tampilkan konfirmasi password">
                                <i class="bx bx-hide" id="toggleIconConfirm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="auth-btn">
                        Buat Akun
                    </button>
                </form>

                {{-- Divider --}}
                <div class="auth-divider">
                    <span>atau daftar dengan</span>
                </div>

                {{-- Google OAuth --}}
                <a href="{{ route('customer.google.login') }}" class="auth-btn-google">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                    Daftar dengan Google
                </a>

                {{-- Login Link --}}
                <p class="auth-footer-link">
                    Sudah punya akun?
                    <a href="{{ route('customer.login') }}">Login di sini</a>
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

        // Password toggle helper
        function makeToggle(btnId, inputId, iconId) {
            const btn = document.getElementById(btnId);
            const inp = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!btn) return;
            btn.addEventListener('click', () => {
                const hidden = inp.type === 'password';
                inp.type = hidden ? 'text' : 'password';
                icon.className = hidden ? 'bx bx-show' : 'bx bx-hide';
            });
        }

        makeToggle('togglePassword', 'password', 'toggleIcon');
        makeToggle('toggleConfirm', 'password_confirmation', 'toggleIconConfirm');
    </script>
@endsection
