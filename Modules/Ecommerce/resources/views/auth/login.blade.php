@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@extends('layouts/commonMaster')
@section('title', 'Login Pelanggan - JO Computer')

@section('vendor-style')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@endsection

@section('layoutContent')
    @yield('content')
    <x-market-header :kategoris="$kategoris"></x-market-header>

    <section id="login" class="section-py bg-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5 col-xl-4" data-aos="fade-up">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold mb-2">Selamat Datang Kembali! 👋</h3>
                                <p class="text-muted">Silahkan login untuk melanjutkan belanja di JO Computer.</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('customer.login.post') }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Masukkan email Anda" value="{{ old('email') }}" required autofocus>
                                </div>

                                <div class="mb-4 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="password">Password</label>
                                        <a href="javascript:void(0);" class="text-decoration-none"><small>Lupa
                                                Password?</small></a>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            required>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="icon-base bx bx-hide"></i></span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                        <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                                    </div>
                                </div>

                                <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                            </form>

                            <div class="divider my-4">
                                <div class="divider-text text-muted">atau masuk dengan</div>
                            </div>

                            <!-- Tombol Investasi Google OAuth -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.google.login') }}"
                                    class="btn btn-outline-secondary d-flex justify-content-center align-items-center gap-2">
                                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                                        width="20">
                                    Masuk dengan Google
                                </a>
                            </div>

                            <p class="text-center mt-4 mb-0">
                                Belum punya akun?
                                <a href="{{ route('customer.register') }}"
                                    class="text-primary fw-bold text-decoration-none">Daftar sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-market-footer></x-market-footer>
@endsection

@section('page-script')
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
@endsection
