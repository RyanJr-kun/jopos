@extends('layouts/contentNavbarLayout')

@section('title', 'Riwayat Penyesuaian Stok - Inventory')

@section('content')

    <div class="row g-3 align-items-stretch mb-1 swipeable-row">

        {{-- STAT CARD: Total Penyesuaian --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-transfer-alt fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Total Penyesuaian</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['total'] ?? $stocks->total() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARD: Bulan Ini --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm"
                style="background: linear-gradient(135deg, #17a2b8 0%, #5fd4e6 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-info shadow-sm">
                            <i class="bx bx-calendar fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Bulan Ini</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['bulan_ini'] ?? '-' }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARD: Barang Masuk --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm"
                style="background: linear-gradient(135deg, #28c76f 0%, #6ee7a0 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-success shadow-sm">
                            <i class="bx bx-trending-up fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Total Barang Masuk</p>
                        <h3 class="text-white mb-0 fw-bold">+{{ $stats['total_masuk'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARD: Barang Keluar --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm"
                style="background: linear-gradient(135deg, #ea5455 0%, #f28f8f 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-danger shadow-sm">
                            <i class="bx bx-trending-down fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Total Barang Keluar</p>
                        <h3 class="text-white mb-0 fw-bold">-{{ $stats['total_keluar'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('stok-penyesuaian.index') }}" method="GET">
                        <div class="row g-3 align-items-end">

                            <div class="col-12 col-md-4">
                                <label for="search" class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <input type="text" id="search" name="search" class="form-control"
                                        placeholder="Cari kode atau user..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="filter-date-range" class="form-label fw-semibold">Rentang Tanggal</label>
                                <div class="input-group">
                                    <input type="text" id="filter-date-range" class="form-control"
                                        placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                </div>
                            </div>
                            @can('view-toko-gudang')
                                <div class="col-12 col-md-3">
                                    <label for="store_id" class="form-label fw-semibold">Toko</label>
                                    <select name="store_id" id="store_id" class="form-select select2"
                                        data-placeholder="Semua Toko">
                                        <option value="">Semua Toko</option>
                                        @foreach ($stores ?? [] as $store)
                                            <option value="{{ $store->id }}" @selected(request('store_id') == $store->id)>
                                                {{ $store->name_toko }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan

                            <div class="col-2 justify-content-end d-flex">
                                <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary px-2"
                                    title="Reset Filter" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-reset fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card rounded-3 shadow-sm border-0">
                <div
                    class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Riwayat Penyesuaian Stok</h5>
                        <p class="text-sm text-muted mb-0">Semua catatan koreksi stok di luar transaksi normal</p>
                    </div>
                    <a href="{{ route('stok-penyesuaian.create') }}" class="btn btn-outline-blue px-2"
                        title="Buat Penyesuaian Stok Baru" data-bs-toggle="tooltip" data-bs-placement="top">
                        <i class="bx bx-plus-circle"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-secondary text-dark">
                                <tr>
                                    <th class="">No.</th>
                                    <th class="text-xs font-weight-bolder py-3 ps-4">Kode Penyesuaian</th>
                                    <th class="text-xs font-weight-bolder py-3">Tanggal</th>
                                    <th class="text-xs font-weight-bolder py-3">User</th>
                                    <th class="text-xs font-weight-bolder text-center py-3">Item</th>
                                    <th class="text-xs font-weight-bolder text-center py-3" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stocks as $key => $penyesuaian)
                                    <tr>
                                        <td class="">{{ ++$key }}</td>
                                        <td class="ps-4">
                                            <div class="d-flex flex-column">
                                                @can('view-toko-gudang')
                                                    <div class="">
                                                        <span class="badge bg-label-secondary fs-6">
                                                            {{ $penyesuaian->store->name_toko ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                @endcan
                                                <span
                                                    class="fw-semibold text-sm">{{ $penyesuaian->kode_penyesuaian }}</span>
                                            </div>
                                        </td>

                                        <td>
                                            <p class="text-sm mb-0">
                                                {{ $penyesuaian->tanggal_penyesuaian->translatedFormat('d M Y') }},&nbsp;
                                                {{ $penyesuaian->tanggal_penyesuaian->format('H:i') }} WIB</p>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xs me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($penyesuaian->user->username ?? '?', 0, 1)) }}
                                                    </span>
                                                </span>
                                                <span class="text-sm">{{ $penyesuaian->user->username ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-dark rounded-pill">
                                                {{ $penyesuaian->details_count ?? $penyesuaian->details->count() }} item
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn p-0" type="button"
                                                    id="cardOpt{{ $penyesuaian->id }}" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded fs-4"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="cardOpt{{ $penyesuaian->id }}">
                                                    <a class="dropdown-item text-info"
                                                        href="{{ route('stok-penyesuaian.show', $penyesuaian->kode_penyesuaian) }}""><i
                                                            class="bx bx-show me-2"></i> Detail</a>

                                                    @can('delete-stok-penyesuaian')
                                                        <button type="button" class="dropdown-item text-danger"
                                                            data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal"
                                                            data-kode-penyesuaian="{{ $penyesuaian->kode_penyesuaian }}"
                                                            title="Batalkan">
                                                            <i class="bx bx-trash"></i> Delete
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                                <i class="bx bx-package fs-1 text-muted d-block mb-2"></i>
                                                <p class="text-sm fw-bold mb-0">Tidak ada riwayat penyesuaian stok
                                                    ditemukan.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center px-4 py-3">
                        <p class="text-sm text-muted mb-0">
                            Menampilkan {{ $stocks->firstItem() ?? 0 }}-{{ $stocks->lastItem() ?? 0 }}
                            dari {{ $stocks->total() }} data
                        </p>
                        {{ $stocks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI PEMBATALAN --}}
    @can('delete-stok-penyesuaian')
        <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center mt-3">
                        <i class="bx bx-exclamation-triangle-fill fs-1 text-warning mb-3"></i>
                        <h5 class="mb-2">Batalkan Penyesuaian?</h5>
                        <p class="mb-0">Anda yakin ingin membatalkan penyesuaian <br>
                            <strong id="kodePenyesuaianToCancel"></strong>?
                        </p>
                        <small class="text-danger">
                            Tindakan ini akan mengembalikan stok produk <br> ke keadaan semula dan tidak dapat diurungkan.
                        </small>
                        <div class="mt-4">
                            <form id="cancelForm" method="POST" action="#">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                                <button type="button" class="btn btn-outline-secondary ms-2"
                                    data-bs-dismiss="modal">Tutup</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

@endsection

@section('page-script')
    <script type="module">
        flatpickr("#filter-date-range", {
            mode: "range",
            dateFormat: "Y-m-d",
        });
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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterDateRange = document.getElementById('filter-date-range');
            const cancelModal = document.getElementById('cancelConfirmationModal');

            let debounceTimer = null;

            if (cancelModal) {
                cancelModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const kodePenyesuaian = button.getAttribute('data-kode-penyesuaian');
                    const form = cancelModal.querySelector('#cancelForm');
                    const text = cancelModal.querySelector('#kodePenyesuaianToCancel');

                    if (text) text.textContent = kodePenyesuaian;
                    if (form) form.action = `{{ url('stok-penyesuaian') }}/${kodePenyesuaian}`;
                });
            }

            // Auto-submit filter toko begitu dipilih
            const storeFilter = document.getElementById('store_id');
            if (storeFilter) {
                storeFilter.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            }
        });
    </script>
@endsection
