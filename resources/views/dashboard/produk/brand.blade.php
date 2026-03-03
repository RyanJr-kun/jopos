@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection
@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Manajemen Brand', 'url' => route('brand.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">List Brand</h6>
                    <p class="text-sm mb-0">Kelola Data Brandmu</p>
                </div>
                <div class="ms-md-auto mt-2">
                    {{-- triger-modal --}}
                    <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import">
                        <i class="fa fa-plus cursor-pointer pe-2"></i>Brand
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="filter-container">
                <div class="row g-3 align-items-center justify-content-between">
                    <!-- Filter Pencarian Brand -->
                    <div class="col-md-4 ms-3">
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Cari Brand..." value="{{ request('search') }}">
                    </div>
                    <!-- Filter Dropdown Status -->
                    <div class="col-md-3 me-3">
                        <select name="status" id="statusFilter" class="form-select">
                            <option value="">Semua Status</option>
                            {{-- Status akan diisi oleh JS atau dari controller --}}
                            <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                            <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="brand-table-container">
                @include('dashboard.produk._brand_table')
            </div>
        </div>
    </div>
    {{-- modal-create --}}
    <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="ModalLabel">Buat Data Brand Baru</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createBrandForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <p class="text-dark fw-bold ">Gambar Brand:</p>
                                <input type="file" class="filepond" name="img_brand" id="img_brand_create">
                            </div>
                            <div class="col-md-6">
                                <div class="mt-md-4">
                                    <label for="nama" class="form-label">Brand</label>
                                    <input id="nama" name="nama" type="text"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        value="{{ old('nama') }}" required>
                                    <div class="invalid-feedback" id="nama-error"></div>
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
                                Brand</button>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Data Brand</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBrandForm" method="post" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-dark fw-bold ">Gambar Brand:</p>
                                <input type="file" class="filepond" name="img_brand" id="img_brand_edit">
                            </div>

                            <!-- Kolom Kanan untuk Input Teks -->
                            <div class="col-md-6">
                                <div class="mt-4">
                                    <label for="edit_nama" class="form-label">Nama</label>
                                    <input id="edit_nama" name="nama" type="text" class="form-control"
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
                    <p class="mb-0">Apakah Anda yakin ingin menghapus Brand ini?</p>
                    <h6 class="mt-2" id="brandNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteBrandForm" method="POST" action="#">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        // Fungsi untuk membuat baris tabel baru dari data
        function createTableRow(brand) {
            const statusBadge = brand.status ? '<span class="badge badge-success">Aktif</span>' :
                '<span class="badge badge-secondary">Tidak Aktif</span>';
            const imageUrl = brand.img_brand ? `{{ asset('storage') }}/${brand.img_brand}` :
                `{{ asset('assets/img/produk.webp') }}`;
            const editUrl = `{{ url('brand/getjson') }}/${brand.slug}`;
            const updateUrl = `{{ url('brand') }}/${brand.slug}`;

            return `
                    <tr id="brand-row-${brand.slug}">
                        <td>
                            <div title="foto & nama brand" class="d-flex ms-2 px-2 py-1 align-items-center">
                                <img src="${imageUrl}" class="avatar avatar-sm me-3" alt="${brand.nama}">
                                <h6 class="mb-0 text-sm">${brand.nama}</h6>
                            </div>
                        </td>
                        <td><p class="text-xs text-dark fw-bold mb-0">${brand.produks_count}</p></td>
                        <td><p class="text-xs text-dark fw-bold mb-0">${brand.created_at_formatted}</p></td>
                        <td class="align-middle text-center text-sm">${statusBadge}</td>
                        <td class="align-middle">
                            <a href="#" class="text-dark fw-bold px-3 text-xs" data-bs-toggle="modal" data-bs-target="#editModal" data-url="${editUrl}" data-update-url="${updateUrl}" title="Edit brand">
                                <i class="bx bx-pencil-square text-dark text-sm opacity-10"></i>
                            </a>
                            <a href="#" class="text-dark delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-brand-slug="${brand.slug}" data-brand-name="${brand.nama}" title="Hapus Unit">
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
            const createPond = FilePond.create(document.querySelector('#img_brand_create'), {
                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                // Aktifkan pratinjau gambar
                allowImagePreview: true,
                // Aktifkan validasi ukuran file
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                // Aktifkan crop gambar
                allowImageCrop: true,
                imageCropAspectRatio: '1:1',
                // stylePanelAspectRatio: '1:1',
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                labelFileTypeNotAllowed: 'Jenis file tidak valid. Hanya PNG, JPG, WEBP, dan SVG yang diizinkan.',
                server: {
                    process: {
                        url: '/dashboard/brand/upload', // Pastikan route ini ada
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    },
                    revert: {
                        url: '/dashboard/brand/revert',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                }
            });

            // --- MODAL CREATE ---
            const createModal = document.getElementById('import');
            if (createModal) {
                const namaInput = createModal.querySelector('#nama');
                const slugInput = createModal.querySelector('#slug');

                // Event listener untuk slug otomatis
                namaInput.addEventListener('change', function() {
                    fetch(`/dashboard/brand/chekSlug?nama=${namaInput.value}`)
                        .then(response => response.json())
                        .then(data => slugInput.value = data.slug);
                });

                // Tampilkan modal jika ada error validasi dari server
                const hasError = document.querySelector('.is-invalid');
                if (hasError) {
                    var createModalInstance = new bootstrap.Modal(createModal);
                    createModalInstance.show();
                }

                // Logika submit form create via AJAX
                const createForm = document.getElementById('createBrandForm');
                const submitCreateBtn = document.getElementById('submit-create-button');

                if (submitCreateBtn) {
                    submitCreateBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const formData = new FormData(createForm);

                        // Reset error states
                        createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                            'is-invalid'));
                        createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                        fetch('{{ route('brand.store') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json' // Penting untuk memberitahu Laravel kita mau JSON
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
                                    document.getElementById('brand-row-empty')?.remove();

                                    const modalInstance = bootstrap.Modal.getInstance(createModal);
                                    modalInstance.hide();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            })
                            .catch(errorData => {
                                if (errorData.errors) {
                                    Object.keys(errorData.errors).forEach(key => {
                                        const input = createForm.querySelector(
                                            `[name="${key}"]`);
                                        const errorDiv = createForm.querySelector(
                                            `#${key}-error`);
                                        if (input) input.classList.add('is-invalid');
                                        if (errorDiv) errorDiv.textContent = errorData.errors[
                                            key][0];
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: errorData.message || 'Terjadi kesalahan server.'
                                    });
                                }
                            });
                    });
                }
            }

            // Logika tombol batalkan di modal create untuk membersihkan form dan FilePond
            const cancelCreateBtn = createModal.querySelector('#cancel-create-button');
            if (cancelCreateBtn) {
                cancelCreateBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah penutupan modal otomatis oleh data-bs-dismiss

                    const createForm = document.getElementById('createBrandForm');
                    const modalInstance = bootstrap.Modal.getInstance(createModal);

                    // 1. Reset nilai input pada form ke nilai defaultnya
                    createForm.reset();

                    // 2. Hapus semua pesan error validasi
                    createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                        'is-invalid'));
                    createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                    // 3. Hapus file dari FilePond (ini juga akan memicu revert di server)
                    //    dan tutup modal setelah selesai.
                    createPond.removeFiles().then(() => {
                        modalInstance.hide();
                    });
                });
            }

            // --- MODAL EDIT ---
            const editModal = document.getElementById('editModal');
            let editPond = null; // Untuk menyimpan instance FilePond modal edit
            const csrfToken = '{{ csrf_token() }}';

            if (editModal) {
                const editForm = editModal.querySelector('#editBrandForm');
                const inputNama = editModal.querySelector('#edit_nama');
                const inputSlug = editModal.querySelector('#edit_slug');
                const inputStatus = editModal.querySelector('#edit_status');

                // Event listener untuk menampilkan modal edit
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    // Set action form untuk update
                    editForm.action = updateUrl;

                    fetch(dataUrl)
                        .then(response => response.json())
                        .then(data => {
                            // Isi form dengan data yang ada
                            inputNama.value = data.nama;
                            inputSlug.value = data.slug;
                            inputStatus.checked = data.status == 1;

                            const pondFiles = [];
                            if (data.img_brand) {
                                // Cukup berikan URL lengkap ke gambar yang ada, FilePond akan menampilkannya.
                                pondFiles.push(`/storage/${data.img_brand}`);
                            }

                            // Buat instance FilePond baru untuk modal edit
                            editPond = FilePond.create(document.querySelector('#img_brand_edit'), {
                                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                                files: pondFiles,
                                allowImagePreview: true,
                                allowFileSizeValidation: true,
                                maxFileSize: '2MB',
                                allowImageCrop: true,
                                imageCropAspectRatio: '1:1',
                                // stylePanelAspectRatio: '1:1',
                                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp',
                                    'image/svg+xml'
                                ],
                                labelFileTypeNotAllowed: 'Jenis file tidak valid.',
                                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                                labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                                server: {
                                    process: {
                                        url: '/dashboard/brand/upload', // Pastikan route ini ada
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken
                                        }
                                    },
                                    revert: {
                                        url: '/dashboard/brand/revert',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken
                                        }
                                    }
                                }
                            });

                            // --- Fitur Keamanan: Nonaktifkan tombol simpan saat upload ---
                            const submitEditBtn = editForm.querySelector('#submit-edit-button');
                            const pondEditInput = document.querySelector('#img_brand_edit');

                            pondEditInput.addEventListener('FilePond:addfile', (e) => {
                                // Nonaktifkan tombol saat file ditambahkan dan mulai diunggah
                                submitEditBtn.disabled = true;
                                submitEditBtn.innerHTML =
                                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...`;
                            });

                            pondEditInput.addEventListener('FilePond:processfile', (e) => {
                                // Aktifkan kembali setelah proses selesai (berhasil atau gagal)
                                submitEditBtn.disabled = false;
                                submitEditBtn.innerHTML = 'Simpan Perubahan';
                            });

                            pondEditInput.addEventListener('FilePond:removefile', (e) => {
                                // Aktifkan kembali jika file dibatalkan/dihapus
                                submitEditBtn.disabled = false;
                                submitEditBtn.innerHTML = 'Simpan Perubahan';
                            });
                        })
                        .catch(error => console.error('Error fetching brand data:', error));
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
                                const oldRow = document.getElementById(`brand-row-${data.data.slug}`);
                                if (oldRow) {
                                    oldRow.outerHTML = updatedRow;
                                }
                                bootstrap.Modal.getInstance(editModal).hide();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan.'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
                        });
                });

                // Event listener untuk slug otomatis di modal edit
                inputNama.addEventListener('change', function() {
                    fetch(`/dashboard/brand/chekSlug?nama=${inputNama.value}`)
                        .then(response => response.json())
                        .then(data => inputSlug.value = data.slug);
                });

                // Logika tombol batalkan di modal edit
                const cancelEditBtn = editModal.querySelector('#cancel-edit-button');
                if (cancelEditBtn) {
                    cancelEditBtn.addEventListener('click', function(e) {
                        e.preventDefault(); // Mencegah penutupan modal otomatis jika ada data-bs-dismiss

                        // Cari file yang BARU diunggah oleh pengguna dan sudah selesai diproses
                        const newFile = editPond.getFiles().find(file =>
                            file.origin === FilePond.FileOrigin.INPUT &&
                            file.status === FilePond.FileStatus.PROCESSING_COMPLETE
                        );

                        const modalInstance = bootstrap.Modal.getInstance(editModal);

                        if (newFile && newFile.serverId) {
                            // Jika ada file baru yang sudah diunggah, hapus dulu dari server
                            fetch('{{ route('brand.revert') }}', {
                                method: 'DELETE', // Laravel handles PUT/PATCH via _method field
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

                // Tambahkan event listener untuk membersihkan FilePond saat modal ditutup.
                // Ini penting untuk mencegah state gambar dari edit sebelumnya terbawa.
                editModal.addEventListener('hidden.bs.modal', function() {
                    if (editPond) {
                        editPond.destroy();
                        editPond = null; // Pastikan instance lama benar-benar dihapus
                    }
                });
            }

            // --- MODAL DELETE ---
            const deleteModalEl = document.getElementById('deleteConfirmationModal');
            if (deleteModalEl) {
                const deleteForm = deleteModalEl.querySelector('#deleteBrandForm');
                const modalBodyName = deleteModalEl.querySelector('#brandNameToDelete');
                const deleteModalInstance = new bootstrap.Modal(deleteModalEl);
                let brandSlugToDelete = null;

                deleteModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    brandSlugToDelete = button.getAttribute('data-brand-slug');
                    const brandName = button.getAttribute('data-brand-name');
                    modalBodyName.textContent = brandName;
                });

                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!brandSlugToDelete) return;

                    const url = `/brand/${brandSlugToDelete}`;

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
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                document.getElementById(`brand-row-${brandSlugToDelete}`).remove();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan.'
                                });
                            }
                        })
                        .catch(error => {
                            deleteModalInstance.hide();
                            Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
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
                        timeout = setTimeout(() => func.apply(this, args), delay);
                    };
                }

                // Fungsi untuk mengambil data dengan AJAX
                function fetchData(page = 1) {
                    let search = $('#searchInput').val();
                    let status = $('#statusFilter').val();
                    let url = '{{ route('brand.index') }}';

                    $('#brand-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            status: status,
                            page: page
                        },
                        success: function(data) {
                            $('#brand-table-container').html(data).css('opacity', 1);
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&status=' + status
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&status=' + status);
                        },
                        error: function() {
                            $('#brand-table-container').css('opacity', 1);
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
                $(document).on('click', '#brand-table-container .pagination a', function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    if (page) fetchData(page);
                });
            });

        });
    </script>
@endsection
@endsection
