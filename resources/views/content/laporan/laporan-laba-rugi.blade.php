@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Laporan', 'url' => '#'],
            ['name' => 'Laba Rugi', 'url' => route('laporan.laba-rugi')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Laporan Laba Rugi</h6>
                    <p class="text-sm mb-0">Menampilkan ringkasan pendapatan, beban, dan laba bersih.</p>
                </div>
                <a href="#" id="exportPdf" class="btn btn-outline-danger me-2 p-2 mb-0" data-bs-toggle="tooltip"
                    title="Export PDF">
                    <img src="{{ asset('assets/img/pdf.png') }}" alt="Download PDF" width="20" height="20">
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            {{-- Filter Section --}}
            <div class="filter-container p-3 border-bottom mb-4">
                <form action="{{ route('laporan.laba-rugi') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex mb-n3">
                                <button type="submit" class="btn btn-dark w-100 me-2">Filter</button>
                                <a href="{{ route('laporan.laba-rugi') }}"
                                    class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Report Summary --}}
            <div class="row px-3">
                <div class="col-lg-7">
                    <div class="report-summary">
                        <h6 class="mb-3">Ringkasan Periode: <span
                                class="fw-bold">{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} -
                                {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}</span></h6>

                        <ul class="list-group rounded-2">
                            {{-- Pendapatan --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Pendapatan dari Sale</span>
                                <span class="fw-bold text-success">@money($totalRevenue)</span>
                            </li>
                            {{-- HPP --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Harga Pokok Sale (HPP)</span>
                                <span>(@money($cogs))</span>
                            </li>
                            {{-- Laba Kotor --}}
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="fw-bold">Laba Kotor</span>
                                <span class="fw-bold">@money($grossProfit)</span>
                            </li>
                            {{-- Pendapatan Lain-lain --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Pendapatan Lain-lain</span>
                                <span class="fw-bold text-info">@money($totalOtherIncome)</span>
                            </li>
                            {{-- Beban --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Beban Operasional</span>
                                <span>(@money($totalExpenses))</span>
                            </li>
                            {{-- Laba Bersih --}}
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-top pt-3 {{ $netProfit >= 0 ? 'bg-success-light' : 'bg-danger-light' }}">
                                <h5 class="mb-0">Laba Bersih</h5>
                                <h5 class="mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    @money($netProfit)</h5>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center @if (isset($isExport) && $isExport) d-none @endif">
                    <div class="chart w-100">
                        <canvas id="profit-loss-pie-chart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="row mt-4 @if (isset($isExport) && $isExport) d-none @endif">
        <div class="col-12">
            <div class="card rounded-2 z-index-2">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Grafik Laba Rugi 6 Bulan Terakhir</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">Laba</span> vs
                        <i class="fa fa-arrow-down text-danger"></i>
                        <span class="font-weight-bold">Rugi</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="profit-loss-chart" class="chart-canvas" height="300"></canvas>
                    </div>
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
                // Pie Chart untuk Ringkasan Laba Rugi
                const pieCtx = document.getElementById('profit-loss-pie-chart').getContext('2d');
                const pieLabels = ['Pendapatan', 'HPP', 'Beban'];
                const pieData = [@json($totalRevenue), @json($cogs),
                    @json($totalExpenses)
                ];

                // Hanya tampilkan chart jika ada pendapatan
                if (pieData[0] > 0) {
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: pieLabels,
                            datasets: [{
                                label: 'Jumlah',
                                data: pieData,
                                backgroundColor: [
                                    'rgba(20, 214, 125, 0.8)', // Hijau untuk Pendapatan
                                    'rgba(251, 99, 64, 0.8)', // Oranye untuk HPP
                                    'rgba(234, 51, 94, 0.8)', // Merah untuk Beban
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
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR'
                                            }).format(context.raw);
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Line Chart untuk Laba Rugi 6 Bulan
                const ctx = document.getElementById('profit-loss-chart').getContext('2d');
                const data = @json($chartNetProfits);

                // Warna untuk Laba (hijau) dan Rugi (merah)
                const profitColor = 'rgba(20, 214, 125, 1)';
                const lossColor = 'rgba(234, 51, 94, 1)';
                const profitBgColor = 'rgba(20, 214, 125, 0.1)';
                const lossBgColor = 'rgba(234, 51, 94, 0.1)';

                // Fungsi untuk membuat gradient berdasarkan nilai
                const getGradient = (ctx, chartArea, value) => {
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    if (value >= 0) {
                        gradient.addColorStop(0, profitBgColor);
                        gradient.addColorStop(1, 'rgba(20, 214, 125, 0)');
                    } else {
                        gradient.addColorStop(0, lossBgColor);
                        gradient.addColorStop(1, 'rgba(234, 51, 94, 0)');
                    }
                    return gradient;
                };
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Laba Bersih',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            // Mengatur warna garis secara dinamis per segmen
                            segment: {
                                borderColor: ctx => (ctx.p0.parsed.y >= 0 && ctx.p1.parsed.y >= 0) ?
                                    profitColor : (ctx.p0.parsed.y < 0 && ctx.p1.parsed.y < 0) ?
                                    lossColor : 'gray',
                            },
                            // Mengatur warna background area
                            backgroundColor: context => {
                                const chart = context.chart;
                                const {
                                    ctx,
                                    chartArea
                                } = chart;
                                if (!chartArea) {
                                    return null;
                                }
                                return getGradient(ctx, chartArea, context.dataset.data[context
                                    .dataIndex]);
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
                        }
                    }
                });

                // Handle Export
                const exportPdfBtn = document.getElementById('exportPdf');

                function handleExport(e) {
                    e.preventDefault();

                    // Ambil nilai filter saat ini dari form
                    const form = document.querySelector('.filter-container form');
                    const params = new URLSearchParams(new FormData(form)).toString();

                    // Bangun URL untuk ekspor
                    const exportUrl = `{{ route('laporan.laba-rugi.export') }}?${params}`;

                    // Buka URL di tab baru untuk memulai unduhan
                    window.open(exportUrl, '_blank');
                }
                exportPdfBtn.addEventListener('click', handleExport);
            });
        </script>
    @endsection
@endif
@endsection
