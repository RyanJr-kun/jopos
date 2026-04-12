@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    {{-- Tambahkan style khusus jika diperlukan --}}
@endsection
{{-- Bagian Ringkasan Keuangan --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card rounded-2">
            <div class="card-body p-3">
                <form action="{{ route('keuangan') }}" method="GET">
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
                        <div class="col-md-3 d-flex mb-n3">
                            <button type="submit" class="btn btn-dark w-100 me-3"><i
                                    class="bx bx-funnel-fill me-2"></i>Filter</button>
                            <a href="{{ route('keuangan') }}" class="btn btn-outline-secondary w-100"><i
                                    class="bx bx-arrow-clockwise me-2"></i>Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card rounded-2">
            <div class="card-body p-3 text-start">
                <div class="d-flex">
                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-lg">
                        <i class="bx bx-arrow-down-circle-fill opacity-10"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-start mb-n1">Total Income</h6>
                        <span class="text-xs">Jumlah pendapatan</span>
                    </div>
                </div>
                <hr class="horizontal dark my-3">
                <h6 class="mb-0 text-success text-center">+ @money($totalIncome)</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card rounded-2">
            <div class="card-body p-3 text-start">
                <div class="d-flex">
                    <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-lg">
                        <i class="bx bx-arrow-up-circle-fill opacity-10"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-start mb-n1">Total Expense</h6>
                        <span class="text-xs">Jumlah pendapatan</span>
                    </div>
                </div>
                <hr class="horizontal dark my-3">
                <h6 class="mb-0 text-danger text-center">- @money($totalExpense)</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card rounded-2">
            <div class="card-body p-3 text-start">
                <div class="d-flex">
                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-lg">
                        <i class="bx bx-ui-checks opacity-10"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-start mb-n1">Total Order</h6>
                        <span class="text-xs">Jumlah Transaksi</span>
                    </div>
                </div>
                <hr class="horizontal dark my-3">
                <h6 class="mb-0 text-info text-center">{{ $totalTransaksi }}</h6>
            </div>
        </div>
    </div>
    {{-- Bagian Grafik Keuangan --}}
    <div class="col-12">
        <div class="card z-index-2 h-100 rounded-2">
            <div class="card-header pb-0 pt-3 bg-transparent">
                <h6 class="text-capitalize">Grafik Keuangan</h6>
                <p class="text-sm mb-0">
                    <i class="bx bx-arrow-up text-success"></i>
                    <span class="font-weight-bold">Income</span> vs
                    <i class="bx bx-arrow-down text-danger"></i>
                    <span class="font-weight-bold">Expense</span>
                </p>
            </div>
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="financial-chart" class="chart-canvas" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card rounded-2 h-100">
            <div class="card-header pb-0 px-3">
                <div class="row">
                    <div class="col-6">
                        <h6 class="mb-0">Transaksi Terakhir</h6>
                    </div>
                    <div class="col-6 d-flex justify-content-end align-items-center">
                        <i class="far fa-calendar-alt me-2"></i>
                        <small>{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM') }} -
                            {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4 p-3">
                <ul class="list-group">
                    @forelse ($recentTransactions as $transaction)
                        @php
                            $isIncome = $transaction->type === 'income';
                            $iconClass = $isIncome ? 'fa-arrow-down text-success' : 'fa-arrow-up text-danger';
                            $amountClass = $isIncome ? 'text-success' : 'text-danger';
                            $amountSign = $isIncome ? '+' : '-';
                        @endphp
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <button
                                    class="btn btn-icon-only btn-rounded btn-outline-{{ $isIncome ? 'success' : 'danger' }} mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                    <i class="fas {{ $iconClass }}"></i>
                                </button>
                                <div class="d-flex flex-column">
                                    <div class="mb-1 text-dark text-sm">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($transaction->keterangan), 50) }}
                                    </div>
                                    <span
                                        class="text-xs">{{ \Carbon\Carbon::parse($transaction->tanggal)->isoFormat('D MMM Y, HH:mm') }}</span>
                                </div>
                            </div>
                            <div
                                class="d-flex align-items-center {{ $amountClass }} text-gradient text-sm font-weight-bold">
                                {{ $amountSign }} @money($transaction->jumlah)
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item border-0 text-center">
                            <p class="text-muted text-sm">Belum ada transaksi pada periode ini.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card rounded-2 h-100">
            <div class="card-header pb-0 px-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Invoice Terbaru</h6>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('penjualan.index') }}"
                            class="btn btn-outline-dark btn-xs text-xs mb-0 fw-bolder">Detail</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4 p-3">
                <ul class="list-group">
                    @forelse ($recentInvoices as $invoice)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">
                                    {{ $invoice->tanggal_penjualan }}</h6>
                                <span class="text-xs">{{ $invoice->referensi }}</span>
                            </div>
                            <div class="d-flex align-items-center text-sm">
                                @money($invoice->total_akhir)
                                <a href="{{ route('penjualan.show', $invoice->referensi) }}"
                                    class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                        class="fas fa-file-pdf text-lg me-1"></i> PDF</a>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item border-0 text-center">
                            <p class="text-muted text-sm">Belum ada invoice penjualan.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
{{-- Bagian Transaksi Terbaru --}}

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financial-chart').getContext('2d');
            const chartData = @json($chartData);

            // Gradient untuk Income (Hijau)
            var gradientStrokeIncome = ctx.createLinearGradient(0, 230, 0, 50);
            gradientStrokeIncome.addColorStop(1, 'rgba(20, 214, 125, 0.2)');
            gradientStrokeIncome.addColorStop(0.2, 'rgba(20, 214, 125, 0.0)');
            gradientStrokeIncome.addColorStop(0, 'rgba(20, 214, 125, 0)');

            // Gradient untuk Expense (Merah)
            var gradientStrokeExpense = ctx.createLinearGradient(0, 230, 0, 50);
            gradientStrokeExpense.addColorStop(1, 'rgba(234, 51, 94, 0.2)');
            gradientStrokeExpense.addColorStop(0.2, 'rgba(234, 51, 94, 0.0)');
            gradientStrokeExpense.addColorStop(0, 'rgba(234, 51, 94, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Income',
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        borderColor: 'rgba(20, 214, 125, 1)',
                        backgroundColor: gradientStrokeIncome,
                        fill: true,
                        data: chartData.income,
                    }, {
                        label: 'Expense',
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        borderColor: 'rgba(234, 51, 94, 1)',
                        backgroundColor: gradientStrokeExpense,
                        fill: true,
                        data: chartData.expense,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true, // Tetap tampilkan legend karena ada 2 dataset
                            position: 'top',
                            labels: {
                                color: '#6c757d'
                            }
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
                    },
                }
            });
        });
    </script>
@endsection
@endsection
