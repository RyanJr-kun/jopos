@extends('layouts/contentNavbarLayout')

@section('title', 'Laporan Inventaris')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-dashboard.scss'])
@endsection

@section('content')
    <div class="laporan-container">

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card dash-stat-card dash-stat--sale h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-cube-alt"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Jenis Produk</span>
                            <h4 class="dash-stat-value">{{ number_format($summary->total_produk, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card dash-stat-card dash-stat--purchase h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-archive"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Total Stok</span>
                            <h4 class="dash-stat-value">{{ number_format($summary->total_stok, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card dash-stat-card dash-stat--pendapatan h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-log-in-circle"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Total Masuk <span class="text-muted">(Filter)</span></span>
                            <h4 class="dash-stat-value text-success">
                                +{{ number_format($summary->total_masuk, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card dash-stat-card dash-stat--expense h-100">
                    <div class="card-body">
                        <div class="dash-stat-icon">
                            <i class="bx bx-log-out-circle"></i>
                        </div>
                        <div class="dash-stat-content">
                            <span class="dash-stat-label">Total Keluar <span class="text-muted">(Filter)</span></span>
                            <h4 class="dash-stat-value text-danger">
                                -{{ number_format($summary->total_keluar, 0, ',', '.') }}</h4>
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
                        <i class="bx bx-transfer-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Laporan Pergerakan Inventaris</h6>
                        <span class="text-muted small">Melacak semua transaksi masuk dan keluar barang.</span>
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
                    <form action="{{ route('laporan.inventaris') }}" method="GET">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label for="store_id" class="form-label small fw-semibold">Toko</label>
                                <select name="store_id" id="store_id"
                                    class="form-select form-select-sm @if ($canViewAllStore) select2 @endif"
                                    data-placeholder="Pilih Toko" @disabled(!$canViewAllStore)>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" @selected($selectedStoreId == $store->id)>
                                            {{ $store->name_toko }}</option>
                                    @endforeach
                                </select>
                                @unless ($canViewAllStore)
                                    {{-- Select di-disable, jadi tidak ikut ke-submit di form GET. Kirim value lewat hidden input. --}}
                                    <input type="hidden" name="store_id" value="{{ $selectedStoreId }}">
                                @endunless
                            </div>
                            <div class="col-md-3">
                                <label for="product_id" class="form-label small fw-semibold">Produk</label>
                                <select name="product_id" id="product_id" class="form-select form-select-sm select2"
                                    data-placeholder="Pilih Produk">
                                    <option value="">Semua Produk</option>
                                    @foreach ($products as $produk)
                                        {{-- Gunakan $produk->display_name sesuai object bentukan di Controller --}}
                                        <option value="{{ $produk->id }}" @selected(request('product_id') == $produk->id)>
                                            {{ $produk->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tipe_gerakan" class="form-label small fw-semibold">Tipe Gerakan</label>
                                <select name="tipe_gerakan" id="tipe_gerakan" class="form-select form-select-sm select2"
                                    data-placeholder="Pilih Tipe Gerakan">

                                    <option value="">Semua Tipe</option>
                                    @foreach ($tipe_gerakan_options as $tipe)
                                        <option value="{{ $tipe }}" @selected(request('tipe_gerakan') == $tipe)>
                                            {{ $tipe }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md">
                                <label for="start_date" class="form-label small fw-semibold">Mulai</label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                                    value="{{ request('start_date') }}">
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
                                <a href="{{ route('laporan.inventaris') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-reset me-1"></i>Reset
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
                                <th>Produk</th>
                                <th>Tipe</th>
                                <th>Referensi</th>
                                <th class="text-center">Masuk</th>
                                <th class="text-center">Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pergerakan as $item)
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-sm fw-semibold">
                                            {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y, H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm fw-semibold d-block">
                                            {{ $item->name_product ?? 'Produk Dihapus' }}
                                            {{-- Tampilkan varian jika ada --}}
                                            @if (!empty($item->nama_varian))
                                                <span
                                                    class="badge bg-label-secondary ms-1">{{ $item->nama_varian }}</span>
                                            @endif
                                        </span>
                                        <span class="text-xs text-muted">SKU: {{ $item->sku ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-sm bg-label-{{ ['Purchase' => 'success', 'Sale' => 'info', 'Retur Sale' => 'dark', 'Stock Opname' => 'primary', 'Penyesuaian' => 'warning', 'Transfer Keluar' => 'danger', 'Transfer Masuk' => 'success'][$item->tipe_gerakan] ?? 'secondary' }}">
                                            {{ $item->tipe_gerakan }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->route_name && $item->referensi_id)
                                            <a href="{{ route($item->route_name, $item->referensi_id) }}"
                                                class="fw-bold text-sm text-primary" data-bs-toggle="tooltip"
                                                title="Lihat Detail {{ $item->tipe_gerakan }}">
                                                {{ $item->referensi }}
                                            </a>
                                        @else
                                            <span class="text-sm fw-semibold">{{ $item->referensi }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="text-sm text-success fw-bold">
                                            {{ $item->jumlah_masuk > 0 ? '+' . $item->jumlah_masuk : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-sm text-danger fw-bold">
                                            {{ $item->jumlah_keluar > 0 ? '-' . $item->jumlah_keluar : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ $item->keterangan ?: '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="dash-list-empty">
                                            <div class="dash-list-empty-icon">
                                                <i class="bx bx-transfer-alt"></i>
                                            </div>
                                            <h6>Tidak Ada Data</h6>
                                            <p>Tidak ada data pergerakan inventaris ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center my-4">
                    {{ $pergerakan->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('page-script')
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10
                    });
                });
            } else {
                setTimeout(initSelect2, 100);
            }
        };
        initSelect2();

        $(document).ready(function() {

            function handleExport(e) {
                e.preventDefault();
                const exportType = this.id === 'exportXlsx' ? 'xlsx' : 'pdf';
                const form = document.querySelector('form');
                const params = new URLSearchParams(new FormData(form)).toString();
                window.open(`{{ route('laporan.inventaris.export') }}?type=${exportType}&${params}`, '_blank');
            }

            document.getElementById('exportXlsx').addEventListener('click', handleExport);
            document.getElementById('exportPdf').addEventListener('click', handleExport);
        });
    </script>
@endsection