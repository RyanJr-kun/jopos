@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-dashboard.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')
    <div class="dash-container">

        {{-- ===== FILTER BAR ===== --}}
        <div class="dash-filter-bar card mb-4">
            <div class="card-body py-3 px-4">
                <form action="{{ route('dashboard') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-auto d-flex align-items-center me-2">
                            <i class="bx bx-calendar-alt text-primary me-2 fs-5"></i>
                            <span class="fw-semibold text-body">Periode</span>
                        </div>
                        <div class="col-md col-6">
                            <input type="text" name="start_date" id="start_date"
                                class="form-control form-control-sm flatpickr-date" placeholder="Mulai"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-auto d-flex align-items-center px-0">
                            <span class="text-muted">—</span>
                        </div>
                        <div class="col-md col-6">
                            <input type="text" name="end_date" id="end_date"
                                class="form-control form-control-sm flatpickr-date" placeholder="Selesai"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-search me-1"></i>Terapkan
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-reset"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== KPI STAT CARDS ===== --}}
        <div class="row g-3 mb-4">
            {{-- Card: Total Sale --}}
            <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-6' }} col-6">
                <div class="card dash-stat-card dash-stat--sale h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-cart-alt"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Total Sale</span>
                            <h4 class="dash-stat-value">@money($totalSalePeriode)</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Total Purchase (admin only) --}}
            @if (auth()->user()->role_id != 2)
                <div class="col-md-3 col-6">
                    <div class="card dash-stat-card dash-stat--purchase h-100">
                        <div class="card-body">
                            <div class="dash-stat-icon">
                                <i class="bx bx-package"></i>
                            </div>
                            <div class="dash-stat-content">
                                <span class="dash-stat-label">Total Purchase</span>
                                <h4 class="dash-stat-value">@money($totalPurchasePeriode)</h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card: Pendapatan --}}
            <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-6' }} col-6">
                <div class="card dash-stat-card dash-stat--pendapatan h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-trending-up"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Pendapatan</span>
                            <h4 class="dash-stat-value">@money($pendapatanPeriodeIni)</h4>
                            <span
                                class="dash-stat-badge {{ $persentasePendapatan >= 0 ? 'dash-stat-badge--up' : 'dash-stat-badge--down' }}">
                                <i
                                    class="bx {{ $persentasePendapatan >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                {{ number_format(abs($persentasePendapatan), 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Transaksi --}}
            <div class="{{ auth()->user()->role_id != 2 ? 'col-md-3' : 'col-md-6' }} col-6">
                <div class="card dash-stat-card dash-stat--transaksi h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Transaksi</span>
                            <h4 class="dash-stat-value">{{ number_format($transaksiPeriodeIni) }}</h4>
                            <span
                                class="dash-stat-badge {{ $persentaseTransaksi >= 0 ? 'dash-stat-badge--up' : 'dash-stat-badge--down' }}">
                                <i
                                    class="bx {{ $persentaseTransaksi >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                {{ number_format(abs($persentaseTransaksi), 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== EXPENSE & LABA BERSIH (admin only) ===== --}}
        @if (auth()->user()->role_id != 2)
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-6">
                    <div class="card dash-stat-card dash-stat--expense h-100">
                        <div class="card-body">
                            <div class="dash-stat-icon">
                                <i class="bx bx-wallet"></i>
                            </div>
                            <div class="dash-stat-content">
                                <span class="dash-stat-label">Pengeluaran</span>
                                <h4 class="dash-stat-value">@money($totalExpensePeriode)</h4>
                                <a href="{{ route('keuangan') }}" class="dash-stat-link">
                                    Lihat Detail <i class="bx bx-right-arrow-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="card dash-stat-card dash-stat--laba h-100">
                        <div class="card-body">
                            <div class="dash-stat-icon">
                                <i class="bx bx-line-chart"></i>
                            </div>
                            <div class="dash-stat-content">
                                <span class="dash-stat-label">Laba Bersih</span>
                                <h4
                                    class="dash-stat-value {{ $labaBersihPeriode >= 0 ? 'text-success' : 'text-danger' }}">
                                    @money($labaBersihPeriode)
                                </h4>
                                <a href="{{ route('laporan.laba-rugi') }}" class="dash-stat-link">
                                    Lihat Laporan <i class="bx bx-right-arrow-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== CHART AREA + PRODUK TERLARIS ===== --}}
        <div class="row g-3 mb-4">
            {{-- Grafik Sale --}}
            <div class="col-lg-7">
                <div class="card dash-chart-card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="dash-chart-icon me-2">
                                <i class="bx bx-bar-chart-alt-2"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Grafik Penjualan</h6>
                                <span class="text-muted small">Pendapatan 30 hari terakhir</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <canvas id="chart-line" class="chart-canvas" height="320"></canvas>
                    </div>
                </div>
            </div>

            {{-- Produk Terlaris --}}
            <div class="col-lg-5">
                <div class="card dash-list-card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="dash-list-icon dash-list-icon--success me-2">
                                <i class="bx bx-trophy"></i>
                            </div>
                            <h6 class="mb-0">Produk Terlaris</h6>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="dash-list">
                            @forelse ($produkTerlaris as $produk)
                                <div class="dash-list-item">
                                    <img src="{{ $produk->image_url }}" class="dash-list-avatar" alt="Produk">
                                    <div class="dash-list-info">
                                        <span class="dash-list-title">{{ $produk->name_product }}</span>
                                        <span class="dash-list-subtitle">
                                            @money($produk->harga_jual)
                                            <span class="dash-list-dot"></span>
                                            <strong>{{ $produk->total_terjual }}</strong> terjual
                                        </span>
                                    </div>
                                    @if (isset($produk->percentage_increase) && $produk->percentage_increase !== 0)
                                        <span
                                            class="dash-stat-badge {{ $produk->percentage_increase >= 0 ? 'dash-stat-badge--up' : 'dash-stat-badge--down' }}">
                                            {{ $produk->percentage_increase >= 0 ? '+' : '' }}{{ number_format($produk->percentage_increase, 1) }}%
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <div class="dash-list-empty">
                                    <div class="dash-list-empty-icon">
                                        <i class="bx bx-package"></i>
                                    </div>
                                    <h6>Tidak Ada Data</h6>
                                    <p>Belum ada produk terlaris pada periode ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STOK RENDAH + PELANGGAN TERBAIK ===== --}}
        <div class="row g-3 mb-4">
            {{-- Stok Rendah --}}
            <div class="col-lg-6">
                <div class="card dash-list-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="dash-list-icon dash-list-icon--warning me-2">
                                <i class="bx bx-error-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">
                                    Stok Rendah
                                    @if ($stokRendahCount > 0)
                                        <span class="badge bg-danger ms-1">{{ $stokRendahCount }}</span>
                                    @endif
                                </h6>
                            </div>
                        </div>
                        <a href="{{ route('stok.rendah') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="dash-list">
                            @forelse ($produkStockRendah as $produk)
                                <div class="dash-list-item">
                                    <img src="{{ $produk->image_url }}" class="dash-list-avatar" alt="Produk">
                                    <div class="dash-list-info">
                                        <span class="dash-list-title">{{ $produk->name_product }}</span>
                                        <span class="dash-list-subtitle">
                                            Sisa <strong class="text-danger">{{ $produk->qty }}</strong>
                                            <span class="dash-list-dot"></span>
                                            Min. <strong>{{ $produk->stok_minimum }}</strong>
                                        </span>
                                    </div>
                                    @if (auth()->user()->role_id != 2)
                                        <a href="{{ route('pembelian.create') }}" class="btn btn-sm btn-primary px-2 py-1"
                                            data-bs-toggle="tooltip" title="Beli Produk">
                                            <i class="bx bx-cart-add"></i>
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <div class="dash-list-empty">
                                    <div class="dash-list-empty-icon dash-list-empty-icon--success">
                                        <i class="bx bx-check-shield"></i>
                                    </div>
                                    <h6>Stok Aman</h6>
                                    <p>Tidak ada produk dengan stok rendah saat ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pelanggan Terbaik --}}
            <div class="col-lg-6">
                <div class="card dash-list-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="dash-list-icon dash-list-icon--info me-2">
                                <i class="bx bx-crown"></i>
                            </div>
                            <h6 class="mb-0">Pelanggan Terbaik</h6>
                        </div>
                        <a href="{{ route('pelanggan.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="dash-list">
                            @forelse ($pelangganTerbaik as $pelanggan)
                                <div class="dash-list-item">
                                    <div class="dash-list-avatar-initial">
                                        {{ strtoupper(substr($pelanggan->name, 0, 2)) }}
                                    </div>
                                    <div class="dash-list-info">
                                        <span class="dash-list-title">{{ $pelanggan->name }}</span>
                                        <span class="dash-list-subtitle">
                                            <strong>{{ $pelanggan->total_orders }}</strong> order
                                            <span class="dash-list-dot"></span>
                                            Total <strong>@money($pelanggan->total_spent)</strong>
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="dash-list-empty">
                                    <div class="dash-list-empty-icon">
                                        <i class="bx bx-user-x"></i>
                                    </div>
                                    <h6>Tidak Ada Transaksi</h6>
                                    <p>Belum ada transaksi dari pelanggan pada periode ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== AKTIVITAS TERAKHIR + KATEGORI ===== --}}
        <div class="row g-3">
            {{-- Aktivitas Transaksi Terakhir --}}
            <div class="col-lg-7">
                <div class="card dash-list-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="dash-list-icon dash-list-icon--secondary me-2">
                                <i class="bx bx-transfer-alt"></i>
                            </div>
                            <h6 class="mb-0">Aktivitas Terakhir</h6>
                        </div>
                        @if (auth()->user()->role_id == 1)
                            <ul class="nav nav-pills nav-pills-sm" id="transactionTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="sales-tab" data-bs-toggle="tab" href="#recent-sales"
                                        role="tab">Sale</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="purchases-tab" data-bs-toggle="tab"
                                        href="#recent-purchases" role="tab">Purchase</a>
                                </li>
                            </ul>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="transactionTabsContent">
                            {{-- Tab: Sale --}}
                            <div class="tab-pane fade show active" id="recent-sales" role="tabpanel">
                                <div class="dash-list">
                                    @forelse ($recentSales as $sale)
                                        <div class="dash-list-item">
                                            <a href="{{ route('penjualan.show', $sale->referensi) }}"
                                                class="dash-activity-icon dash-activity-icon--success">
                                                <i class="bx bx-up-arrow-alt"></i>
                                            </a>
                                            <div class="dash-list-info">
                                                <span class="dash-list-title">{{ $sale->referensi }}</span>
                                                <span class="dash-list-subtitle">
                                                    {{ $sale->pelanggan->name ?? 'Customer Umum' }}
                                                    &bull;
                                                    {{ \Carbon\Carbon::parse($sale->tanggal_penjualan)->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="dash-list-end">
                                                <span class="dash-list-amount text-success">@money($sale->total_akhir)</span>
                                                <x-badge-status-pembayaran :status="$sale->status_pembayaran" />
                                            </div>
                                        </div>
                                    @empty
                                        <div class="dash-list-empty">
                                            <div class="dash-list-empty-icon">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                            <h6>Belum Ada Penjualan</h6>
                                            <p>Transaksi penjualan Anda akan muncul di sini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Tab: Purchase (admin only) --}}
                            @if (auth()->user()->role_id == 1)
                                <div class="tab-pane fade" id="recent-purchases" role="tabpanel">
                                    <div class="dash-list">
                                        @forelse ($recentPurchases as $purchase)
                                            <div class="dash-list-item">
                                                <a href="{{ route('pembelian.show', $purchase->id) }}"
                                                    class="dash-activity-icon dash-activity-icon--danger">
                                                    <i class="bx bx-down-arrow-alt"></i>
                                                </a>
                                                <div class="dash-list-info">
                                                    <span class="dash-list-title">{{ $purchase->referensi }}</span>
                                                    <span class="dash-list-subtitle">
                                                        {{ $purchase->pemasok->name ?? 'N/A' }}
                                                        &bull;
                                                        {{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="dash-list-end">
                                                    <span
                                                        class="dash-list-amount text-danger">@money($purchase->total_akhir)</span>
                                                    <x-badge-status-pembayaran :status="$purchase->status_pembayaran" />
                                                </div>
                                            </div>
                                        @empty
                                            <div class="dash-list-empty">
                                                <div class="dash-list-empty-icon">
                                                    <i class="bx bx-cart"></i>
                                                </div>
                                                <h6>Belum Ada Pembelian</h6>
                                                <p>Transaksi pembelian Anda akan muncul di sini.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kategori Terlaris (Pie Chart) --}}
            <div class="col-lg-5">
                <div class="card dash-chart-card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="dash-chart-icon me-2">
                                <i class="bx bx-pie-chart-alt-2"></i>
                            </div>
                            <h6 class="mb-0">Kategori Terlaris</h6>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        @if ($categoryChartLabels->isNotEmpty())
                            <canvas id="category-pie-chart" class="chart-canvas" height="300"></canvas>
                        @else
                            <div class="dash-list-empty">
                                <div class="dash-list-empty-icon">
                                    <i class="bx bx-pie-chart-alt-2"></i>
                                </div>
                                <h6>Belum Ada Kategori</h6>
                                <p>Data penjualan berdasarkan kategori akan tampil di sini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Flatpickr
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // --- PIE CHART: Kategori Terlaris ---
            const categoryCtx = document.getElementById("category-pie-chart");
            if (categoryCtx) {
                const categoryLabels = {!! json_encode($categoryChartLabels) !!};
                const categoryData = {!! json_encode($categoryChartData) !!};

                if (categoryLabels.length > 0) {
                    new Chart(categoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                data: categoryData,
                                backgroundColor: [
                                    'rgba(105, 108, 255, 0.85)',
                                    'rgba(40, 199, 111, 0.85)',
                                    'rgba(255, 159, 67, 0.85)',
                                    'rgba(0, 207, 232, 0.85)',
                                    'rgba(234, 84, 85, 0.85)',
                                ],
                                borderColor: '#fff',
                                borderWidth: 3,
                                borderRadius: 4,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#697a8d',
                                        padding: 16,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        font: {
                                            size: 12,
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // --- LINE CHART: Grafik Penjualan ---
            const lineCtx = document.getElementById("chart-line");
            if (lineCtx) {
                const ctx = lineCtx.getContext("2d");
                const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, 'rgba(105, 108, 255, 0.25)');
                gradient.addColorStop(1, 'rgba(105, 108, 255, 0.02)');

                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: {!! json_encode($salesChartLabels) !!},
                        datasets: [{
                            label: "Pendapatan",
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#696cff',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            borderColor: "#696cff",
                            backgroundColor: gradient,
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
                                backgroundColor: 'rgba(50, 50, 50, 0.9)',
                                titleFont: {
                                    size: 12
                                },
                                bodyFont: {
                                    size: 13
                                },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0,
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
                                    color: 'rgba(0,0,0,0.05)',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    maxTicksLimit: 6,
                                    padding: 10,
                                    color: '#a1acb8',
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) +
                                            'jt';
                                        if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                        return 'Rp ' + value;
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    color: '#a1acb8',
                                    padding: 10,
                                    font: {
                                        size: 11,
                                    },
                                }
                            },
                        },
                    },
                });
            }
        });
    </script>
@endsection
