@extends('layouts/contentNavbarLayout')

@section('title', 'Laporan Laba Rugi')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-dashboard.scss'])
@endsection

@section('content')
    <div class="laporan-container">

        {{-- Header --}}
        <div class="card laporan-header-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="laporan-header-icon me-3">
                            <i class="bx bx-line-chart"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Laporan Laba Rugi</h5>
                            <span class="text-muted small">Ringkasan pendapatan, beban, dan laba bersih.</span>
                        </div>
                    </div>
                    <a href="#" id="exportPdf" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip"
                        title="Export PDF">
                        <i class="bx bx-file me-1"></i>PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card dash-filter-bar mb-4">
            <div class="card-body py-3 px-4">
                <form action="{{ route('laporan.laba-rugi') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-auto d-flex align-items-center me-2">
                            <i class="bx bx-calendar-alt text-primary me-2 fs-5"></i>
                            <span class="fw-semibold text-body">Periode</span>
                        </div>
                        <div class="col-md col-6">
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-auto d-flex align-items-center px-0">
                            <span class="text-muted">—</span>
                        </div>
                        <div class="col-md col-6">
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-search me-1"></i>Terapkan
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('laporan.laba-rugi') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-reset"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Report Summary + Pie Chart --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            Ringkasan Periode:
                            <span class="fw-bold text-primary">
                                {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} —
                                {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}
                            </span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="laporan-summary-list">
                            <div class="laporan-summary-item">
                                <span class="laporan-summary-label">Pendapatan dari Penjualan</span>
                                <span class="laporan-summary-value text-success fw-bold">@money($totalRevenue)</span>
                            </div>
                            <div class="laporan-summary-item">
                                <span class="laporan-summary-label">Harga Pokok Penjualan (HPP)</span>
                                <span class="laporan-summary-value">(@money($cogs))</span>
                            </div>
                            <div class="laporan-summary-item laporan-summary-item--highlight">
                                <span class="laporan-summary-label fw-bold">Laba Kotor</span>
                                <span class="laporan-summary-value fw-bold">@money($grossProfit)</span>
                            </div>
                            <div class="laporan-summary-item">
                                <span class="laporan-summary-label">Pendapatan Lain-lain</span>
                                <span class="laporan-summary-value text-info fw-bold">@money($totalOtherIncome)</span>
                            </div>
                            <div class="laporan-summary-item">
                                <span class="laporan-summary-label">Beban Operasional</span>
                                <span class="laporan-summary-value">(@money($totalExpenses))</span>
                            </div>
                            <div
                                class="laporan-summary-item laporan-summary-item--total {{ $netProfit >= 0 ? 'laporan-summary-item--profit' : 'laporan-summary-item--loss' }}">
                                <span class="laporan-summary-label fw-bold fs-6">Laba Bersih</span>
                                <span class="laporan-summary-value fw-bold fs-6">@money($netProfit)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 @if (isset($isExport) && $isExport) d-none @endif">
                <div class="card dash-chart-card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="dash-chart-icon me-2">
                                <i class="bx bx-pie-chart-alt-2"></i>
                            </div>
                            <h6 class="mb-0">Komposisi Keuangan</h6>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="profit-loss-pie-chart" class="chart-canvas" height="280"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Line Chart --}}
        <div class="row @if (isset($isExport) && $isExport) d-none @endif">
            <div class="col-12">
                <div class="card dash-chart-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="dash-chart-icon me-2">
                                <i class="bx bx-trending-up"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Grafik Laba Rugi 6 Bulan Terakhir</h6>
                                <span class="text-muted small">
                                    <i class="bx bx-up-arrow-alt text-success"></i> Laba vs
                                    <i class="bx bx-down-arrow-alt text-danger"></i> Rugi
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="profit-loss-chart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if (!isset($isExport) || !$isExport)
        @section('page-script')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // --- Pie Chart ---
                    const pieCtx = document.getElementById('profit-loss-pie-chart');
                    if (pieCtx) {
                        const pieData = [@json($totalRevenue), @json($cogs), @json($totalExpenses)];
                        if (pieData[0] > 0) {
                            new Chart(pieCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Pendapatan', 'HPP', 'Beban'],
                                    datasets: [{
                                        data: pieData,
                                        backgroundColor: [
                                            'rgba(40, 199, 111, 0.85)',
                                            'rgba(255, 159, 67, 0.85)',
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
                                                padding: 16,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                            }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    let label = context.label || '';
                                                    if (label) label += ': ';
                                                    label += new Intl.NumberFormat('id-ID', {
                                                        style: 'currency',
                                                        currency: 'IDR',
                                                        minimumFractionDigits: 0,
                                                    }).format(context.raw);
                                                    return label;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // --- Line Chart ---
                    const lineCtx = document.getElementById('profit-loss-chart');
                    if (lineCtx) {
                        const ctx = lineCtx.getContext('2d');
                        const data = @json($chartNetProfits);
                        const profitColor = 'rgba(40, 199, 111, 1)';
                        const lossColor = 'rgba(234, 84, 85, 1)';

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($chartLabels),
                                datasets: [{
                                    label: 'Laba Bersih',
                                    tension: 0.4,
                                    borderWidth: 2.5,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: data.map(v => v >= 0 ? profitColor :
                                        lossColor),
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    segment: {
                                        borderColor: ctx => (ctx.p0.parsed.y >= 0 && ctx.p1.parsed
                                                .y >= 0) ? profitColor :
                                            (ctx.p0.parsed.y < 0 && ctx.p1.parsed.y < 0) ?
                                            lossColor : '#a1acb8',
                                    },
                                    backgroundColor: context => {
                                        const chart = context.chart;
                                        const {
                                            ctx,
                                            chartArea
                                        } = chart;
                                        if (!chartArea) return null;
                                        const gradient = ctx.createLinearGradient(0, chartArea
                                            .bottom, 0, chartArea.top);
                                        const val = context.dataset.data[context.dataIndex];
                                        if (val >= 0) {
                                            gradient.addColorStop(0, 'rgba(40, 199, 111, 0)');
                                            gradient.addColorStop(1, 'rgba(40, 199, 111, 0.15)');
                                        } else {
                                            gradient.addColorStop(0, 'rgba(234, 84, 85, 0)');
                                            gradient.addColorStop(1, 'rgba(234, 84, 85, 0.15)');
                                        }
                                        return gradient;
                                    },
                                    fill: true,
                                    data: data,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(50, 50, 50, 0.9)',
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
                                    mode: 'index'
                                },
                                scales: {
                                    y: {
                                        grid: {
                                            drawBorder: false,
                                            color: 'rgba(0,0,0,0.05)',
                                            borderDash: [5, 5]
                                        },
                                        ticks: {
                                            padding: 10,
                                            color: '#a1acb8',
                                            font: {
                                                size: 11
                                            },
                                            callback: function(value) {
                                                if (Math.abs(value) >= 1000000) return 'Rp ' + (value /
                                                    1000000).toFixed(1) + 'jt';
                                                if (Math.abs(value) >= 1000) return 'Rp ' + (value /
                                                    1000).toFixed(0) + 'rb';
                                                return 'Rp ' + value;
                                            }
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#a1acb8',
                                            padding: 10,
                                            font: {
                                                size: 11
                                            }
                                        }
                                    },
                                }
                            }
                        });
                    }

                    // --- Export ---
                    document.getElementById('exportPdf').addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = document.querySelector('form');
                        const params = new URLSearchParams(new FormData(form)).toString();
                        window.open(`{{ route('laporan.laba-rugi.export') }}?${params}`, '_blank');
                    });
                });
            </script>
        @endsection
    @endif
@endsection
