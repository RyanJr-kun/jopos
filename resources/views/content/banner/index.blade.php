@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Banner')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')
    <div class="card">
        <div class="card-header pb-0 px-3 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {{-- [SNEAT] Ganti class text-sm -> text-muted, h6 tetap h6 --}}
                    <h5 class="mb-1">Manajemen Banner</h5>
                    <p class="text-muted mb-0">Kelola gambar banner untuk halaman depan.</p>
                </div>
                <button class="btn btn-outline-primary ms-md-auto mt-2" data-bs-toggle="modal"
                    data-bs-target="#createModal">
                    <i class="bx bx-plus me-1"></i> Banner
                </button>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div id="banner-table-container" class="table-responsive p-0 mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            {{-- [SNEAT] Ganti text-uppercase text-secondary text-xs fw-bolder text-dark -> text-nowrap --}}
                            <th class="text-nowrap">Gambar & Judul</th>
                            <th class="text-nowrap ps-2">Link Tujuan</th>
                            <th class="text-nowrap ps-2">Posisi</th>
                            <th class="text-nowrap text-center">Status</th>
                            <th class="text-nowrap text-center">Dibuat</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="banner-table-body">
                        @forelse ($banners as $banner)
                            <tr id="banner-row-{{ $banner->id }}">
                                <td>
                                    <div class="d-flex px-2 py-1 align-items-center">
                                        <div>
                                            <img src="{{ asset('storage/' . $banner->img_banner) }}" {{-- [SNEAT] Ganti avatar avatar-sm -> avatar avatar-sm rounded --}}
                                                class="avatar avatar-sm rounded me-3" alt="Banner Image">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0">{{ $banner->judul ?? 'Tanpa Judul' }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($banner->url_tujuan)
                                        {{-- [SNEAT] Ganti text-xs font-weight-bold -> small fw-semibold --}}
                                        <a href="{{ $banner->url_tujuan }}" target="_blank"
                                            class="small fw-semibold text-primary mb-0">{{ Str::limit($banner->url_tujuan, 30) }}</a>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    {{-- [SNEAT] Ganti text-secondary text-sm text-dark -> text-muted small --}}
                                    <small
                                        class="text-muted">{{ \App\Enums\BannerPosition::from($banner->posisi)->getLabel() }}</small>
                                </td>
                                <td class="text-center">
                                    @if ($banner->is_active)
                                        {{-- [SNEAT] Ganti badge-success -> bg-label-success --}}
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        {{-- [SNEAT] Ganti badge-secondary -> bg-label-secondary --}}
                                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- [SNEAT] Ganti font-weight-bold -> fw-semibold --}}
                                    <small
                                        class="text-muted fw-semibold">{{ $banner->created_at->translatedFormat('d M Y') }}</small>
                                </td>
                                <td>
                                    <a href="javascript:;" class="text-secondary me-3" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-id="{{ $banner->id }}" title="Edit banner">
                                        <i class="bx bx-edit fs-5"></i>
                                    </a>
                                    {{-- ✅ Fix — tambahkan data-bs-toggle & data-bs-target --}}
                                    <a href="javascript:;" class="text-danger btn-delete" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal" data-id="{{ $banner->id }}"
                                        data-title="{{ $banner->judul ?? 'Tanpa Judul' }}"
                                        data-url="{{ route('banner.destroy', $banner->id) }}" title="Hapus banner">
                                        <i class="bx bx-trash fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="banner-row-empty">
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted fw-semibold mb-0">Belum ada data banner.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Create -->

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Banner Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createBannerForm" enctype="multipart/form-data">
                    <div class="modal-body pt-0">
                        <div class="mb-3">
                            <label class="form-label">Gambar Banner
                                <i class="bx bx-question-mark text-muted cursor-pointer ms-1" data-bs-toggle="popover"
                                    data-bs-trigger="hover focus" data-bs-placement="right" data-bs-html="true"
                                    title="Rekomendasi Ukuran Banner"
                                    data-bs-content="<ul><li><strong>Main Banner:</strong> 1920x700 piksel</li><li><strong>Promotion Banner:</strong> 400x500 piksel</li><li><strong>Bestseller Banner:</strong> 600x300 piksel</li></ul>"></i>
                            </label>
                            <input type="file" class="filepond" name="img_banner" id="create_img_banner" required>
                            <div class="invalid-feedback" id="img_banner-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="create_title" class="form-label">Judul (Opsional)</label>
                            <input type="text" class="form-control" id="create_title" name="judul"
                                placeholder="cth: Promotion Kemerdekaan">
                            <div class="invalid-feedback" id="judul-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="create_url_tujuan" class="form-label">Link Tujuan (Opsional)</label>
                            <input type="url" class="form-control" id="create_url_tujuan" name="url_tujuan"
                                placeholder="https://tokoanda.com/promo">
                            <div class="invalid-feedback" id="create-url_tujuan-error"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="create_posisi" class="form-label">Posisi</label>
                                    <select class="form-select" id="create_posisi" name="posisi" required>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->value }}">{{ $position->getLabel() }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="posisi-error"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="create_urutan" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="create_urutan" name="urutan"
                                        value="0" min="0" required>
                                    <div class="invalid-feedback" id="urutan-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="create_is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="create_is_active">Aktifkan Banner</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitCreateBtn" class="btn btn-primary btn-sm px-3">Simpan</button>
                        <button type="button" id="cancel-create-button"
                            class="btn btn-outline-secondary btn-sm px-3">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBannerForm" enctype="multipart/form-data">
                    <div class="modal-body pt-0">
                        <div class="mb-3">
                            <label class="form-label">Gambar Banner
                                <i class="bx bx-question-mark text-muted cursor-pointer ms-1" data-bs-toggle="popover"
                                    data-bs-trigger="hover focus" data-bs-placement="right" data-bs-html="true"
                                    title="Rekomendasi Ukuran Banner"
                                    data-bs-content="<ul><li><strong>Main Banner:</strong> 1920x700 piksel</li><li><strong>Promotion Banner:</strong> 400x500 piksel</li><li><strong>Bestseller Banner:</strong> 600x300 piksel</li></ul>"></i>
                            </label>
                            <input type="file" class="filepond" name="img_banner" id="edit_img_banner">
                        </div>
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Judul (Opsional)</label>
                            <input type="text" class="form-control" id="edit_title" name="judul">
                            <div class="invalid-feedback" id="edit-judul-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_url_tujuan" class="form-label">Link Tujuan (Opsional)</label>
                            <input type="url" class="form-control" id="edit_url_tujuan" name="url_tujuan">
                            <div class="invalid-feedback" id="edit-url_tujuan-error"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="edit_posisi" class="form-label">Posisi</label>
                                    <select class="form-select" id="edit_posisi" name="posisi" required>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->value }}">{{ $position->getLabel() }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="edit-posisi-error"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_urutan" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="edit_urutan" name="urutan"
                                        min="0" required>
                                    <div class="invalid-feedback" id="edit-urutan-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                value="1">
                            <label class="form-check-label" for="edit_is_active">Aktifkan Banner</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitEditBtn" class="btn btn-primary btn-sm px-3">Simpan
                            Perubahan</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                            data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <x-delete-modal message="Apakah Anda yakin ingin menghapus banner ini?" item-title-id="delete-banner-title"
        modal-id="deleteModal" />
@endsection

@section('page-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        // ============================================================
        // HELPER: Buat baris tabel dari data banner (Sneat classes)
        // ============================================================
        function createTableRow(banner) {
            const imageUrl = banner.img_banner ?
                `{{ asset('storage') }}/${banner.img_banner}` :
                'https://via.placeholder.com/100x100';

            // [SNEAT] badge bg-label-success / bg-label-secondary
            const statusBadge = banner.is_active ?
                '<span class="badge bg-label-success">Aktif</span>' :
                '<span class="badge bg-label-secondary">Tidak Aktif</span>';

            const urlDisplay = banner.url_tujuan ?
                `<a href="${banner.url_tujuan}" target="_blank" class="small fw-semibold text-primary">${banner.url_tujuan.length > 30 ? banner.url_tujuan.substring(0, 30) + '...' : banner.url_tujuan}</a>` :
                '<small class="text-muted">-</small>';

            const createdAt = new Date(banner.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            return `
                <tr id="banner-row-${banner.id}">
                    <td>
                        <div class="d-flex px-2 py-1 align-items-center">
                            <img src="${imageUrl}" class="avatar avatar-sm rounded me-3" alt="Banner Image">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0">${banner.judul || 'Tanpa Judul'}</h6>
                            </div>
                        </div>
                    </td>
                    <td>${urlDisplay}</td>
                    <td><small class="text-muted">${banner.posisi_label || banner.posisi}</small></td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center"><small class="text-muted fw-semibold">${createdAt}</small></td>
                    <td>
                        <a href="javascript:;" class="text-secondary me-3" data-bs-toggle="modal"
                            data-bs-target="#editModal" data-id="${banner.id}" title="Edit banner">
                            <i class="bx bx-edit fs-5"></i>
                        </a>
                        <a href="javascript:;" class="text-danger delete-btn" data-bs-toggle="modal"
                            data-bs-target="#deleteModal" data-id="${banner.id}"
                            data-title="${banner.judul || 'Tanpa Judul'}" title="Hapus banner">
                            <i class="bx bx-trash fs-5"></i>
                        </a>
                    </td>
                </tr>`;
        }

        // ============================================================
        // HELPER: Tampilkan toast (memanggil window.showToast dari layout)
        // ============================================================
        function showToast(type, message) {
            const toastId = type === 'success' ? 'successToast' : 'errorToast';
            const toastEl = document.getElementById(toastId);
            if (!toastEl) return;
            toastEl.querySelector('.toast-body').textContent = message;
            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            toast.show();
        }

        // ============================================================
        // HELPER: Reset semua error state pada form
        // ============================================================
        function clearFormErrors(form) {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // ============================================================
        // HELPER: Tampilkan error validasi dari response
        // ============================================================
        function showValidationErrors(form, errors, prefix = '') {
            Object.keys(errors).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                const errorDiv = form.querySelector(`#${prefix}${key}-error`);
                if (input) input.classList.add('is-invalid');
                if (errorDiv) errorDiv.textContent = errors[key][0];
            });
        }

        // ============================================================
        // MAIN
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {

            // --- Setup FilePond ---
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType
            );

            const csrfToken = "{{ csrf_token() }}";
            const pondOptions = {
                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                labelFileTypeNotAllowed: 'Hanya PNG, JPG, WEBP, dan SVG yang diizinkan.',
                server: {
                    process: {
                        url: '{{ route('banner.upload') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                    revert: {
                        url: '{{ route('banner.revert') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                }
            };

            // ========================
            // CREATE MODAL
            // ========================
            const createForm = document.getElementById('createBannerForm');
            const createModal = document.getElementById('createModal');
            const submitCreateBtn = document.getElementById('submitCreateBtn');

            const createPond = FilePond.create(document.querySelector('#create_img_banner'), pondOptions);

            submitCreateBtn.addEventListener('click', function() {
                clearFormErrors(createForm);

                const formData = new FormData(createForm);
                const pondFile = createPond.getFile();
                if (pondFile) formData.set('img_banner', pondFile.serverId);

                submitCreateBtn.disabled = true;
                submitCreateBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...`;

                fetch('{{ route('banner.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hapus baris "belum ada data" jika ada
                            const emptyRow = document.getElementById('banner-row-empty');
                            if (emptyRow) emptyRow.remove();

                            document.getElementById('banner-table-body').insertAdjacentHTML('beforeend',
                                createTableRow(data.data));
                            bootstrap.Modal.getInstance(createModal).hide();
                            showToast('success', data.message);
                        } else {
                            if (data.errors) {
                                showValidationErrors(createForm, data.errors);
                            } else {
                                showToast('error', data.message || 'Terjadi kesalahan.');
                            }
                        }
                    })
                    .catch(() => showToast('error', 'Tidak dapat terhubung ke server.'))
                    .finally(() => {
                        submitCreateBtn.disabled = false;
                        submitCreateBtn.innerHTML = 'Simpan';
                    });
            });

            // Reset form saat modal create ditutup
            document.getElementById('cancel-create-button').addEventListener('click', function() {
                clearFormErrors(createForm);
                createForm.reset();
                createPond.removeFiles();
                bootstrap.Modal.getInstance(createModal).hide();
            });

            createModal.addEventListener('hidden.bs.modal', function() {
                clearFormErrors(createForm);
                createForm.reset();
                createPond.removeFiles();
            });

            // ========================
            // EDIT MODAL
            // ========================
            let editPond = null;
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editBannerForm');
            const submitEditBtn = document.getElementById('submitEditBtn');

            // Bersihkan FilePond dan form saat modal edit ditutup
            editModal.addEventListener('hidden.bs.modal', function() {
                if (editPond) {
                    editPond.destroy();
                    editPond = null;
                }
                clearFormErrors(editForm);
                editForm.reset();
            });

            // Muat data banner ke form saat modal edit dibuka
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const bannerId = button.getAttribute('data-id');
                editForm.dataset.updateUrl = `/banner/${bannerId}`;

                fetch(`/banner/${bannerId}/json`)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        document.getElementById('edit_title').value = data.judul ?? '';
                        document.getElementById('edit_url_tujuan').value = data.url_tujuan ?? '';
                        document.getElementById('edit_posisi').value = data.posisi ?? '';
                        document.getElementById('edit_urutan').value = data.urutan ?? 0;
                        document.getElementById('edit_is_active').checked = !!data.is_active;

                        const pondFiles = data.img_banner ? [`/storage/${data.img_banner}`] : [];

                        editPond = FilePond.create(document.querySelector('#edit_img_banner'), {
                            ...pondOptions,
                            files: pondFiles,
                        });

                        // Nonaktifkan tombol simpan saat upload berlangsung
                        const editInput = document.querySelector('#edit_img_banner');
                        editInput.addEventListener('FilePond:addfile', () => {
                            submitEditBtn.disabled = true;
                            submitEditBtn.innerHTML =
                                `<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah...`;
                        });
                        editInput.addEventListener('FilePond:processfile', () => {
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML = 'Simpan Perubahan';
                        });
                        editInput.addEventListener('FilePond:removefile', () => {
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML = 'Simpan Perubahan';
                        });
                    })
                    .catch(err => {
                        console.error('Error fetch banner:', err);
                        bootstrap.Modal.getInstance(editModal).hide();
                        showToast('error', 'Tidak dapat mengambil data banner. Silakan coba lagi.');
                    });
            });

            // Submit form edit
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearFormErrors(editForm);

                const formData = new FormData(editForm);
                formData.append('_method', 'PUT');

                if (editPond) {
                    const pondFile = editPond.getFile();
                    if (pondFile && pondFile.status === FilePond.FileStatus.PROCESSING_COMPLETE) {
                        formData.set('img_banner', pondFile.serverId);
                    } else if (!pondFile) {
                        formData.set('img_banner', '');
                    } else {
                        formData.delete('img_banner');
                    }
                }

                submitEditBtn.disabled = true;
                submitEditBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...`;

                fetch(editForm.dataset.updateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const oldRow = document.getElementById(`banner-row-${data.data.id}`);
                            if (oldRow) oldRow.outerHTML = createTableRow(data.data);
                            bootstrap.Modal.getInstance(editModal).hide();
                            showToast('success', data.message);
                        } else {
                            if (data.errors) {
                                showValidationErrors(editForm, data.errors, 'edit-');
                            } else {
                                showToast('error', data.message || 'Terjadi kesalahan.');
                            }
                        }
                    })
                    .catch(() => showToast('error', 'Tidak dapat terhubung ke server.'))
                    .finally(() => {
                        submitEditBtn.disabled = false;
                        submitEditBtn.innerHTML = 'Simpan Perubahan';
                    });
            });

            // ========================
            // DELETE MODAL
            // ========================
            const deleteModal = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let bannerIdToDelete = null;

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                bannerIdToDelete = button.getAttribute('data-id');
                document.getElementById('delete-banner-title').textContent =
                    button.getAttribute('data-title') || 'banner ini';
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (!bannerIdToDelete) return;

                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...`;

                fetch(`/banner/${bannerIdToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                    })
                    .then(res => res.json())
                    .then(data => {
                        bootstrap.Modal.getInstance(deleteModal).hide();
                        if (data.success) {
                            const row = document.getElementById(`banner-row-${bannerIdToDelete}`);
                            if (row) row.remove();

                            // Tampilkan baris kosong jika tidak ada data lagi
                            const tbody = document.getElementById('banner-table-body');
                            if (tbody && tbody.children.length === 0) {
                                tbody.innerHTML = `
                                <tr id="banner-row-empty">
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted fw-semibold mb-0">Belum ada data banner.</p>
                                    </td>
                                </tr>`;
                            }
                            showToast('success', data.message);
                        } else {
                            showToast('error', data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(() => {
                        bootstrap.Modal.getInstance(deleteModal).hide();
                        showToast('error', 'Tidak dapat terhubung ke server.');
                    })
                    .finally(() => {
                        confirmDeleteBtn.disabled = false;
                        confirmDeleteBtn.innerHTML = 'Ya, Hapus';
                        bannerIdToDelete = null;
                    });
            });

            // ========================
            // POPOVER INIT
            // ========================
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                new bootstrap.Popover(el);
            });

        }); // END DOMContentLoaded
    </script>
@endsection
