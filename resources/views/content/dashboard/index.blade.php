@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    {{-- breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumbItems = [['name' => 'Dashboard', 'url' => route('dashboard')]];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="row g-3">
        {{-- Bagian Filter Tanggal --}}
        <div class="col-12">
            <div class="card rounded-2">
                <div class="card-body p-3">
                    <form action="{{ route('dashboard') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <h5 class="mb-0">Selamat Datang, <span
                                        class="fw-bolder text-warning">{{ auth()->user()->username }}</span></h5>
                                <span class="mb-0 text-sm">Berikut adalah Ringkasan Data selama Sebulan Terakhir</span>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2 d-flex mb-n3">
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
            <div class="card rounded-2" style="background-color: #29b648;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-start">
                        <div
                            class="icon icon-shape bg-white shadow shadow-success align-items-center text-center rounded-3 me-3">
                            <i class="bx bx-cart-check-fill" aria-hidden="true" style="color: #29b648;"></i>
                        </div>
                        <div class="numbers w-100">
                            <p class="text-xs mt-2 mb-0 text-white font-weight-bold">Sale</p>
                            <h5 class="font-weight-bolder text-white mb-0">@money($totalSalePeriode)</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Tampilkan card berikut hanya jika pengguna bukan kasir (role_id != 2) --}}
        @if (auth()->user()->role_id != 2)
            <div class="col-md-3">
                <div class="card rounded-2" style="background-color: #2e4466;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-start">
                            <div
                                class="icon icon-shape bg-white shadow shadow-danger align-items-center text-center rounded-3 me-3">
                                <i class="bx bx-box-arrow-in-down-right" aria-hidden="true" style="color: #2e4466;"></i>
                            </div>
                            <div class="numbers">
                                <p class="text-xs mt-2 mb-0 text-white font-weight-bold">Retur Sale</p>
                                <h5 class="font-weight-bolder text-white">@money($totalReturSalePeriode)</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-2" style="background-color: #178ae8;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-start">
                            <div
                                class="icon icon-shape bg-white shadow shadow-info align-items-center text-center rounded-3 me-3">
                                <i class="bx bx-bag-plus-fill" aria-hidden="true" style="color: #178ae8;"></i>
                            </div>
                            <div class="numbers">
                                <p class="text-xs mt-2 mb-0 text-white font-weight-bold">Purchase</p>
                                <h5 class="font-weight-bolder text-white">@money($totalPurchasePeriode)</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-2" style="background-color: #8392ab;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-start">
                            <div
                                class="icon icon-shape bg-white shadow shadow-secondary align-items-center text-center rounded-3 me-3">
                                <i class="bx bx-box-arrow-down-left" aria-hidden="true" style="color: #8392ab;"></i>
                            </div>
                            <div class="numbers">
                                <p class="text-xs mt-2 mb-0 text-white font-weight-bold">Retur Purchase</p>
                                <h5 class="font-weight-bolder text-white">@money($totalReturPurchasePeriode)</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Card Pendapatan --}}
        {{-- Jika kasir, buat kolom lebih lebar (col-md-6) --}}
        <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-6' }}">
            <div class="card rounded-2 border border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-success shadow-success text-center me-3">
                            <i class="bx bx-cash-coin text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Pendapatan</p>
                            <h5 class="font-weight-bolder mb-0">@money($pendapatanPeriodeIni)</h5>
                        </div>
                    </div>
                    <p class="mb-0 mt-2 text-sm">
                        <span
                            class="font-weight-bolder {{ $persentasePendapatan >= 0 ? 'text-success' : 'text-danger' }}">
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
            <div class="card rounded-2 border border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-primary shadow-primary text-center me-3">
                            <i class="bx bx-receipt-cutoff text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Transaksi</p>
                            <h5 class="font-weight-bolder mb-0">{{ $transaksiPeriodeIni }}</h5>
                        </div>
                    </div>
                    <p class="mb-0 mt-2 text-sm">
                        <span
                            class="font-weight-bolder {{ $persentaseTransaksi >= 0 ? 'text-success' : 'text-danger' }}">
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
                <div class="card rounded-2 border border-warning">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center me-3">
                                <i class="bx bx-wallet2 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Expense</p>
                                <h5 class="font-weight-bolder mb-0">@money($totalExpensePeriode)</h5>
                            </div>
                        </div>
                        <p class="mb-0 mt-2 text-sm">
                            <a href="{{ route('keuangan') }}" class="text-primary font-weight-bolder">Lihat
                                Detail</a>
                        </p>
                    </div>
                </div>
            </div>
            {{-- Card Laba Bersih --}}
            <div class="col-md-3">
                <div class="card rounded-2 border border-info">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center me-3">
                                <i class="bx bx-graph-up-arrow text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Laba Bersih</p>
                                <h5
                                    class="font-weight-bolder mb-0 {{ $labaBersihPeriode >= 0 ? 'text-success' : 'text-danger' }}">
                                    @money($labaBersihPeriode)</h5>
                            </div>
                        </div>
                        <p class="mb-0 mt-2 text-sm">
                            <a href="{{ route('laporan.laba-rugi') }}" class="text-primary font-weight-bolder">Lihat
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
                        <i class="fa fa-arrow-up text-success me-1"></i>
                        <span class="font-weight-bold">Total Pendapatan</span> 30 hari terakhir
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
            <div class="card rounded-2">
                <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center">
                    <div class="badge badge-success p-2 me-2">
                        <i class="bx bx-box2-fill"></i>
                    </div>
                    <h6 class="mb-0">Product Terlaris</h6>
                </div>
                <hr class="horizontal dark mb-n2">
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse ($produkTerlaris as $produk)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp') }}"
                                            class="avatar avatar-lg rounded-2 me-3" alt="Gambar produk">
                                        <div>
                                            <h6 class="mb-n1 text-dark text-sm">{{ $produk->name_produk }}</h6>
                                            <span class="text-xs">@money($produk->harga_jual)</span>
                                            <i class="bx bx-circle-fill bi-xs mx-1 text-success"></i>
                                            <span class="text-xs"><span
                                                    class="font-weight-bold">{{ $produk->total_terjual }}</span>
                                                terjual</span>
                                        </div>
                                    </div>
                                    <div>
                                        @if (isset($produk->percentage_increase) && $produk->percentage_increase !== 0)
                                            <span class="text-xs">
                                                <span
                                                    class="font-weight-bolder {{ $produk->percentage_increase >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $produk->percentage_increase >= 0 ? '+' : '' }}{{ number_format($produk->percentage_increase, 2) }}%
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 ps-0 text-center">
                                <p class="text-sm text-muted">Belum ada produk yang terjual pada periode ini.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-2">
                <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="badge badge-warning p-2 me-2">
                            <i class="bx bx-box-seam-fill"></i>
                        </div>
                        <h6 class="mb-0">Product Stock Rendah</h6>
                    </div>
                    <a href="{{ route('stok.rendah') }}" class="btn btn-outline-dark btn-xs mb-0">Detail</a>
                </div>
                <hr class="horizontal dark mb-n2">
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse ($produkStockRendah as $produk)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp') }}"
                                            class="avatar avatar-lg rounded-2 me-3" alt="Gambar produk">
                                        <div>
                                            <h6 class="mb-n1 text-dark text-sm">{{ $produk->name_produk }}</h6>
                                            <span class="text-xs">Sisa <span
                                                    class="font-weight-bold text-danger">{{ $produk->qty }}</span></span>
                                            <i class="bx bx-circle-fill bi-xs mx-1 text-secondary"></i>
                                            <span class="text-xs">Min. <span
                                                    class="font-weight-bold">{{ $produk->stok_minimum }}</span></span>
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
                            <li class="list-group-item border-0 ps-0 text-center">
                                <p class="text-sm text-muted">Tidak ada produk dengan stok rendah saat ini.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-2">
                <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="badge badge-info p-2 me-2">
                            <i class="bx bx-person-check-fill"></i>
                        </div>
                        <h6 class="mb-0">Customer Terbaik</h6>
                    </div>
                    <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-dark btn-xs mb-0">Detail</a>
                </div>
                <hr class="horizontal dark mb-n2">
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse ($pelangganTerbaik as $pelanggan)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar avatar-lg rounded-2 me-3 bg-gradient-dark d-flex align-items-center justify-content-center">
                                            <span
                                                class="text-white font-weight-bold">{{ strtoupper(substr($pelanggan->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-n1 text-dark text-sm">{{ $pelanggan->name }}</h6>
                                            <span class="text-xs"><span
                                                    class="font-weight-bold">{{ $pelanggan->total_orders }}</span>
                                                order</span>
                                            <i class="bx bx-circle-fill bi-xs mx-1 text-info"></i>
                                            <span class="text-xs">Total <span
                                                    class="font-weight-bold">@money($pelanggan->total_spent)</span></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 ps-0 text-center">
                                <p class="text-sm text-muted">Belum ada transaksi dari pelanggan pada periode ini.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card rounded-2">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="badge badge-secondary p-2 me-2">
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
                                    <li
                                        class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('penjualan.show', $sale->referensi) }}"
                                                class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                                <i class="bx bx-arrow-up"></i>
                                            </a>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-1 text-dark text-sm">{{ $sale->referensi }}</h6>
                                                <span class="text-xs">{{ $sale->pelanggan->name ?? 'Customer Umum' }}
                                                    &bull;
                                                    {{ \Carbon\Carbon::parse($sale->tanggal_penjualan)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-end">
                                            <div class="me-3">
                                                <p class="text-sm mb-0 text-success font-weight-bold">
                                                    @money($sale->total_akhir)</p>
                                            </div>
                                            <x-badge-status-pembayaran :status="$sale->status_pembayaran" />
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item border-0 text-center">
                                        <p class="text-muted text-sm">Belum ada transaksi penjualan.</p>
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
                                            class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('pembelian.show', $purchase->id) }}"
                                                    class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-arrow-down"></i>
                                                </a>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">{{ $purchase->referensi }}</h6>
                                                    <span class="text-xs">{{ $purchase->pemasok->name ?? 'N/A' }}
                                                        &bull;
                                                        {{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-end">
                                                <div class="me-3">
                                                    <p class="text-sm mb-0 text-danger font-weight-bold">
                                                        @money($purchase->total_akhir)</p>
                                                </div>
                                                <x-badge-status-pembayaran :status="$purchase->status_pembayaran" />
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item border-0 text-center">
                                            <p class="text-muted text-sm">Belum ada transaksi pembelian.</p>
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
                    <div class="card rounded-2 h-100 ">
                        <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center">
                            <div class="badge badge-primary p-2 me-2">
                                <i class="bx bx-info-circle"></i>
                            </div>
                            <h6 class="mb-0">Ringkasan Keseluruhan</h6>
                        </div>
                        <hr class="horizontal dark mb-n2">
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
                    <div class="card rounded-2 h-100 ">
                        <div class="card-header ps-3 pt-3 pb-0 d-flex align-items-center">
                            <div class="badge badge-primary p-2 me-2">
                                <i class="bx bx-pie-chart-fill"></i>
                            </div>
                            <h6 class="mb-0">Kategori Terlaris</h6>
                        </div>
                        <hr class="horizontal dark mb-n2">
                        <div class="card-body p-3">
                            @if ($categoryChartLabels->isNotEmpty())
                                <canvas id="category-pie-chart" class="chart-canvas" height="300"></canvas>
                            @else
                                <p class="text-center text-sm text-muted mt-5">Belum ada data penjualan kategori untuk
                                    ditampilkan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('page-script')
    <script src="{{ asset('assets/js copy/plugins/Chart.extension.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
