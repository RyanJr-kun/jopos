@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endsection
{{-- breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Manajemen Garansi', 'url' => route('garansi.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="mb-0">
                    <h6 class="mb-n1">Data Garansi</h6>
                    <p class="text-sm mb-0">
                        Kelola data garansimu
                    </p>
                </div>
                <div class="ms-auto mt-2">
                    {{-- triger-modal-create --}}
                    <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import"><i
                            class="fa fa-plus fixed-plugin-button-nav cursor-pointer pe-2"></i>Buat Garansi</button>
                </div>
            </div>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
            <div class="filter-container">
                <div class="row g-3 align-items-center justify-content-between">
                    <div class="col-5 col-lg-3 ms-3">
                        <input type="text" id="searchInput" name="search" class="form-control"
                            placeholder="Cari garansi..." value="{{ request('search') }}">
                    </div>
                    <div class="col-5 col-lg-2 me-3">
                        <select id="statusFilter" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" @selected(request('status') == '1')>Aktif</option>
                            <option value="0" @selected(request('status') == '0')>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="garansi-table-container">
                @include('dashboard.produk._garansi_table', ['garansis' => $garansis])
            </div>
        </div>
    </div>
    {{-- modal-create --}}
    <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n2">
                    <h6 class="modal-title" id="ModalLabel">Buat Garansi Baru</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createGaransiForm" action="{{ route('garansi.store') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label ">Nama</label>
                            <input id="nama" name="nama" type="string"
                                class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input id="slug" name="slug" type="string"
                                class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}"
                                required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="durasi" class="form-label">Durasi</label>
                                    <input type="number" class="form-control @error('durasi') is-invalid @enderror"
                                        id="durasi" name="durasi" value="{{ old('durasi') }}" required
                                        min="1">
                                    @error('durasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="period" class="form-label"> Periode </label>
                                    <select class="form-select" id="period" name="period" required>
                                        <option value="Day">Hari</option>
                                        <option value="Week">Minggu</option>
                                        <option value="Month">Bulan</option>
                                        <option value="Year">Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span
                                    class="text-danger">*</span></label>
                            <div id="quill-editor-create" style="min-height: 100px;">{!! old('deskripsi') !!}</div>
                            <div class="text-end text-muted small" id="counter-create">0/60</div>
                            <input type="hidden" name="deskripsi" id="deskripsi-create"
                                value="{{ old('deskripsi') }}">
                            @error('deskripsi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-2 justify-content-end form-check form-switch form-check-reverse">
                            <label class="me-auto form-check-label" for="status">Status</label>
                            <input id="status" class="form-check-input" type="checkbox" name="status"
                                value="1" checked>
                        </div>
                        <div class="modal-footer border-0 pb-0">
                            <button type="button" id="submit-create-button" class="btn btn-outline-info btn-sm">Buat
                                Garansi</button>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Garansi </h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGaransiForm" method="post">
                        @method('put')
                        @csrf
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama</label>
                            <input id="edit_nama" name="nama" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_slug" class="form-label">Slug</label>
                            <input id="edit_slug" name="slug" type="text" class="form-control" required
                                readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="edit_durasi" class="form-label">Durasi</label>
                                    <input type="number" class="form-control" id="edit_durasi" name="durasi"
                                        required min="1">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="edit_period" class="form-label">Periode</label>
                                    <select class="form-select" id="edit_period" name="period" required>
                                        <option value="Day">Hari</option>
                                        <option value="Week">Minggu</option>
                                        <option value="Month">Bulan</option>
                                        <option value="Year">Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <div id="quill-editor-edit" style="min-height: 100px;"></div>
                            <div class="text-end text-muted small" id="counter-edit">0/60</div>
                            <input type="hidden" name="deskripsi" id="deskripsi-edit">
                        </div>
                        <div class="justify-content-end form-check form-switch form-check-reverse mt-3">
                            <label class="me-auto form-check-label" for="edit_status">Status</label>
                            <input id="edit_status" class="form-check-input" type="checkbox" name="status"
                                value="1">
                        </div>
                        <div class="modal-footer border-0 pb-0 mt-2">
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
                    <p class="mb-0">Apakah Anda yakin ingin menghapus garansi ini?</p>
                    <h6 class="mt-2" id="garansiNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteGaransiForm" method="POST" action="#">
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
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // QUILL
            const maxLength = 60;
            let quillCreate, quillEdit;

            // Fungsi untuk menangani perubahan teks dan counter
            function handleTextChange(quill, counterElement, hiddenInputElement) {
                const length = quill.getText().length - 1; // -1 untuk mengabaikan newline di akhir

                counterElement.textContent = `${length}/${maxLength}`;

                if (length > maxLength) {
                    quill.deleteText(maxLength, length);
                    counterElement.classList.add('text-danger');
                } else {
                    counterElement.classList.remove('text-danger');
                }

                hiddenInputElement.value = quill.root.innerHTML;
            }

            // Inisialisasi Quill untuk modal CREATE
            const counterCreate = document.getElementById('counter-create');
            const hiddenInputCreate = document.getElementById('deskripsi-create');
            const createModalEl = document.getElementById('import');
            const createForm = document.getElementById('createGaransiForm');
            const createNamaInput = document.getElementById('nama');
            const createSlugInput = document.getElementById('slug');
            const submitCreateBtn = document.getElementById('submit-create-button');

            quillCreate = new Quill('#quill-editor-create', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi di sini...',
            });
            quillCreate.on('text-change', () => handleTextChange(quillCreate, counterCreate, hiddenInputCreate));
            if (hiddenInputCreate.value) {
                quillCreate.root.innerHTML = hiddenInputCreate.value;
            }

            // Inisialisasi Quill untuk modal EDIT
            const counterEdit = document.getElementById('counter-edit');
            const hiddenInputEdit = document.getElementById('deskripsi-edit');
            quillEdit = new Quill('#quill-editor-edit', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi di sini...',
            });
            quillEdit.on('text-change', () => handleTextChange(quillEdit, counterEdit, hiddenInputEdit));

            // --- MODAL CREATE ---
            if (createModalEl) {
                // Slug otomatis untuk modal create
                createNamaInput.addEventListener('change', function() {
                    fetch(`/dashboard/garansi/chekSlug?nama=${this.value}`) // Pastikan route ini ada
                        .then(response => response.json())
                        .then(data => createSlugInput.value = data.slug);
                });

                // Tampilkan modal jika ada error validasi dari server (saat reload)
                const hasError = document.querySelector('.is-invalid');
                if (hasError) {
                    new bootstrap.Modal(createModalEl).show();
                }

                // Submit form create via AJAX
                submitCreateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const formData = new FormData(createForm);

                    // Reset error states
                    createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                        'is-invalid'));
                    createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                    fetch('{{ route('garansi.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const tableBody = document.getElementById('isiTable');
                                const newRowHtml = createTableRow(data.data);
                                tableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                                document.getElementById('garansi-row-empty')?.remove();
                                document.getElementById('garansi-row-empty')?.remove();
                                // Re-initialize event listeners for the new row's buttons
                                initializeModalEventListeners();

                                createForm.reset();
                                quillCreate.setText('');
                                bootstrap.Modal.getInstance(createModalEl).hide();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else if (data.errors) {
                                // Handle validation errors
                                Object.keys(data.errors).forEach(key => {
                                    const input = createForm.querySelector(`[name="${key}"]`);
                                    const errorDiv = input.nextElementSibling;
                                    if (input) input.classList.add('is-invalid');
                                    if (errorDiv && errorDiv.classList.contains(
                                            'invalid-feedback')) {
                                        errorDiv.textContent = data.errors[key][0];
                                    } else if (key === 'deskripsi') {
                                        // Khusus untuk Quill
                                        const quillErrorDiv = document.querySelector(
                                            '#deskripsi-create + .invalid-feedback');
                                        if (quillErrorDiv) quillErrorDiv.textContent = data
                                            .errors[key][0];
                                    }
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Terjadi kesalahan.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
                        });
                });
            }


            // --- INITIALIZE MODAL EVENT LISTENERS ---
            function initializeModalEventListeners() {
                // --- MODAL DELETE ---
                const deleteModal = document.getElementById('deleteConfirmationModal');
                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        if (!button) return; // Guard clause
                        const garansiSlug = button.getAttribute('data-garansi-slug');
                        const garansiName = button.getAttribute('data-garansi-name');
                        const modalBodyName = deleteModal.querySelector('#garansiNameToDelete');
                        const deleteForm = deleteModal.querySelector('#deleteGaransiForm');

                        modalBodyName.textContent = garansiName;
                        deleteForm.action = `/garansi/${garansiSlug}`;
                    });
                }

                // --- MODAL EDIT ---
                const editModal = document.getElementById('editModal');
                if (editModal) {
                    const editForm = editModal.querySelector('#editGaransiForm');
                    const inputNama = editModal.querySelector('#edit_nama');
                    const inputSlug = editModal.querySelector('#edit_slug');
                    const inputDurasi = editModal.querySelector('#edit_durasi');
                    const selectPeriod = editModal.querySelector('#edit_period');
                    const inputStatus = editModal.querySelector('#edit_status');

                    editModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        if (!button) return; // Guard clause
                        const dataUrl = button.getAttribute('data-url');
                        const updateUrl = button.getAttribute('data-update-url');
                        editForm.action = updateUrl;

                        fetch(dataUrl)
                            .then(response => response.json())
                            .then(data => {
                                inputNama.value = data.nama;
                                inputSlug.value = data.slug;
                                inputStatus.checked = data.status == 1;

                                const totalDays = data.durasi; // Ini adalah total hari (misal: 400)

                                if (totalDays && totalDays > 0) {
                                    if (totalDays % 360 === 0) {
                                        selectPeriod.value = 'Year';
                                        inputDurasi.value = totalDays / 360;
                                    } else if (totalDays % 30 === 0) {
                                        selectPeriod.value = 'Month';
                                        inputDurasi.value = totalDays / 30;
                                    } else if (totalDays % 7 === 0) {
                                        selectPeriod.value = 'Week';
                                        inputDurasi.value = totalDays / 7;
                                    } else {
                                        // Jika tidak habis dibagi, tampilkan sebagai hari
                                        selectPeriod.value = 'Day';
                                        inputDurasi.value = totalDays;
                                    }
                                } else {
                                    // Fallback jika durasi 0 atau null
                                    inputDurasi.value = '';
                                    selectPeriod.value = 'Day'; // Default
                                }

                                quillEdit.root.innerHTML = data.deskripsi || '';
                                handleTextChange(quillEdit, counterEdit, hiddenInputEdit);
                            })
                            .catch(error => console.error('Error fetching garansi data:', error));
                    });

                    inputNama.addEventListener('change', function() {
                        fetch(`/dashboard/garansi/chekSlug?nama=${this.value}`)
                            .then(response => response.json())
                            .then(data => inputSlug.value = data.slug);
                    });
                }
            }

            // Panggil fungsi inisialisasi saat halaman pertama kali dimuat
            initializeModalEventListeners();

            // Fungsi untuk membuat baris tabel baru dari data
            function createTableRow(garansi) {
                const statusBadge = garansi.status ? '<span class="badge badge-success">Aktif</span>' :
                    '<span class="badge badge-secondary">Tidak Aktif</span>';
                const editUrl = `{{ url('dashboard/garansi/getjson') }}/${garansi.slug}`;
                const updateUrl = `{{ url('garansi') }}/${garansi.slug}`;
                const deleteUrl = `{{ url('garansi') }}/${garansi.slug}`;

                // Fungsi untuk membersihkan dan membatasi teks deskripsi
                const stripAndLimit = (html, limit) => {
                    const text = new DOMParser().parseFromString(html, 'text/html').body.textContent || "";
                    return text.length > limit ? text.substring(0, limit) + '...' : text;
                };

                return `
                        <tr>
                            <td><p title="garansi" class="ms-3 text-xs text-dark fw-bold mb-0">${garansi.nama}</p></td>
                            <td><p title="Deskripsi" class="text-xs text-dark fw-bold mb-0">${stripAndLimit(garansi.deskripsi, 60)}</p></td>
                            <td class="align-middle"><span class="text-dark text-xs fw-bold">${garansi.formatted_duration}</span></td>
                            <td class="align-middle text-center text-sm">${statusBadge}</td>
                            <td class="align-middle">
                                <a href="#" class="text-dark fw-bold px-3 text-xs"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-url="${editUrl}"
                                    data-update-url="${updateUrl}"
                                    title="Edit garansi">
                                    <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                                </a>
                                <a href="#" class="text-dark delete-user-btn"
                                    data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                    data-garansi-slug="${garansi.slug}"
                                    data-garansi-name="${garansi.nama}"
                                    title="Hapus garansi">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                    `;
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
                    let url = '{{ route('garansi.index') }}';

                    $('#garansi-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            status: status,
                            page: page
                        },
                        success: function(data) {
                            $('#garansi-table-container').html(data).css('opacity', 1);
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&status=' + status
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&status=' + status);
                        },
                        error: function() {
                            $('#garansi-table-container').css('opacity', 1);
                            alert('Gagal memuat data. Silakan coba lagi.');
                        }
                    });
                }

                $('#searchInput').on('keyup', debounce(function() {
                    fetchData(1);
                }, 500));
                $('#statusFilter').on('change', function() {
                    fetchData(1);
                });
                $(document).on('click', '#garansi-table-container .pagination a', function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    if (page) {
                        fetchData(page);
                    }
                });
            });
        });
    </script>
@endsection
@endsection
