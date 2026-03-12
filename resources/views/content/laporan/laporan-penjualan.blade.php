@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Laporan', 'url' => '#'],
            ['name' => 'Laporan Sale', 'url' => route('laporan.penjualan')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection
<div class="container-fluid p-3">
    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card rounded-2 border border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-primary shadow-primary text-center me-3">
                            <i class="bx bx-cash-coin text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Sale</p>
                            <h5 class="font-weight-bolder mb-0">
                                @money($totals->grand_total ?? 0)
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card rounded-2 border border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-success shadow-success text-center me-3">
                            <i class="bx bx-box2-fill text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Product Terjual</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($totals->total_products_sold ?? 0, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card rounded-2 border border-danger">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-danger shadow-danger text-center me-3">
                            <i class="bx bx-receipt-cutoff text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Jumlah Transaksi</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($totals->total_transactions ?? 0, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Laporan Sale</h6>
                    <p class="text-sm mb-0">Analisis semua transaksi penjualan.</p>
                </div>
                <div class="dropdown mt-2">
                    <a href="#" id="exportPdf" class="btn btn-outline-danger me-2 p-2 mb-0"
                        data-bs-toggle="tooltip" title="Export PDF">
                        <img src="{{ asset('assets/img/pdf.png') }}" alt="Download PDF" width="20" height="20">
                    </a>
                    <a href="#" id="exportXlsx" class="btn btn-outline-success me-2 p-2 mb-0"
                        data-bs-toggle="tooltip" title="Export EXCEL">
                        <img src="{{ asset('assets/img/xls.png') }}" alt="Download Excel" width="20" height="20">
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            {{-- Filter Section --}}
            <div class="filter-container p-3 border-bottom">
                <form action="{{ route('laporan.penjualan') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select">
                                <option value="">Semua Customer</option>
                                @foreach ($customers as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" @selected(request('customer_id') == $pelanggan->id)>
                                        {{ $pelanggan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_pembayaran" class="form-label">Status Bayar</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($statusPembayaranOptions as $status)
                                    <option value="{{ $status }}" @selected(request('status_pembayaran') == $status)>{{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-1 d-flex mb-n3">
                            <button type="submit" class="btn btn-dark w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table Section --}}
            <div class="table-responsive p-0">
                <table class="table table-hover align-items-center mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Tanggal</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Referensi</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Customer</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder text-center">Status Bayar
                            </th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder text-end pe-2">Total</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder text-end pe-4">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $penjualan)
                            <tr>
                                <td class="ps-4">
                                    <p class="text-sm font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d M Y') }}
                                    </p>
                                </td>
                                <td>
                                    <a href="{{ route('penjualan.show', $penjualan->referensi) }}"
                                        class="text-info fw-bold text-sm" data-bs-toggle="tooltip"
                                        title="Lihat Detail Sale">
                                        {{ $penjualan->referensi }}
                                    </a>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">
                                        {{ $penjualan->pelanggan->name ?? 'N/A' }}</p>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-sm badge-{{ ['Lunas' => 'success', 'Belum Lunas' => 'warning', 'Dibatalkan' => 'danger'][$penjualan->status_pembayaran] ?? 'light' }}">{{ $penjualan->status_pembayaran }}</span>
                                </td>
                                <td class="text-end">
                                    <p class="text-sm font-weight-bold mb-0">@money($penjualan->total_akhir)</p>
                                </td>
                                <td class="text-end pe-4">
                                    <p
                                        class="text-sm font-weight-bold mb-0 {{ $penjualan->sisa_pembayaran > 0 ? 'text-danger' : '' }}">
                                        @money($penjualan->sisa_pembayaran)</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-sm fw-bold mb-0">Tidak ada data penjualan yang ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center my-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#customer_id').select2({
                theme: "bootstrap-5",
                placeholder: 'Pilih Customer',
            });

            const exportXlsxBtn = document.getElementById('exportXlsx');
            const exportPdfBtn = document.getElementById('exportPdf');

            function handleExport(e) {
                e.preventDefault();
                const exportType = this.id === 'exportXlsx' ? 'xlsx' : 'pdf';

                // Ambil nilai filter saat ini dari form
                const form = document.querySelector('.filter-container form');
                const params = new URLSearchParams(new FormData(form)).toString();

                // Bangun URL untuk ekspor
                const exportUrl = `{{ route('laporan.penjualan.export') }}?type=${exportType}&${params}`;

                // Buka URL di tab baru untuk memulai unduhan
                window.open(exportUrl, '_blank');
            }

            exportXlsxBtn.addEventListener('click', handleExport);
            exportPdfBtn.addEventListener('click', handleExport);
        });
    </script>
@endsection
@endsection
