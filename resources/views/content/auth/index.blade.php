@extends('layouts/blankLayout')

@section('title', 'Login Karyawan — JOPOS')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
    <div class="el-wrapper">

        {{-- ── Left panel · brand ── --}}
        <aside class="el-panel el-panel--brand">
            <div class="el-brand-inner">

                {{-- Decorative geometry --}}
                <div class="el-geo el-geo--circle"></div>
                <div class="el-geo el-geo--ring"></div>
                <div class="el-accent-bar"></div>

                <div class="el-brand-logo">
                    <img src="{{ asset('assets/img/LM-Default.png') }}" alt="JOPOS Logo" class="el-logo-img" />
                </div>

                <div class="el-brand-copy">
                    <p class="el-brand-eyebrow">Portal Karyawan</p>
                    <h1 class="el-brand-name">JO POS</h1>
                    <div class="el-brand-rule"></div>
                    <p class="el-brand-desc">
                        Sistem manajemen terpadu untuk seluruh operasional tim Anda.
                        Hadir setiap hari, pantau semuanya dalam satu tempat.
                    </p>
                </div>

                <ul class="el-feature-list">
                    <li class="el-feature-item">
                        <span class="el-feature-num">01</span>
                        <span class="el-feature-text">Manajemen Presensi Real-time</span>
                    </li>
                    <li class="el-feature-item">
                        <span class="el-feature-num">02</span>
                        <span class="el-feature-text">Laporan &amp; Analitik Instan</span>
                    </li>
                    <li class="el-feature-item">
                        <span class="el-feature-num">03</span>
                        <span class="el-feature-text">Notifikasi &amp; Pengumuman</span>
                    </li>
                </ul>

                <p class="el-brand-footer">&copy; {{ date('Y') }} JOPOS</p>
            </div>
        </aside>

        {{-- ── Right panel · form ── --}}
        <main class="el-panel el-panel--form">
            <div class="el-form-inner">

                {{-- Mobile brand --}}
                <div class="el-mobile-brand">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="JOPOS" class="el-mobile-logo" />
                    <span class="el-mobile-name">JOPOS</span>
                </div>

                <header class="el-form-header">
                    <p class="el-form-eyebrow">Selamat datang kembali</p>
                    <h2 class="el-form-title">Masuk ke Akun<br>Karyawan Anda</h2>
                    <p class="el-form-subtitle">
                        Gunakan username atau email yang terdaftar untuk melanjutkan.
                    </p>
                </header>

                {{-- Flash messages --}}
                @if (session('status'))
                    <div class="el-alert el-alert--success" role="alert">
                        <svg class="el-alert-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="el-alert el-alert--danger" role="alert">
                        <svg class="el-alert-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <ul class="el-alert-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form --}}
                <form id="formAuthentication" class="el-form" action="{{ route('employee.login.post') }}" method="POST"
                    novalidate>
                    @csrf

                    {{-- Username --}}
                    <div class="el-field">
                        <label for="username" class="el-label">Username / Email</label>
                        <div class="el-input-wrap">
                            <span class="el-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input type="text" id="username" name="login"
                                class="el-input @error('login') is-error @enderror" placeholder="username atau email"
                                value="{{ old('login') }}" autocomplete="username" autofocus required />
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="el-field">
                        <label for="password" class="el-label">Password</label>
                        <div class="el-input-wrap">
                            <span class="el-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input type="password" id="password" name="password" class="el-input" placeholder="••••••••"
                                autocomplete="current-password" required />
                            <button type="button" class="el-eye-toggle" aria-label="Tampilkan password"
                                data-target="password">
                                <svg class="el-eye el-eye--show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="el-eye el-eye--hide" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.6" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="el-btn-submit">
                        <span class="el-btn-text">Masuk Sekarang</span>
                        <span class="el-btn-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </button>

                </form>

                <p class="el-forgot">
                    Lupa password?
                    <a href="{{ route('password.request') }}" class="el-forgot-link">Reset di sini</a>
                </p>

            </div>
        </main>

    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('.el-eye-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                btn.querySelector('.el-eye--show').style.display = isText ? '' : 'none';
                btn.querySelector('.el-eye--hide').style.display = isText ? 'none' : '';
            });
        });
    </script>
@endsection
