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
            ['name' => 'Administrasi', 'url' => route('keuangan')],
            ['name' => 'Manajemen Kategori', 'url' => route('kategoritransaksi.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3 ">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Kategori Transaksi</h6>
                    <p class="text-sm mb-0">Kelola data Kategorimu</p>
                </div>
                <div class="ms-md-auto mt-2">
                    {{-- triger-modal-create --}}
                    <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import">
                        <i class="fa fa-plus fixed-plugin-button-nav cursor-pointer pe-2"></i> Kategori
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <form action="{{ route('kategoritransaksi.index') }}" method="GET" class="container-fluid">
                <div class="row g-2  px-2">
                    <div class="col-md-4">
                        <input type="text" id="searchInput" name="search" class="form-control"
                            placeholder="Cari nama kategori..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select id="jenisFilter" name="jenis" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="pemasukan" @selected(request('jenis') == 'pemasukan')>Pemasukan</option>
                            <option value="pengeluaran" @selected(request('jenis') == 'pengeluaran')>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" @selected(request('status') == '1')>Aktif</option>
                            <option value="0" @selected(request('status') == '0')>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </form>
            <div id="kategori-table-container">
                @include('dashboard.keuangan._kategori_table', ['kategoris' => $kategoris])
            </div>
        </div>
    </div>
    {{-- modal-create --}}
    <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="ModalLabel">Buat kategori Baru</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('kategoritransaksi.store') }}" method="post">
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

                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis Kategori</label>
                            <select class="form-select @error('jenis') is-invalid @enderror" id="jenis"
                                name="jenis" required>
                                <option value="" disabled selected>Pilih Jenis...</option>
                                <option value="pemasukan" @selected(old('jenis') == 'pemasukan')>Pemasukan</option>
                                <option value="pengeluaran" @selected(old('jenis') == 'pengeluaran')>Pengeluaran</option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span
                                    class="text-danger">*</span></label>
                            <div id="quill-editor-create" style="min-height: 100px;">{!! old('deskripsi') !!}</div>
                            <div class="text-end text-muted small" id="counter-create">0/60</div>
                            <input type="hidden" name="deskripsi" id="deskripsi-create" value="{{ old('deskripsi') }}">
                            @error('deskripsi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="justify-content-end form-check form-switch form-check-reverse mb-2">
                            <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                            <input id="status" class="form-check-input" type="checkbox" name="status"
                                value="1" checked>
                        </div>

                        <div class="modal-footer border-0 pb-0">
                            <button type="submit" class="btn btn-outline-info btn-sm">Buat Kategori</button>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Kategori Transaksi</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editKategoriForm" method="post">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="form-group">
                                <div class="mb-3">
                                    <label for="edit_nama" class="form-label">Nama</label>
                                    <input id="edit_nama" name="nama" type="text" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_slug" class="form-label">Slug</label>
                                    <input id="edit_slug" name="slug" type="text" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_jenis" class="form-label">Jenis Kategori</label>
                                    <select class="form-select" id="edit_jenis" name="jenis" required>
                                        <option value="" disabled>Pilih Jenis...</option>
                                        <option value="pemasukan">Pemasukan</option>
                                        <option value="pengeluaran">Pengeluaran</option>
                                    </select>
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
                            </div>
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
                    <p class="mb-0">Apakah Anda yakin ingin menghapus kategori ini?</p>
                    <h6 class="mt-2" id="kategoriNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteKategoriForm" method="POST" action="#">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- INISIALISASI QUILL DENGAN CHARACTER COUNTER ---
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
            const nama = document.querySelector('#nama ')
            const slug = document.querySelector('#slug')

            nama.addEventListener('change', function() {
                fetch('/dashboard/kategoritransaksi/chekSlug?nama=' + nama.value)
                    .then(response => response.json())
                    .then(data => slug.value = data.slug)
            });

            // --- MODAL EDIT ---
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    const editForm = document.getElementById('editKategoriForm');
                    const inputNama = document.getElementById('edit_nama');
                    const inputSlug = document.getElementById('edit_slug');
                    const inputStatus = document.getElementById('edit_status');
                    const selectJenis = document.getElementById('edit_jenis');

                    editForm.action = updateUrl;

                    fetch(dataUrl)
                        .then(response => response.json())
                        .then(data => {
                            inputNama.value = data.nama;
                            inputSlug.value = data.slug;
                            selectJenis.value = data.jenis;
                            inputStatus.checked = data.status == 1;

                            // FIX: Isi editor Quill dan input hidden secara eksplisit
                            quillEdit.root.innerHTML = data.deskripsi || '';
                            hiddenInputEdit.value = data.deskripsi ||
                                ''; // Baris ini memastikan data tersalin
                            handleTextChange(quillEdit, counterEdit,
                                hiddenInputEdit); // Panggil fungsi untuk update counter
                        })
                        .catch(error => console.error('Error fetching category data:', error));
                });

                const editNama = document.querySelector('#edit_nama');
                const editSlug = document.querySelector('#edit_slug');
                editNama.addEventListener('change', function() {
                    fetch(`/dashboard/kategoritransaksi/chekSlug?nama=${this.value}`)
                        .then(response => response.json())
                        .then(data => editSlug.value = data.slug)
                });
            }

            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    // Ambil 'username' dari atribut data-*
                    const kategoriSlug = button.getAttribute('data-kategori-slug'); // <-- DIUBAH DI SINI
                    const kategoriName = button.getAttribute('data-kategori-name');
                    const modalBodyName = deleteModal.querySelector('#kategoriNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deleteKategoriForm');
                    modalBodyName.textContent = kategoriName;
                    // Atur action form menggunakan slug
                    deleteForm.action = `/kategoritransaksi/${kategoriSlug}`;
                });
            }

            // eror input
            const hasError = document.querySelector('.is-invalid');
            if (hasError) {
                var importModal = new bootstrap.Modal(document.getElementById('import'));
                importModal.show();
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
                    let jenis = $('#jenisFilter').val();
                    let status = $('#statusFilter').val();
                    let url = '{{ route('kategoritransaksi.index') }}';

                    $('#kategori-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            jenis: jenis,
                            status: status,
                            page: page
                        },
                        success: function(data) {
                            $('#kategori-table-container').html(data).css('opacity', 1);
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&jenis=' + jenis + '&status=' + status
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&jenis=' + jenis + '&status=' + status);
                        },
                        error: function() {
                            $('#kategori-table-container').css('opacity', 1);
                            Swal.fire('Gagal', 'Gagal memuat data. Silakan coba lagi.',
                                'error');
                        }
                    });
                }

                $('#searchInput').on('keyup', debounce(function() {
                    fetchData(1);
                }, 500));
                $('#jenisFilter, #statusFilter').on('change', function() {
                    fetchData(1);
                });
                $(document).on('click', '#kategori-table-container .pagination a', function(e) {
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
