@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endsection
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '#'],
            ['name' => 'Daftar Invoice Penjualan', 'url' => route('penjualan.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2 ">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Invoice Penjualan</h6>
                    <p class="text-sm mb-0"> riwayat transaksi penjualan.</p>
                </div>
                <div class="ms-md-auto mt-2">
                    <a href="{{ route('penjualan.create') }}" class="btn btn-outline-info mb-0">
                        <i class="fa fa-plus me-2"></i>Transaksi
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="filter-container p-3">
                <div class="row g-3 align-items-center justify-content-between">
                    <!-- Filter Pencarian Unit -->
                    <div class="col-md-4">
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Cari invoice atau pelanggan..." value="{{ request('search') }}">
                    </div>
                    <!-- Filter Dropdown Status -->
                    <div class="col-md-3">
                        <select name="status" id="statusFilter" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div id="penjualan-table-container" class="mt-3">
                @include('dashboard.penjualan._penjualan_table')
            </div>
        </div>
    </div>
</div>

{{-- Ubah Modal --}}
<div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-3 mx-n5">
                <i class="bx bx-exclamation-triangle fa-2x text-warning mb-3"></i> {{-- Ganti ikon --}}
                <p class="mb-0">Anda yakin ingin membatalkan transaksi ini?</p> {{-- Ganti teks --}}
                <h6 class="mt-2" id="invoiceNumberToCancel"></h6>
                <small class="text-warning">Tindakan ini akan mengembalikan stok produk yang terjual.</small>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@endsection
@endsection
