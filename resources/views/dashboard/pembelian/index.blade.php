@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    {{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '#'],
            ['name' => 'Daftar Invoice Pembelian', 'url' => route('pembelian.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2 mb-4">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Invoice Pembelian</h6>
                    <p class="text-sm mb-0"> riwayat transaksi pembelian.</p>
                </div>
                <div class="ms-md-auto mt-2">
                    <a href="{{ route('pembelian.create') }}" class="btn btn-outline-info mb-0">
                        <i class="fa fa-plus me-2"></i>Transaksi
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="filter-container p-3">
                <div class="row g-3 align-items-center justify-content-between">
                    <!-- Filter Pencarian -->
                    <div class="col-md-4">
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Cari invoice atau pemasok..." value="{{ request('search') }}">
                    </div>
                    <!-- Filter Dropdown Status -->
                    <div class="col-md-3">
                        <select name="status" id="statusFilter" class="form-select">
                            <option value="">Semua Status Pembayaran</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}
                                </option>
                            @endforeach
                            <option value="Dibatalkan" @selected(request('status') == 'Dibatalkan')>Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="pembelian-table-container" class="mt-3">
                @include('dashboard.pembelian._pembelian_table')
            </div>
        </div>
    </div>
</div>

{{-- modal edit --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 mb-n3">
                <h6 class="modal-title" id="editModalLabel">Edit Unit Produk</h6>
                <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    @method('put')
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <div class="mb-3">
                                <label for="edit_nama" class="form-label">Nama</label>
                                <input id="edit_nama" name="nama" type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_slug" class="form-label">Slug</label>
                                <input id="edit_slug" name="slug" type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_singkat" class="form-label">Nama Pendek</label>
                                <input id="edit_singkat" name="singkat" type="text" class="form-control" required>
                            </div>
                            <div class="justify-content-end form-check form-switch form-check-reverse mt-3">
                                <label class="me-auto form-check-label" for="edit_status">Status</label>
                                <input id="edit_status" class="form-check-input" type="checkbox" name="status"
                                    value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-0">
                        <button type="submit" class="btn btn-info btn-sm">Simpan Perubahan</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Batalkan Transaksi --}}
<div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-3 mx-n5">
                <i class="bx bx-exclamation-triangle fa-2x text-warning mb-3"></i>
                <p class="mb-0">Anda yakin ingin membatalkan transaksi ini?</p>
                <h6 class="mt-2" id="invoiceNumberToCancel"></h6>
                <small class="text-warning">Tindakan ini akan mengembalikan stok produk yang telah ditambahkan.</small>
                <div class="mt-4">
                    <form id="cancelInvoiceForm" method="POST" action="#">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="status_pembayaran" value="Dibatalkan">
                        <button type="submit" class="btn btn-warning btn-sm">Ya, Batalkan</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                            data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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
                let url = '{{ route('pembelian.index') }}';

                // Tambahkan spinner atau loading state di sini jika diinginkan
                $('#pembelian-table-container').css('opacity', 0.5);

                $.ajax({
                    url: url,
                    data: {
                        search: search,
                        status: status,
                        page: page
                    },
                    success: function(data) {
                        $('#pembelian-table-container').html(data);
                        $('#pembelian-table-container').css('opacity', 1);
                        // Update URL di browser
                        window.history.pushState({
                                path: url + '?page=' + page + '&search=' + search + '&status=' +
                                    status
                            }, '', url + '?page=' + page + '&search=' + search + '&status=' +
                            status);
                    },
                    error: function() {
                        // Handle error, misalnya tampilkan pesan
                        $('#pembelian-table-container').css('opacity', 1);
                        alert('Gagal memuat data. Silakan coba lagi.');
                    }
                });
            }

            // Event listener untuk input pencarian dengan debounce
            $('#searchInput').on('keyup', debounce(function() {
                fetchData(1); // Selalu kembali ke halaman 1 saat melakukan pencarian baru
            }, 500)); // Tunggu 500ms setelah user berhenti mengetik

            // Event listener untuk filter status
            $('#statusFilter').on('change', function() {
                fetchData(1); // Selalu kembali ke halaman 1 saat filter diubah
            });

            // Event listener untuk klik paginasi
            $(document).on('click', '#pembelian-table-container .pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                if (page) {
                    fetchData(page);
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelModal = document.getElementById('cancelConfirmationModal');
            if (cancelModal) {
                cancelModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const pembelianReferensi = button.getAttribute('data-pembelian-referensi');
                    cancelModal.querySelector('#invoiceNumberToCancel').textContent = pembelianReferensi;
                    cancelModal.querySelector('#cancelInvoiceForm').action =
                        `{{ url('pembelian') }}/${pembelianReferensi}`;
                });
            }
        });
    </script>
@endsection
@endsection
