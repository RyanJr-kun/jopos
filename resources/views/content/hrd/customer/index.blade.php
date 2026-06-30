@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="row mb-4 align-items-stretch">
        <div class="col-12 col-md-4 col-xl-3 mb-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #696cff 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-group fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Pelanggan</p>
                        {{-- Mengambil total data dari paginasi atau count biasa --}}
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalPelanggan">
                            {{ method_exists($customers, 'total') ? $customers->total() : $customers->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="row g-3 align-items-center justify-content-start w-100 m-0">
                        <div class="col-12 col-sm-6 col-md-5">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari invoice atau pelanggan..." value="{{ request('search') }}">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <select name="status" id="statusFilter" class="form-select select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(request('status') == $status)>
                                        {{ $status == 1 ? 'Aktif' : 'Tidak Aktif' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-auto ms-md-auto">
                            <button class="btn btn-info w-100 w-md-auto" data-bs-toggle="modal"
                                data-bs-target="#createModal">
                                <i class="bx bx-plus cursor-pointer pe-1"></i>Pelanggan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card rounded-2">
        <div class="card-header pb-0 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-n1 fw-bolder">List Pelanggan</h5>
                    <p class="text-sm mb-0">Kelola data pelangganmu.</p>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            {{-- Container untuk tabel yang akan di-update oleh AJAX --}}
            <div id="pelanggan-table-container" class="mt-3">
                {{-- Memuat tabel parsial untuk tampilan awal --}}
                @include('content.hrd.customer._pelanggan_table', ['customers' => $customers])
            </div>
        </div>
    </div>
    {{-- modal-create --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title">Buat Customer Baru</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
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
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
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
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
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
    </script>
    <script>
        // Sedikit pelindung untuk memastikan jQuery sudah siap di script biasa
        const runAjaxScripts = () => {
            if (typeof $ !== 'undefined') {
                $(document).ready(function() {

                    // Fungsi Utama Fetch Data
                    window.fetchData = function(page = 1) {
                        let search = $('#searchInput').val();
                        let status = $('#statusFilter').val();

                        $('#penjualan-table-container').css('opacity', 0.5);

                        $.ajax({
                            url: "{{ route('pelanggan.index') }}",
                            type: "GET",
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            data: {
                                page: page,
                                search: search,
                                status: status,
                            },
                            success: function(response) {
                                // Update isi tabel
                                $('#pelanggan-table-container').html(response).css('opacity',
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
                                $('#pelanggan-table-container').css('opacity', 1);
                                console.error("Terjadi kesalahan: ", xhr.responseText);
                            }
                        });
                    };

                    function updateBrowserURL(page, search, status) {
                        let params = new URLSearchParams();
                        if (page > 1) params.set('page', page);
                        if (search) params.set('search', search);
                        if (status) params.set('status', status);

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- MODAL EDIT ---
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editCustomerForm');

            if (editModal) {
                // Kita tetap pakai fetch HANYA untuk mengambil data saat modal dibuka
                // agar inputan terisi otomatis. Namun saat tombol simpan ditekan, form akan submit biasa.
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    const inputNama = document.getElementById('edit_name');
                    const inputKontak = document.getElementById('edit_kontak');
                    const inputEmail = document.getElementById('edit_email');
                    const inputAlamat = document.getElementById('edit_alamat');
                    const inputStatus = document.getElementById('edit_status');

                    // Update action form agar mengarah ke route update yang benar
                    if (editForm) {
                        editForm.action = updateUrl;
                    }

                    // Ambil data JSON untuk mengisi form
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
            }

            // --- MODAL DELETE ---
            const deleteModalEl = document.getElementById('deleteConfirmationModal');
            if (deleteModalEl) {
                const deleteForm = deleteModalEl.querySelector('#deleteCustomerForm');
                const modalBodyName = deleteModalEl.querySelector('#pelangganNameToDelete');

                deleteModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const pelangganIdToDelete = button.getAttribute('data-pelanggan-id');
                    const pelangganName = button.getAttribute('data-pelanggan-name');

                    modalBodyName.textContent = pelangganName;

                    // Set atribut action pada form agar sesuai dengan ID yang mau dihapus
                    // Misalnya mengarah ke /pelanggan/1
                    if (deleteForm && pelangganIdToDelete) {
                        deleteForm.action = `/pelanggan/${pelangganIdToDelete}`;
                    }
                });
            }

            // --- SCROLLBAR ---
            const win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
                    damping: '0.5'
                });
            }
        });
    </script>
@endsection
