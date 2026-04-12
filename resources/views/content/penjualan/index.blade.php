@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                    <input type="text" id="dateRangePicker" class="form-control"
                        placeholder="Pilih rentang tanggal..."
                        value="{{ request('date_from') && request('date_to') ? request('date_from') . ' to ' . request('date_to') : '' }}">
                    @if (request('date_from'))
                        <button type="button" class="btn btn-outline-secondary" id="clearDateBtn">
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

<div class="card rounded-2 ">
    <div class="card-header pb-0 px-3 pt-2 mb-3">
        <div class="d-flex justify-content-start align-items-center">
            <div>
                <h4 class="mb-n1 fw-bolder">Invoice Sale</h4>
                <p class="text-sm mb-0"> riwayat transaksi penjualan.</p>
            </div>
        </div>
    </div>
    <div class="card-body px-0 pt-0 pb-2">

        <div id="penjualan-table-container" class="mt-3">
            @include('content.penjualan._penjualan_table')
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
                    <form id="cancelInvoiceForm" method="POST" action="#">
                        @method('PUT') {{-- Atau PATCH --}}
                        @csrf
                        <input type="hidden" name="status_pembayaran" value="Dibatalkan">
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

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        flatpickr('#dateRangePicker', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            locale: 'id',
            allowInput: true,
            onClose(selectedDates) {
                if (selectedDates.length === 2) {
                    applyFilters();
                }
            }
        });

        function getUrlParams() {
            const params = new URLSearchParams(window.location.search);
            return params;
        }

        function applyFilters() {
            const params = getUrlParams();
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const dateRange = document.getElementById('dateRangePicker').value;

            search ? params.set('search', search) : params.delete('search');
            status ? params.set('status', status) : params.delete('status');

            if (dateRange.includes(' to ')) {
                const [from, to] = dateRange.split(' to ');
                params.set('date_from', from.trim());
                params.set('date_to', to.trim());
            } else {
                params.delete('date_from');
                params.delete('date_to');
            }

            params.delete('page'); // reset ke halaman 1
            window.location.search = params.toString();
        }

        // Trigger filter
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500); // debounce 500ms
        });

        document.getElementById('statusFilter').addEventListener('change', applyFilters);

        // Clear tanggal
        document.getElementById('clearDateBtn')?.addEventListener('click', () => {
            const params = getUrlParams();
            params.delete('date_from');
            params.delete('date_to');
            params.delete('page');
            window.location.search = params.toString();
        });
    </script>
    <script>
        // Ganti script lama untuk modal delete dengan yang ini
        document.addEventListener('DOMContentLoaded', function() {
            const cancelModal = document.getElementById('cancelConfirmationModal');
            if (cancelModal) {
                cancelModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const invoiceNumber = button.getAttribute('data-invoice-number');
                    const form = cancelModal.querySelector('#cancelInvoiceForm');
                    const text = cancelModal.querySelector('#invoiceNumberToCancel');

                    if (text) text.textContent = invoiceNumber;
                    // Arahkan form ke route update
                    if (form) form.action = `/penjualan/${invoiceNumber}`;
                });
            }
        });

        // AJAX untuk filter dan pencarian
        $(document).ready(function() {
            // Fungsi untuk menunda eksekusi (debounce)
            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            // Fungsi untuk mengambil data dengan AJAX
            function fetchData(page = 1) {
                let search = $('#searchInput').val();
                let status = $('#statusFilter').val();
                let url = '{{ route('penjualan.index') }}';

                $('#penjualan-table-container').css('opacity', 0.5); // Efek loading

                $.ajax({
                    url: url,
                    data: {
                        search: search,
                        status: status,
                        page: page
                    },
                    success: function(data) {
                        $('#penjualan-table-container').html(data).css('opacity', 1);
                        window.history.pushState({
                                path: url + '?page=' + page + '&search=' + search + '&status=' +
                                    status
                            }, '', url + '?page=' + page + '&search=' + search + '&status=' +
                            status);
                    },
                    error: function() {
                        $('#penjualan-table-container').css('opacity', 1);
                        alert('Gagal memuat data. Silakan coba lagi.');
                    }
                });
            }

            // Event listener untuk input pencarian dengan debounce
            $('#searchInput').on('keyup', debounce(function() {
                fetchData(1); // Kembali ke halaman 1 saat mencari
            }, 500));

            // Event listener untuk filter status
            $('#statusFilter').on('change', function() {
                fetchData(1); // Kembali ke halaman 1 saat filter berubah
            });

            // Event listener untuk klik paginasi (delegasi event)
            $(document).on('click', '#penjualan-table-container .pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                if (page) fetchData(page);
            });
        });
    </script>
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length >
                            0,
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
@endsection
@endsection
