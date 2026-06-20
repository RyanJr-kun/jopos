@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endsection

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center justify-content-start">
            <!-- Filter Pencarian -->
            <div class="col-md-3">
                <input type="text" name="search" id="searchInput" class="form-control"
                    placeholder="Cari invoice atau pelanggan..." value="{{ request('search') }}">
            </div>

            <!-- Filter Rentang Tanggal -->
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" id="flatpickr-date" class="form-control"
                        placeholder="Pilih rentang tanggal..."
                        value="{{ request('date_from') && request('date_to') ? request('date_from') . ' to ' . request('date_to') : '' }}">
                    @if (request('date_from'))
                        <button type="button" class="btn" id="clearDateBtn" title="Hapus Filter Tanggal"
                            data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="bx bx-x"></i>
                        </button>
                    @else
                        <button type="button" class="btn d-none" id="clearDateBtn" title="Hapus Filter Tanggal"
                            data-bs-toggle="tooltip" data-bs-placement="top">
                            <i class="bx bx-x"></i>
                        </button>
                    @endif
                </div>
            </div>
            <!-- Filter Dropdown Status -->
            <div class="col-md-2">
                <select name="status" id="statusFilter" class="form-select select2" data-placeholder="Semua Status">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Tombol Tambah -->
            <div class="col-md-auto ms-md-auto">
                <a href="{{ route('penjualan.create') }}" class="btn btn-outline-info mb-0">
                    <i class="bx bx-plus me-2"></i>Transaksi
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header pb-0 px-3 pt-2 mb-3">
        <div class="d-flex justify-content-start align-items-center">
            <div>
                <h5 class="mb-n1 fw-bolder">Invoice Sale</h5>
                <p class="text-sm mb-0"> riwayat transaksi penjualan.</p>
            </div>
        </div>
    </div>
    <div class="card-body px-0 pt-0 pb-2">

        <div id="penjualan-table-container" class="mt-3">
            @include('pos::penjualan.partials._penjualan_table')
        </div>
    </div>
</div>

{{-- Ubah Modal --}}
<div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-3">
                <i class="bx bx-ban icon-xl text-warning mb-3"></i> {{-- Ganti ikon --}}
                <p class="mb-0">Anda yakin ingin membatalkan transaksi ini?</p> {{-- Ganti teks --}}
                <h6 class="mt-2" id="invoiceNumberToCancel"></h6>
                <div class="mt-4">
                    {{-- Form ini akan mengirim request ke method 'update' atau method khusus 'cancel' --}}
                    <form id="cancelInvoiceForm" method="POST" action="">
                        @method('PUT') {{-- Atau PATCH --}}
                        @csrf
                        <input type="hidden" name="status_pembayaran" value="Batal">
                        <button type="submit" class="btn btn-warning btn-sm">Ya, Batalkan</button>
                        {{-- Ganti teks & warna --}}
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                            data-bs-dismiss="modal">Tutup</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script type="module">
    // --- Inisialisasi Flatpickr (Gaya Anda) ---
    document.addEventListener('DOMContentLoaded', function() {
        const flatpickrDate = document.querySelector('#flatpickr-date');
        const clearDateBtn = document.querySelector('#clearDateBtn');
        let fp;

        if (flatpickrDate) {
            fp = flatpickrDate.flatpickr({
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'id',
                allowInput: true,
                onClose(selectedDates) {
                    if (selectedDates.length === 2) {
                        if (clearDateBtn) clearDateBtn.classList.remove('d-none');
                        if (typeof window.fetchData === 'function') window.fetchData(1);
                    } else if (selectedDates.length === 0) {
                        if (clearDateBtn) clearDateBtn.classList.add('d-none');
                        if (typeof window.fetchData === 'function') window.fetchData(1);
                    }
                }
            });
        }

        if (clearDateBtn) {
            clearDateBtn.addEventListener('click', () => {
                if (fp) fp.clear();
                clearDateBtn.classList.add('d-none');
                if (typeof window.fetchData === 'function') window.fetchData(1);
            });
        }
    });

    // --- Inisialisasi Select2 (Gaya Anda) ---
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
    // Sedikit pelindung untuk memastikan jQuery sudah siap di script biasa
    const runAjaxScripts = () => {
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                // Script untuk Modal Pembatalan Transaksi
                $('#cancelConfirmationModal').on('show.bs.modal', function(event) {
                    // Tombol yang memicu modal
                    let button = $(event.relatedTarget);

                    // Ambil data dari atribut tombol
                    let invoiceNumber = button.data('invoice-number');
                    let formUrl = button.data('url');

                    // Temukan elemen di dalam modal dan perbarui nilainya
                    let modal = $(this);
                    modal.find('#invoiceNumberToCancel').text('Nomor Referensi: ' + invoiceNumber);
                    modal.find('#cancelInvoiceForm').attr('action', formUrl); // Inject URL ke form
                });

                // Fungsi Utama Fetch Data
                window.fetchData = function(page = 1) {
                    let search = $('#searchInput').val();
                    let status = $('#statusFilter').val();
                    let dateRange = $('#flatpickr-date').val();

                    let date_from = '';
                    let date_to = '';

                    if (dateRange && dateRange.includes(' to ')) {
                        let dates = dateRange.split(' to ');
                        date_from = dates[0].trim();
                        date_to = dates[1].trim();
                    }

                    $('#penjualan-table-container').css('opacity', 0.5);

                    $.ajax({
                        url: "{{ route('penjualan.index') }}",
                        type: "GET",
                        // PENTING: Header ini wajib agar $request->ajax() di controller merespon true
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: {
                            page: page,
                            search: search,
                            status: status,
                            date_from: date_from,
                            date_to: date_to
                        },
                        success: function(response) {
                            // Update isi tabel
                            $('#penjualan-table-container').html(response).css('opacity',
                                1);

                            // PENTING: Inisialisasi ulang Tooltips Bootstrap agar tombol action tidak mati
                            if (typeof bootstrap !== 'undefined') {
                                const tooltipTriggerList = [].slice.call(document
                                    .querySelectorAll('[data-bs-toggle="tooltip"]'));
                                tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            }

                            // Update URL browser
                            updateBrowserURL(page, search, status, date_from, date_to);
                        },
                        error: function(xhr) {
                            $('#penjualan-table-container').css('opacity', 1);
                            console.error("Terjadi kesalahan: ", xhr.responseText);
                        }
                    });
                };

                function updateBrowserURL(page, search, status, from, to) {
                    let params = new URLSearchParams();
                    if (page > 1) params.set('page', page);
                    if (search) params.set('search', search);
                    if (status) params.set('status', status);
                    if (from) params.set('date_from', from);
                    if (to) params.set('date_to', to);

                    let newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() :
                        '');
                    window.history.pushState({
                        path: newUrl
                    }, '', newUrl);
                }

                // --- Event Listeners ---

                // 1. Search dengan Debounce
                let timeout = null;
                $('#searchInput').on('keyup', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        window.fetchData(1);
                    }, 500);
                });

                // 2. Filter Status Select2 (Ditambahkan event select2 spesifik)
                $('#statusFilter').on('select2:select select2:clear change', function() {
                    window.fetchData(1);
                });

                // 3. Pagination Link Click
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    // Ambil angka halamannya dengan lebih aman menggunakan URL API
                    let url = new URL($(this).attr('href'), window.location.origin);
                    let page = url.searchParams.get('page');
                    if (page) window.fetchData(page);
                });

            });
        } else {
            setTimeout(runAjaxScripts, 50); // Ulangi cek jika jQuery belum load
        }
    };

    runAjaxScripts();
</script>
@endsection
