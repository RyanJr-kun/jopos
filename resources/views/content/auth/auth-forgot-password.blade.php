@extends('layouts/blankLayout')

@section('title', 'Lupa Password — JOPOS')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
    <div class="fp-wrapper">

        {{-- Decorative background shapes --}}
        <div class="fp-geo fp-geo--tl"></div>
        <div class="fp-geo fp-geo--br"></div>

        <div class="fp-card">

            {{-- Logo --}}
            <div class="fp-logo">
                <img src="{{ asset('assets/img/LM-Default.png') }}" alt="JOPOS" class="fp-logo-img" />
            </div>

            <header class="fp-header">
                <p class="fp-eyebrow">Pemulihan Akses</p>
                <h1 class="fp-title">Lupa Password?</h1>
                <p class="fp-subtitle">
                    Masukkan alamat email yang terdaftar. Kami akan mengirimkan
                    tautan untuk mengatur ulang password Anda.
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

            <form id="formAuthentication" class="fp-form" action="{{ route('password.email') }}" method="POST" novalidate>
                @csrf

                <div class="el-field">
                    <label for="email" class="el-label">Alamat Email</label>
                    <div class="el-input-wrap">
                        <span class="el-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email"
                            class="el-input @error('email') is-error @enderror" placeholder="email@perusahaan.com"
                            value="{{ old('email') }}" autofocus required />
                    </div>
                </div>

                <button type="submit" class="el-btn-submit">
                    <span class="el-btn-text">Kirim Tautan Reset</span>
                    <span class="el-btn-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </span>
                </button>
            </form>

            <div class="fp-back">
                <a href="{{ route('employee.login') }}" class="fp-back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    Kembali ke halaman login
                </a>
            </div>

        </div>
    </div>
@endsection
