@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')

    <div class="row g-3">
        <div class="col-xxl-8">
            <div class="card">
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Selamat Datang, {{ auth()->user()->username }} 🎉</h5>
                            <p class="">Berikut adalah Ringkasan Data selama Sebulan Terakhir profile.
                            </p>
                            <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-6">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="175"
                                alt="View Badge User" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Bagian Filter Tanggal --}}
        <div class="col-xxl-4 d-flex">
            <div class="card">
                <div class="card-body d-flex align-items-start ">
                    <form action="{{ route('dashboard') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="text" name="start_date" id="start_date" class="form-control flatpickr-date"
                                    placeholder="YYYY-MM-DD" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="text" name="end_date" id="end_date" class="form-control flatpickr-date"
                                    placeholder="YYYY-MM-DD" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-12 d-flex mb-n3">
                                <button type="submit" class="btn btn-dark w-100 me-3"><i
                                        class="bx bx-funnel-fill me-2"></i>Filter</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100"><i
                                        class="bx bx-arrow-clockwise me-2"></i>Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-12' }}">
            <div class="card bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-start">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-white text-success"><i
                                    class="bx bx-cart-check-fill bx-sm" aria-hidden="true"></i></span>
                        </div>
                        <div class="numbers w-100">
                            <p class="small mt-2 mb-0 fw-bold">Sale</p>
                            <h5 class="fw-bolder text-white mb-0">@money($totalSalePeriode)</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Tampilkan card berikut hanya jika pengguna bukan kasir (role_id != 2) --}}
        @if (auth()->user()->role_id != 2)
            <div class="col-md-3">
                <div class="card bg-dark text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-start">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-white text-dark"><i
                                        class="bx bx-box-arrow-in-down-right bx-sm" aria-hidden="true"></i></span>
                            </div>
                            <div class="numbers">
                                <p class="small mt-2 mb-0 fw-bold">Retur Sale</p>
                                <h5 class="fw-bolder text-white">@money($totalReturSalePeriode)</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-start">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-white text-primary"><i
                                        class="bx bx-bag-plus-fill bx-sm" aria-hidden="true"></i></span>
                            </div>
                            <div class="numbers">
                                <p class="small mt-2 mb-0 fw-bold">Purchase</p>
                                <h5 class="fw-bolder text-white">@money($totalPurchasePeriode)</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-start">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-white text-secondary"><i
                                        class="bx bx-box-arrow-down-left bx-sm" aria-hidden="true"></i></span>
                            </div>
                            <div class="numbers">
                                <p class="small mt-2 mb-0 fw-bold">Retur Purchase</p>
                                <h5 class="fw-bolder text-white">@money($totalReturPurchasePeriode)</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Card Pendapatan --}}
        {{-- Jika kasir, buat kolom lebih lebar (col-md-6) --}}
        <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-6' }}">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-cash-coin bx-sm"
                                    aria-hidden="true"></i></span>
                        </div>
                        <div class="numbers">
                            <p class="small mb-0 fw-bold">Pendapatan</p>
                            <h5 class="fw-bolder mb-0">@money($pendapatanPeriodeIni)</h5>
                        </div>
                    </div>
                    <p class="mb-0 mt-2 small">
                        <span class="fw-bolder {{ $persentasePendapatan >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $persentasePendapatan >= 0 ? '+' : '' }}{{ number_format($persentasePendapatan, 2) }}%
                        </span>
                        dari periode lalu
                    </p>
                </div>
            </div>
        </div>
        {{-- Card Transaksi --}}
        {{-- Jika kasir, buat kolom lebih lebar (col-md-6) --}}
        <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-6' }}">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-receipt-cutoff bx-sm"
                                    aria-hidden="true"></i></span>
                        </div>
                        <div class="numbers">
                            <p class="small mb-0 fw-bold">Transaksi</p>
                            <h5 class="fw-bolder mb-0">{{ $transaksiPeriodeIni }}</h5>
                        </div>
                    </div>
                    <p class="mb-0 mt-2 small">
                        <span class="fw-bolder {{ $persentaseTransaksi >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $persentaseTransaksi >= 0 ? '+' : '' }}{{ number_format($persentaseTransaksi, 2) }}%
                        </span>
                        dari periode lalu
                    </p>
                </div>
            </div>
        </div>
        {{-- Tampilkan card berikut hanya jika pengguna bukan kasir (role_id != 2) --}}
        @if (auth()->user()->role_id != 2)
            {{-- Card Expense --}}
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-wallet2 bx-sm"
                                        aria-hidden="true"></i></span>
                            </div>
                            <div class="numbers">
                                <p class="small mb-0 fw-bold">Expense</p>
                                <h5 class="fw-bolder mb-0">@money($totalExpensePeriode)</h5>
                            </div>
                        </div>
                        <p class="mb-0 mt-2 small">
                            <a href="{{ route('keuangan') }}" class="text-primary fw-bolder">Lihat
                                Detail</a>
                        </p>
                    </div>
                </div>
            </div>
            {{-- Card Laba Bersih --}}
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-graph-up-arrow bx-sm"
                                        aria-hidden="true"></i></span>
                            </div>
                            <div class="numbers">
                                <p class="small mb-0 fw-bold">Laba Bersih</p>
                                <h5 class="fw-bolder mb-0 {{ $labaBersihPeriode >= 0 ? 'text-success' : 'text-danger' }}">
                                    @money($labaBersihPeriode)</h5>
                            </div>
                        </div>
                        <p class="mb-0 mt-2 small">
                            <a href="{{ route('laporan.laba-rugi') }}" class="text-primary fw-bolder">Lihat
                                Laporan</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-7">
            <div class="card z-index-2 rounded-2 ">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Grafik Sale</h6>
                    <p class="text-sm mb-0">
                        <i class="bx bx-arrow-up text-success me-1"></i>
                        <span class="fw-bold">Total Pendapatan</span> 30 hari terakhir
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center">
                    <div class="badge bg-label-success p-2 me-2">
                        <i class="bx bx-box2-fill"></i>
                    </div>
                    <h6 class="mb-0">Product Terlaris</h6>
                </div>

                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse ($produkTerlaris as $produk)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 rounded">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp') }}"
                                            class="avatar avatar-lg rounded-2 me-3" alt="Gambar produk">
                                        <div>
                                            <h6 class="mb-n1 text-dark text-sm">{{ $produk->name_product }}</h6>
                                            <span class="small">@money($produk->harga_jual)</span>
                                            <i class="bx bx-circle-fill bi-xs mx-1 text-success"></i>
                                            <span class="small"><span
                                                    class="fw-bold">{{ $produk->total_terjual }}</span>
                                                terjual</span>
                                        </div>
                                    </div>
                                    <div>
                                        @if (isset($produk->percentage_increase) && $produk->percentage_increase !== 0)
                                            <span class="small">
                                                <span
                                                    class="fw-bolder {{ $produk->percentage_increase >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $produk->percentage_increase >= 0 ? '+' : '' }}{{ number_format($produk->percentage_increase, 2) }}%
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 p-5 text-center">
                                <div class="avatar avatar-md d-flex mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary w-100 h-100">
                                        <i class="bx bx-package bx-sm"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">Tidak Ada Data</h6>
                                <p class="text-muted small mb-0">Belum ada produk terlaris pada periode ini.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-warning p-2 me-2">
                            <i class="bx bx-box-seam-fill"></i>
                        </div>
                        <h6 class="mb-0">Product Stock Rendah</h6>
                    </div>
                    <a href="{{ route('stok.rendah') }}" class="btn btn-outline-dark btn-xs mb-0">Detail</a>
                </div>

                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse ($produkStockRendah as $produk)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 rounded">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp') }}"
                                            class="avatar avatar-lg rounded-2 me-3" alt="Gambar produk">
                                        <div>
                                            <h6 class="mb-n1 text-dark text-sm">{{ $produk->name_product }}</h6>
                                            <span class="small">Sisa <span
                                                    class="fw-bold text-danger">{{ $produk->qty }}</span></span>
                                            <i class="bx bx-circle-fill bi-xs mx-1 text-secondary"></i>
                                            <span class="small">Min. <span
                                                    class="fw-bold">{{ $produk->stok_minimum }}</span></span>
                                        </div>
                                    </div>
                                    {{-- Tombol beli hanya untuk non-kasir --}}
                                    @if (auth()->user()->role_id != 2)
                                        <a href="{{ route('pembelian.create') }}"
                                            class="btn btn-sm btn-dark mb-0 px-2 py-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Beli Product Ini">
                                            <i class="bx bx-cart-plus bi-sm"></i>
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 p-5 text-center">
                                <div class="avatar avatar-md d-flex mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary w-100 h-100">
                                        <i class="bx bx-box bx-sm"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">Stok Aman</h6>
                                <p class="text-muted small mb-0">Tidak ada produk dengan stok rendah saat ini.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-info p-2 me-2">
                            <i class="bx bx-person-check-fill"></i>
                        </div>
                        <h6 class="mb-0">Customer Terbaik</h6>
                    </div>
                    <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-dark btn-xs mb-0">Detail</a>
                </div>

                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse ($pelangganTerbaik as $pelanggan)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 rounded">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar avatar-lg rounded-2 me-3 bg-gradient-dark d-flex align-items-center justify-content-center">
                                            <span
                                                class="text-white fw-bold">{{ strtoupper(substr($pelanggan->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-n1 text-dark text-sm">{{ $pelanggan->name }}</h6>
                                            <span class="small"><span
                                                    class="fw-bold">{{ $pelanggan->total_orders }}</span>
                                                order</span>
                                            <i class="bx bx-circle-fill bi-xs mx-1 text-info"></i>
                                            <span class="small">Total <span
                                                    class="fw-bold">@money($pelanggan->total_spent)</span></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 p-5 text-center">
                                <div class="avatar avatar-md d-flex mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary w-100 h-100">
                                        <i class="bx bx-user-x bx-sm"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">Tidak Ada Transaksi</h6>
                                <p class="text-muted small mb-0">Belum ada transaksi dari pelanggan pada periode ini.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-secondary p-2 me-2">
                            <i class="bx bx-arrow-down-up"></i>
                        </div>
                        <h6 class="mb-0">Aktivitas Transaksi Terakhir</h6>
                    </div>
                    {{-- Tampilkan tab hanya jika pengguna adalah admin --}}
                    @if (auth()->user()->role_id == 1)
                        <ul class="nav nav-tabs card-header-tabs" id="transactionTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-sm fw-bolder" id="sales-tab" data-bs-toggle="tab"
                                    href="#recent-sales" role="tab" aria-controls="recent-sales"
                                    aria-selected="true">Sale</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-sm fw-bolder" id="purchases-tab" data-bs-toggle="tab"
                                    href="#recent-purchases" role="tab" aria-controls="recent-purchases"
                                    aria-selected="false">Purchase</a>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="tab-content" id="transactionTabsContent">
                        {{-- Tab Sale Terakhir --}}
                        <div class="tab-pane fade show active" id="recent-sales" role="tabpanel"
                            aria-labelledby="sales-tab">
                            <ul class="list-group">
                                @forelse ($recentSales as $sale)
                                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('penjualan.show', $sale->referensi) }}"
                                                class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                                <i class="bx bx-arrow-up"></i>
                                            </a>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-1 text-dark text-sm">{{ $sale->referensi }}</h6>
                                                <span class="small">{{ $sale->pelanggan->name ?? 'Customer Umum' }}
                                                    &bull;
                                                    {{ \Carbon\Carbon::parse($sale->tanggal_penjualan)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-end">
                                            <div class="me-3">
                                                <p class="text-sm mb-0 text-success fw-bold">
                                                    @money($sale->total_akhir)</p>
                                            </div>
                                            <x-badge-status-pembayaran :status="$sale->status_pembayaran" />
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item border-0 p-5 text-center rounded">
                                        <div class="avatar avatar-md d-flex mx-auto mb-3">
                                            <span class="avatar-initial rounded-circle bg-label-secondary w-100 h-100">
                                                <i class="bx bx-receipt bx-sm"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-1">Belum Ada Penjualan</h6>
                                        <p class="text-muted small mb-0">Transaksi penjualan Anda akan muncul di sini.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        {{-- Tab Purchase Terakhir (hanya untuk admin) --}}
                        @if (auth()->user()->role_id == 1)
                            <div class="tab-pane fade" id="recent-purchases" role="tabpanel"
                                aria-labelledby="purchases-tab">
                                <ul class="list-group">
                                    @forelse ($recentPurchases as $purchase)
                                        <li
                                            class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 rounded">
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('pembelian.show', $purchase->id) }}"
                                                    class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-arrow-down"></i>
                                                </a>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">{{ $purchase->referensi }}</h6>
                                                    <span class="small">{{ $purchase->pemasok->name ?? 'N/A' }}
                                                        &bull;
                                                        {{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-end">
                                                <div class="me-3">
                                                    <p class="text-sm mb-0 text-danger fw-bold">
                                                        @money($purchase->total_akhir)</p>
                                                </div>
                                                <x-badge-status-pembayaran :status="$purchase->status_pembayaran" />
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item border-0 p-5 text-center rounded">
                                            <div class="avatar avatar-md d-flex mx-auto mb-3">
                                                <span class="avatar-initial rounded-circle bg-label-secondary w-100 h-100">
                                                    <i class="bx bx-cart bx-sm"></i>
                                                </span>
                                            </div>
                                            <h6 class="mb-1">Belum Ada Pembelian</h6>
                                            <p class="text-muted small mb-0">Transaksi pembelian Anda akan muncul di sini.
                                            </p>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-100 ">
                        <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center">
                            <div class="badge bg-label-primary p-2 me-2">
                                <i class="bx bx-info-circle"></i>
                            </div>
                            <h6 class="mb-0">Ringkasan Keseluruhan</h6>
                        </div>

                        <div class="card-body p-3 d-flex">
                            <div class="row g-3 w-100">
                                <div class="col-6 col-md-3">
                                    <div class="rounded-3 border bg-light">
                                        <div class="text-center mb-3">
                                            <i class="bx bx-people-fill bi-lg  text-info opacity-10"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="mb-1 text-sm text-center">Total Customer</span>
                                            <h6 class="text-center fw-bolder">{{ $totalCustomer }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @if (auth()->user()->role_id == 1)
                                    <div class="col-6 col-md-3">
                                        <div class="rounded-3 border bg-light ">
                                            <div class="text-center mb-3">
                                                <i class="bx bx-person-fill-check bi-lg text-warning opacity-10"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="mb-1 text-sm text-center">Total Supplier</span>
                                                <h6 class="text-center fw-bolder">{{ $totalSupplier }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-6 col-md-3">
                                    <div class="rounded-3 border bg-light">
                                        <div class="text-center mb-3">
                                            <i class="bx bx-cart-dash-fill bi-lg text-success opacity-10"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="mb-1 text-sm text-center">Total Order</span>
                                            <h6 class="text-center fw-bolder">{{ $totalOrder }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @if (auth()->user()->role_id == 1)
                                    <div class="col-6 col-md-3">
                                        <div class="rounded-3 border bg-light">
                                            <div class="text-center mb-3">
                                                <i class="bx bx-cart-plus-fill bi-lg text-danger opacity-10"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="mb-1 text-sm text-center">Total Purchase</span>
                                                <h6 class="text-center fw-bolder">{{ $totalPurchase }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card h-100 ">
                        <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center">
                            <div class="badge bg-label-primary me-2">
                                <i class="bx bx-pie-chart-fill"></i>
                            </div>
                            <h6 class="mb-0">Kategori Terlaris</h6>
                        </div>

                        <div class="card-body p-3">
                            @if ($categoryChartLabels->isNotEmpty())
                                <canvas id="category-pie-chart" class="chart-canvas" height="300"></canvas>
                            @else
                                <div class="text-center p-5">
                                    <div class="avatar avatar-md d-flex mx-auto mb-3">
                                        <span class="avatar-initial rounded-circle bg-label-secondary w-100 h-100">
                                            <i class="bx bx-pie-chart-alt-2 bx-sm"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-1">Belum Ada Kategori</h6>
                                    <p class="text-muted small mb-0">Data penjualan berdasarkan kategori akan tampil di
                                        sini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('page-script')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="{{ asset('assets/js copy/plugins/Chart.extension.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Inisialisasi Flatpickr
                flatpickr(".flatpickr-date", {
                    dateFormat: "Y-m-d",
                    allowInput: true
                });
                // Pie Chart untuk Kategori Terlaris
                const categoryCtx = document.getElementById("category-pie-chart");
                if (categoryCtx) {
                    const categoryLabels = {!! json_encode($categoryChartLabels) !!};
                    const categoryData = {!! json_encode($categoryChartData) !!};

                    if (categoryLabels.length > 0) {
                        new Chart(categoryCtx, {
                            type: 'pie',
                            data: {
                                labels: categoryLabels,
                                datasets: [{
                                    label: 'Jumlah Terjual',
                                    data: categoryData,
                                    backgroundColor: [
                                        'rgba(94, 114, 228, 0.8)', // Primary
                                        'rgba(45, 206, 137, 0.8)', // Success
                                        'rgba(251, 99, 64, 0.8)', // Warning
                                        'rgba(23, 162, 184, 0.8)', // Info
                                        'rgba(245, 54, 92, 0.8)', // Danger
                                    ],
                                    borderColor: '#fff',
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            color: '#6c757d',
                                            padding: 15,
                                            font: {
                                                size: 11,
                                                family: "Open Sans",
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            });
        </script>
        <script>
            var ctx1 = document.getElementById("chart-line").getContext("2d");

            var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

            gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
            gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
            gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');

            new Chart(ctx1, {
                type: "line", // Mengubah tipe chart menjadi 'line'
                data: {
                    labels: {!! json_encode($salesChartLabels) !!},
                    datasets: [{
                        label: "Pendapatan",
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        borderColor: "#5e72e4",
                        backgroundColor: gradientStroke1, // Menggunakan gradient untuk background
                        fill: true,
                        data: {!! json_encode($salesChartData) !!}
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                maxTicksLimit: 6, // Batasi jumlah tick/label pada sumbu Y
                                display: true,
                                padding: 10,
                                color: '#6c757d',
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                callback: function(value, index, values) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                color: '#6c757d',
                                padding: 20,
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                    },
                },
            });
        </script>
    @endsection
@endsection
