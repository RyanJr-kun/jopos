@extends('layouts/blankLayout')

@section('title', 'Reset Password — JOPOS')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
    <div class="fp-wrapper">

        {{-- Decorative background shapes --}}
        <div class="fp-geo fp-geo--tl"></div>
        <div class="fp-geo fp-geo--br"></div>

        {{-- Extra decorative ring --}}
        <div class="rp-geo-ring"></div>

        <div class="fp-card rp-card">

            <div class="fp-logo">
                <img src="{{ asset('assets/img/LM-Default.webp') }}" alt="JOPOS" class="fp-logo-img" />
            </div>

            <header class="fp-header">
                <p class="fp-eyebrow">Keamanan Akun</p>
                <h1 class="fp-title">Buat Password<br>Baru</h1>
                <p class="fp-subtitle">
                    Pastikan password baru Anda kuat dan belum pernah digunakan sebelumnya.
                </p>
            </header>

            {{-- Flash / Error --}}
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

            <form id="formResetPassword" class="fp-form" action="{{ route('password.update') }}" method="POST" novalidate>
                @csrf

                {{-- Hidden token & email --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? request('email') }}">

                {{-- New Password --}}
                <div class="el-field">
                    <label for="password" class="el-label">Password Baru</label>
                    <div class="el-input-wrap">
                        <span class="el-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password"
                            class="el-input @error('password') is-error @enderror" placeholder="Minimal 8 karakter"
                            autocomplete="new-password" autofocus required />
                        <button type="button" class="el-eye-toggle" aria-label="Tampilkan password" data-target="password">
                            <svg class="el-eye el-eye--show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="el-eye el-eye--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>

                    {{-- Strength meter --}}
                    <div class="rp-strength" id="strengthMeter" aria-live="polite">
                        <div class="rp-strength-bars">
                            <span class="rp-bar" id="bar1"></span>
                            <span class="rp-bar" id="bar2"></span>
                            <span class="rp-bar" id="bar3"></span>
                            <span class="rp-bar" id="bar4"></span>
                        </div>
                        <span class="rp-strength-label" id="strengthLabel">Masukkan password</span>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="el-field">
                    <label for="password_confirmation" class="el-label">Konfirmasi Password</label>
                    <div class="el-input-wrap">
                        <span class="el-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="el-input"
                            placeholder="Ulangi password baru" autocomplete="new-password" required />
                        <button type="button" class="el-eye-toggle" aria-label="Tampilkan konfirmasi"
                            data-target="password_confirmation">
                            <svg class="el-eye el-eye--show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="el-eye el-eye--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>

                    {{-- Match indicator --}}
                    <div class="rp-match" id="matchIndicator" style="display:none">
                        <svg class="rp-match-icon" id="matchIcon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="rp-match-text" id="matchText">Password cocok</span>
                    </div>
                </div>

                {{-- Password rules hint --}}
                <ul class="rp-rules" id="passwordRules">
                    <li class="rp-rule" id="rule-length">
                        <svg class="rp-rule-icon" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="10" r="9" fill="currentColor" opacity="0.15" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Minimal 8 karakter
                    </li>
                    <li class="rp-rule" id="rule-upper">
                        <svg class="rp-rule-icon" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="10" r="9" fill="currentColor" opacity="0.15" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Mengandung huruf kapital
                    </li>
                    <li class="rp-rule" id="rule-number">
                        <svg class="rp-rule-icon" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="10" r="9" fill="currentColor" opacity="0.15" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Mengandung angka
                    </li>
                    <li class="rp-rule" id="rule-special">
                        <svg class="rp-rule-icon" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="10" r="9" fill="currentColor" opacity="0.15" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Mengandung karakter khusus (!@#$%)
                    </li>
                </ul>

                {{-- Submit --}}
                <button type="submit" class="el-btn-submit" id="submitBtn" disabled>
                    <span class="el-btn-text">Simpan Password Baru</span>
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

@section('page-script')
    <script>
        // ── Toggle password visibility ─────────────────
        document.querySelectorAll('.el-eye-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                btn.querySelector('.el-eye--show').style.display = isText ? '' : 'none';
                btn.querySelector('.el-eye--hide').style.display = isText ? 'none' : '';
            });
        });

        // ── Password strength meter ────────────────────
        const pwdInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const bars = [1, 2, 3, 4].map(n => document.getElementById(`bar${n}`));
        const label = document.getElementById('strengthLabel');
        const matchBox = document.getElementById('matchIndicator');
        const matchIcon = document.getElementById('matchIcon');
        const matchText = document.getElementById('matchText');
        const submitBtn = document.getElementById('submitBtn');

        const rules = {
            length: {
                el: document.getElementById('rule-length'),
                fn: v => v.length >= 8
            },
            upper: {
                el: document.getElementById('rule-upper'),
                fn: v => /[A-Z]/.test(v)
            },
            number: {
                el: document.getElementById('rule-number'),
                fn: v => /[0-9]/.test(v)
            },
            special: {
                el: document.getElementById('rule-special'),
                fn: v => /[^A-Za-z0-9]/.test(v)
            },
        };

        const levels = [{
                label: 'Sangat Lemah',
                color: '#e53e3e',
                count: 1
            },
            {
                label: 'Lemah',
                color: '#dd6b20',
                count: 2
            },
            {
                label: 'Cukup',
                color: '#d69e2e',
                count: 3
            },
            {
                label: 'Kuat',
                color: '#38a169',
                count: 4
            },
        ];

        function evaluateStrength(val) {
            if (!val) return 0;
            return Object.values(rules).filter(r => r.fn(val)).length;
        }

        function updateRules(val) {
            Object.values(rules).forEach(r => {
                const passed = r.fn(val);
                r.el.classList.toggle('rp-rule--passed', passed);
            });
        }

        function updateBars(score) {
            bars.forEach((bar, i) => {
                bar.style.background = '';
                bar.classList.remove('rp-bar--active');
            });

            if (score === 0) {
                label.textContent = 'Masukkan password';
                label.style.color = '';
                return;
            }

            const lvl = levels[score - 1];
            label.textContent = lvl.label;
            label.style.color = lvl.color;

            for (let i = 0; i < lvl.count; i++) {
                bars[i].style.background = lvl.color;
                bars[i].classList.add('rp-bar--active');
            }
        }

        function checkMatch() {
            const pwd = pwdInput.value;
            const confirm = confirmInput.value;

            if (!confirm) {
                matchBox.style.display = 'none';
                return;
            }

            matchBox.style.display = 'flex';
            const match = pwd === confirm;
            matchIcon.style.color = match ? '#38a169' : '#e53e3e';
            matchText.style.color = match ? '#38a169' : '#e53e3e';
            matchText.textContent = match ? 'Password cocok' : 'Password tidak cocok';

            // Update confirm input border
            confirmInput.style.borderColor = match ? '#68d391' : '#fc8181';
        }

        function updateSubmit() {
            const score = evaluateStrength(pwdInput.value);
            const match = pwdInput.value === confirmInput.value && confirmInput.value.length > 0;
            const ready = score >= 2 && match;
            submitBtn.disabled = !ready;
            submitBtn.style.opacity = ready ? '1' : '0.5';
            submitBtn.style.cursor = ready ? 'pointer' : 'not-allowed';
        }

        pwdInput.addEventListener('input', () => {
            const val = pwdInput.value;
            const score = evaluateStrength(val);
            updateBars(score);
            updateRules(val);
            checkMatch();
            updateSubmit();
        });

        confirmInput.addEventListener('input', () => {
            checkMatch();
            updateSubmit();
        });

        // Initial state
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
    </script>
@endsection
