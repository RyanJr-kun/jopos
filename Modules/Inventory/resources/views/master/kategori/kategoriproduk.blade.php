@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori Produk')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row g-3">

        {{-- Tab Navigasi Jenis Kategori --}}
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="nav-align-top">
                        <ul class="nav nav-pills flex-md-column flex-row flex-nowrap overflow-x-auto gap-2 pb-2 pb-md-0"
                            style="scrollbar-width: none;">
                            <li class="nav-item text-nowrap">
                                <a class="nav-link category-tab {{ request('type') === 'utama' ? 'active' : '' }}"
                                    href="#" data-type="utama">
                                    <i class="icon-base bx bx-badge-2 icon-sm me-1_5"></i>
                                    Kategori Utama
                                </a>
                            </li>
                            <li class="nav-item text-nowrap">
                                <a class="nav-link category-tab {{ request('type') === 'sub' ? 'active' : '' }}"
                                    href="#" data-type="sub">
                                    <i class="icon-base bx bx-category icon-sm me-1_5"></i>
                                    Sub Kategori
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Tombol Tambah --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-sm-6 col-lg-5">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari kategori..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <select name="status" id="statusFilter" class="form-select select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-auto ms-lg-auto">
                            <button class="btn btn-outline-info w-100 mb-0 d-flex justify-content-center align-items-center"
                                data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bx bx-plus icon-md me-2"></i>Kategori
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Kategori --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header p-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <h5 class="mb-0 fw-bold">List Kategori</h5>
                            <small class="text-muted mb-0">Kelola data Kategorimu</small>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive" id="kategori-table-container">
                        @include('inventory::master.kategori._category_table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL CREATE                                                  --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title">Buat Kategori Baru</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createKategoriForm" enctype="multipart/form-data">
                        @csrf

                        {{-- Toggle: Jenis Kategori --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark d-block mb-2">Jenis Kategori</label>
                            <div class="btn-group w-100" role="group" id="create-type-toggle">
                                <input type="radio" class="btn-check" name="category_type_create" id="create_type_utama"
                                    value="utama" checked>
                                <label class="btn btn-outline-info" for="create_type_utama">
                                    <i class="bx bx-badge-2 me-1"></i> Kategori Utama
                                </label>

                                <input type="radio" class="btn-check" name="category_type_create" id="create_type_sub"
                                    value="sub">
                                <label class="btn btn-outline-warning" for="create_type_sub">
                                    <i class="bx bx-category me-1"></i> Sub Kategori
                                </label>
                            </div>
                        </div>

                        {{-- Dropdown Parent — hanya muncul jika Sub Kategori --}}
                        <div class="mb-3 d-none" id="create-parent-wrapper">
                            <label for="create_parent_id" class="form-label">
                                Parent Kategori <span class="text-danger">*</span>
                            </label>
                            <select id="create_parent_id" name="parent_id" class="form-select select2-create"
                                data-placeholder="Pilih kategori induk...">
                                <option value=""></option>
                                @foreach ($parentKategoris as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="parent_id-error"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <p class="text-dark fw-bold">Gambar Kategori:</p>
                                <input type="file" class="filepond" name="img_kategori" id="img_kategori_create">
                            </div>
                            <div class="col-md-6">
                                <div class="mt-md-0">
                                    <label for="create_name" class="form-label">Nama Kategori</label>
                                    <input id="create_name" name="name" type="text" class="form-control" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="create_slug" class="form-label">Slug</label>
                                    <input id="create_slug" name="slug" type="text" class="form-control" required>
                                    <div class="invalid-feedback" id="slug-error"></div>
                                </div>
                                <div class="justify-content-end form-check form-switch form-check-reverse">
                                    <label class="me-auto form-check-label" for="create_status">Status</label>
                                    <input id="create_status" class="form-check-input" type="checkbox" name="status"
                                        value="1" checked>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pb-0 mt-3">
                            <button type="button" id="submit-create-button" class="btn btn-outline-info btn-sm">
                                Buat Kategori
                            </button>
                            <button type="button" id="cancel-create-button" class="btn btn-danger btn-sm">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL EDIT                                                    --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="editModalLabel">Edit Kategori Product</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editKategoriForm" method="post" enctype="multipart/form-data">
                        @method('put')
                        @csrf

                        {{-- Toggle: Jenis Kategori --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark d-block mb-2">Jenis Kategori</label>
                            <div class="btn-group w-100" role="group" id="edit-type-toggle">
                                <input type="radio" class="btn-check" name="category_type_edit" id="edit_type_utama"
                                    value="utama">
                                <label class="btn btn-outline-info" for="edit_type_utama">
                                    <i class="bx bx-badge-2 me-1"></i> Kategori Utama
                                </label>

                                <input type="radio" class="btn-check" name="category_type_edit" id="edit_type_sub"
                                    value="sub">
                                <label class="btn btn-outline-warning" for="edit_type_sub">
                                    <i class="bx bx-category me-1"></i> Sub Kategori
                                </label>
                            </div>
                        </div>

                        {{-- Dropdown Parent — hanya muncul jika Sub Kategori --}}
                        <div class="mb-3 d-none" id="edit-parent-wrapper">
                            <label for="edit_parent_id" class="form-label">
                                Parent Kategori <span class="text-danger">*</span>
                            </label>
                            <select id="edit_parent_id" name="parent_id" class="form-select select2-edit"
                                data-placeholder="Pilih kategori induk...">
                                <option value=""></option>
                                @foreach ($parentKategoris as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit-parent_id-error"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-dark fw-bold">Gambar Kategori:</p>
                                <input type="file" class="filepond" name="img_kategori" id="img_kategori_edit">
                            </div>
                            <div class="col-md-6">
                                <div class="mt-0">
                                    <label for="edit_name" class="form-label">Nama</label>
                                    <input id="edit_name" name="name" type="text" class="form-control" required>
                                    <div class="invalid-feedback" id="edit-name-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_slug" class="form-label">Slug</label>
                                    <input id="edit_slug" name="slug" type="text" class="form-control" required>
                                    <div class="invalid-feedback" id="edit-slug-error"></div>
                                </div>
                                <div class="justify-content-end form-check form-switch form-check-reverse mt-2">
                                    <label class="me-auto form-check-label" for="edit_status">Status</label>
                                    <input id="edit_status" class="form-check-input" type="checkbox" name="status"
                                        value="1">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pb-0 mt-3">
                            <button type="submit" class="btn btn-outline-info btn-sm" id="submit-edit-button">
                                Simpan Perubahan
                            </button>
                            <button type="button" id="cancel-edit-button" class="btn btn-danger btn-sm"
                                data-bs-dismiss="modal">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL DELETE                                                  --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
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
@endsection

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script>
        // Inisialisasi semua popover Bootstrap (termasuk tombol "+N lainnya")
        document.addEventListener('DOMContentLoaded', function() {
            const popoverEls = document.querySelectorAll('[data-bs-toggle="popover"]');
            popoverEls.forEach(function(el) {
                new bootstrap.Popover(el, {
                    html: false, // konten plain text, aman dari XSS
                    sanitize: true,
                });
            });
        });
    </script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
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

    <script>
        // ================================================================
        // HELPER: Buat baris tabel dari data objek kategori
        // ================================================================
        function createTableRow(kategori) {
            let typeColumn = '';
            const currentType = document.querySelector('.category-tab.active').getAttribute('data-type');

            if (currentType === 'utama') {
                // Tampilkan placeholder tag untuk data baru
                typeColumn = '<span class="text-muted text-xs">Baru dibuat</span>';
            } else {
                typeColumn = (kategori.parent && kategori.parent.name) ?
                    `<span class="badge bg-label-primary">${kategori.parent.name}</span>` :
                    '<span class="text-muted text-xs">—</span>';
            }

            return `
            <tr id="kategori-row-${kategori.slug}">
                <td>${typeColumn}</td>
                </tr>
        `;
            const statusBadge = kategori.status ?
                '<span class="badge bg-label-success">Aktif</span>' :
                '<span class="badge bg-label-secondary">Tidak Aktif</span>';

            const imageUrl = kategori.img_kategori ?
                `{{ asset('storage') }}/${kategori.img_kategori}` :
                `{{ asset('assets/img/produk.png') }}`;

            const editUrl = `{{ url('kategoriproduk') }}/${kategori.slug}/json`;
            const updateUrl = `{{ url('kategoriproduk') }}/${kategori.slug}`;

            const typeLabel = kategori.parent_id ?
                '<small class="text-muted text-xs">Sub Kategori</small>' :
                '<small class="text-info text-xs">Kategori Utama</small>';

            const parentLabel = (kategori.parent && kategori.parent.name) ?
                `<span class="badge bg-label-primary">${kategori.parent.name}</span>` :
                '<span class="text-muted text-xs">—</span>';

            return `
            <tr id="kategori-row-${kategori.slug}">
                <td>—</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${imageUrl}" class="avatar avatar-sm me-3" alt="${kategori.name}">
                        <div>
                            <h6 class="mb-0 text-sm">${kategori.name}</h6>
                            ${typeLabel}
                        </div>
                    </div>
                </td>
                <td><p class="text-xs text-dark fw-bold mb-0">${kategori.slug}</p></td>
                <td>${parentLabel}</td>
                <td class="text-center">${kategori.products_count ?? 0}</td>
                <td class="align-middle text-center text-sm">${statusBadge}</td>
                <td class="align-middle">
                    <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-url="${editUrl}"
                        data-update-url="${updateUrl}"
                        title="Edit kategori">
                        <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                    </a>
                    <a href="#" class="text-dark delete-btn" data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmationModal"
                        data-kategori-slug="${kategori.slug}"
                        data-kategori-name="${kategori.name}"
                        title="Hapus kategori">
                        <i class="bx bx-trash"></i>
                    </a>
                </td>
            </tr>
        `;
        }

        // ================================================================
        // INIT SELECT2 (khusus per modal agar dropdownParent benar)
        // ================================================================
        function initSelect2InModal(modalEl, selector) {
            if (typeof $ === 'undefined' || !$.fn.select2) {
                setTimeout(() => initSelect2InModal(modalEl, selector), 100);
                return;
            }
            $(modalEl).find(selector).each(function() {
                const $this = $(this);
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                $this.select2({
                    placeholder: $this.data('placeholder') || 'Pilih...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(modalEl),
                });
            });
        }

        // ================================================================
        // HELPER: Toggle tampilkan/sembunyikan dropdown parent
        // ================================================================
        function setupTypeToggle({
            radioUtama,
            radioSub,
            parentWrapper,
            selectParent,
            modalEl
        }) {
            function onToggleChange() {
                const isSub = radioSub.checked;
                parentWrapper.classList.toggle('d-none', !isSub);

                if (!isSub) {
                    // Reset dan nonaktifkan select parent saat Kategori Utama dipilih
                    $(selectParent).val('').trigger('change');
                    selectParent.removeAttribute('required');
                } else {
                    selectParent.setAttribute('required', 'required');
                    // Pastikan Select2 sudah terinisialisasi saat wrapper muncul
                    initSelect2InModal(modalEl, '.' + selectParent.className.split(' ').find(c => c.startsWith(
                        'select2-')));
                }
            }

            radioUtama.addEventListener('change', onToggleChange);
            radioSub.addEventListener('change', onToggleChange);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';

            // ================================================================
            // FILEPOND SETUP
            // ================================================================
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType,
                FilePondPluginImageTransform
            );

            const filepCondCreateEl = document.querySelector('#img_kategori_create');
            const createPond = FilePond.create(filepCondCreateEl, {
                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                imageCropAspectRatio: '1:1',
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                labelFileTypeNotAllowed: 'Jenis file tidak valid. Hanya PNG, JPG, WEBP, dan SVG.',
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                server: {
                    process: {
                        url: '{{ route('kategoriproduk.upload') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                    revert: {
                        url: '{{ route('kategoriproduk.revert') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                },
            });

            // ================================================================
            // MODAL CREATE
            // ================================================================
            const createModalEl = document.getElementById('createModal');
            const createForm = document.getElementById('createKategoriForm');
            const submitCreateBtn = document.getElementById('submit-create-button');
            const createNameInput = document.getElementById('create_name');
            const createSlugInput = document.getElementById('create_slug');

            // Referensi elemen toggle create
            const createRadioUtama = document.getElementById('create_type_utama');
            const createRadioSub = document.getElementById('create_type_sub');
            const createParentWrapper = document.getElementById('create-parent-wrapper');
            const createParentSelect = document.getElementById('create_parent_id');

            // Pasang toggle saat modal pertama kali dibuka
            createModalEl.addEventListener('shown.bs.modal', function() {
                initSelect2InModal(createModalEl, '.select2-create');
                setupTypeToggle({
                    radioUtama: createRadioUtama,
                    radioSub: createRadioSub,
                    parentWrapper: createParentWrapper,
                    selectParent: createParentSelect,
                    modalEl: createModalEl,
                });
            });

            // Reset toggle ke "Kategori Utama" setiap kali modal dibuka ulang
            createModalEl.addEventListener('show.bs.modal', function() {
                createRadioUtama.checked = true;
                createRadioSub.checked = false;
                createParentWrapper.classList.add('d-none');
                createParentSelect.removeAttribute('required');
            });

            // Slug otomatis dari nama
            createNameInput.addEventListener('change', function() {
                fetch(`/dashboard/kategoriproduk/chekSlug?name=${encodeURIComponent(this.value)}`)
                    .then(r => r.json())
                    .then(data => createSlugInput.value = data.slug);
            });

            submitCreateBtn.addEventListener('click', function(e) {
                // Validasi manual jika diperlukan
                if (createRadioSub.checked && !createParentSelect.value) {
                    createParentSelect.classList.add('is-invalid');
                    return;
                }

                // Langsung submit form secara native (ini akan memicu refresh page)
                createForm.method = 'POST';
                createForm.action = "{{ route('kategoriproduk.store') }}";
                createForm.submit();
            });

            // Tombol Batalkan pada modal create
            document.getElementById('cancel-create-button').addEventListener('click', function() {
                createForm.reset();
                // Reset toggle ke Kategori Utama
                createRadioUtama.checked = true;
                createRadioSub.checked = false;
                createParentWrapper.classList.add('d-none');
                createParentSelect.removeAttribute('required');
                $(createParentSelect).val('').trigger('change');

                createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                createPond.removeFiles().then(() => {
                    bootstrap.Modal.getInstance(createModalEl).hide();
                });
            });

            // ================================================================
            // MODAL EDIT
            // ================================================================
            const editModalEl = document.getElementById('editModal');
            const editForm = editModalEl.querySelector('#editKategoriForm');
            const inputNama = editModalEl.querySelector('#edit_name');
            const inputSlug = editModalEl.querySelector('#edit_slug');
            const inputStatus = editModalEl.querySelector('#edit_status');

            // Referensi elemen toggle edit
            const editRadioUtama = document.getElementById('edit_type_utama');
            const editRadioSub = document.getElementById('edit_type_sub');
            const editParentWrapper = document.getElementById('edit-parent-wrapper');
            const editParentSelect = document.getElementById('edit_parent_id');
            let editPond = null;

            // Pasang toggle saat modal edit pertama kali selesai tampil
            editModalEl.addEventListener('shown.bs.modal', function() {
                initSelect2InModal(editModalEl, '.select2-edit');
                setupTypeToggle({
                    radioUtama: editRadioUtama,
                    radioSub: editRadioSub,
                    parentWrapper: editParentWrapper,
                    selectParent: editParentSelect,
                    modalEl: editModalEl,
                });
            });

            // Isi form ketika modal edit dibuka
            editModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const dataUrl = button.getAttribute('data-url');
                const updateUrl = button.getAttribute('data-update-url');
                editForm.action = updateUrl;

                fetch(dataUrl)
                    .then(r => r.json())
                    .then(data => {
                        inputNama.value = data.name;
                        inputSlug.value = data.slug;
                        inputStatus.checked = data.status == 1;

                        // Set toggle berdasarkan data parent_id
                        const isSub = !!data.parent_id;
                        editRadioUtama.checked = !isSub;
                        editRadioSub.checked = isSub;
                        editParentWrapper.classList.toggle('d-none', !isSub);

                        if (isSub) {
                            editParentSelect.setAttribute('required', 'required');
                            // Set nilai parent di select2 (gunakan timeout agar select2 sudah init)
                            setTimeout(() => {
                                $(editParentSelect).val(data.parent_id).trigger('change');
                            }, 100);
                        } else {
                            editParentSelect.removeAttribute('required');
                            $(editParentSelect).val('').trigger('change');
                        }

                        // FilePond
                        const pondFiles = [];
                        if (data.img_kategori) {
                            pondFiles.push(`/storage/${data.img_kategori}`);
                        }

                        const submitEditBtn = editForm.querySelector('#submit-edit-button');

                        editPond = FilePond.create(document.querySelector('#img_kategori_edit'), {
                            labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                            files: pondFiles,
                            allowImagePreview: true,
                            allowFileSizeValidation: true,
                            maxFileSize: '2MB',
                            allowImageCrop: true,
                            imageCropAspectRatio: '1:1',
                            acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp',
                                'image/svg+xml'
                            ],
                            labelFileTypeNotAllowed: 'Jenis file tidak valid.',
                            labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                            labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                            server: {
                                process: {
                                    url: '{{ route('kategoriproduk.upload') }}',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                },
                                revert: {
                                    url: '{{ route('kategoriproduk.revert') }}',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                },
                            },
                        });

                        const pondEditInput = document.querySelector('#img_kategori_edit');
                        pondEditInput.addEventListener('FilePond:addfile', () => {
                            submitEditBtn.disabled = true;
                            submitEditBtn.innerHTML =
                                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...`;
                        });
                        pondEditInput.addEventListener('FilePond:processfile', () => {
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML = 'Simpan Perubahan';
                        });
                        pondEditInput.addEventListener('FilePond:removefile', () => {
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML = 'Simpan Perubahan';
                        });
                    })
                    .catch(err => console.error('Error fetching kategori data:', err));
            });

            // Bersihkan FilePond & reset toggle ketika modal edit ditutup
            editModalEl.addEventListener('hidden.bs.modal', function() {
                if (editPond) {
                    editPond.destroy();
                    editPond = null;
                }
                // Reset toggle ke Kategori Utama
                editRadioUtama.checked = true;
                editRadioSub.checked = false;
                editParentWrapper.classList.add('d-none');
                editParentSelect.removeAttribute('required');
                $(editParentSelect).val('').trigger('change');

                editForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                editForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            });

            // Slug otomatis pada modal edit
            inputNama.addEventListener('change', function() {
                fetch(`/dashboard/kategoriproduk/chekSlug?name=${encodeURIComponent(this.value)}`)
                    .then(r => r.json())
                    .then(data => inputSlug.value = data.slug);
            });

            editForm.addEventListener('submit', function(e) {
                // Hapus e.preventDefault() agar form benar-benar terkirim
                if (editRadioSub.checked && !editParentSelect.value) {
                    e.preventDefault();
                    editParentSelect.classList.add('is-invalid');
                    return;
                }

                // Jangan gunakan fetch() di sini, biarkan browser yang bekerja
            });

            // Tombol Batalkan pada modal edit
            document.getElementById('cancel-edit-button').addEventListener('click', function() {
                if (editPond) {
                    const newFile = editPond.getFiles().find(file =>
                        file.origin === FilePond.FileOrigin.INPUT &&
                        file.status === FilePond.FileStatus.PROCESSING_COMPLETE
                    );
                    if (newFile && newFile.serverId) {
                        fetch('{{ route('kategoriproduk.revert') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: newFile.serverId,
                        }).finally(() => bootstrap.Modal.getInstance(editModalEl).hide());
                        return;
                    }
                }
                bootstrap.Modal.getInstance(editModalEl).hide();
            });

            // ================================================================
            // MODAL DELETE
            // ================================================================
            const deleteModalEl = document.getElementById('deleteConfirmationModal');
            const deleteForm = deleteModalEl.querySelector('#deleteKategoriForm');
            const modalBodyName = deleteModalEl.querySelector('#kategoriNameToDelete');
            let kategoriSlugToDelete = null;

            deleteModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                kategoriSlugToDelete = button.getAttribute('data-kategori-slug');
                modalBodyName.textContent = button.getAttribute('data-kategori-name');
            });

            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!kategoriSlugToDelete) return;

                fetch(`/kategoriproduk/${kategoriSlugToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json().then(data => ({
                        ok: response.ok,
                        data
                    })))
                    .then(({
                        ok,
                        data
                    }) => {
                        bootstrap.Modal.getInstance(deleteModalEl).hide();
                        if (ok && data.success) {
                            // Hapus baris dari tabel
                            document.getElementById(`kategori-row-${kategoriSlugToDelete}`)?.remove();
                            window.showToast('success', data.message);
                        } else {
                            window.showToast('error', data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(() => {
                        bootstrap.Modal.getInstance(deleteModalEl).hide();
                        window.showToast('error', 'Tidak dapat terhubung ke server.');
                    });
            });

            // ================================================================
            // AJAX: Tab filter (Semua / Utama / Sub)
            // ================================================================
            let currentType = new URLSearchParams(window.location.search).get('type') || 'utama';

            document.addEventListener('DOMContentLoaded', function() {
                // Memastikan tab yang benar memiliki class active saat load pertama kali
                const tabs = document.querySelectorAll('.category-tab');

                tabs.forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Hapus class active dari semua tab
                        tabs.forEach(t => t.classList.remove('active'));

                        // Tambah active ke tab yang diklik
                        this.classList.add('active');

                        // Update currentType dan ambil data
                        currentType = this.getAttribute('data-type');
                        fetchData(1);
                    });
                });
            });

            // ================================================================
            // AJAX: Filter & Pencarian
            // ================================================================
            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            function fetchData(page = 1) {
                const search = document.getElementById('searchInput').value;
                const status = document.getElementById('statusFilter').value;
                const url = '{{ route('kategoriproduk.index') }}';

                document.getElementById('kategori-table-container').style.opacity = '0.5';

                $.ajax({
                    url: url,
                    data: {
                        search,
                        status,
                        type: currentType,
                        page
                    },
                    success: function(data) {
                        document.getElementById('kategori-table-container').innerHTML = data;
                        document.getElementById('kategori-table-container').style.opacity = '1';
                        window.history.pushState({}, '',
                            `${url}?page=${page}&search=${search}&status=${status}&type=${currentType}`
                        );
                    },
                    error: function() {
                        document.getElementById('kategori-table-container').style.opacity = '1';
                        window.showToast('error', 'Gagal memuat data. Silakan coba lagi.');
                    },
                });
            }

            document.getElementById('searchInput').addEventListener('keyup', debounce(() => fetchData(1), 500));
            document.getElementById('statusFilter').addEventListener('change', () => fetchData(1));

            $(document).on('click', '.category-tab', function(e) {
                e.preventDefault();

                // 1. Ambil tipe dari data-type tab yang diklik
                currentType = $(this).data('type');

                // 2. Update tampilan UI Tab (Aktif/Non-aktif)
                $('.category-tab').removeClass('active');
                $(this).addClass('active');

                // 3. Ambil data (fetch) berdasarkan tipe baru
                fetchData(1);
            });

            function fetchData(page) {
                const search = document.getElementById('searchInput').value;
                const status = document.getElementById('statusFilter').value;
                const url = "{{ route('kategoriproduk.index') }}";

                // Efek loading
                document.getElementById('kategori-table-container').style.opacity = '0.5';

                $.ajax({
                    url: url,
                    data: {
                        search: search,
                        status: status,
                        type: currentType, // Mengirim 'utama' atau 'sub'
                        page: page
                    },
                    success: function(data) {
                        document.getElementById('kategori-table-container').innerHTML = data;
                        document.getElementById('kategori-table-container').style.opacity = '1';

                        // PENTING: Update URL agar parameter ?type=... sesuai dengan tab yang aktif
                        const newUrl =
                            `${url}?page=${page}&search=${search}&status=${status}&type=${currentType}`;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);
                    },
                    error: function() {
                        document.getElementById('kategori-table-container').style.opacity = '1';
                        window.showToast('error', 'Gagal memuat data.');
                    }
                });
            }
        });
    </script>
@endsection
