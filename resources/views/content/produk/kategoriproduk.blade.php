@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection

<div class="container-fluid p-3 ">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">List Kategori</h6>
                    <p class="text-sm mb-0">
                        Kelola data Kategorimu
                    </p>
                </div>
                <div class="ms-auto mt-2">
                    {{-- triger-modal-create --}}
                    <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import"><i
                            class="bx bx-plus-lg fixed-plugin-button-nav cursor-pointer pe-2"></i>Kategori</button>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="filter-container">
                <div class="row g-3 align-items-center justify-content-between">
                    <div class="col-md-4 ms-3">
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Cari kategori..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 me-3">
                        <select name="status" id="statusFilter" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                            <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="kategori-table-container">
                @include('content.produk._category_table')
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
                    <form id="createKategoriForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <p class="text-dark fw-bold ">Gambar Kategori:</p>
                                <input type="file" class="filepond" name="img_kategori" id="img_kategori_create">
                            </div>

                            <div class="col-md-6">
                                <div class="mt-md-4">
                                    <label for="name" class="form-label">Kategori</label>
                                    <input id="name" name="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input id="slug" name="slug" type="text"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug') }}" required>
                                    <div class="invalid-feedback" id="slug-error"></div>
                                </div>

                                <div class="justify-content-end form-check form-switch form-check-reverse">
                                    <label class="me-auto form-check-label" for="status">Status</label>
                                    <input id="status" class="form-check-input" type="checkbox" name="status"
                                        value="1" checked>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pb-0 mt-3">
                            <button type="button" id="submit-create-button" class="btn btn-outline-info btn-sm">Buat
                                Kategori</button>
                            <button type="button" id="cancel-create-button"
                                class="btn btn-danger btn-sm">Batalkan</button>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Kategori Product</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editKategoriForm" method="post" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-dark fw-bold ">Gambar Kategori:</p>
                                <input type="file" class="filepond" name="img_kategori" id="img_kategori_edit">
                            </div>
                            <div class="col-md-6">
                                <div class="mt-4">
                                    <label for="edit_name" class="form-label">Nama</label>
                                    <input id="edit_name" name="name" type="text" class="form-control"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_slug" class="form-label">Slug</label>
                                    <input id="edit_slug" name="slug" type="text" class="form-control"
                                        required>
                                </div>
                                <div class="justify-content-end form-check form-switch form-check-reverse mt-4">
                                    <label class="me-auto form-check-label" for="edit_status">Status</label>
                                    <input id="edit_status" class="form-check-input" type="checkbox" name="status"
                                        value="1">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pb-0 mt-3">
                            <button type="submit" class="btn btn-outline-info btn-sm" id="submit-edit-button">Simpan
                                Perubahan</button>
                            <button type="button" id="cancel-edit-button" class="btn btn-danger btn-sm"
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        // Fungsi untuk membuat baris tabel baru dari data
        function createTableRow(kategori) {
            const statusBadge = kategori.status ? '<span class="badge bg-label-success">Aktif</span>' :
                '<span class="badge bg-label-secondary">Tidak Aktif</span>';
            const imageUrl = kategori.img_kategori ? `{{ asset('storage') }}/${kategori.img_kategori}` :
                `{{ asset('assets/img/produk.webp') }}`;
            const editUrl = `{{ url('kategoriproduk/getjson') }}/${kategori.slug}`;
            const updateUrl = `{{ url('kategoriproduk') }}/${kategori.slug}`;

            return `
                    <tr id="kategori-row-${kategori.slug}">
                        <td>
                            <div title="image & Nama Kategori" class="d-flex align-items-center px-2 py-1">
                                <img src="${imageUrl}" class="avatar avatar-sm me-3" alt="${kategori.name}">
                                <h6 class="mb-0 text-sm">${kategori.name}</h6>
                            </div>
                        </td>
                        <td><p title="kategori slug" class="text-xs text-dark fw-bold mb-0">${kategori.slug}</p></td>
                        <td><p class="text-xs text-dark fw-bold mb-0">${kategori.products_count}</p></td>
                        <td class="align-middle"><span class="text-dark text-xs fw-bold">${kategori.created_at_formatted}</span></td>
                        <td class="align-middle text-center text-sm">${statusBadge}</td>
                        <td class="align-middle">
                            <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal" data-bs-target="#editModal" data-url="${editUrl}" data-update-url="${updateUrl}" title="Edit kategori">
                                <i class="bx bx-edit text-dark text-sm opacity-10"></i>
                            </a>
                            <a href="#" class="text-dark delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-kategori-slug="${kategori.slug}" data-kategori-name="${kategori.name}" title="Hapus kategori">
                                <i class="bx bx-trash"></i>
                            </a>
                        </td>
                    </tr>
                `;
        }

        document.addEventListener('DOMContentLoaded', function() {
                    // --- FILEPOND SETUP ---
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview,
                        FilePondPluginFileValidateSize,
                        FilePondPluginImageCrop,
                        FilePondPluginFileValidateType,
                        FilePondPluginImageTransform
                    );

                    // Setup FilePond untuk modal create
                    const createPond = FilePond.create(document.querySelector('#img_kategori_create'), {
                        labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                        allowImagePreview: true,
                        allowFileSizeValidation: true,
                        maxFileSize: '2MB',
                        allowImageCrop: true,
                        imageCropAspectRatio: '1:1',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                        labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                        labelFileTypeNotAllowed: 'Jenis file tidak valid. Hanya PNG, JPG, WEBP, dan SVG yang diizinkan.',
                        server: {
                            process: {
                                url: '/dashboard/kategoriproduk/upload', // Disesuaikan untuk kategori
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            },
                            revert: {
                                url: '/dashboard/kategoriproduk/revert', // Disesuaikan untuk kategori
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }
                        }
                    });

                    // --- MODAL CREATE ---
                    const createModal = document.getElementById('import');
                    if (createModal) {
                        const nameInput = createModal.querySelector('#name');
                        const slugInput = createModal.querySelector('#slug');

                        // Tampilkan modal jika ada error validasi dari server
                        const hasError = document.querySelector('.is-invalid');
                        if (hasError) {
                            var createModalInstance = new bootstrap.Modal(createModal);
                            createModalInstance.show();
                        }

                        // Logika submit form create via AJAX
                        const createForm = document.getElementById('createKategoriForm');
                        const submitCreateBtn = document.getElementById('submit-create-button');

                        if (submitCreateBtn) {
                            submitCreateBtn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const formData = new FormData(createForm);

                                    // Reset error states
                                    createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                                        'is-invalid'));
                                    createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                                    fetch('{{ route('kategoriproduk.store') }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: formData
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                return response.json().then(data => {
                                                    throw data;
                                                });
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                                if (data.success) {
                                                    const tableBody = document.getElementById('isiTable');
                                                    const newRowHtml = createTableRow(data.data);
                                                    tableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                                                    document.getElementById('kategori-row-empty')?.remove();

                                                    const modalInstance = bootstrap.Modal.getInstance(createModal);
                                                    modalInstance.hide();

                                                    window.showToast('success', data.message);
                                                })
                                            .catch(errorData => {
                                                    if (errorData.errors) {
                                                        Object.keys(data.errors).forEach(key => {
                                                            const input = createForm.querySelector(
                                                                `[name="${key}"]`);
                                                            const errorDiv = createForm.querySelector(
                                                                `#${key}-error`);
                                                            if (input) input.classList.add('is-invalid');
                                                            if (errorDiv) errorDiv.textContent = errorData
                                                                .errors[
                                                                    key][0];
                                                        });
                                                    } else {
                                                        window.showToast('error', errorData.message ||
                                                            'Terjadi kesalahan server.');
                                                    });
                                            });
                                }

                                // Slug otomatis
                                nameInput.addEventListener('change', function() {
                                    fetch(`/dashboard/kategoriproduk/chekSlug?name=${nameInput.value}`)
                                        .then(response => response.json())
                                        .then(data => slugInput.value = data.slug);
                                });

                                const cancelCreateBtn = createModal.querySelector('#cancel-create-button');
                                if (cancelCreateBtn) {
                                    cancelCreateBtn.addEventListener('click', function(e) {
                                        e.preventDefault();

                                        const createForm = document.getElementById('createKategoriForm');
                                        const modalInstance = bootstrap.Modal.getInstance(createModal);

                                        createForm.reset();
                                        createForm.querySelectorAll('.is-invalid').forEach(el => el.classList
                                            .remove(
                                                'is-invalid'));
                                        createForm.querySelectorAll('.invalid-feedback').forEach(el => el
                                            .textContent = '');
                                        createPond.removeFiles().then(() => {
                                            modalInstance.hide();
                                        });
                                    });
                                }
                            }

                            // --- MODAL EDIT ---
                            const editModal = document.getElementById('editModal');
                            let editPond = null;
                            const csrfToken = '{{ csrf_token() }}';

                            if (editModal) {
                                const editForm = editModal.querySelector('#editKategoriForm');
                                const inputNama = editModal.querySelector('#edit_name');
                                const inputSlug = editModal.querySelector('#edit_slug');
                                const inputStatus = editModal.querySelector('#edit_status');

                                editModal.addEventListener('show.bs.modal', function(event) {
                                    const button = event.relatedTarget;
                                    const dataUrl = button.getAttribute('data-url');
                                    const updateUrl = button.getAttribute('data-update-url');

                                    // Set action form untuk update
                                    editForm.action = updateUrl;

                                    fetch(dataUrl)
                                        .then(response => response.json())
                                        .then(data => {
                                            inputNama.value = data.name;
                                            inputSlug.value = data.slug;
                                            inputStatus.checked = data.status == 1;

                                            const pondFiles = [];
                                            if (data.img_kategori) {
                                                pondFiles.push(`/storage/${data.img_kategori}`);
                                            }

                                            editPond = FilePond.create(document.querySelector(
                                                '#img_kategori_edit'), {
                                                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                                                files: pondFiles,
                                                allowImagePreview: true,
                                                allowFileSizeValidation: true,
                                                maxFileSize: '2MB',
                                                allowImageCrop: true,
                                                imageCropAspectRatio: '1:1',
                                                acceptedFileTypes: ['image/png', 'image/jpeg',
                                                    'image/webp',
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
                                                    }
                                                }
                                            });

                                            const submitEditBtn = editForm.querySelector('#submit-edit-button');
                                            const pondEditInput = document.querySelector('#img_kategori_edit');

                                            pondEditInput.addEventListener('FilePond:addfile', (e) => {
                                                submitEditBtn.disabled = true;
                                                submitEditBtn.innerHTML =
                                                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...`;
                                            });

                                            pondEditInput.addEventListener('FilePond:processfile', (e) => {
                                                submitEditBtn.disabled = false;
                                                submitEditBtn.innerHTML = 'Simpan Perubahan';
                                            });

                                            pondEditInput.addEventListener('FilePond:removefile', (e) => {
                                                submitEditBtn.disabled = false;
                                                submitEditBtn.innerHTML = 'Simpan Perubahan';
                                            });
                                        })
                                        .catch(error => console.error('Error fetching kategori data:', error));
                                });

                                editForm.addEventListener('submit', function(e) {
                                            e.preventDefault();
                                            const formData = new FormData(this);
                                            const updateUrl = this.action;

                                            fetch(updateUrl, {
                                                    method: 'POST', // Laravel handles PUT/PATCH via _method field
                                                    headers: {
                                                        'X-CSRF-TOKEN': csrfToken,
                                                        'Accept': 'application/json'
                                                    },
                                                    body: formData
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        const updatedRow = createTableRow(data.data);
                                                        const oldRow = document.getElementById(
                                                            `kategori-row-${data.data.slug}`);
                                                        if (oldRow) {
                                                            oldRow.outerHTML = updatedRow;
                                                        }
                                                        bootstrap.Modal.getInstance(editModal).hide();
                                                        window.showToast('success', data.message);
                                                        else {
                                                            // Handle validation or other errors
                                                            window.showToast('error', data.message ||
                                                                'Terjadi kesalahan.');
                                                        })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        window.showToast('error',
                                                            'Tidak dapat terhubung ke server.');
                                                    });
                                                });

                                            inputNama.addEventListener('change', function() {
                                                fetch(`/dashboard/kategoriproduk/chekSlug?name=${inputNama.value}`)
                                                    .then(response => response.json())
                                                    .then(data => inputSlug.value = data.slug);
                                            });

                                            const cancelEditBtn = editModal.querySelector('#cancel-edit-button');
                                            if (cancelEditBtn) {
                                                cancelEditBtn.addEventListener('click', function(e) {
                                                    e.preventDefault();

                                                    const newFile = editPond.getFiles().find(file =>
                                                        file.origin === FilePond.FileOrigin.INPUT &&
                                                        file.status === FilePond.FileStatus.PROCESSING_COMPLETE
                                                    );

                                                    const modalInstance = bootstrap.Modal.getInstance(editModal);

                                                    if (newFile && newFile.serverId) {
                                                        fetch('{{ route('kategoriproduk.revert') }}', {
                                                            method: 'DELETE',
                                                            headers: {
                                                                'X-CSRF-TOKEN': csrfToken
                                                            },
                                                            body: newFile.serverId
                                                        }).finally(() => {
                                                            modalInstance.hide();
                                                        });
                                                    } else {
                                                        modalInstance.hide();
                                                    }
                                                });
                                            }

                                            editModal.addEventListener('hidden.bs.modal', function() {
                                                if (editPond) {
                                                    editPond.destroy();
                                                    editPond = null;
                                                }
                                            });
                                        }

                                        // --- MODAL DELETE ---
                                        const deleteModalEl = document.getElementById('deleteConfirmationModal');
                                        if (deleteModalEl) {
                                            const deleteForm = deleteModalEl.querySelector('#deleteKategoriForm');
                                            const modalBodyName = deleteModalEl.querySelector('#kategoriNameToDelete');
                                            const deleteModalInstance = new bootstrap.Modal(deleteModalEl);
                                            let kategoriSlugToDelete = null;

                                            deleteModalEl.addEventListener('show.bs.modal', function(event) {
                                                const button = event.relatedTarget;
                                                kategoriSlugToDelete = button.getAttribute('data-kategori-slug');
                                                const kategoriName = button.getAttribute('data-kategori-name');
                                                modalBodyName.textContent = kategoriName;
                                            });

                                            deleteForm.addEventListener('submit', function(e) {
                                                    e.preventDefault();
                                                    if (!kategoriSlugToDelete) return;

                                                    const url = `/kategoriproduk/${kategoriSlugToDelete}`;

                                                    fetch(url, {
                                                            method: 'DELETE',
                                                            headers: {
                                                                'X-CSRF-TOKEN': csrfToken,
                                                                'Accept': 'application/json'
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
                                                                    window.showToast('error', data.message ||
                                                                        'Terjadi kesalahan.');
                                                                })
                                                            .catch(error => {
                                                                deleteModalInstance.hide();
                                                                window.showToast('error',
                                                                    'Tidak dapat terhubung ke server.');
                                                            });
                                                        });
                                                }


                                                // --- AJAX FILTER & SEARCH ---
                                                $(document).ready(function() {
                                                    // Fungsi untuk menunda eksekusi (debounce)
                                                    function debounce(func, delay) {
                                                        let timeout;
                                                        return function(...args) {
                                                            clearTimeout(timeout);
                                                            timeout = setTimeout(() => func.apply(this, args),
                                                                delay);
                                                        };
                                                    }

                                                    // Fungsi untuk mengambil data dengan AJAX
                                                    function fetchData(page = 1) {
                                                        let search = $('#searchInput').val();
                                                        let status = $('#statusFilter').val();
                                                        let url = '{{ route('kategoriproduk.index') }}';

                                                        $('#kategori-table-container').css('opacity',
                                                        0.5); // Efek loading

                                                        $.ajax({
                                                            url: url,
                                                            data: {
                                                                search: search,
                                                                status: status,
                                                                page: page
                                                            },
                                                            success: function(data) {
                                                                $('#kategori-table-container').html(
                                                                    data).css('opacity', 1);
                                                                window.history.pushState({
                                                                        path: url + '?page=' +
                                                                            page + '&search=' +
                                                                            search +
                                                                            '&status=' + status
                                                                    }, '', url + '?page=' + page +
                                                                    '&search=' + search +
                                                                    '&status=' + status);
                                                            },
                                                            error: function() {
                                                                $('#kategori-table-container').css(
                                                                    'opacity', 1);
                                                                alert(
                                                                    'Gagal memuat data. Silakan coba lagi.');
                                                            }
                                                        });
                                                    }

                                                    $('#searchInput').on('keyup', debounce(function() {
                                                        fetchData(1);
                                                    }, 500));
                                                    $('#statusFilter').on('change', function() {
                                                        fetchData(1);
                                                    });
                                                    $(document).on('click',
                                                        '#kategori-table-container .pagination a',
                                                        function(e) {
                                                            e.preventDefault();
                                                            let page = $(this).attr('href').split('page=')[1];
                                                            if (page) fetchData(page);
                                                        });
                                                });

                                            });
    </script>
@endsection
@endsection
