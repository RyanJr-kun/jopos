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
                                <label for="filter-search" class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <input type="text" id="filter-search" class="form-control"
                                        placeholder="Cari kode atau user...">
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
                                    <label for="filter-store" class="form-label fw-semibold">Toko</label>
                                    <select name="store_id" id="filter-store" class="form-select select2"
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
                    <div id="tableLoadingOverlay"
                        class="position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
                        style="background: rgba(255,255,255,0.6); z-index: 10;">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>

                    <div id="tableContainer">
                        @include('inventory::inventaris.adjustment._table', ['stocks' => $stocks])
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
            const dataUrl = "{{ route('stok-penyesuaian.index') }}";
            const tableContainer = document.getElementById('tableContainer');
            const loadingOverlay = document.getElementById('tableLoadingOverlay');
            const filterSearch = document.getElementById('filter-search');
            const filterDateRange = document.getElementById('filter-date-range');
            const filterStore = document.getElementById('filter-store');
            const btnResetFilter = document.getElementById('btn-reset-filter');

            let debounceTimer = null;
            let abortController = null;

            function buildParams(page = 1) {
                const params = new URLSearchParams();
                if (filterSearch.value.trim()) params.set('search', filterSearch.value.trim());
                if (filterStore.value) params.set('store_id', filterStore.value);

                const dateRangeValue = filterDateRange.value;

                if (dateRangeValue.includes(' to ')) {
                    // Memecah rentang tanggal menjadi dua parameter terpisah
                    const dates = dateRangeValue.split(' to ');
                    params.set('start_date', dates[0]);
                    params.set('end_date', dates[1]);
                } else if (dateRangeValue) {
                    // Jika user baru memilih 1 tanggal (klik pertama)
                    params.set('start_date', dateRangeValue);
                    params.set('end_date', dateRangeValue);
                }

                params.set('page', page);
                return params;
            }

            async function fetchTable(page = 1) {
                // Batalkan request sebelumnya kalau masih jalan (hindari race condition)
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                loadingOverlay.classList.remove('d-none');
                loadingOverlay.classList.add('d-flex');

                try {
                    const response = await fetch(`${dataUrl}?${buildParams(page).toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: abortController.signal
                    });

                    if (!response.ok) throw new Error('Gagal memuat data');

                    const html = await response.text();
                    tableContainer.innerHTML = html;
                    attachPaginationHandlers();
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error(error);
                        window.showToast?.('error', 'Gagal memuat data transfer.');
                    }
                } finally {
                    loadingOverlay.classList.remove('d-flex');
                    loadingOverlay.classList.add('d-none');
                }
            }

            function attachPaginationHandlers() {
                tableContainer.querySelectorAll('.pagination a[href]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page') || 1;
                        fetchTable(page);
                        window.scrollTo({
                            top: tableContainer.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    });
                });
            }

            function debouncedFetch() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchTable(1), 400);
            }

            filterSearch.addEventListener('input', debouncedFetch);

            // KOREKSI 3: Pasang event listener ke elemen 'filterDateRange'
            filterDateRange.addEventListener('change', () => fetchTable(1));

            // select2 pakai event jQuery, bukan native 'change' DOM biasa
            $('#filter-store').on('select2:select select2:clear', () =>
                fetchTable(1));

            btnResetFilter.addEventListener('click', function() {
                filterSearch.value = '';

                filterDateRange.value = '';

                $('#filter-store').val('').trigger('change');
                fetchTable(1);
            });

            attachPaginationHandlers();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const cancelModal = document.getElementById('cancelConfirmationModal');

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
