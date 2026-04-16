@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="container-fluid p-3">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-n1">List Supplier</h6>
                        <p class="text-sm mb-0">Kelola data pemasokmu.</p>
                    </div>
                    <div class="ms-md-auto mt-2">
                        {{-- triger-modal-create --}}
                        <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="bx bx-plus fixed-plugin-button-nav cursor-pointer pe-2"></i>Supplier
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="">
                    <div class="row g-3 align-items-center justify-content-between">
                        <div class="col-5 col-lg-3 ms-3">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari Supplier..." value="{{ request('search') }}">
                        </div>
                        <div class="col-5 col-lg-2 me-3">
                            <select name="status" id="statusFilter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1" @selected(request('status') == '1')>Aktif</option>
                                <option value="0" @selected(request('status') == '0')>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="pemasok-table-container">
                    @include('content.pembelian._pemasok_table')
                </div>
            </div>
        </div>
        {{-- modal-create --}}
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title">Tambah Supplier Baru</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- PERBAIKAN: Form ini sekarang akan melakukan submit standar (full page refresh) --}}
                        <form action="{{ route('pemasok.store') }}" method="post">
                            @csrf
                            <x-pemasok-form-fields />
                            <div class="justify-content-end form-check form-switch form-check-reverse my-2">
                                <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                                <input id="status" class="form-check-input" type="checkbox" name="status" value="1"
                                    checked>
                            </div>
                            <div class="modal-footer border-0 pb-0">
                                <button type="submit" class="btn btn-outline-info btn-sm">Buat Supplier</button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-dismiss="modal">Batalkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal edit --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title" id="editModalLabel">Edit Supplier</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editSupplierForm" method="post">
                            @method('put')
                            @csrf
                            <x-pemasok-form-fields prefix="edit_" :pemasok="new \App\Models\Supplier()" />
                            <div class="justify-content-end form-check form-switch form-check-reverse mt-3">
                                <label class="me-auto form-check-label" for="edit_status">Status</label>
                                <input id="edit_status" class="form-check-input" type="checkbox" name="status"
                                    value="1">
                            </div>
                            <div class="modal-footer border-0 pb-0">
                                <button type="submit" class="btn btn-outline-info btn-sm">Simpan Perubahan</button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-dismiss="modal">Batalkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal delete --}}
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center mt-3 mx-n5">
                        <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                        <p class="mb-0">Apakah Anda yakin ingin menghapus pemasok ini?</p>
                        <h6 class="mt-2" id="pemasokNameToDelete"></h6>
                        <div class="mt-4">
                            <form id="deleteSupplierForm" method="POST" action="#">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-2"
                                    data-bs-dismiss="modal">Batal</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- MODAL EDIT ---
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    const editForm = document.getElementById('editSupplierForm');
                    const inputNama = document.getElementById('edit_name');
                    const inputPerusahaan = document.getElementById('edit_perusahaan');
                    const inputKontak = document.getElementById('edit_kontak');
                    const inputEmail = document.getElementById('edit_email');
                    const inputAlamat = document.getElementById('edit_alamat');
                    const inputNote = document.getElementById('edit_note');
                    const inputStatus = document.getElementById('edit_status');

                    editForm.action = updateUrl;

                    fetch(dataUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            inputNama.value = data.name;
                            inputPerusahaan.value = data.perusahaan;
                            inputKontak.value = data.kontak;
                            inputEmail.value = data.email;
                            inputAlamat.value = data.alamat;
                            inputNote.value = data.note;
                            inputStatus.checked = data.status == 1;
                        })
                        .catch(error => console.error('Error fetching pemasok data:', error));
                });
            }

            // --- MODAL DELETE ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const pemasokId = button.getAttribute('data-pemasok-id');
                    const pemasokName = button.getAttribute('data-pemasok-name');
                    const modalBodyName = deleteModal.querySelector('#pemasokNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deleteSupplierForm');

                    modalBodyName.textContent = pemasokName;
                    // Pastikan route untuk delete sudah benar, contoh: /pemasok/{id}
                    deleteForm.action = `{{ url('pemasok') }}/${pemasokId}`;
                });
            }

            // --- AJAX FILTER & SEARCH ---
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
                    let url = '{{ route('pemasok.index') }}';

                    $('#pemasok-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            status: status,
                            page: page
                        },
                        success: function(data) {
                            $('#pemasok-table-container').html(data).css('opacity', 1);
                            // Update URL di browser
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&status=' + status
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&status=' + status);
                        },
                        error: function() {
                            $('#pemasok-table-container').css('opacity', 1);
                            window.showToast('error', 'Gagal memuat data. Silakan coba lagi.');
                        }
                    });
                }

                // Event listener untuk input pencarian dengan debounce
                $('#searchInput').on('keyup', debounce(function() {
                    fetchData(1); // Selalu kembali ke halaman 1 saat melakukan pencarian baru
                }, 500));

                // Event listener untuk filter status
                $('#statusFilter').on('change', function() {
                    fetchData(1); // Selalu kembali ke halaman 1 saat mengubah filter
                });

                // Event listener untuk klik pada link pagination
                $(document).on('click', '#pemasok-table-container .pagination a', function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    if (page) {
                        fetchData(page);
                    }
                });
            });

            // --- SHOW CREATE MODAL ON VALIDATION ERROR ---
            const hasError = document.querySelector('.is-invalid');
            @if ($errors->any())
                var createModal = new bootstrap.Modal(document.getElementById('createModal'));
                createModal.show();
            @endif

        });
    </script>
@endsection
@endsection
