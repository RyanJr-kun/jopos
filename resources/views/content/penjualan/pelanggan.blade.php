@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="container-fluid p-3">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-n1">List Customer</h6>
                        <p class="text-sm mb-0">Kelola data pelangganmu.</p>
                    </div>
                    <div class="ms-md-auto mt-2">
                        {{-- triger-modal-create --}}
                        <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="bx bx-plus cursor-pointer pe-2"></i>Customer
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="">
                    <div class="row g-3 align-items-center justify-content-between">
                        <div class="col-5 col-lg-3 ms-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="cari Customer ...">
                        </div>
                        <div class="col-5 col-lg-2 me-3">
                            <select id="statusFilter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{-- Container untuk tabel yang akan di-update oleh AJAX --}}
                <div id="pelanggan-table-container" class="mt-3">
                    {{-- Memuat tabel parsial untuk tampilan awal --}}
                    @include('content.penjualan._pelanggan_table', ['customers' => $customers])
                </div>
            </div>
        </div>
        {{-- modal-create --}}
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title">Buat Customer Baru</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createCustomerForm" action="{{ route('pelanggan.store') }}" method="post">
                            @csrf
                            <div class="mb-2">
                                <label for="name" class="form-label">Nama</label>
                                <input id="name" name="name" type="text" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-1">
                                <label for="kontak" class="form-label">Kontak</label>
                                <input id="kontak" name="kontak" type="text" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-1">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="example@gmail.com">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-1">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="justify-content-end form-check form-switch form-check-reverse mb-2">
                                <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                                <input id="status" class="form-check-input" type="checkbox" name="status" value="1"
                                    checked>
                            </div>
                            <div class="modal-footer border-0 pb-0">
                                <button type="submit" class="btn btn-outline-info btn-sm p-2">Tambah Customer</button>
                                <button type="button" class="btn btn-danger btn-sm p-2"
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
                        <h6 class="modal-title" id="editModalLabel">Edit Customer</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCustomerForm" method="post">
                            @method('put')
                            @csrf
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama</label>
                                <input id="edit_name" name="name" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_kontak" class="form-label">Kontak</label>
                                <input id="edit_kontak" name="kontak" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input id="edit_email" name="email" type="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="edit_alamat" class="form-label">Alamat</label>
                                <textarea id="edit_alamat" name="alamat" class="form-control" rows="3"></textarea>
                            </div>
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
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1"
            aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center mt-3 mx-n5">
                        <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                        <p class="mb-0">Apakah Anda yakin ingin menghapus pelanggan ini?</p>
                        <h6 class="mt-2" id="pelangganNameToDelete"></h6>
                        <div class="mt-4">
                            <form id="deleteCustomerForm" method="POST" action="#">
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
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // --- AJAX UNTUK FILTER, PENCARIAN, DAN PAGINASI ---
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
                        let url = '{{ route('pelanggan.index') }}';

                        $('#pelanggan-table-container').css('opacity', 0.5); // Efek loading

                        $.ajax({
                            url: url,
                            data: {
                                search: search,
                                status: status,
                                page: page
                            },
                            success: function(data) {
                                $('#pelanggan-table-container').html(data).css('opacity', 1);
                                // Update URL di browser
                                window.history.pushState({
                                        path: url + '?page=' + page + '&search=' + search + '&status=' +
                                            status
                                    }, '', url + '?page=' + page + '&search=' + search + '&status=' +
                                    status);
                            },
                            error: function() {
                                $('#pelanggan-table-container').css('opacity', 1);
                                alert('Gagal memuat data. Silakan coba lagi.');
                            }
                        });
                    }

                    // Event listener untuk input pencarian dengan debounce
                    $('#searchInput').on('keyup', debounce(function() {
                        fetchData(1); // Kembali ke halaman 1 saat mencari
                    }, 500)); // tunda 500ms

                    // Event listener untuk filter status
                    $('#statusFilter').on('change', function() {
                        fetchData(1); // Kembali ke halaman 1 saat filter berubah
                    });

                    // Event listener untuk klik paginasi (delegasi event)
                    $(document).on('click', '#pelanggan-table-container .pagination a', function(e) {
                        e.preventDefault();
                        let pageUrl = $(this).attr('href');
                        let page = new URL(pageUrl).searchParams.get("page");
                        if (page) {
                            fetchData(page);
                        }
                    });

                    // Fungsi untuk mendapatkan halaman saat ini dari URL
                    const getCurrentPage = () => new URL(window.location.href).searchParams.get('page') || 1;

                    // --- MODAL EDIT ---
                    const editModal = document.getElementById('editModal');
                    const editForm = document.getElementById('editCustomerForm');

                    if (editModal) {
                        editModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget;
                            const dataUrl = button.getAttribute('data-url');
                            const updateUrl = button.getAttribute('data-update-url');

                            const inputNama = document.getElementById('edit_name');
                            const inputKontak = document.getElementById('edit_kontak');
                            const inputEmail = document.getElementById('edit_email');
                            const inputAlamat = document.getElementById('edit_alamat');
                            const inputStatus = document.getElementById('edit_status');

                            if (editForm) {
                                editForm.action = updateUrl;
                            }

                            fetch(dataUrl)
                                .then(response => response.json())
                                .then(data => {
                                    if (inputNama) inputNama.value = data.name;
                                    if (inputKontak) inputKontak.value = data.kontak;
                                    if (inputEmail) inputEmail.value = data.email;
                                    if (inputAlamat) inputAlamat.value = data.alamat;
                                    if (inputStatus) inputStatus.checked = data.status == 1;
                                })
                                .catch(error => console.error('Error fetching pelanggan data:', error));
                        });

                        // AJAX untuk submit form edit
                        $(editForm).on('submit', function(e) {
                                e.preventDefault();
                                const form = $(this);

                                $.ajax({
                                        url: form.attr('action'),
                                        method: 'POST', // Laravel handles PUT via _method field
                                        data: form.serialize(),
                                        success: function(response) {
                                            // PERBAIKAN: Tambahkan feedback setelah sukses
                                            if (response.success) {
                                                bootstrap.Modal.getInstance(editModal).hide();
                                                window.showToast('success', response.message);
                                            },
                                            error: function(xhr) {
                                                // PERBAIKAN: Tambahkan feedback jika gagal
                                                // Handle error (misal: tampilkan pesan error validasi)
                                                const errors = xhr.responseJSON.errors;
                                                window.showToast('error',
                                                    'Periksa kembali data yang Anda masukkan.');
                                            }
                                        });
                                });
                        }

                        // --- MODAL DELETE ---
                        const deleteModalEl = document.getElementById('deleteConfirmationModal');
                        if (deleteModalEl) {
                            const deleteForm = deleteModalEl.querySelector('#deleteCustomerForm');
                            const modalBodyName = deleteModalEl.querySelector('#pelangganNameToDelete');
                            const deleteModalInstance = new bootstrap.Modal(deleteModalEl);
                            let pelangganIdToDelete = null;

                            deleteModalEl.addEventListener('show.bs.modal', function(event) {
                                const button = event.relatedTarget;
                                pelangganIdToDelete = button.getAttribute('data-pelanggan-id');
                                const pelangganName = button.getAttribute('data-pelanggan-name');

                                modalBodyName.textContent = pelangganName;
                            });

                            deleteForm.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    if (!pelangganIdToDelete) return;

                                    const url = `/pelanggan/${pelangganIdToDelete}`;

                                    fetch(url, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json',
                                            }
                                        })
                                        .then(response => response.json().then(data => ({
                                            ok: response.ok,
                                            data
                                        })))
                                        .then(({
                                            ok,
                                            data
                                        }) => {
                                            deleteModalInstance.hide();
                                            if (ok && data.success) {
                                                window.showToast('success', data.message);
                                                else {
                                                    window.showToast('error', data.message || 'Terjadi kesalahan.');
                                                })
                                            .catch(error => {
                                                deleteModalInstance.hide();
                                                window.showToast('error',
                                                    'Tidak dapat terhubung ke server.');
                                            });
                                        });
                                }

                                // --- MODAL CREATE (AJAX) ---
                                const createModalEl = document.getElementById('createModal');
                                const createForm = document.getElementById('createCustomerForm');

                                $(createForm).on('submit', function(e) {
                                    e.preventDefault();
                                    const form = $(this);

                                    $.ajax({
                                        url: form.attr('action'),
                                        method: 'POST',
                                        data: form.serialize(),
                                        success: function(response) {
                                            if (response.success) {
                                                bootstrap.Modal.getInstance(createModalEl).hide();
                                                form[0].reset(); // Reset form
                                                window.showToast('success', response.message);
                                                fetchData(1); // Muat ulang dari halaman pertama
                                            }
                                        },
                                        error: function(xhr) {
                                            const errors = xhr.responseJSON.errors;
                                            // Menghapus error sebelumnya
                                            form.find('.is-invalid').removeClass('is-invalid');
                                            form.find('.invalid-feedback').text('');

                                            // Menampilkan error baru
                                            $.each(errors, function(key, value) {
                                                const input = form.find(`[name="${key}"]`);
                                                input.addClass('is-invalid');
                                                input.next('.invalid-feedback').text(value[0]);
                                            });
                                        }
                                    });
                                });

                                // --- SHOW CREATE MODAL ON VALIDATION ERROR ---
                                const hasError = document.querySelector('.is-invalid');
                                if (hasError) {
                                    var createModal = new bootstrap.Modal(document.getElementById('createModal'));
                                    createModal.show();
                                }
                                // Scrollbar
                                const win = navigator.platform.indexOf('Win') > -1;
                                if (win && document.querySelector('#sidenav-scrollbar')) {
                                    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
                                        damping: '0.5'
                                    });
                                }
                            });
    </script>
@endsection
@endsection
