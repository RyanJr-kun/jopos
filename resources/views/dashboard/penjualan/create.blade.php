<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
    <link rel="canonical" href="https://www.jocomputer.com/" />

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script defer src="https://kit.fontawesome.com/939a218158.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100">
    <nav class="navbar navbar-main navbar-expand-lg border-bottom sticky-top"
        style="height: 70px; background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px);">
        <div class="container-fluid">
            <a class="navbar-brand ms-n3 pt-2 mt-n3 m-md-0" href="/dashboard" target="_blank">
                <img src="{{ asset('assets/img/logomiring.svg') }}" class="navbar-brand" alt="Logo Jo Computer"
                    style="height: 60px;">
            </a>
            <div class="text-white rounded px-3 py-1 mt-n2 ms-n6 m-md-2  d-flex align-items-center order-lg-2 ms-auto ms-lg-2"
                style="background-color: #1f8f6d;">
                <i class="bx bx-clock-fill me-2"></i>
                <span id="realtime-clock" class="fw-bold small">Memuat...</span>
            </div>

            <div class="collapse navbar-collapse order-lg-3" id="navbar">
                <ul class="navbar-nav align-items-center ms-auto">
                    <li class="nav-item d-none d-lg-block">
                        <a href="/dashboard" class="nav-link"><button class="btn btn-primary btn-sm px-3 mb-0"><i
                                    class="bx bx-globe me-2"></i>Dashboard</button></a>
                    </li>
                    <div class="vr m-2 d-none d-lg-block"></div>
                    <li class="nav-item d-none d-md-block">
                        <a href="#fullscreen" class="nav-link" onclick="toggleFullScreen(event)">
                            <button class="btn btn-light d-flex align-items-center justify-content-center mb-0"
                                style="width: 30px; height: 30px; padding: 0;">
                                <i class="bx bx-fullscreen"></i>
                            </button>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#salesHistoryModal"
                            title="Riwayat Penjualan">
                            <button class="btn btn-light d-flex align-items-center justify-content-center mb-0"
                                style="width: 30px; height: 30px; padding: 0;">
                                <i class="bx bx-clock-history"></i>
                            </button>
                        </a>
                    </li>
                </ul>
            </div>
            @auth
                <div class="d-flex align-items-center order-lg-4 ms-lg-2">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link text-dark p-0 mb-2 mb-md-0" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @if (auth()->user()->img_user)
                                <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                    class="avatar avatar-sm rounded cursor-pointer">
                            @else
                                <i class="bx bx-person-circle cursor-pointer fs-5"></i>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="userDropdown">
                            <li class="text-start d-flex m-2">
                                @if (auth()->user()->img_user)
                                    <img src="{{ asset('storage/' . auth()->user()->img_user) }}" alt="Profile"
                                        class="avatar avatar-sm rounded-circle cursor-pointer">
                                @else
                                    <img src="{{ asset('assets/img/user.webp') }}" class="avatar avatar-sm me-3"
                                        alt="Gambar produk default">
                                @endif
                                <div class="ms-2">
                                    <p class="mb-1 text-xs fw-bolder">{{ auth()->user()->username }}</p>
                                    <p class="text-xs text-secondary">{{ auth()->user()->role->nama }}</p>
                                </div>
                            </li>
                            <li class="d-md-none d-block">
                                <hr class="horizontal dark mt-n2 mb-2">
                                <a class="dropdown-item border-radius-md" href="#" data-bs-toggle="modal"
                                    data-bs-target="#salesHistoryModal">
                                    <i class="bx bx-clock-history me-2"></i> Riwayat Penjualan
                                </a>
                            </li>
                            <li class="d-md-none d-block">
                                <a class="dropdown-item border-radius-md" href="/dashboard">
                                    <i class="bx bx-globe me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <hr class="horizontal dark mt-n2 mb-2 d-none d-md-block">
                                <a class="dropdown-item border-radius-md" href="/">
                                    <i class="bx bx-shop me-2"></i> Web Market
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="dropdown-item border-radius-md w-100 text-danger"
                                        style="text-align: left;">
                                        <i class="bx bx-box-arrow-right me-2"></i>Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <main class="main-content position-relative border-radius-lg d-flex flex-column min-vh-100">
        <div class="row m-0 m-md-3 d-md-flex d-block">
            <div class="col-md-8 col-12 border-end ">
                <div class="row mt-3 mx-2">
                    <div class="col-md-8 col-12">
                        <h6 class="mb-0 text-dark fw-bolder">Selamat Datang, <u
                                class="text-warning">{{ auth()->user()->username }}</u></h6>
                        <p class="text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="col-md-4 col-12 mt-2 mt-md-0">
                        <div class="ms-md-auto">
                            <input type="text" id="product-search" class="form-control "
                                placeholder="Cari produk...">
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="category-container" class="d-flex flex-nowrap gap-2 pb-2" style="overflow-x: auto;">
                            <div class="category-btn category-active border rounded-1 d-flex align-items-center ms-1 my-3 p-2"
                                style="height: 40px; cursor: pointer;" data-category-id="all"><i
                                    class="bx bx-tags me-1"></i>
                                <p class="fw-bolder text-xs ms-1 mb-0">Semua</p>
                            </div>
                            @foreach ($kategoris as $kategori)
                                @if ($kategori->produks->isNotEmpty())
                                    <div class="category-btn border rounded-1 d-flex align-items-center my-3 p-2 "
                                        style="height: 40px;  cursor: pointer;"
                                        data-category-id="{{ $kategori->id }}">
                                        <img src="{{ $kategori->img_kategori ? asset('storage/' . $kategori->img_kategori) : asset('assets/img/produk.png') }}"
                                            class="avatar avatar-xs rounded-1" alt="{{ $kategori->nama }}">
                                        <p class="fw-bolder text-xs ms-2 mb-0 ">{{ $kategori->nama }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="p-3 product-list-container mb-3" style="max-height: 80vh; overflow-y: auto;">
                    <div class="row" id="product-list">
                        @forelse ($produks as $produk)
                            <div class="col-12 col-md-4 col-xl-3 mb-3"
                                data-product-category-id="{{ $produk->kategori_produk_id }}">
                                <div class="card product-card rounded-2 p-2" data-id="{{ $produk->id }}"
                                    data-nama="{{ $produk->nama_produk }}"
                                    data-harga="{{ $produk->harga_diskon ?? $produk->harga_jual }}"
                                    data-harga-asli="{{ $produk->harga_jual }}"
                                    data-img="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                    data-stok="{{ $produk->qty }}"
                                    data-wajib-seri="{{ $produk->wajib_seri ? 'true' : 'false' }}"
                                    data-pajak-id="{{ $produk->pajak_id }}"
                                    data-pajak-rate="{{ $produk->pajak->rate ?? 0 }}"
                                    data-disabled="{{ $produk->qty < 1 ? 'true' : 'false' }}">
                                    <img class="card-img-top rounded-2"
                                        src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png') }}"
                                        alt="Gambar Produk">
                                    {{-- Badge Stok Habis & Promo --}}
                                    @if ($produk->qty < 1)
                                        <div class="product-badge">
                                            <span class="badge badge-danger">Stok Habis</span>
                                        </div>
                                    @elseif($produk->promos->isNotEmpty() && ($promo = $produk->promos->first()))
                                        <div class="product-badge">
                                            @if ($promo->tipe_diskon == 'percentage')
                                                <span class="badge badge-danger">{{ (int) $promo->nilai_diskon }}%
                                                    OFF</span>
                                            @else
                                                <span class="badge badge-info">PROMO</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <div class="row g-1">
                                            <div class="col-12">
                                                <p class="text-xs mb-1"><span
                                                        class="font-weight-bold">{{ $produk->kategori_produk->nama }}</span>
                                                </p>
                                                <h6 class="mb-0 product-name text-sm">{{ $produk->nama_produk }}</h6>
                                            </div>
                                            <hr class="horizontal dark my-2">
                                            @if ($produk->harga_diskon)
                                                <div class="col-12">
                                                    <div class="d-flex">
                                                        <p
                                                            class="text-sm text-muted  me-2 text-decoration-line-through">
                                                            {{ $produk->harga_formatted }}</p>
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
                            <div class="col-12 text-center">
                                <p class="text-muted">Tidak ada produk yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 ">
                <form action="{{ route('penjualan.store') }}" method="POST" id="penjualanForm">
                    @csrf
                    {{-- Display All Validation Errors --}}
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
                                    <p class="text-sm mb-0">Invoice: <span
                                            class="font-weight-bold">{{ $referensi }}</span></p>
                                    <input type="hidden" name="referensi" value="{{ $referensi }}">
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-md badge-success me-2" id="cart-item-count">
                                        <i class="fas fa-shopping-cart me-1"></i> 0 Item
                                    </span>
                                    <button type="button"
                                        class="btn btn-outline-danger btn-tooltip btn-sm py-1 px-2 mb-0 btn-reset-cart"
                                        id="btn-reset-cart" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        data-container="body" data-animation="true" title="Kosongkan Keranjang"
                                        disabled>
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            {{-- Pilihan Pelanggan --}}
                            <div class="mb-3">
                                <label for="Pelanggan" class="form-label">Pelanggan <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <select class="form-select me-2 @error('pelanggan_id') is-invalid @enderror"
                                        name="pelanggan_id" id="Pelanggan">
                                        {{-- <option value="1" selected>pelanggan umum</option> --}}
                                        @foreach ($pelanggans as $item)
                                            <option value="{{ $item->id }}" @selected(old('pelanggan_id') == $item->id)>
                                                {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('pelanggan_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <button type="button" class="btn btn-outline-info btn-xs mb-0"
                                        data-bs-toggle="modal" data-bs-target="#createPelangganModal"
                                        title="Tambah pelanggan Baru">
                                        <i class="bx bx-plus-lg cursor-pointer"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive p-0" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-hover align-items-center mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th class="text-uppercase text-dark text-xs font-weight-bolder">Produk</th>
                                            <th
                                                class="text-center text-uppercase text-dark text-xs font-weight-bolder">
                                                Qty</th>
                                            <th class="text-end text-uppercase text-dark text-xs font-weight-bolder ">
                                                PPN</th>
                                            <th class="text-end text-uppercase text-dark text-xs font-weight-bolder ">
                                                Subtotal</th>
                                            <th class="text-end text-uppercase text-dark text-xs font-weight-bolder">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-items-container">
                                        {{-- Cart items will be injected here by JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer pt-0">
                            <hr class="mt-0">
                            {{-- Rincian Biaya --}}
                            <div class="d-flex justify-content-between mb-0">
                                <p class="text-sm">Subtotal</p>
                                <p class="text-sm font-weight-bold" id="subtotal">Rp 0</p>
                            </div>
                            <div class="d-flex justify-content-between mb-0">
                                <p class="text-sm">PPN</p>
                                <p class="text-sm font-weight-bold" id="pajak-total-display">Rp 0</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="service"
                                data-label="Service">
                                <p class="text-sm mb-0">Service</p>
                                <p class="text-sm font-weight-bold mb-0" id="service-display">Rp 0</p>
                                <input type="hidden" name="service" id="service-input" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center my-3"
                                style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                                data-type="ongkir" data-label="Ongkos Kirim">
                                <p class="text-sm mb-0">Ongkir</p>
                                <p class="text-sm font-weight-bold mb-0" id="ongkir-display">Rp 0</p>
                                <input type="hidden" name="ongkir" id="ongkir-input" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="diskon"
                                data-label="Diskon">
                                <p class="text-sm mb-0">Diskon (Rp)</p>
                                <p class="text-sm font-weight-bold mb-0" id="diskon-display">Rp 0</p>
                                <input type="hidden" name="diskon" id="diskon-input" value="0">
                            </div>
                            <hr class="horizontal dark my-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-bold">Total</h6>
                                <h6 class="font-weight-bold" id="total-akhir">Rp 0</h6>
                            </div>
                            {{-- Metode Pembayaran & Catatan --}}

                            <div class="row mt-3">
                                {{-- Pembayaran & Kembalian --}}
                                <div class="col-12 d-flex justify-content-between">
                                    <label for="jumlah-dibayar-input" class="form-label text-sm">Jumlah
                                        Dibayar</label>
                                    <input type="text" name="jumlah_dibayar" id="jumlah-dibayar-input"
                                        class="form-control form-control-sm text-end w-30" value="0">
                                </div>
                                <div class="col-12 d-flex justify-content-between">
                                    <label class="form-label text-sm">Kembalian</label>
                                    <p class="form-control-plaintext text-end fw-bold mb-0" id="change-display">Rp 0
                                    </p>
                                </div>
                                <div class="col-6">
                                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select"
                                        required>
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
                                <button type="button" class="btn btn-info me-3" id="btn-pay-exact">Bayar
                                    Pas</button>
                                <button type="submit" class="btn btn-dark" id="btn-save-transaction">
                                    <i class="fas fa-save me-1"></i> Simpan Transaksi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- modal-create --}}
    <div class="modal fade" id="createPelangganModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title">Buat Pelanggan Baru</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createPelangganForm" action="{{ route('pelanggan.store') }}" method="post">
                        @csrf
                        <div class="mb-2">
                            <label for="nama" class="form-label">Nama</label>
                            <input id="nama" name="nama" type="text"
                                class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-1">
                            <label for="kontak" class="form-label">Kontak</label>
                            <input id="kontak" name="kontak" type="text"
                                class="form-control @error('kontak') is-invalid @enderror"
                                value="{{ old('kontak') }}" required>
                            @error('kontak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-1">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="example@gmail.com"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-1">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="justify-content-end form-check form-switch form-check-reverse mb-2">
                            <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                            <input id="status" class="form-check-input" type="checkbox" name="status"
                                value="1" checked>
                        </div>
                        <div class="modal-footer border-0 pb-0">
                            <button type="submit" class="btn btn-outline-info btn-sm p-2">Tambah Pelanggan</button>
                            <button type="button" class="btn btn-danger btn-sm p-2"
                                data-bs-dismiss="modal">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Item Keranjang --}}
    <div class="modal fade" id="editCartItemModal" tabindex="-1" aria-labelledby="editCartItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCartItemModalLabel">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCartItemForm" onsubmit="return false;">
                        <input type="hidden" id="edit-item-id">
                        <div class="row g-3 px-1">
                            <div class="col-12">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="edit-item-nama" readonly disabled>
                            </div>
                            <div class="col-12 form-group">
                                <label for="harga" class="form-control-label">Harga Jual <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control" id="edit-item-harga" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="edit-item-diskon" class="form-label">Diskon (Rp)</label>
                                <input type="text" class="form-control" id="edit-item-diskon" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label for="edit-item-pajak-id" class="form-label">Pajak</label>
                                <select class="form-select" id="edit-item-pajak-id">
                                    <option value="" data-rate="0" selected>Tidak Ada</option>
                                    @foreach ($pajaks as $pajak)
                                        <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                            {{ $pajak->nama_pajak }} ({{ $pajak->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" id="saveItemChangesBtn">Simpan
                        Perubahan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Riwayat Penjualan --}}
    <div class="modal fade" id="salesHistoryModal" tabindex="-1" aria-labelledby="salesHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="salesHistoryModalLabel">Riwayat Penjualan Hari Ini</h6>
                    <button type="button" class="btn btn-dark bg-dark btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="salesHistoryBody">
                    {{-- Konten akan dimuat di sini melalui AJAX --}}
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Biaya Tambahan (Service, Ongkir, Diskon) --}}
    <div class="modal fade" id="editExtraCostModal" tabindex="-1" aria-labelledby="editExtraCostModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editExtraCostModalLabel">Edit Biaya</h6>
                    <button type="button" class="btn bg-dark btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editExtraCostForm" onsubmit="return false;">
                        <input type="hidden" id="extra-cost-type">
                        {{-- Promo Code Section (Initially hidden) --}}
                        <div id="promo-code-section" class="mb-3" style="display: none;">
                            <label for="promo-code-input" class="form-control-label">Kode Promo</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promo-code-input"
                                    placeholder="Masukkan kode promo">
                                <button class="btn btn-outline-secondary mb-0" type="button"
                                    id="apply-promo-btn">Terapkan</button>
                            </div>
                            <div id="promo-feedback" class="mt-2 text-xs"></div>
                        </div>

                        <div class="form-group">
                            <label for="extra-cost-value" class="form-control-label"
                                id="extra-cost-label">Jumlah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="extra-cost-value" min="0"
                                    required>
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

    {{-- Modal Pembayaran --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Pembayaran</h5>
                    <button type="button" class="btn bg-dark btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <p class="text-sm mb-0">Total Tagihan</p>
                        <h3 class="font-weight-bolder" id="payment-modal-total">Rp 0</h3>
                    </div>
                    <div class="form-group">
                        <label for="payment-amount-input" class="form-control-label">Jumlah Bayar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control form-control-lg text-end"
                                id="payment-amount-input" placeholder="0">
                        </div>
                    </div>
                    <div class="row gx-2 mt-3">
                        <div class="col"><button class="btn btn-outline-secondary w-100 quick-pay-btn"
                                data-amount="pas">Uang Pas</button></div>
                        <div class="col"><button class="btn btn-outline-secondary w-100 quick-pay-btn"
                                data-amount="50000">50.000</button></div>
                        <div class="col"><button class="btn btn-outline-secondary w-100 quick-pay-btn"
                                data-amount="100000">100.000</button></div>
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
    {{-- Modal Pemilihan Nomor Seri --}}
    <div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="serialNumberModalLabel">Pilih Nomor Seri</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sn-produk-id">
                    <div class="badge badge-info p-2 text-sm w-100 text-start mb-2" role="alert">
                        Produk: <strong id="sn-nama-produk" class=" text-warning">Nama Produk</strong><br>
                        Anda harus memilih <strong id="sn-required-count">1</strong> nomor seri.
                    </div>
                    <div id="sn-list-container" class="list-group" style="max-height: 300px; overflow-y: auto;">
                        {{-- Daftar nomor seri akan dimuat di sini oleh AJAX --}}
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm"></div>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block mt-2" id="sn-error-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="btn-confirm-sn">Simpan Pilihan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk toggle fullscreen
        function toggleFullScreen(event) {
            event.preventDefault();
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- UTILITIES ---
            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);

            const formatNumberInput = (e) => {
                // Format input dengan pemisah ribuan saat diketik
                let value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = new Intl.NumberFormat('id-ID').format(value || 0);
            };

            // --- STATE ---
            const cart = new Map(); // Using Map for easier item management

            const pajakRates = new Map();
            @foreach ($pajaks as $pajak)
                pajakRates.set({{ $pajak->id }}, {{ $pajak->rate }});
            @endforeach

            // --- DOM ELEMENTS ---
            const productList = document.getElementById('product-list');
            const allProductCards = document.querySelectorAll('.product-card'); // Ambil semua kartu produk
            const cartContainer = document.getElementById('cart-items-container');
            const cartItemCount = document.getElementById('cart-item-count');
            const subtotalEl = document.getElementById('subtotal');
            const serviceInput = document.getElementById('service-input');
            const ongkirInput = document.getElementById('ongkir-input');
            const diskonInput = document.getElementById('diskon-input');
            const paymentDisplay = document.getElementById('payment-display');
            const changeDisplay = document.getElementById('change-display');
            const paymentInputHidden = document.getElementById('jumlah-dibayar-input');
            const payExactButton = document.getElementById('btn-pay-exact');
            const totalAkhirEl = document.getElementById('total-akhir');
            const productSearchInput = document.getElementById('product-search');
            const mainForm = document.getElementById('penjualanForm');
            const saveButton = document.getElementById('btn-save-transaction');
            const resetButton = document.getElementById('btn-reset-cart');
            const editItemModal = new bootstrap.Modal(document.getElementById('editCartItemModal'));
            const editItemForm = document.getElementById('editCartItemForm');
            const paymentInput = document.getElementById('jumlah-dibayar-input');

            // --- SERIAL NUMBER MODAL ELEMENTS (BARU) ---
            const serialNumberModalEl = document.getElementById('serialNumberModal');
            const serialNumberModal = new bootstrap.Modal(serialNumberModalEl);
            const snListContainer = document.getElementById('sn-list-container');
            const snNamaProduk = document.getElementById('sn-nama-produk');
            const snRequiredCount = document.getElementById('sn-required-count');
            const snConfirmBtn = document.getElementById('btn-confirm-sn');
            const snErrorMessage = document.getElementById('sn-error-message');
            let tempProductDataForSN = {}; // Untuk menyimpan data produk sementara


            // --- FUNCTIONS ---
            const updateCartAndTotals = () => {
                renderCart();
                calculateTotals();
                toggleSaveButton();
            };

            // DIUBAH: Fungsi ini sekarang menangani penambahan produk ke state `cart`
            const addProductToCart = (productData, serialNumbers = []) => {
                // PERBAIKAN 1: Ambil juga `hargaAsli` dari productData
                const {
                    id,
                    nama,
                    harga,
                    hargaAsli,
                    stok,
                    img,
                    wajibSeri,
                    pajakId,
                    pajakRate
                } = productData;
                const parsedId = parseInt(id);
                const parsedHargaFinal = parseFloat(
                harga); // Ini sudah harga diskon (jika ada) atau harga jual normal
                const parsedHargaAsli = parseFloat(hargaAsli); // Ini harga jual asli sebelum diskon
                const parsedStok = parseInt(stok);

                if (cart.has(parsedId)) {
                    const item = cart.get(parsedId);
                    if (item.jumlah < item.stok) {
                        item.jumlah++;
                    } else {
                        alert(`Stok untuk ${nama} tidak mencukupi.`);
                    }
                } else {
                    if (parsedStok > 0) {
                        const initialQuantity = serialNumbers.length > 0 ? serialNumbers.length : 1;

                        cart.set(parsedId, {
                            id: parsedId,
                            nama,
                            harga: parsedHargaAsli,
                            stok: parsedStok,
                            img,
                            jumlah: initialQuantity,
                            harga_jual: parsedHargaFinal,
                            diskon: 0,
                            pajak_id: pajakId ? parseInt(pajakId) : null,
                            pajak_rate: pajakRate ? parseFloat(pajakRate) : 0,
                            serial_numbers: serialNumbers,
                            wajib_seri: wajibSeri === 'true'
                        });
                    } else {
                        alert(`${nama} kehabisan stok.`);
                    }
                }
                updateCartAndTotals();
            };


            const updateQuantity = (id, newQuantity) => {
                if (!cart.has(id)) return;
                const item = cart.get(id);
                newQuantity = parseInt(newQuantity);

                // Logika baru untuk produk BERSERI (hanya saat dikurangi)
                if (item.serial_numbers && item.serial_numbers.length > 0) {
                    if (newQuantity > 0 && newQuantity < item.jumlah) {
                        // Jika kuantitas dikurangi, hapus nomor seri terakhir
                        item.serial_numbers.pop();
                        item.jumlah = newQuantity;
                    } else if (newQuantity <= 0) {
                        // Jika kuantitas menjadi 0 atau kurang, hapus item dari keranjang
                        cart.delete(id);
                    }
                }
                // Logika lama untuk produk NON-BERSERI
                else {
                    if (newQuantity > 0 && newQuantity <= item.stok) {
                        item.jumlah = newQuantity;
                    } else if (newQuantity > item.stok) {
                        item.jumlah = item.stok;
                        alert(`Stok maksimum untuk ${item.nama} adalah ${item.stok}.`);
                    } else {
                        // Jika kuantitas menjadi 0 atau kurang, hapus item
                        cart.delete(id);
                    }
                }

                // Perbarui tampilan keranjang dan total
                updateCartAndTotals();
            };

            const removeFromCart = (id) => {
                cart.delete(id);
                updateCartAndTotals();
            };

            const renderCart = () => {
                cartContainer.innerHTML = '';
                if (cart.size === 0) {
                    const emptyRow = `
                            <tr id="cart-empty-message" >
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <p>Keranjang masih kosong</p>
                                </td>
                            </tr>`;
                    cartContainer.innerHTML = emptyRow;
                } else {
                    let formIndex = 0;
                    cart.forEach(item => {
                        const subtotalItem = (item.harga_jual * item.jumlah) - item
                        .diskon; // Subtotal sebelum pajak
                        const pajakItem = subtotalItem * (item.pajak_rate /
                        100); // Hitung PPN untuk item ini
                        // Harga jual total untuk item ini (sudah termasuk pajak) dikurangi diskon item
                        const hargaJualTotalItem = (item.harga_jual * item.jumlah) - item.diskon;
                        // Hitung DPP (Dasar Pengenaan Pajak) dan Pajak dari harga jual inklusif
                        const dppItem = hargaJualTotalItem / (1 + (item.pajak_rate / 100));
                        const pajakAmountItem = hargaJualTotalItem - dppItem;

                        let serialNumberInputs = '';
                        if (item.serial_numbers && item.serial_numbers.length > 0) {
                            item.serial_numbers.forEach(sn => {
                                serialNumberInputs +=
                                    `<input type="hidden" name="items[${formIndex}][serial_numbers][]" value="${sn}">`;
                            });
                        }

                        // BARU: Tampilkan nomor seri di bawah nama produk
                        let serialNumberDisplay = '';
                        if (item.serial_numbers && item.serial_numbers.length > 0) {
                            serialNumberDisplay =
                                `<span class="text-xs text-muted d-block">SN: ${item.serial_numbers.join(', ')}</span>`;
                        }

                        let hargaDisplay =
                            `<span class="text-xs">${formatCurrency(item.harga_jual)}</span>`;
                        if (item.harga_jual < item
                            .harga) { // Jika harga jual (final) lebih kecil dari harga asli
                            hargaDisplay = `
                                    <span class="text-xs text-danger">${formatCurrency(item.harga_jual)}</span>
                                    <br>
                                    <small class="text-muted text-decoration-line-through">${formatCurrency(item.harga)}</small>
                                `;
                        }

                        // BARU: Logika untuk menampilkan/menyembunyikan tombol edit
                        const editButtonHtml = item.wajib_seri ? '' : `
                                <button class="btn btn-link text-dark p-0 cart-action-btn edit-item" data-id="${item.id}" title="Edit Item" type="button">
                                    <i class="bx bx-pencil-square bi-sm"></i>
                                </button>
                            `;

                        const itemHtml = `
                                <tr class="cart-item-row">
                                    <input type="hidden" name="items[${formIndex}][produk_id]" value="${item.id}">
                                    <input type="hidden" name="items[${formIndex}][jumlah]" value="${item.jumlah}">
                                    <input type="hidden" name="items[${formIndex}][harga_jual]" value="${item.harga_jual}">
                                    <input type="hidden" name="items[${formIndex}][diskon]" value="${item.diskon}">
                                    <input type="hidden" name="items[${formIndex}][pajak_id]" value="${item.pajak_id || ''}">
                                    ${serialNumberInputs}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="${item.img}" class="avatar avatar-sm rounded me-2" alt="product image">
                                            <div class="d-flex flex-column" style="min-width: 0;">
                                                <h6 class="mb-0 text-xs text-wrap">${item.nama}</h6>
                                                ${serialNumberDisplay}
                                                <span class="text-xs">${formatCurrency(item.harga_jual)}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-outline-primary btn-sm rounded-circle p-0 mt-3 qty-decrease" data-id="${item.id}" type="button" style="width: 20px; height: 20px;">-</button>
                                        <span class="fw-bold px-1 text-sm">${item.jumlah}</span>
                                        <button class="btn btn-outline-primary btn-sm rounded-circle p-0 mt-3 qty-increase" data-id="${item.id}" type="button" style="width: 20px; height: 20px;">+</button>
                                    </td>
                                    <td class="align-middle text-end"><span class="text-xs fw-bold">${formatCurrency(pajakAmountItem)}</span></td>
                                    <td class="align-middle text-end"><span class="text-xs fw-bold">${formatCurrency(dppItem)}</span></td>
                                    <td class="align-middle text-end" style="padding-top: 25px;">
                                        <div class="d-flex justify-content-end">
                                            ${editButtonHtml}
                                            <button class="btn btn-link text-danger p-0 mx-2 cart-action-btn remove-item" data-id="${item.id}" title="Hapus Item" type="button">
                                                <i class="bx bx-trash bi-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        cartContainer.insertAdjacentHTML('beforeend', itemHtml);
                        formIndex++;
                    });
                }
                const totalItems = Array.from(cart.values()).reduce((sum, item) => sum + item.jumlah, 0);
                cartItemCount.innerHTML = `<i class="fas fa-shopping-cart me-1"></i> ${totalItems} Item`;

                // Update status 'active' pada kartu produk
                allProductCards.forEach(card => {
                    const cardId = parseInt(card.dataset.id);
                    if (cart.has(cardId)) card.classList.add('active');
                    else card.classList.remove('active');
                });
            };

            const calculateTotals = () => {
                let subtotal = 0;
                let totalPajak = 0;

                cart.forEach(item => {
                    // Harga jual total untuk item ini (sudah termasuk pajak) dikurangi diskon item
                    const hargaJualTotalItem = (item.harga_jual * item.jumlah) - item.diskon;
                    // Hitung DPP (Dasar Pengenaan Pajak) dan Pajak dari harga jual inklusif
                    const dppItem = hargaJualTotalItem / (1 + (item.pajak_rate / 100));
                    const pajakAmountItem = hargaJualTotalItem - dppItem;

                    subtotal += dppItem; // Subtotal adalah total DPP
                    totalPajak += pajakAmountItem; // Akumulasi total pajak
                });

                const service = parseFloat(serviceInput.value) || 0;
                const ongkir = parseFloat(ongkirInput.value) || 0;
                const diskon = parseFloat(diskonInput.value) || 0;
                const total = (subtotal + totalPajak + service + ongkir) -
                diskon; // Total akhir adalah DPP + Pajak + Biaya Lain - Diskon Global

                subtotalEl.textContent = formatCurrency(subtotal);
                document.getElementById('pajak-total-display').textContent = formatCurrency(totalPajak);
                totalAkhirEl.textContent = formatCurrency(total);

                calculateChange(); // Panggil kalkulasi kembalian
                return total;
            };

            const calculateChange = () => {
                const total = parseFloat(totalAkhirEl.textContent.replace(/[^0-9]/g, '')) || 0;
                const paymentAmount = parseFloat(paymentInput.value.replace(/[^0-9]/g, '')) ||
                0; // Get clean value
                const change = paymentAmount - total;

                changeDisplay.textContent = formatCurrency(change);
                // FIX: Always set the hidden input with the raw, unformatted number.
                paymentInput.value = paymentAmount;
                if (change < 0) {
                    changeDisplay.classList.add('text-danger');
                } else {
                    changeDisplay.classList.remove('text-danger');
                }
            };

            let selectedCategoryId = 'all'; // State untuk menyimpan kategori yang aktif

            const filterProducts = () => {
                const searchTerm = productSearchInput.value.toLowerCase();

                allProductCards.forEach(card => {
                    const wrapper = card.parentElement; // The .col-* div
                    const productName = card.dataset.nama.toLowerCase();
                    const productCategoryId = wrapper.dataset
                    .productCategoryId; // FIX: Ambil ID kategori dari wrapper

                    const categoryMatch = (selectedCategoryId === 'all' || selectedCategoryId ==
                        productCategoryId);
                    const searchMatch = productName.includes(searchTerm);

                    // Logika yang disederhanakan: langsung tampilkan atau sembunyikan
                    if (categoryMatch && searchMatch) {
                        wrapper.style.display =
                        ''; // Menghapus style inline akan mengembalikannya ke default (block)
                    } else {
                        wrapper.style.display = 'none';
                    }
                });
            };

            const toggleSaveButton = () => {
                saveButton.disabled = cart.size === 0;
                resetButton.disabled = cart.size === 0;
            };

            // --- BARCODE SCANNER LOGIC ---
            const handleBarcodeScan = async (barcode) => {
                if (!barcode) return;

                try {
                    const url =
                        "{{ route('get-data.produk.by-barcode', ['barcode' => 'BARCODE_PLACEHOLDER']) }}"
                        .replace('BARCODE_PLACEHOLDER', barcode);
                    const response = await fetch(url);
                    const data = await response.json();

                    if (!response.ok) {
                        // Menampilkan pesan error dari server (misal: stok habis, tidak ditemukan)
                        throw new Error(data.message || 'Produk tidak ditemukan.');
                    }

                    // Jika produk ditemukan, siapkan data untuk ditambahkan ke keranjang
                    const productData = {
                        id: data.id,
                        nama: data.nama_produk,
                        harga: data.harga_jual,
                        stok: data.qty,
                        img: data.img_produk ? `{{ asset('storage/') }}/${data.img_produk}` :
                            `{{ asset('assets/img/produk.png') }}`,
                        wajibSeri: data.wajib_seri ? 'true' : 'false',
                        pajakId: data.pajak_id,
                        pajakRate: data.pajak ? data.pajak.rate : 0
                    };

                    // Cek apakah produk sudah ada di keranjang
                    const itemInCart = cart.get(productData.id);

                    if (productData.wajibSeri === 'true') {
                        // Jika wajib seri, selalu buka modal untuk memilih SN
                        const requiredQty = itemInCart ? itemInCart.jumlah + 1 : 1;
                        const existingSerials = itemInCart ? itemInCart.serial_numbers : [];
                        tempProductDataForSN = productData; // Simpan data produk untuk modal
                        openSerialNumberModal(productData.id, productData.nama, requiredQty,
                            existingSerials);
                    } else {
                        // Jika tidak wajib seri, langsung tambah/update kuantitas
                        if (itemInCart) {
                            updateQuantity(productData.id, itemInCart.jumlah + 1);
                        } else {
                            addProductToCart(productData);
                        }
                    }

                    // Kosongkan input setelah berhasil
                    productSearchInput.value = '';

                } catch (error) {
                    // Tampilkan notifikasi error kepada kasir
                    alert(`Error: ${error.message}`);
                }
            };

            // --- EVENT LISTENERS ---
            // --- PERUBAHAN 3: Membaca data-pajak-id saat produk diklik ---
            productList.addEventListener('click', (e) => {
                const card = e.target.closest('.product-card');
                if (!card) return;

                // Ambil pajakId dari dataset card
                const {
                    id,
                    nama,
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
                // Simpan semua data termasuk pajakId ke objek sementara
                tempProductDataForSN = {
                    id,
                    nama,
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
                    openSerialNumberModal(id, nama, requiredQty, existingSerials);
                } else {
                    const item = cart.get(parseInt(id));
                    if (item) {
                        updateQuantity(parseInt(id), item.jumlah + 1);
                    } else {
                        // Kirim objek lengkap ke fungsi addProductToCart
                        addProductToCart(tempProductDataForSN);
                    }
                }
            });
            // --- AKHIR PERUBAHAN 3 ---

            cartContainer.addEventListener('click', (e) => {
                const id = parseInt(e.target.closest('[data-id]')?.dataset.id);
                if (!id) return;

                const item = cart.get(id); // Ambil data item di awal
                if (!item) return;

                // Logika untuk tombol TAMBAH (+)
                if (e.target.classList.contains('qty-increase')) {
                    // JIKA PRODUK BERSERI
                    if (item.serial_numbers && item.serial_numbers.length > 0) {
                        // Siapkan data untuk membuka modal
                        tempProductDataForSN = {
                            id: item.id,
                            nama: item.nama,
                            harga: item.harga,
                            stok: item.stok,
                            img: item.img,
                            wajibSeri: item.wajib_seri,
                            pajakId: item.pajak_id,
                            pajakRate: item.pajak_rate
                        };
                        const requiredQty = item.jumlah + 1;
                        // Buka modal untuk memilih SN tambahan
                        openSerialNumberModal(item.id, item.nama, requiredQty, item.serial_numbers);
                    } else {
                        // Jika produk non-berseri, panggil fungsi updateQuantity seperti biasa
                        updateQuantity(id, item.jumlah + 1);
                    }
                }
                // Logika untuk tombol KURANG (-)
                else if (e.target.classList.contains('qty-decrease')) {
                    // Untuk produk berseri atau non-berseri, panggil fungsi updateQuantity
                    updateQuantity(id, item.jumlah - 1);
                }
                // Logika untuk Hapus dan Edit Item
                else if (e.target.closest('.remove-item')) {
                    removeFromCart(id);
                } else if (e.target.closest('.edit-item')) {
                    openEditModal(id);
                }
            });

            if (resetButton) {
                resetButton.addEventListener('click', () => {
                    if (cart.size > 0 && confirm('Anda yakin ingin mengosongkan keranjang?')) {
                        cart.clear();
                        updateCartAndTotals();
                    }
                });
            }

            // Menggabungkan event listener untuk pencarian manual dan scan barcode
            productSearchInput.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Mencegah form ter-submit
                    handleBarcodeScan(e.target.value.trim());
                }
                filterProducts(); // Tetap jalankan filter untuk pencarian manual
            });

            mainForm.addEventListener('submit', function(e) {
                if (cart.size === 0) {
                    e.preventDefault();
                    alert('Keranjang belanja kosong. Silakan tambahkan produk terlebih dahulu.');
                    return;
                }
                // The value is already clean due to the fix in calculateChange, so no extra cleanup is needed here.
            });

            paymentInput.addEventListener('input', (e) => {
                formatNumberInput(e);
                calculateChange();
            });

            payExactButton.addEventListener('click', () => {
                const total = parseFloat(totalAkhirEl.textContent.replace(/[^0-9]/g, '')) || 0;
                paymentInput.value = new Intl.NumberFormat('id-ID').format(total);
                calculateChange();
            });

            // --- INITIALIZATION ---
            updateCartAndTotals();

            // --- EDIT ITEM MODAL LOGIC ---
            const editItemHargaInput = document.getElementById('edit-item-harga');
            const editItemDiskonInput = document.getElementById('edit-item-diskon');

            function openEditModal(id) {
                const item = cart.get(id);
                if (!item) return;

                // PENYESUAIAN: Mencegah edit harga/diskon untuk item berseri
                if (item.serial_numbers.length > 0) {
                    alert(
                    'Pengeditan item dengan nomor seri tidak diizinkan. Silakan hapus dan tambahkan kembali.');
                    return;
                }

                document.getElementById('edit-item-id').value = id;
                document.getElementById('edit-item-nama').value = item.nama;
                editItemHargaInput.value = new Intl.NumberFormat('id-ID').format(item.harga_jual);
                editItemDiskonInput.value = new Intl.NumberFormat('id-ID').format(item.diskon);
                document.getElementById('edit-item-pajak-id').value = item.pajak_id || "";

                editItemModal.show();
            }

            editItemHargaInput.addEventListener('input', formatNumberInput);
            editItemDiskonInput.addEventListener('input', formatNumberInput);

            document.getElementById('saveItemChangesBtn').addEventListener('click', () => {
                const id = parseInt(document.getElementById('edit-item-id').value);
                const item = cart.get(id);
                if (!item) return;

                item.harga_jual = parseFloat(editItemHargaInput.value.replace(/[^0-9]/g, '')) || item.harga;
                item.diskon = parseFloat(editItemDiskonInput.value.replace(/[^0-9]/g, '')) || 0;

                const pajakSelect = document.getElementById('edit-item-pajak-id');
                const selectedPajak = pajakSelect.options[pajakSelect.selectedIndex];
                item.pajak_id = selectedPajak.value ? parseInt(selectedPajak.value) : null;
                item.pajak_rate = parseFloat(selectedPajak.dataset.rate) || 0;

                updateCartAndTotals();
                editItemModal.hide();
            });

            // --- EXTRA COST MODAL LOGIC ---
            const extraCostModal = new bootstrap.Modal(document.getElementById('editExtraCostModal'));
            const extraCostModalEl = document.getElementById('editExtraCostModal');
            const extraCostValueInput = document.getElementById('extra-cost-value');
            const promoCodeSection = document.getElementById('promo-code-section');
            const applyPromoBtn = document.getElementById('apply-promo-btn');

            extraCostModalEl.addEventListener('show.bs.modal', function(event) {
                const triggerElement = event.relatedTarget;
                const type = triggerElement.dataset.type;
                const label = triggerElement.dataset.label;

                const targetInput = document.getElementById(`${type}-input`);
                const currentValue = targetInput.value;

                document.getElementById('extra-cost-type').value = type;
                document.getElementById('editExtraCostModalLabel').textContent = `Edit ${label}`;
                document.getElementById('extra-cost-label').textContent = label;
                extraCostValueInput.value = new Intl.NumberFormat('id-ID').format(currentValue || 0);

                // Show promo section only for 'diskon'
                if (type === 'diskon') {
                    promoCodeSection.style.display = 'block';
                    extraCostValueInput.readOnly = false; // Pastikan tidak readonly
                    document.getElementById('promo-code-input').value = '';
                    document.getElementById('promo-feedback').innerHTML = '';
                } else {
                    promoCodeSection.style.display = 'none';
                }
                setTimeout(() => extraCostValueInput.focus(), 500); // Fokus ke input setelah modal tampil
            });

            extraCostValueInput.addEventListener('input', formatNumberInput);

            document.getElementById('saveExtraCostBtn').addEventListener('click', () => {
                const type = document.getElementById('extra-cost-type').value;
                const newValue = parseFloat(extraCostValueInput.value.replace(/[^0-9]/g, '')) || 0;

                const targetInput = document.getElementById(`${type}-input`);
                const targetDisplay = document.getElementById(`${type}-display`);

                targetInput.value = newValue;
                targetDisplay.textContent = formatCurrency(newValue);

                calculateTotals();
                extraCostModal.hide();
            });

            applyPromoBtn.addEventListener('click', async () => {
                const promoCode = document.getElementById('promo-code-input').value.trim();
                const promoFeedback = document.getElementById('promo-feedback');
                if (!promoCode) {
                    promoFeedback.innerHTML =
                        `<span class="text-danger">Silakan masukkan kode promo.</span>`;
                    return;
                }

                // Ambil subtotal (DPP) dari total
                let subtotal = 0;
                cart.forEach(item => {
                    const hargaJualTotalItem = (item.harga_jual * item.jumlah) - item.diskon;
                    const dppItem = hargaJualTotalItem / (1 + (item.pajak_rate / 100));
                    subtotal += dppItem;
                });

                promoFeedback.innerHTML = `<span class="text-muted">Memvalidasi...</span>`;

                try {
                    const response = await fetch("{{ route('promo.validateCode') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            kode_promo: promoCode,
                            subtotal: subtotal
                        })
                    });

                    const result = await response.json();

                    if (!result.success) {
                        throw new Error(result.message);
                    }

                    const promo = result.promo;
                    let discountAmount = 0;

                    // PERBAIKAN: Terapkan pembulatan pada diskon persentase
                    if (promo.tipe_diskon === 'percentage') {
                        // Hitung nilai diskon mentah
                        let rawDiscount = subtotal * (promo.nilai_diskon / 100);
                        // Bulatkan ke bilangan bulat terdekat
                        discountAmount = Math.round(rawDiscount);

                        if (promo.max_diskon && discountAmount > promo.max_diskon) {
                            discountAmount = promo.max_diskon;
                        }
                    } else { // fixed
                        discountAmount = promo.nilai_diskon;
                    }

                    extraCostValueInput.value = new Intl.NumberFormat('id-ID').format(discountAmount);
                    extraCostValueInput.readOnly = true; // Kunci input setelah promo diterapkan
                    promoFeedback.innerHTML =
                        `<span class="text-success fw-bold">Promo "${promo.nama_promo}" berhasil diterapkan!</span>`;

                } catch (error) {
                    promoFeedback.innerHTML = `<span class="text-danger">${error.message}</span>`;
                    extraCostValueInput.readOnly = false; // Buka kembali jika gagal
                }
            });

            // GANTI SELURUH FUNGSI INI
            async function openSerialNumberModal(produkId, namaProduk, requiredQty = 1, existingSerials = []) {
                snNamaProduk.textContent = tempProductDataForSN.nama || namaProduk;
                snRequiredCount.textContent = requiredQty; // Menampilkan jumlah yang benar di pesan info
                snErrorMessage.textContent = '';
                snListContainer.innerHTML =
                    '<div class="text-center"><div class="spinner-border spinner-border-sm"></div></div>';
                serialNumberModal.show();

                // Simpan data kuantitas yang dibutuhkan di elemen modal itu sendiri
                serialNumberModalEl.dataset.produkId = produkId;
                serialNumberModalEl.dataset.requiredQty = requiredQty; // INI SANGAT PENTING

                try {
                    const url = "{{ route('serialNumber.getByProduct', ['produk_id' => 'ID_PRODUK']) }}"
                        .replace('ID_PRODUK', produkId);
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) throw new Error('Gagal memuat data nomor seri.');
                    const serialNumbers = await response.json();
                    snListContainer.innerHTML = '';
                    if (serialNumbers.length === 0 && existingSerials.length === 0) {
                        snListContainer.innerHTML =
                            '<p class="text-muted text-center">Tidak ada nomor seri yang tersedia.</p>';
                        snConfirmBtn.disabled = true;
                    } else {
                        const allPossibleSerials = [...new Set([...serialNumbers, ...existingSerials])];
                        allPossibleSerials.forEach(sn => {
                            const isChecked = existingSerials.includes(sn) ? 'checked' : '';
                            const snElement = `
                                    <label class="list-group-item">
                                        <input class="form-check-input me-1" type="checkbox" value="${sn}" ${isChecked}>
                                        ${sn}
                                    </label>`;
                            snListContainer.insertAdjacentHTML('beforeend', snElement);
                        });
                        snConfirmBtn.disabled = false;
                    }
                } catch (error) {
                    snListContainer.innerHTML =
                        `<div class="alert alert-danger text-white">${error.message}</div>`;
                    snConfirmBtn.disabled = true;
                }
            }

            snConfirmBtn.addEventListener('click', () => {
                const selectedCheckboxes = snListContainer.querySelectorAll(
                    'input[type="checkbox"]:checked');

                // Mengambil jumlah yang dibutuhkan dari 'dataset' yang sudah kita simpan sebelumnya
                const requiredCount = parseInt(serialNumberModalEl.dataset.requiredQty);

                // Validasi baru berdasarkan jumlah yang dibutuhkan
                if (selectedCheckboxes.length !== requiredCount) {
                    snErrorMessage.textContent = `Anda harus memilih tepat ${requiredCount} nomor seri.`;
                    return; // Hentikan jika tidak sesuai
                }

                snErrorMessage.textContent = '';
                const selectedSerials = Array.from(selectedCheckboxes).map(cb => cb.value);
                const produkId = parseInt(tempProductDataForSN.id);

                if (cart.has(produkId)) {
                    // Update item yang ada di keranjang
                    const item = cart.get(produkId);
                    item.jumlah = selectedSerials.length;
                    item.serial_numbers = selectedSerials;
                } else {
                    // Tambah item baru ke keranjang
                    addProductToCart(tempProductDataForSN, selectedSerials);
                }

                updateCartAndTotals();
                serialNumberModal.hide();
                tempProductDataForSN = {};
            });


            // --- MODAL PELANGGAN BARU (AJAX) ---
            const addCustomerModalEl = document.getElementById('createPelangganModal');
            const addCustomerForm = document.getElementById('createPelangganForm');
            const pelangganSelect = document.getElementById('Pelanggan');

            addCustomerForm.addEventListener('submit', async function(e) {
                e.preventDefault(); // Mencegah submit form standar

                // 1. Hapus pesan error sebelumnya
                addCustomerForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                    'is-invalid'));
                addCustomerForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                // 2. Kumpulkan data dari form modal
                const data = {
                    nama: addCustomerForm.querySelector('#nama').value,
                    kontak: addCustomerForm.querySelector('#kontak').value,
                    email: addCustomerForm.querySelector('#email').value,
                    alamat: addCustomerForm.querySelector('#alamat').value,
                    status: addCustomerForm.querySelector('#status').checked
                };

                try {
                    // 3. Kirim data ke controller
                    const response = await fetch("{{ route('pelanggan.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' // Penting agar Laravel tahu ini permintaan AJAX
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        // 4. Tangani error validasi (status 422)
                        if (response.status === 422 && result.errors) {
                            Object.keys(result.errors).forEach(key => {
                                const input = addCustomerForm.querySelector(`#${key}`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'invalid-feedback';
                                    errorDiv.innerText = result.errors[key][0];
                                    // Sisipkan pesan error tepat di bawah input
                                    input.parentNode.insertBefore(errorDiv, input.nextSibling);
                                }
                            });
                        } else {
                            throw new Error(result.message || 'Terjadi kesalahan server.');
                        }
                    } else {
                        // 5. Tangani respons sukses (status 201)
                        const newPelanggan = result.data;

                        // Tambahkan pelanggan baru ke dropdown dan langsung pilih
                        const newOption = new Option(newPelanggan.nama, newPelanggan.id, true, true);
                        pelangganSelect.appendChild(newOption);

                        // Tutup modal dan reset form
                        const modal = bootstrap.Modal.getInstance(addCustomerModalEl);
                        modal.hide();
                        addCustomerForm.reset();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal menyimpan pelanggan: ' + error.message);
                }
            });

            // --- CATEGORY FILTER HIGHLIGHT ---
            const categoryFilterContainer = document.getElementById('category-container');
            if (categoryFilterContainer) {
                categoryFilterContainer.addEventListener('click', function(e) {
                    const clickedButton = e.target.closest('.category-btn');

                    // Keluar jika yang diklik bukan tombol kategori atau jika tombol yang diklik sudah aktif
                    if (!clickedButton || clickedButton.classList.contains('category-active')) {
                        return;
                    }

                    // 1. Cari tombol yang aktif saat ini dan hapus kelasnya
                    const currentActive = categoryFilterContainer.querySelector('.category-active');
                    if (currentActive) {
                        currentActive.classList.remove('category-active');
                    }

                    // 2. Tambahkan kelas 'category-active' ke tombol yang baru saja diklik
                    clickedButton.classList.add('category-active');

                    // 3. Update state kategori yang dipilih dan panggil fungsi filter
                    selectedCategoryId = clickedButton.dataset.categoryId;
                    filterProducts(); // Panggil filter setiap kali kategori diubah
                });
            }

            // --- MODAL RIWAYAT PENJUALAN ---
            const salesHistoryModal = document.getElementById('salesHistoryModal');
            if (salesHistoryModal) {
                salesHistoryModal.addEventListener('show.bs.modal', async function() {
                    const modalBody = document.getElementById('salesHistoryBody');
                    // Tampilkan spinner saat memuat
                    modalBody.innerHTML = `
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>`;

                    try {
                        // Ganti 'penjualan.history.today' dengan nama route Anda yang sebenarnya
                        const response = await fetch("{{ route('penjualan.history.today') }}", {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat data riwayat penjualan.');
                        }

                        const sales = await response.json();

                        if (sales.length === 0) {
                            modalBody.innerHTML = `
                                    <div class="text-center py-5 text-muted">
                                        <i class="bx bx-cart-x fa-3x mb-3"></i>
                                        <p class="mb-0">Belum ada transaksi hari ini.</p>
                                    </div>`;
                            return;
                        }

                        let historyHtml = '<div class="list-group list-group-flush">';
                        sales.forEach(sale => {
                            historyHtml += `
                                    <a href="/penjualan/${sale.referensi}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 text-sm">${sale.referensi}</h6>
                                            <p class="text-xs text-secondary mb-0">${sale.nama} &bull; ${sale.waktu}</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-sm fw-bold text-dark me-2">${formatCurrency(sale.total_akhir)}</span>
                                            <p class="badge badge-sm badge-success mb-0">${sale.status}</p>
                                        </div>
                                    </a>`;
                        });
                        historyHtml += '</div>';
                        modalBody.innerHTML = historyHtml;
                    } catch (error) {
                        modalBody.innerHTML =
                            `<div class="alert alert-danger text-white">${error.message}</div>`;
                    }
                });
            }
            updateCartAndTotals();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clockElement = document.getElementById('realtime-clock');

            function updateClock() {
                if (clockElement) {
                    const now = new Date();
                    const options = {
                        timeZone: 'Asia/Jakarta', // Zona Waktu Indonesia Barat
                        // weekday: 'long',
                        // day: 'numeric',
                        // month: 'long',
                        // year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    };
                    clockElement.textContent = new Intl.DateTimeFormat('id-ID', options).format(now).replace(/\./g,
                        ':');
                }
            }
            setInterval(updateClock, 1000); // Update setiap detik
            updateClock(); // Panggil sekali saat halaman dimuat
        });
    </script>
</body>

</html>
