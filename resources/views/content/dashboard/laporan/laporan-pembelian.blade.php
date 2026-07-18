@extends('layouts/contentNavbarLayout')

@section('title', 'Laporan Pembelian')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-dashboard.scss'])
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')
    <div class="laporan-container">

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-sm-6">
                <div class="card dash-stat-card dash-stat--purchase h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-wallet"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Total Pembelian</span>
                            <h4 class="dash-stat-value">@money($totals->grand_total ?? 0)</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card dash-stat-card dash-stat--pendapatan h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-box"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Produk Diterima</span>
                            <h4 class="dash-stat-value">
                                {{ number_format($totals->total_products_received ?? 0, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card dash-stat-card dash-stat--transaksi h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Jumlah Transaksi</span>
                            <h4 class="dash-stat-value">
                                {{ number_format($totals->total_transactions ?? 0, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card dash-list-card">
            {{-- Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="laporan-header-icon me-3">
                        <i class="bx bx-package"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Laporan Pembelian</h6>
                        <span class="text-muted small">Analisis semua transaksi pembelian.</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" id="exportPdf" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip"
                        title="Export PDF">
                        <i class="bx bx-file me-1"></i>PDF
                    </a>
                    <a href="#" id="exportXlsx" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip"
                        title="Export Excel">
                        <i class="bx bx-spreadsheet me-1"></i>Excel
                    </a>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                {{-- Filter --}}
                <div class="laporan-filter-section p-3 border-bottom">
                    <form action="{{ route('laporan.pembelian') }}" method="GET">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="supplier_id" class="form-label small fw-semibold">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-select form-select-sm">
                                    <option value="">Semua Supplier</option>
                                    @foreach ($suppliers as $pemasok)
                                        <option value="{{ $pemasok->id }}" @selected(request('supplier_id') == $pemasok->id)>
                                            {{ $pemasok->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status_pembayaran" class="form-label small fw-semibold">Status Bayar</label>
                                <select name="status_pembayaran" id="status_pembayaran"
                                    class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    @foreach ($statusPembayaranOptions as $status)
                                        <option value="{{ $status }}" @selected(request('status_pembayaran') == $status)>{{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status_barang" class="form-label small fw-semibold">Status Barang</label>
                                <select name="status_barang" id="status_barang" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    @foreach ($statusBarangOptions as $status)
                                        <option value="{{ $status }}" @selected(request('status_barang') == $status)>
                                            {{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md">
                                <label for="start_date" class="form-label small fw-semibold">Mulai</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="form-control form-control-sm" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md">
                                <label for="end_date" class="form-label small fw-semibold">Selesai</label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-auto d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bx bx-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('laporan.pembelian') }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-reset"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 laporan-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Referensi</th>
                                <th>Supplier</th>
                                <th class="text-center">Status Bayar</th>
                                <th class="text-center">Status Barang</th>
                                <th class="text-end">Total</th>
                                <th class="text-end pe-4">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $pembelian)
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-sm fw-semibold">
                                            {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('pembelian.show', $pembelian->referensi) }}"
                                            class="fw-bold text-sm text-primary" data-bs-toggle="tooltip"
                                            title="Lihat Detail">
                                            {{ $pembelian->referensi }}
                                        </a>
                                    </td>
                                    <td>
                                        <span
                                            class="text-sm fw-semibold">{{ $pembelian->pemasok->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-sm bg-label-{{ ['Lunas' => 'success', 'Belum Lunas' => 'warning', 'Jatuh Tempo' => 'danger'][$pembelian->status_pembayaran] ?? 'secondary' }}">
                                            {{ $pembelian->status_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-sm bg-label-{{ ['Diterima' => 'success', 'Dikirim' => 'info', 'Dipesan' => 'primary', 'Dibatalkan' => 'secondary'][$pembelian->status_barang] ?? 'light' }}">
                                            {{ $pembelian->status_barang }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-sm fw-bold">@money($pembelian->total_akhir)</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span
                                            class="text-sm fw-bold {{ $pembelian->sisa_pembayaran > 0 ? 'text-danger' : '' }}">
                                            @money($pembelian->sisa_pembayaran)
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="dash-list-empty">
                                            <div class="dash-list-empty-icon">
                                                <i class="bx bx-cart"></i>
                                            </div>
                                            <h6>Tidak Ada Data</h6>
                                            <p>Tidak ada data pembelian yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center my-4">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#supplier_id').select2({
                theme: "bootstrap-5",
                placeholder: 'Pilih Supplier',
            });

            function handleExport(e) {
                e.preventDefault();
                const exportType = this.id === 'exportXlsx' ? 'xlsx' : 'pdf';
                const form = document.querySelector('form');
                const params = new URLSearchParams(new FormData(form)).toString();
                window.open(`{{ route('laporan.pembelian.export') }}?type=${exportType}&${params}`, '_blank');
            }

            document.getElementById('exportXlsx').addEventListener('click', handleExport);
            document.getElementById('exportPdf').addEventListener('click', handleExport);
        });
    </script>
@endsection
