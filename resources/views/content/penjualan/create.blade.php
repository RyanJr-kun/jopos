@extends('layouts/blankLayout')

@section('title', 'Point of Sales - JO Computer')

{{-- =========================================================
     HEAD: Style & Meta
     ========================================================= --}}
@section('page-style')
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- [PERBAIKAN] Meta CSRF diperlukan agar fetch() bisa membaca token tanpa hardcode --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('vendor-script')
    @vite(['resources/js/app.js'])
    <script defer src="https://kit.fontawesome.com/939a218158.js" crossorigin="anonymous"></script>
@endsection

{{-- =========================================================
     KONTEN UTAMA
     ========================================================= --}}
@section('content')
    <div class="bg-gray-100 min-vh-100">

        {{-- =====================================================
         NAVBAR
         ===================================================== --}}
        <nav class="navbar navbar-main navbar-expand-lg border-bottom sticky-top" aria-label="Navigasi Utama POS"
            style="height: 70px; background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px);">
            <div class="container-fluid align-items-center">

                <a href="/dashboard" target="_blank" aria-label="Ke Dashboard">
                    <img src="{{ asset('assets/img/LM-Default.png') }}" class="navbar-brand" alt="Logo JO Computer"
                        style="height: 50px;">
                </a>

                <div class="badge bg-label-success" aria-live="polite" aria-atomic="true">
                    <i class="bx bx-clock-fill me-2" aria-hidden="true"></i>
                    <span id="realtime-clock" class="fw-bold small">Memuat...</span>
                </div>

                <div class="collapse navbar-collapse order-lg-3" id="navbar">
                    <ul class="navbar-nav align-items-center ms-auto">
                        <li class="nav-item d-none d-lg-block">
                            <a href="/dashboard" class="nav-link">
                                <button class="btn btn-primary btn-sm px-3 mb-0" type="button">
                                    <i class="bx bx-globe me-2" aria-hidden="true"></i>Dashboard
                                </button>
                            </a>
                        </li>
                        <div class="vr m-2 d-none d-lg-block" role="separator"></div>
                        <li class="nav-item d-none d-md-block">
                            <a href="#fullscreen" class="nav-link" onclick="toggleFullScreen(event)"
                                aria-label="Fullscreen">
                                <button class="btn btn-light d-flex align-items-center justify-content-center mb-0"
                                    type="button" style="width: 30px; height: 30px; padding: 0;"
                                    aria-label="Toggle Fullscreen">
                                    <i class="bx bx-fullscreen" aria-hidden="true"></i>
                                </button>
                            </a>
                        </li>
                        <li class="nav-item d-none d-md-block">
                            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#salesHistoryModal"
                                title="Riwayat Sale" aria-label="Buka Riwayat Penjualan">
                                <button class="btn btn-light d-flex align-items-center justify-content-center mb-0"
                                    type="button" style="width: 30px; height: 30px; padding: 0;">
                                    <i class="bx bx-history" aria-hidden="true"></i>
                                </button>
                            </a>
                        </li>
                    </ul>
                </div>

                @auth
                    {{-- [DRY] Komponen avatar profile dipusatkan logikanya --}}
                    @php
                        $profileImg = auth()->user()->img_user
                            ? asset('storage/' . auth()->user()->img_user)
                            : asset('assets/img/user.webp');
                        $profileImgIsCustom = (bool) auth()->user()->img_user;
                    @endphp

                    <div class="d-flex align-items-center order-lg-4 ms-lg-2">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link text-dark p-0 mb-2 mb-md-0" id="userDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="Menu Pengguna">
                                @if ($profileImgIsCustom)
                                    <img src="{{ $profileImg }}" alt="Foto Profil {{ auth()->user()->username }}"
                                        class="avatar avatar-sm rounded cursor-pointer">
                                @else
                                    <i class="bx bx-person-circle cursor-pointer fs-5" aria-hidden="true"></i>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="userDropdown">
                                <li class="text-start d-flex m-2">
                                    <img src="{{ $profileImg }}" alt="Foto Profil {{ auth()->user()->username }}"
                                        class="avatar avatar-sm {{ $profileImgIsCustom ? 'rounded-circle' : '' }} me-3 cursor-pointer">
                                    <div class="ms-2">
                                        <p class="mb-1 text-xs fw-bolder">{{ auth()->user()->username }}</p>
                                        <p class="text-xs text-secondary">{{ Auth::user()->getRolesName() }}</p>
                                    </div>
                                </li>
                                <li class="d-md-none d-block">
                                    <hr class="horizontal dark mt-n2 mb-2">
                                    <a class="dropdown-item border-radius-md" href="#" data-bs-toggle="modal"
                                        data-bs-target="#salesHistoryModal">
                                        <i class="bx bx-history me-2" aria-hidden="true"></i> Riwayat Sale
                                    </a>
                                </li>
                                <li class="d-md-none d-block">
                                    <a class="dropdown-item border-radius-md" href="/dashboard">
                                        <i class="bx bx-globe me-2" aria-hidden="true"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <hr class="horizontal dark mt-n2 mb-2 d-none d-md-block">
                                    <a class="dropdown-item border-radius-md" href="/">
                                        <i class="bx bx-shop me-2" aria-hidden="true"></i> Web Market
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item border-radius-md w-100 text-danger"
                                            style="text-align: left;">
                                            <i class="bx bx-log-out me-2" aria-hidden="true"></i>Log Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>

        {{-- =====================================================
         MAIN CONTENT
         ===================================================== --}}

        <div class="row m-0 m-md-3 d-md-flex d-block">

            {{-- ==========================================
                 PANEL KIRI: Daftar & Filter Produk
                 ========================================== --}}
            <div class="col-md-7 col-12 border-end">
                <div class="row mt-3 mx-2">
                    <div class="col-md-8 col-12">
                        <h6 class="mb-0 text-dark fw-bolder">
                            Selamat Datang,
                            <u class="text-warning">{{ auth()->user()->username }}</u>
                        </h6>
                        <p class="text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="col-md-4 col-12 mt-2 mt-md-0">
                        <div class="ms-md-auto">
                            {{-- [UX] aria-label ditambahkan untuk screen reader --}}
                            <input type="text" id="product-search" class="form-control"
                                placeholder="Cari produk atau scan barcode..." aria-label="Cari atau scan produk"
                                autocomplete="off">
                        </div>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="col-12">
                        <div id="category-container" class="d-flex flex-nowrap gap-2 pb-2" style="overflow-x: auto;"
                            role="tablist" aria-label="Filter Kategori">
                            <div class="category-btn category-active border rounded-1 d-flex align-items-center ms-1 my-3 p-2"
                                style="height: 40px; cursor: pointer;" data-category-id="all" role="tab"
                                aria-selected="true" tabindex="0">
                                <i class="bx bx-category me-1" aria-hidden="true"></i>
                                <p class="fw-bolder text-xs ms-1 mb-0">Semua</p>
                            </div>
                            @foreach ($kategoris as $kategori)
                                @if ($kategori->products->isNotEmpty())
                                    <div class="category-btn border rounded-1 d-flex align-items-center my-3 p-2"
                                        style="height: 40px; cursor: pointer;" data-category-id="{{ $kategori->id }}"
                                        role="tab" aria-selected="false" tabindex="0">
                                        <img src="{{ $kategori->img_kategori ? asset('storage/' . $kategori->img_kategori) : asset('assets/img/produk.png') }}"
                                            class="avatar avatar-xs rounded-1" alt="{{ $kategori->name }}"
                                            loading="lazy">
                                        <p class="fw-bolder text-xs ms-2 mb-0">{{ $kategori->name }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Daftar Produk --}}
                <div class="p-3 product-list-container mb-3" style="max-height: 80vh; overflow-y: auto;">
                    <div class="row" id="product-list">
                        @forelse ($products as $produk)
                            <div class="col-12 col-md-4 col-xl-3 mb-3"
                                data-product-category-id="{{ $produk->category_id }}">
                                {{--
                                    [KEAMANAN] Semua data-* yang berisi string di-escape dengan e()
                                    untuk mencegah XSS jika nama produk mengandung karakter khusus.
                                --}}
                                <div class="card product-card rounded-2 p-2" data-id="{{ $produk->id }}"
                                    data-name="{{ e($produk->name_product) }}"
                                    data-harga="{{ $produk->harga_diskon ?? $produk->harga_jual }}"
                                    data-harga-asli="{{ $produk->harga_jual }}"
                                    data-img="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                    data-stok="{{ $produk->qty }}"
                                    data-wajib-seri="{{ $produk->wajib_seri ? 'true' : 'false' }}"
                                    data-pajak-id="{{ $produk->taxe_id }}"
                                    data-pajak-rate="{{ $produk->pajak->rate ?? 0 }}"
                                    data-disabled="{{ $produk->qty < 1 ? 'true' : 'false' }}" role="button"
                                    aria-label="Tambah {{ e($produk->name_product) }} ke keranjang"
                                    tabindex="{{ $produk->qty < 1 ? '-1' : '0' }}">

                                    <img class="card-img-top rounded-2"
                                        src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                        alt="Gambar {{ e($produk->name_product) }}" loading="lazy">

                                    {{-- Badge Stock Habis & Promosi --}}
                                    @if ($produk->qty < 1)
                                        <div class="product-badge">
                                            <span class="badge bg-label-danger">Stok Habis</span>
                                        </div>
                                    @elseif($produk->promotions->isNotEmpty() && ($promo = $produk->promotions->first()))
                                        <div class="product-badge">
                                            @if ($promo->type == 'percentage')
                                                <span class="badge bg-label-danger">{{ (int) $promo->nilai_diskon }}%
                                                    OFF</span>
                                            @else
                                                <span class="badge bg-label-info">PROMO</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="card-body p-2">
                                        <div class="row g-1">
                                            <div class="col-12">
                                                <p class="text-xs mb-1">
                                                    <span class="font-weight-bold">{{ $produk->category->name }}</span>
                                                </p>
                                                <h6 class="mb-0 product-name text-sm">{{ $produk->name_product }}</h6>
                                            </div>
                                            <hr class="horizontal dark my-2">
                                            @if ($produk->harga_diskon)
                                                <div class="col-12">
                                                    <div class="d-flex align-items-center">
                                                        <p
                                                            class="text-sm text-muted me-2 text-decoration-line-through mb-0">
                                                            {{ $produk->harga_formatted }}
                                                        </p>
                                                        <p class="text-sm font-weight-bold text-dark mb-0">
                                                            {{ 'Rp ' . number_format($produk->harga_diskon, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-end">
                                                    <p class="text-xs mt-n2 mb-0">{{ $produk->qty }}
                                                        {{ $produk->unit->singkat }}</p>
                                                </div>
                                            @else
                                                <div class="col-8">
                                                    <p class="font-weight-bold text-sm mb-0">
                                                        {{ $produk->harga_formatted }}</p>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <p class="text-xs mb-0">{{ $produk->qty }}
                                                        {{ $produk->unit->singkat }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-footer p-2 pt-0 border-0">
                                        @if ($produk->qty < 1)
                                            <p class="text-danger text-xs text-center fw-bold mb-0">Stok Habis</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bx bx-package fa-3x text-muted mb-2 d-block fs-1" aria-hidden="true"></i>
                                <p class="text-muted">Tidak ada produk yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 PANEL KANAN: Keranjang & Transaksi
                 ========================================== --}}
            <div class="col-md-5 col-12">
                <form action="{{ route('penjualan.store') }}" method="POST" id="penjualanForm" novalidate
                    aria-label="Form Transaksi Penjualan">
                    @csrf

                    {{-- Tampilkan Error Validasi Server --}}
                    @if ($errors->any())
                        <div class="alert alert-danger text-white mt-3" role="alert">
                            <strong class="font-weight-bold">Oops! Terjadi kesalahan:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card rounded-2 mt-3">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Detail Pesanan</h6>
                                    <p class="text-sm mb-0">Invoice:
                                        <span class="font-weight-bold">{{ $referensi }}</span>
                                    </p>
                                    <input type="hidden" name="referensi" value="{{ $referensi }}">
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-md bg-label-success me-2" id="cart-item-count"
                                        aria-live="polite" aria-atomic="true">
                                        <i class="fas fa-shopping-cart me-1" aria-hidden="true"></i>
                                        <span>0 Item</span>
                                    </span>
                                    <button type="button"
                                        class="btn btn-outline-danger btn-tooltip btn-sm py-1 px-2 mb-0"
                                        id="btn-reset-cart" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        title="Kosongkan Keranjang" aria-label="Kosongkan Keranjang" disabled>
                                        <i class="bx bx-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-2">
                            {{-- Pilihan Customer --}}
                            <div class="mb-3">
                                <label for="Customer" class="form-label">
                                    Customer <span class="text-danger" aria-hidden="true">*</span>
                                </label>
                                <div class="d-flex">
                                    <select class="form-select me-2 @error('customer_id') is-invalid @enderror"
                                        name="customer_id" id="Customer" required aria-required="true">
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}" @selected(old('customer_id') == $item->id)>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <button type="button" class="btn btn-outline-info btn-xs mb-0"
                                        data-bs-toggle="modal" data-bs-target="#createCustomerModal"
                                        title="Tambah Pelanggan Baru" aria-label="Tambah Pelanggan Baru">
                                        <i class="bx bx-plus icon-md cursor-pointer" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Tabel Keranjang --}}
                            <div class="table-responsive p-0" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-hover align-items-center mb-0" aria-label="Keranjang Belanja">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th scope="col" width="40%">Produk</th>
                                            <th scope="col" class="text-center" width="25%">Qty</th>
                                            <th scope="col" width="10%">PPN</th>
                                            <th scope="col" width="30%">Subtotal</th>
                                            <th scope="col" width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-items-container" aria-live="polite">
                                        {{-- Item keranjang di-render oleh JavaScript --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer pt-0">
                            <hr class="mt-0">
                            {{-- Rincian Biaya --}}
                            <div class="d-flex justify-content-between mb-0">
                                <p class="text-sm">Subtotal (DPP)</p>
                                <p class="text-sm font-weight-bold" id="subtotal">Rp 0</p>
                            </div>
                            <div class="d-flex justify-content-between mb-0">
                                <p class="text-sm">PPN</p>
                                <p class="text-sm font-weight-bold" id="pajak-total-display">Rp 0</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="service"
                                data-label="Service" role="button" tabindex="0" aria-label="Edit biaya service">
                                <p class="text-sm mb-0">Service</p>
                                <p class="text-sm font-weight-bold mb-0" id="service-display">Rp 0</p>
                                <input type="hidden" name="service" id="service-input" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center my-3" style="cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="ongkir"
                                data-label="Ongkos Kirim" role="button" tabindex="0" aria-label="Edit ongkos kirim">
                                <p class="text-sm mb-0">Ongkir</p>
                                <p class="text-sm font-weight-bold mb-0" id="ongkir-display">Rp 0</p>
                                <input type="hidden" name="ongkir" id="ongkir-input" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="diskon"
                                data-label="Diskon" role="button" tabindex="0" aria-label="Edit diskon">
                                <p class="text-sm mb-0">Diskon (Rp)</p>
                                <p class="text-sm font-weight-bold mb-0" id="diskon-display">Rp 0</p>
                                <input type="hidden" name="diskon" id="diskon-input" value="0">
                            </div>
                            <hr class="horizontal dark my-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-bold">Total</h6>
                                <h6 class="font-weight-bold" id="total-akhir" aria-live="polite">Rp 0</h6>
                            </div>

                            {{-- Pembayaran --}}
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                                    <label for="jumlah-dibayar-input" class="form-label text-sm mb-0">
                                        Jumlah Dibayar
                                    </label>
                                    <input type="text" name="jumlah_dibayar" id="jumlah-dibayar-input"
                                        class="form-control form-control-sm text-end w-30" value="0"
                                        aria-label="Jumlah dibayar oleh pelanggan" inputmode="numeric">
                                </div>
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <label class="form-label text-sm mb-0">Kembalian</label>
                                    <p class="form-control-plaintext text-end fw-bold mb-0" id="change-display"
                                        aria-live="polite">Rp 0</p>
                                </div>
                                <div class="col-6">
                                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required
                                        aria-required="true">
                                        <option value="TUNAI">Tunai</option>
                                        <option value="TRANSFER">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                    <textarea name="catatan" id="catatan" class="form-control" rows="2"
                                        placeholder="Tambahkan catatan untuk transaksi ini..."></textarea>
                                </div>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-center mt-3">
                                <button type="button" class="btn btn-info me-3" id="btn-pay-exact">
                                    Bayar Pas
                                </button>
                                <button type="submit" class="btn btn-dark" id="btn-save-transaction" disabled>
                                    <i class="fas fa-save me-1" aria-hidden="true"></i>
                                    Simpan Transaksi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- =========================================================
         MODAL: Buat Customer Baru
         ========================================================= --}}
        <div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title" id="createCustomerModalLabel">Buat Customer Baru</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Tutup modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createCustomerForm" action="{{ route('pelanggan.store') }}" method="post" novalidate>
                            @csrf
                            <div class="mb-2">
                                <label for="cust-name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input id="cust-name" name="name" type="text" class="form-control" required
                                    autocomplete="name">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-1">
                                <label for="cust-kontak" class="form-label">Kontak <span
                                        class="text-danger">*</span></label>
                                <input id="cust-kontak" name="kontak" type="text" class="form-control" required
                                    autocomplete="tel">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-1">
                                <label for="cust-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="cust-email" name="email"
                                    placeholder="example@gmail.com" autocomplete="email">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-1">
                                <label for="cust-alamat" class="form-label">Alamat</label>
                                <textarea id="cust-alamat" name="alamat" class="form-control" rows="3" autocomplete="street-address"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="justify-content-end form-check form-switch form-check-reverse mb-2">
                                <label class="me-auto fw-bold form-check-label" for="cust-status">Status Aktif</label>
                                <input id="cust-status" class="form-check-input" type="checkbox" name="status"
                                    value="1" checked>
                            </div>
                            <div class="modal-footer border-0 pb-0">
                                <button type="submit" class="btn btn-outline-info btn-sm p-2"
                                    id="btn-save-customer">Tambah Customer</button>
                                <button type="button" class="btn btn-danger btn-sm p-2"
                                    data-bs-dismiss="modal">Batalkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
         MODAL: Edit Item Keranjang
         ========================================================= --}}
        <div class="modal fade" id="editCartItemModal" tabindex="-1" aria-labelledby="editCartItemModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCartItemModalLabel">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCartItemForm" novalidate>
                            <input type="hidden" id="edit-item-id">
                            <div class="row g-3 px-1">
                                <div class="col-12">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" id="edit-item-name" readonly disabled
                                        aria-readonly="true">
                                </div>
                                <div class="col-12">
                                    <label for="edit-item-harga" class="form-label">
                                        Harga Jual <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="edit-item-harga" placeholder="0"
                                            inputmode="numeric" min="1">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="edit-item-diskon" class="form-label">Diskon (Rp)</label>
                                    <input type="text" class="form-control" id="edit-item-diskon" placeholder="0"
                                        inputmode="numeric">
                                </div>
                                <div class="col-6">
                                    <label for="edit-item-pajak-id" class="form-label">Pajak</label>
                                    <select class="form-select" id="edit-item-pajak-id">
                                        <option value="" data-rate="0" selected>Tidak Ada</option>
                                        @foreach ($taxes as $pajak)
                                            <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                                {{ $pajak->name_taxe }} ({{ $pajak->rate }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-info" id="saveItemChangesBtn">
                            Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
         MODAL: Riwayat Penjualan Hari Ini
         ========================================================= --}}
        <div class="modal fade" id="salesHistoryModal" tabindex="-1" aria-labelledby="salesHistoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="salesHistoryModalLabel">
                            <i class="bx bx-history me-2" aria-hidden="true"></i>
                            Riwayat Penjualan Hari Ini
                        </h6>
                        {{-- [PERBAIKAN] Tombol tutup ditambahkan (sebelumnya tidak ada) --}}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body" id="salesHistoryBody">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Memuat data...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
         MODAL: Edit Biaya Tambahan (Service, Ongkir, Diskon)
         ========================================================= --}}
        <div class="modal fade" id="editExtraCostModal" tabindex="-1" aria-labelledby="editExtraCostModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="editExtraCostModalLabel">Edit Biaya</h6>
                        <button type="button" class="btn bg-dark btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editExtraCostForm" novalidate>
                            <input type="hidden" id="extra-cost-type">
                            {{-- Seksi Kode Promo (Tersembunyi kecuali untuk Diskon) --}}
                            <div id="promo-code-section" class="mb-3" style="display: none;" aria-hidden="true">
                                <label for="promo-code-input" class="form-label">Kode Promosi</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="promo-code-input"
                                        placeholder="Masukkan kode promo" autocomplete="off">
                                    <button class="btn btn-outline-secondary mb-0" type="button"
                                        id="apply-promo-btn">Terapkan</button>
                                </div>
                                <div id="promo-feedback" class="mt-2 text-xs" aria-live="polite"></div>
                            </div>
                            <div class="mb-0">
                                <label for="extra-cost-value" class="form-label" id="extra-cost-label">Jumlah</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="extra-cost-value" min="0"
                                        inputmode="numeric">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-info" id="saveExtraCostBtn">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
         MODAL: Pilih Nomor Seri
         ========================================================= --}}
        <div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="serialNumberModalLabel">Pilih Nomor Seri</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="sn-produk-id">
                        <div class="badge bg-label-info p-2 text-sm w-100 text-start mb-2" role="status">
                            Produk: <strong id="sn-name-produk" class="text-warning">—</strong><br>
                            Pilih tepat <strong id="sn-required-count">1</strong> nomor seri.
                        </div>
                        <div id="sn-list-container" class="list-group" style="max-height: 300px; overflow-y: auto;"
                            role="group" aria-label="Daftar nomor seri">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Memuat nomor seri...</span>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block mt-2" id="sn-error-message" aria-live="assertive"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-info" id="btn-confirm-sn">Simpan Pilihan</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
         MODAL: Pembayaran
         ========================================================= --}}
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Pembayaran</h5>
                        <button type="button" class="btn bg-dark btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <p class="text-sm mb-0">Total Tagihan</p>
                            <h3 class="font-weight-bolder" id="payment-modal-total">Rp 0</h3>
                        </div>
                        <div class="mb-3">
                            <label for="payment-amount-input" class="form-label">Jumlah Bayar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control form-control-lg text-end"
                                    id="payment-amount-input" placeholder="0" inputmode="numeric">
                            </div>
                        </div>
                        <div class="row gx-2 mt-3">
                            <div class="col">
                                <button class="btn btn-outline-secondary w-100 quick-pay-btn" type="button"
                                    data-amount="pas">Uang Pas</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-outline-secondary w-100 quick-pay-btn" type="button"
                                    data-amount="50000">50.000</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-outline-secondary w-100 quick-pay-btn" type="button"
                                    data-amount="100000">100.000</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-info w-100" id="savePaymentBtn">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- akhir .bg-gray-100 --}}
@endsection

{{-- =========================================================
     JAVASCRIPT
     ========================================================= --}}
@section('page-script')
    <script>
        // =========================================================
        // UTILITY: Toggle Fullscreen
        // =========================================================
        function toggleFullScreen(event) {
            event.preventDefault();
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(console.error);
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================================================
            // CLOCK: Realtime (digabung ke satu DOMContentLoaded)
            // =========================================================
            const clockElement = document.getElementById('realtime-clock');

            function updateClock() {
                if (!clockElement) return;
                clockElement.textContent = new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).format(new Date()).replace(/\./g, ':');
            }
            setInterval(updateClock, 1000);
            updateClock();

            // =========================================================
            // UTILITIES
            // =========================================================

            /** Format angka ke format mata uang IDR */
            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);

            /** Format input angka dengan pemisah ribuan saat diketik */
            const formatNumberInput = (e) => {
                const raw = e.target.value.replace(/[^0-9]/g, '');
                // [PERBAIKAN] Jika kosong, biarkan kosong agar UX lebih baik
                e.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            };

            /** [REFAKTOR] Hitung DPP dan pajak dari harga jual inklusif pajak */
            const calcDppAndTax = (hargaJualTotal, pajakRate) => {
                const dpp = hargaJualTotal / (1 + (pajakRate / 100));
                const pajak = hargaJualTotal - dpp;
                return {
                    dpp,
                    pajak
                };
            };

            /** Debounce helper untuk mengoptimalkan event listener input */
            const debounce = (fn, delay = 250) => {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn(...args), delay);
                };
            };

            // Baca CSRF token satu kali dari meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Default headers untuk semua fetch request
            const defaultHeaders = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            };

            // =========================================================
            // STATE
            // =========================================================
            const cart = new Map();
            let selectedCategoryId = 'all';
            let tempProductDataForSN = {};

            // Pra-load pajak rate dari server ke Map
            const pajakRates = new Map();
            @foreach ($taxes as $pajak)
                pajakRates.set({{ $pajak->id }}, {{ $pajak->rate }});
            @endforeach

            // =========================================================
            // DOM ELEMENTS
            // =========================================================
            const productList = document.getElementById('product-list');
            const allProductCards = document.querySelectorAll('.product-card');
            const cartContainer = document.getElementById('cart-items-container');
            const cartItemCount = document.getElementById('cart-item-count');
            const subtotalEl = document.getElementById('subtotal');
            const serviceInput = document.getElementById('service-input');
            const ongkirInput = document.getElementById('ongkir-input');
            const diskonInput = document.getElementById('diskon-input');
            const changeDisplay = document.getElementById('change-display');
            const paymentInputEl = document.getElementById('jumlah-dibayar-input');
            const payExactButton = document.getElementById('btn-pay-exact');
            const totalAkhirEl = document.getElementById('total-akhir');
            const productSearchInput = document.getElementById('product-search');
            const mainForm = document.getElementById('penjualanForm');
            const saveButton = document.getElementById('btn-save-transaction');
            const resetButton = document.getElementById('btn-reset-cart');
            const editItemModal = new bootstrap.Modal(document.getElementById('editCartItemModal'));
            const editItemHargaInput = document.getElementById('edit-item-harga');
            const editItemDiskonInput = document.getElementById('edit-item-diskon');
            const serialNumberModalEl = document.getElementById('serialNumberModal');
            const serialNumberModal = new bootstrap.Modal(serialNumberModalEl);
            const snListContainer = document.getElementById('sn-list-container');
            const snNamaProduct = document.getElementById('sn-name-produk');
            const snRequiredCount = document.getElementById('sn-required-count');
            const snConfirmBtn = document.getElementById('btn-confirm-sn');
            const snErrorMessage = document.getElementById('sn-error-message');

            // =========================================================
            // CART: Fungsi Inti
            // =========================================================

            const updateCartAndTotals = () => {
                renderCart();
                calculateTotals();
                toggleSaveButton();
            };

            const addProductToCart = (productData, serialNumbers = []) => {
                const {
                    id,
                    name,
                    harga,
                    hargaAsli,
                    stok,
                    img,
                    wajibSeri,
                    pajakId,
                    pajakRate
                } = productData;
                const parsedId = parseInt(id);
                const parsedHargaFinal = parseFloat(harga);
                const parsedHargaAsli = parseFloat(hargaAsli ||
                    harga); // Fallback ke harga jika hargaAsli kosong
                const parsedStock = parseInt(stok);

                if (cart.has(parsedId)) {
                    const item = cart.get(parsedId);
                    if (item.jumlah < item.stok) {
                        item.jumlah++;
                    } else {
                        showToast(`Stok untuk "${name}" tidak mencukupi.`, 'warning');
                    }
                } else {
                    if (parsedStock > 0) {
                        const initialQuantity = serialNumbers.length > 0 ? serialNumbers.length : 1;
                        cart.set(parsedId, {
                            id: parsedId,
                            name,
                            harga: parsedHargaAsli,
                            stok: parsedStock,
                            img,
                            jumlah: initialQuantity,
                            harga_jual: parsedHargaFinal,
                            diskon: 0,
                            taxe_id: pajakId ? parseInt(pajakId) : null,
                            pajak_rate: pajakRate ? parseFloat(pajakRate) : 0,
                            serial_numbers: serialNumbers,
                            wajib_seri: wajibSeri === 'true'
                        });
                    } else {
                        showToast(`"${name}" kehabisan stok.`, 'warning');
                    }
                }
                updateCartAndTotals();
            };

            const updateQuantity = (id, newQuantity) => {
                if (!cart.has(id)) return;
                const item = cart.get(id);
                newQuantity = parseInt(newQuantity);

                if (item.serial_numbers && item.serial_numbers.length > 0) {
                    // Produk berseri: kurangi kuantitas = hapus SN terakhir
                    if (newQuantity > 0 && newQuantity < item.jumlah) {
                        item.serial_numbers.pop();
                        item.jumlah = newQuantity;
                    } else if (newQuantity <= 0) {
                        cart.delete(id);
                    }
                } else {
                    // Produk non-seri
                    if (newQuantity > 0 && newQuantity <= item.stok) {
                        item.jumlah = newQuantity;
                    } else if (newQuantity > item.stok) {
                        item.jumlah = item.stok;
                        showToast(`Stok maksimum untuk "${item.name}" adalah ${item.stok}.`, 'warning');
                    } else {
                        cart.delete(id);
                    }
                }
                updateCartAndTotals();
            };

            const removeFromCart = (id) => {
                cart.delete(id);
                updateCartAndTotals();
            };

            // =========================================================
            // CART: Render
            // =========================================================

            const renderCart = () => {
                cartContainer.innerHTML = '';

                if (cart.size === 0) {
                    cartContainer.innerHTML = `
                <tr id="cart-empty-message">
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-shopping-cart fa-2x mb-2 d-block" aria-hidden="true"></i>
                        <p class="mb-0">Keranjang masih kosong</p>
                    </td>
                </tr>`;
                } else {
                    let formIndex = 0;
                    cart.forEach(item => {
                        const hargaJualTotal = (item.harga_jual * item.jumlah) - item.diskon;
                        const {
                            dpp: dppItem,
                            pajak: pajakAmountItem
                        } = calcDppAndTax(hargaJualTotal, item.pajak_rate);

                        // Input hidden untuk nomor seri
                        let serialNumberInputs = '';
                        (item.serial_numbers || []).forEach(sn => {
                            serialNumberInputs +=
                                `<input type="hidden" name="items[${formIndex}][serial_numbers][]" value="${sn}">`;
                        });

                        // Tampilan nomor seri di bawah nama produk
                        const serialNumberDisplay = (item.serial_numbers?.length > 0) ?
                            `<small class="text-xs text-muted d-flex">SN: ${item.serial_numbers.join(', ')}</small>` :
                            '';

                        // Tampilan harga (normal vs diskon)
                        let hargaDisplay =
                            `<span class="text-xs">${formatCurrency(item.harga_jual)}</span>`;
                        if (item.harga_jual < item.harga) {
                            hargaDisplay = `
                        <span class="text-xs text-danger">${formatCurrency(item.harga_jual)}</span><br>
                        <small class="text-muted text-decoration-line-through">${formatCurrency(item.harga)}</small>`;
                        }

                        // Tombol edit hanya tampil untuk item non-berseri
                        const editButtonHtml = item.wajib_seri ? '' : `
                    <button class="btn btn-link text-dark p-0 edit-item" data-id="${item.id}"
                            title="Edit Item" type="button" aria-label="Edit item ${item.name}">
                        <i class="bx bx-edit" aria-hidden="true"></i>
                    </button>`;

                        cartContainer.insertAdjacentHTML('beforeend', `
                    <tr class="cart-item-row">
                        <input type="hidden" name="items[${formIndex}][product_id]"  value="${item.id}">
                        <input type="hidden" name="items[${formIndex}][jumlah]"       value="${item.jumlah}">
                        <input type="hidden" name="items[${formIndex}][harga_jual]"   value="${item.harga_jual}">
                        <input type="hidden" name="items[${formIndex}][diskon]"       value="${item.diskon}">
                        <input type="hidden" name="items[${formIndex}][taxe_id]"      value="${item.taxe_id || ''}">
                        ${serialNumberInputs}
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${item.img}" class="avatar avatar-md rounded me-2" alt="${item.name}" loading="lazy">
                                <div class="d-flex flex-column" style="min-width: 0;">
                                    <h6 class="mb-0 text-xs text-wrap">${item.name}</h6>
                                    <small class="text-xs d-flex">${serialNumberDisplay}</small>
                                    ${hargaDisplay}
                                </div>
                            </div>
                        </td>
                        <td class="align-items-center text-center h-auto">
                            <button class="btn btn-outline-primary btn-sm rounded-circle p-0 qty-decrease"
                                    data-id="${item.id}" type="button"
                                    style="width: 22px; height: 22px; line-height: 1;"
                                    aria-label="Kurangi jumlah ${item.name}">−</button>
                            <span class="fw-bold px-1 text-sm" aria-label="Jumlah: ${item.jumlah}">${item.jumlah}</span>
                            <button class="btn btn-outline-primary btn-sm rounded-circle p-0 qty-increase"
                                    data-id="${item.id}" type="button"
                                    style="width: 22px; height: 22px; line-height: 1;"
                                    aria-label="Tambah jumlah ${item.name}">+</button>
                        </td>
                        <td class="align-items-center text-start">
                            <span class="text-xs fw-bold">${formatCurrency(pajakAmountItem)}</span>
                        </td>
                        <td class="align-items-center text-start">
                            <span class="text-xs fw-bold">${formatCurrency(dppItem)}</span>
                        </td>
                        <td class="align-middle text-end" style="padding-top: 25px;">
                            <div class="d-flex justify-content-end align-items-center">
                                ${editButtonHtml}
                                <button class="btn btn-link text-danger p-0 ms-1 remove-item"
                                        data-id="${item.id}" title="Hapus Item" type="button"
                                        aria-label="Hapus ${item.name} dari keranjang">
                                    <i class="bx bx-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`);
                        formIndex++;
                    });
                }

                // Update jumlah item di badge
                const totalItems = Array.from(cart.values()).reduce((sum, item) => sum + item.jumlah, 0);
                cartItemCount.innerHTML =
                    `<i class="fas fa-shopping-cart me-1" aria-hidden="true"></i> ${totalItems} Item`;

                // Tandai kartu produk yang sudah ada di keranjang
                allProductCards.forEach(card => {
                    const cardId = parseInt(card.dataset.id);
                    card.classList.toggle('active', cart.has(cardId));
                });
            };

            // =========================================================
            // CART: Kalkulasi Total
            // =========================================================

            const calculateTotals = () => {
                let subtotal = 0;
                let totalTaxe = 0;

                cart.forEach(item => {
                    const hargaJualTotal = (item.harga_jual * item.jumlah) - item.diskon;
                    const {
                        dpp,
                        pajak
                    } = calcDppAndTax(hargaJualTotal, item.pajak_rate);
                    subtotal += dpp;
                    totalTaxe += pajak;
                });

                const service = parseFloat(serviceInput.value) || 0;
                const ongkir = parseFloat(ongkirInput.value) || 0;
                const diskon = parseFloat(diskonInput.value) || 0;
                const total = (subtotal + totalTaxe + service + ongkir) - diskon;

                subtotalEl.textContent = formatCurrency(subtotal);
                document.getElementById('pajak-total-display').textContent = formatCurrency(totalTaxe);
                totalAkhirEl.textContent = formatCurrency(Math.max(0, total)); // Tidak boleh negatif

                calculateChange();
                return total;
            };

            /**
             * [BUG FIX] calculateChange tidak lagi memformat ulang nilai paymentInputEl
             * agar tidak merusak nilai saat user sedang mengetik.
             */
            const calculateChange = () => {
                const totalText = totalAkhirEl.textContent.replace(/[^0-9]/g, '');
                const total = parseFloat(totalText) || 0;
                const paymentText = paymentInputEl.value.replace(/[^0-9]/g, '');
                const paymentAmount = parseFloat(paymentText) || 0;
                const change = paymentAmount - total;

                changeDisplay.textContent = formatCurrency(change);
                changeDisplay.classList.toggle('text-danger', change < 0);
                changeDisplay.classList.toggle('text-success', change >= 0);
            };

            const toggleSaveButton = () => {
                saveButton.disabled = cart.size === 0;
                resetButton.disabled = cart.size === 0;
            };

            // =========================================================
            // FILTER PRODUK
            // =========================================================

            const filterProducts = () => {
                const searchTerm = productSearchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                allProductCards.forEach(card => {
                    const wrapper = card.parentElement;
                    const productName = card.dataset.name.toLowerCase();
                    const categoryId = wrapper.dataset.productCategoryId;

                    const categoryMatch = selectedCategoryId === 'all' || selectedCategoryId ==
                        categoryId;
                    const searchMatch = productName.includes(searchTerm);

                    wrapper.style.display = (categoryMatch && searchMatch) ? '' : 'none';
                    if (categoryMatch && searchMatch) visibleCount++;
                });

                // Tampilkan pesan kosong jika tidak ada hasil
                let emptyMsg = document.getElementById('filter-empty-message');
                if (visibleCount === 0) {
                    if (!emptyMsg) {
                        emptyMsg = document.createElement('div');
                        emptyMsg.id = 'filter-empty-message';
                        emptyMsg.className = 'col-12 text-center py-5 text-muted';
                        emptyMsg.innerHTML = '<p>Tidak ada produk yang cocok.</p>';
                        productList.appendChild(emptyMsg);
                    }
                } else if (emptyMsg) {
                    emptyMsg.remove();
                }
            };

            // =========================================================
            // BARCODE SCANNER
            // =========================================================

            const handleBarcodeScan = async (barcode) => {
                if (!barcode) return;
                try {
                    const url =
                        "{{ route('get-data.produk.by-barcode', ['barcode' => 'BARCODE_PLACEHOLDER']) }}"
                        .replace('BARCODE_PLACEHOLDER', encodeURIComponent(barcode));
                    const response = await fetch(url, {
                        headers: defaultHeaders
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Produk tidak ditemukan.');
                    }

                    const productData = {
                        id: data.id,
                        name: data.name_product,
                        harga: data.harga_diskon ?? data.harga_jual,
                        hargaAsli: data.harga_jual,
                        stok: data.qty,
                        img: data.img_produk ?
                            `{{ asset('storage/') }}/${data.img_produk}` :
                            `{{ asset('assets/img/produk.png') }}`,
                        wajibSeri: data.wajib_seri ? 'true' : 'false',
                        pajakId: data.taxe_id,
                        pajakRate: data.pajak ? data.pajak.rate : 0
                    };

                    const itemInCart = cart.get(productData.id);
                    if (productData.wajibSeri === 'true') {
                        const requiredQty = itemInCart ? itemInCart.jumlah + 1 : 1;
                        const existingSerials = itemInCart ? itemInCart.serial_numbers : [];
                        tempProductDataForSN = productData;
                        openSerialNumberModal(productData.id, productData.name, requiredQty,
                            existingSerials);
                    } else {
                        if (itemInCart) {
                            updateQuantity(productData.id, itemInCart.jumlah + 1);
                        } else {
                            addProductToCart(productData);
                        }
                    }
                    productSearchInput.value = '';
                } catch (error) {
                    showToast(`Barcode: ${error.message}`, 'danger');
                }
            };

            // =========================================================
            // MODAL: Nomor Seri
            // =========================================================

            async function openSerialNumberModal(produkId, nameProduct, requiredQty = 1, existingSerials = []) {
                snNamaProduct.textContent = tempProductDataForSN.name || nameProduct;
                snRequiredCount.textContent = requiredQty;
                snErrorMessage.textContent = '';
                snListContainer.innerHTML =
                    '<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Memuat...</span></div></div>';
                serialNumberModalEl.dataset.produkId = produkId;
                serialNumberModalEl.dataset.requiredQty = requiredQty;
                serialNumberModal.show();

                try {
                    const url = "{{ route('serialNumber.getByProduct', ['product_id' => 'ID_PRODUK']) }}"
                        .replace('ID_PRODUK', produkId);
                    const response = await fetch(url, {
                        headers: defaultHeaders
                    });
                    if (!response.ok) throw new Error('Gagal memuat data nomor seri.');
                    const serialNumbers = await response.json();

                    snListContainer.innerHTML = '';
                    const allSerials = [...new Set([...serialNumbers, ...existingSerials])];

                    if (allSerials.length === 0) {
                        snListContainer.innerHTML =
                            '<p class="text-muted text-center py-3">Tidak ada nomor seri tersedia.</p>';
                        snConfirmBtn.disabled = true;
                        return;
                    }

                    allSerials.forEach(sn => {
                        const isChecked = existingSerials.includes(sn) ? 'checked' : '';
                        snListContainer.insertAdjacentHTML('beforeend', `
                    <label class="list-group-item list-group-item-action">
                        <input class="form-check-input me-2" type="checkbox" value="${sn}" ${isChecked}>
                        ${sn}
                    </label>`);
                    });
                    snConfirmBtn.disabled = false;
                } catch (error) {
                    snListContainer.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                    snConfirmBtn.disabled = true;
                }
            }

            snConfirmBtn.addEventListener('click', () => {
                const checked = snListContainer.querySelectorAll('input[type="checkbox"]:checked');
                const requiredCount = parseInt(serialNumberModalEl.dataset.requiredQty);

                if (checked.length !== requiredCount) {
                    snErrorMessage.textContent = `Pilih tepat ${requiredCount} nomor seri.`;
                    return;
                }

                snErrorMessage.textContent = '';
                const selectedSerials = Array.from(checked).map(cb => cb.value);
                const produkId = parseInt(tempProductDataForSN.id);

                if (cart.has(produkId)) {
                    const item = cart.get(produkId);
                    item.jumlah = selectedSerials.length;
                    item.serial_numbers = selectedSerials;
                } else {
                    addProductToCart(tempProductDataForSN, selectedSerials);
                }

                updateCartAndTotals();
                serialNumberModal.hide();
                tempProductDataForSN = {};
            });

            // =========================================================
            // MODAL: Edit Item Keranjang
            // =========================================================

            function openEditModal(id) {
                const item = cart.get(id);
                if (!item) return;

                if (item.serial_numbers?.length > 0) {
                    showToast('Pengeditan item dengan nomor seri tidak diizinkan. Hapus dan tambahkan kembali.',
                        'warning');
                    return;
                }

                document.getElementById('edit-item-id').value = id;
                document.getElementById('edit-item-name').value = item.name;
                editItemHargaInput.value = new Intl.NumberFormat('id-ID').format(item.harga_jual);
                editItemDiskonInput.value = new Intl.NumberFormat('id-ID').format(item.diskon);
                document.getElementById('edit-item-pajak-id').value = item.taxe_id || '';
                editItemModal.show();
            }

            editItemHargaInput.addEventListener('input', formatNumberInput);
            editItemDiskonInput.addEventListener('input', formatNumberInput);

            document.getElementById('saveItemChangesBtn').addEventListener('click', () => {
                const id = parseInt(document.getElementById('edit-item-id').value);
                const item = cart.get(id);
                if (!item) return;

                const newHarga = parseFloat(editItemHargaInput.value.replace(/[^0-9]/g, '')) || 0;

                // [VALIDASI] Harga jual tidak boleh nol atau negatif
                if (newHarga <= 0) {
                    showToast('Harga jual tidak boleh nol atau kosong.', 'warning');
                    editItemHargaInput.focus();
                    return;
                }

                item.harga_jual = newHarga;
                item.diskon = parseFloat(editItemDiskonInput.value.replace(/[^0-9]/g, '')) || 0;

                const pajakSelect = document.getElementById('edit-item-pajak-id');
                const selectedTaxe = pajakSelect.options[pajakSelect.selectedIndex];
                item.taxe_id = selectedTaxe.value ? parseInt(selectedTaxe.value) : null;
                item.pajak_rate = parseFloat(selectedTaxe.dataset.rate) || 0;

                updateCartAndTotals();
                editItemModal.hide();
            });

            // =========================================================
            // MODAL: Edit Biaya Tambahan (Service / Ongkir / Diskon)
            // =========================================================

            const extraCostModal = new bootstrap.Modal(document.getElementById('editExtraCostModal'));
            const extraCostModalEl = document.getElementById('editExtraCostModal');
            const extraCostValueInput = document.getElementById('extra-cost-value');
            const promoCodeSection = document.getElementById('promo-code-section');
            const applyPromotionBtn = document.getElementById('apply-promo-btn');

            extraCostModalEl.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                const type = trigger.dataset.type;
                const label = trigger.dataset.label;
                const currentVal = document.getElementById(`${type}-input`).value;

                document.getElementById('extra-cost-type').value = type;
                document.getElementById('editExtraCostModalLabel').textContent = `Edit ${label}`;
                document.getElementById('extra-cost-label').textContent = label;
                extraCostValueInput.value = new Intl.NumberFormat('id-ID').format(currentVal || 0);

                const isDiskon = type === 'diskon';
                promoCodeSection.style.display = isDiskon ? 'block' : 'none';
                promoCodeSection.setAttribute('aria-hidden', String(!isDiskon));
                if (isDiskon) {
                    extraCostValueInput.readOnly = false;
                    document.getElementById('promo-code-input').value = '';
                    document.getElementById('promo-feedback').innerHTML = '';
                }
                setTimeout(() => extraCostValueInput.focus(), 450);
            });

            extraCostValueInput.addEventListener('input', formatNumberInput);

            document.getElementById('saveExtraCostBtn').addEventListener('click', () => {
                const type = document.getElementById('extra-cost-type').value;
                const newValue = parseFloat(extraCostValueInput.value.replace(/[^0-9]/g, '')) || 0;

                document.getElementById(`${type}-input`).value = newValue;
                document.getElementById(`${type}-display`).textContent = formatCurrency(newValue);

                calculateTotals();
                extraCostModal.hide();
            });

            // --- Validasi & Terapkan Kode Promo ---
            applyPromotionBtn.addEventListener('click', async () => {
                const promoCode = document.getElementById('promo-code-input').value.trim();
                const promoFeedback = document.getElementById('promo-feedback');

                if (!promoCode) {
                    promoFeedback.innerHTML =
                        '<span class="text-danger">Masukkan kode promo terlebih dahulu.</span>';
                    return;
                }

                let subtotal = 0;
                cart.forEach(item => {
                    const hargaJualTotal = (item.harga_jual * item.jumlah) - item.diskon;
                    subtotal += calcDppAndTax(hargaJualTotal, item.pajak_rate).dpp;
                });

                promoFeedback.innerHTML = '<span class="text-muted">Memvalidasi...</span>';

                try {
                    const response = await fetch("{{ route('promo.validateCode') }}", {
                        method: 'POST',
                        headers: {
                            ...defaultHeaders,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            code: promoCode,
                            subtotal
                        })
                    });
                    const result = await response.json();
                    if (!result.success) throw new Error(result.message);

                    const promo = result.promo;
                    let discountAmount = promo.type === 'percentage' ?
                        Math.round(Math.min(subtotal * (promo.nilai_diskon / 100), promo.max_diskon ||
                            Infinity)) :
                        promo.nilai_diskon;

                    extraCostValueInput.value = new Intl.NumberFormat('id-ID').format(discountAmount);
                    extraCostValueInput.readOnly = true;
                    promoFeedback.innerHTML =
                        `<span class="text-success fw-bold">Promo "${promo.name}" berhasil diterapkan!</span>`;
                } catch (error) {
                    promoFeedback.innerHTML = `<span class="text-danger">${error.message}</span>`;
                    extraCostValueInput.readOnly = false;
                }
            });

            // =========================================================
            // MODAL: Customer Baru (AJAX)
            // =========================================================

            const addCustomerForm = document.getElementById('createCustomerForm');
            const pelangganSelect = document.getElementById('Customer');

            addCustomerForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Reset state error
                addCustomerForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                    'is-invalid'));
                addCustomerForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent =
                    '');

                const saveBtn = document.getElementById('btn-save-customer');
                saveBtn.disabled = true;
                saveBtn.textContent = 'Menyimpan...';

                const data = {
                    name: addCustomerForm.querySelector('#cust-name').value,
                    kontak: addCustomerForm.querySelector('#cust-kontak').value,
                    email: addCustomerForm.querySelector('#cust-email').value,
                    alamat: addCustomerForm.querySelector('#cust-alamat').value,
                    status: addCustomerForm.querySelector('#cust-status').checked ? 1 : 0
                };

                try {
                    const response = await fetch("{{ route('pelanggan.store') }}", {
                        method: 'POST',
                        headers: {
                            ...defaultHeaders,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            Object.keys(result.errors).forEach(key => {
                                // [PERBAIKAN] Gunakan ID input baru (cust-{key})
                                const input = addCustomerForm.querySelector(`#cust-${key}`);
                                const feedback = input?.nextElementSibling;
                                if (input) input.classList.add('is-invalid');
                                if (feedback) feedback.textContent = result.errors[key][0];
                            });
                        } else {
                            throw new Error(result.message || 'Terjadi kesalahan server.');
                        }
                    } else {
                        const newCustomer = result.data;
                        const newOption = new Option(newCustomer.name, newCustomer.id, true, true);
                        pelangganSelect.appendChild(newOption);
                        bootstrap.Modal.getInstance(document.getElementById('createCustomerModal'))
                            .hide();
                        addCustomerForm.reset();
                        showToast(`Customer "${newCustomer.name}" berhasil ditambahkan.`, 'success');
                    }
                } catch (error) {
                    showToast('Gagal menyimpan pelanggan: ' + error.message, 'danger');
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Tambah Customer';
                }
            });

            // =========================================================
            // MODAL: Riwayat Penjualan
            // =========================================================

            const salesHistoryModal = document.getElementById('salesHistoryModal');
            if (salesHistoryModal) {
                salesHistoryModal.addEventListener('show.bs.modal', async function() {
                    const modalBody = document.getElementById('salesHistoryBody');
                    modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat data...</span>
                    </div>
                </div>`;
                    try {
                        const response = await fetch("{{ route('penjualan.history.today') }}", {
                            headers: defaultHeaders
                        });
                        if (!response.ok) throw new Error('Gagal memuat riwayat penjualan.');
                        const sales = await response.json();

                        if (sales.length === 0) {
                            modalBody.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="bx bxs-cart fs-1 mb-3 d-block" aria-hidden="true"></i>
                            <p class="mb-0">Belum ada transaksi hari ini.</p>
                        </div>`;
                            return;
                        }

                        let html = '<div class="list-group list-group-flush">';
                        sales.forEach(sale => {
                            html += `
                        <a href="/penjualan/${sale.referensi}" target="_blank" rel="noopener"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 text-sm">${sale.referensi}</h6>
                                <p class="text-xs text-secondary mb-0">${sale.name} &bull; ${sale.waktu}</p>
                            </div>
                            <div class="text-end">
                                <span class="text-sm fw-bold text-dark me-2">${formatCurrency(sale.total_akhir)}</span>
                                <span class="badge badge-sm bg-label-success">${sale.status}</span>
                            </div>
                        </a>`;
                        });
                        html += '</div>';
                        modalBody.innerHTML = html;
                    } catch (error) {
                        modalBody.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                    }
                });
            }

            // =========================================================
            // EVENT LISTENERS
            // =========================================================

            // Klik produk dari daftar
            productList.addEventListener('click', (e) => {
                const card = e.target.closest('.product-card');
                if (!card) return;

                const {
                    id,
                    name,
                    harga,
                    hargaAsli,
                    stok,
                    disabled,
                    img,
                    wajibSeri,
                    pajakId,
                    pajakRate
                } = card.dataset;
                if (disabled === 'true') return;

                const isWajibSeri = wajibSeri === 'true';
                tempProductDataForSN = {
                    id,
                    name,
                    harga,
                    hargaAsli,
                    stok,
                    img,
                    wajibSeri,
                    pajakId,
                    pajakRate
                };

                if (isWajibSeri) {
                    const itemInCart = cart.get(parseInt(id));
                    const existingSerials = itemInCart ? itemInCart.serial_numbers : [];
                    const requiredQty = itemInCart ? itemInCart.jumlah + 1 : 1;
                    openSerialNumberModal(id, name, requiredQty, existingSerials);
                } else {
                    const item = cart.get(parseInt(id));
                    if (item) {
                        updateQuantity(parseInt(id), item.jumlah + 1);
                    } else {
                        addProductToCart(tempProductDataForSN);
                    }
                }
            });

            // Aksi di dalam tabel keranjang (delegasi event)
            cartContainer.addEventListener('click', (e) => {
                const target = e.target.closest('[data-id]');
                if (!target) return;
                const id = parseInt(target.dataset.id);
                const item = cart.get(id);
                if (!item) return;

                if (e.target.closest('.qty-increase')) {
                    if (item.serial_numbers?.length > 0) {
                        tempProductDataForSN = {
                            id: item.id,
                            name: item.name,
                            harga: item.harga,
                            stok: item.stok,
                            img: item.img,
                            wajibSeri: item.wajib_seri,
                            pajakId: item.taxe_id,
                            pajakRate: item.pajak_rate
                        };
                        openSerialNumberModal(item.id, item.name, item.jumlah + 1, item.serial_numbers);
                    } else {
                        updateQuantity(id, item.jumlah + 1);
                    }
                } else if (e.target.closest('.qty-decrease')) {
                    updateQuantity(id, item.jumlah - 1);
                } else if (e.target.closest('.remove-item')) {
                    removeFromCart(id);
                } else if (e.target.closest('.edit-item')) {
                    openEditModal(id);
                }
            });

            // Reset keranjang
            if (resetButton) {
                resetButton.addEventListener('click', () => {
                    if (cart.size > 0 && confirm('Anda yakin ingin mengosongkan keranjang?')) {
                        cart.clear();
                        updateCartAndTotals();
                    }
                });
            }

            // Input pencarian: debounce untuk performa, Enter untuk barcode
            productSearchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const val = e.target.value.trim();
                    if (val) handleBarcodeScan(val);
                }
            });
            productSearchInput.addEventListener('input', debounce(filterProducts, 200));

            // Submit form
            mainForm.addEventListener('submit', function(e) {
                if (cart.size === 0) {
                    e.preventDefault();
                    showToast('Keranjang belanja kosong. Tambahkan produk terlebih dahulu.', 'warning');
                }
            });

            // Input jumlah bayar
            paymentInputEl.addEventListener('input', (e) => {
                // Format visual hanya saat blur agar tidak mengganggu ketikan
                calculateChange();
            });

            paymentInputEl.addEventListener('blur', formatNumberInput);

            // Bayar pas
            payExactButton.addEventListener('click', () => {
                const total = parseFloat(totalAkhirEl.textContent.replace(/[^0-9]/g, '')) || 0;
                paymentInputEl.value = new Intl.NumberFormat('id-ID').format(total);
                calculateChange();
            });

            // Filter kategori (delegasi event)
            const categoryFilterContainer = document.getElementById('category-container');
            if (categoryFilterContainer) {
                categoryFilterContainer.addEventListener('click', function(e) {
                    const clicked = e.target.closest('.category-btn');
                    if (!clicked || clicked.classList.contains('category-active')) return;

                    // Update status aktif
                    const current = categoryFilterContainer.querySelector('.category-active');
                    if (current) {
                        current.classList.remove('category-active');
                        current.setAttribute('aria-selected', 'false');
                    }
                    clicked.classList.add('category-active');
                    clicked.setAttribute('aria-selected', 'true');

                    selectedCategoryId = clicked.dataset.categoryId;
                    filterProducts();
                });
            }

            // =========================================================
            // INISIALISASI
            // =========================================================
            updateCartAndTotals();

        }); // akhir DOMContentLoaded
    </script>
@endsection
