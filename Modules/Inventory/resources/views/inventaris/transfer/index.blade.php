@extends('layouts/contentNavbarLayout')

@section('title', 'Transfer Stok Antar Toko - Inventory')

@section('content')
    <!-- Terapkan class swipeable-row di sini -->
    <div class="row g-3 align-items-stretch mb-3 swipeable-row">
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-transfer fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Total Transfer</p>
                        <h3 class="text-white mb-0 fw-bold" id="statTotal">{{ $transfers->total() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARD: Menunggu Diterima --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm"
                style="background: linear-gradient(135deg, #ff9f43 0%, #ffc98a 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-warning shadow-sm">
                            <i class="bx bx-time-five fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Menunggu Diterima</p>
                        <h3 class="text-white mb-0 fw-bold">
                            {{ $transfers->where('status', 'dikirim')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARD: Diterima --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm"
                style="background: linear-gradient(135deg, #28c76f 0%, #6ee7a0 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-success shadow-sm">
                            <i class="bx bx-check-double fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Diterima</p>
                        <h3 class="text-white mb-0 fw-bold">
                            {{ $transfers->where('status', 'diterima')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARD: Ditolak/Dibatalkan --}}
        <div class="col-12 col-sm-6 col-xl-3 swipeable-card">
            <div class="card h-100 border-0 shadow-sm"
                style="background: linear-gradient(135deg, #ea5455 0%, #f28f8f 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-danger shadow-sm">
                            <i class="bx bx-x-circle fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm opacity-75">Ditolak/Dibatalkan</p>
                        <h3 class="text-white mb-0 fw-bold">
                            {{ $transfers->where('status', 'ditolak')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                {{-- FILTER PANEL (AJAX) --}}
                <div class="card-body">
                    <div class="row g-3 align-items-end" id="filterPanel">
                        <div class="col-12 col-md-6">
                            <label for="filter-search" class="form-label fw-semibold">Pencarian</label>
                            <div class="input-group">
                                <input type="text" id="filter-search" class="form-control"
                                    placeholder="Cari kode atau user...">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="filter-date-range" class="form-label fw-semibold">Rentang Tanggal</label>
                            <div class="input-group">
                                <input type="text" id="filter-date-range" class="form-control"
                                    placeholder="YYYY-MM-DD to YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="filter-status" class="form-label fw-semibold">Status</label>
                            <select id="filter-status" class="form-select select2" data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diterima">Diterima</option>
                                <option value="diterima_sebagian">Diterima Sebagian</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="filter-store-asal" class="form-label fw-semibold">Toko Asal</label>
                            <select id="filter-store-asal" class="form-select select2" data-placeholder="Semua Toko">
                                <option value="">Semua Toko</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name_toko }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label for="filter-store-tujuan" class="form-label fw-semibold">Toko Tujuan</label>
                            <select id="filter-store-tujuan" class="form-select select2" data-placeholder="Semua Toko">
                                <option value="">Semua Toko</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name_toko }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary px-2"
                                title="Reset Filter" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bx bx-reset fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card rounded-3 shadow-sm border-0">
                <div
                    class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Transfer Stok</h5>
                        <p class="text-sm text-muted mb-0">Riwayat pengiriman & penerimaan stok antar cabang</p>
                    </div>
                    @can('create-stok-transfer')
                        <a href="{{ route('stok-transfer.create') }}" class="btn btn-info px-2"
                            title="Buat Transfer Stok Baru" data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="bx bx-plus-circle fs-5"></i>
                        </a>
                    @endcan
                </div>



                {{-- TABLE CONTAINER --}}
                <div class="card-body p-0 position-relative">
                    <div id="tableLoadingOverlay"
                        class="position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
                        style="background: rgba(255,255,255,0.6); z-index: 10;">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>

                    <div id="tableContainer">
                        @include('inventory::inventaris.transfer._table', ['transfers' => $transfers])
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            const dataUrl = "{{ route('stok-transfer.data') }}";
            const tableContainer = document.getElementById('tableContainer');
            const loadingOverlay = document.getElementById('tableLoadingOverlay');

            const filterSearch = document.getElementById('filter-search');
            const filterStatus = document.getElementById('filter-status');
            const filterStoreAsal = document.getElementById('filter-store-asal');
            const filterStoreTujuan = document.getElementById('filter-store-tujuan');

            // KOREKSI 1: Hapus .value di sini. Simpan elemennya saja.
            const filterDateRange = document.getElementById('filter-date-range');

            const btnResetFilter = document.getElementById('btn-reset-filter');

            let debounceTimer = null;
            let abortController = null;

            function buildParams(page = 1) {
                const params = new URLSearchParams();
                if (filterSearch.value.trim()) params.set('search', filterSearch.value.trim());
                if (filterStatus.value) params.set('status', filterStatus.value);
                if (filterStoreAsal.value) params.set('store_asal_id', filterStoreAsal.value);
                if (filterStoreTujuan.value) params.set('store_tujuan_id', filterStoreTujuan.value);

                // KOREKSI 2: Ambil nilainya (.value) di dalam fungsi ini secara dinamis
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
            $('#filter-status, #filter-store-asal, #filter-store-tujuan').on('select2:select select2:clear', () =>
                fetchTable(1));

            btnResetFilter.addEventListener('click', function() {
                filterSearch.value = '';

                // KOREKSI 4: Kosongkan value dari elemennya
                filterDateRange.value = '';

                $('#filter-status, #filter-store-asal, #filter-store-tujuan').val('').trigger('change');
                fetchTable(1);
            });

            attachPaginationHandlers();
        });
    </script>
@endsection
