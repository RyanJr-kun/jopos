@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Manajemen Banner', 'url' => route('banner.index')],
    ]" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Manajemen Banner</h6>
                    <p class="text-sm mb-0">Kelola gambar banner untuk halaman depan.</p>
                </div>
                <button class="btn btn-outline-info ms-md-auto mt-2" data-bs-toggle="modal"
                    data-bs-target="#createModal">
                    <i class="fas fa-plus me-2"></i>Banner
                </button>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div id="banner-table-container" class="table-responsive p-0 mt-3">
                <table class="table table-hover align-items-center mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-uppercase text-secondary text-xs fw-bolder text-dark">Gambar & Judul</th>
                            <th class="text-uppercase text-secondary text-xs fw-bolder text-dark ps-2">Link Tujuan</th>
                            <th class="text-uppercase text-secondary text-xs fw-bolder text-dark ps-2">Posisi</th>
                            <th class="text-uppercase text-secondary text-xs fw-bolder text-dark text-center">Status
                            </th>
                            <th class="text-uppercase text-secondary text-xs fw-bolder text-dark text-center">Dibuat
                            </th>
                            <th class="text-secondary"></th>
                        </tr>
                    </thead>
                    <tbody id="banner-table-body">
                        @forelse ($banners as $banner)
                            <tr id="banner-row-{{ $banner->id }}">
                                <td>
                                    <div class="d-flex px-2 py-1 align-items-center">
                                        <div>
                                            <img src="{{ asset('storage/' . $banner->img_banner) }}"
                                                class="avatar avatar-sm me-3" alt="Banner Image">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-dark">{{ $banner->judul ?? 'Tanpa Judul' }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($banner->url_tujuan)
                                        <a href="{{ $banner->url_tujuan }}" target="_blank"
                                            class="text-xs font-weight-bold mb-0 text-info">{{ Str::limit($banner->url_tujuan, 30) }}</a>
                                    @else
                                        <p class="text-xs text-secondary mb-0">-</p>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span
                                        class="text-secondary text-sm text-dark">{{ \App\Enums\BannerPosition::from($banner->posisi)->getLabel() }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    @if ($banner->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span
                                        class="text-secondary text-sm font-weight-bold">{{ $banner->created_at->translatedFormat('d M Y') }}</span>
                                </td>
                                <td class="align-middle">
                                    <a href="javascript:;" class="text-secondary font-weight-bold text-xs me-3"
                                        data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $banner->id }}"
                                        title="Edit banner">
                                        <i class="bx bx-pencil-square bi-sm text-dark"></i>
                                    </a>
                                    <a href="javascript:;" class="text-secondary font-weight-bold text-xs delete-btn"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="{{ $banner->id }}" data-title="{{ $banner->judul ?? 'Tanpa Judul' }}"
                                        title="Hapus banner">
                                        <i class="bx bx-trash bi-sm text-dark"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="banner-row-empty">
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-dark text-sm fw-bold mb-0">Belum ada data banner.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Banner Baru</h5>
                <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="createBannerForm" enctype="multipart/form-data">
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label">Gambar Banner
                            <i class="fas fa-question-circle text-dark cursor-pointer ms-1" data-bs-toggle="popover"
                                data-bs-trigger="hover focus" data-bs-placement="right" data-bs-html="true"
                                title="Rekomendasi Ukuran Banner"
                                data-bs-content="<ul><li><strong>Main Banner:</strong> 1920x700 piksel</li><li><strong>Promo Banner:</strong> 400x500 piksel</li><li><strong>Bestseller Banner:</strong> 600x300 piksel</li></ul>"></i></label>
                        <input type="file" class="filepond" name="img_banner" id="create_img_banner" required>
                        <div class="invalid-feedback" id="img_banner-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="create_title" class="form-label">Judul (Opsional)</label>
                        <input type="text" class="form-control" id="create_title" name="judul"
                            placeholder="cth: Promo Kemerdekaan">
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
                    <button type="button" id="submitCreateBtn"
                        class="btn btn-outline-info btn-sm px-3">Simpan</button>
                    <button type="button" id="cancel-create-button"
                        class="btn btn-danger btn-sm px-3">Batal</button>
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
                <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="editBannerForm" enctype="multipart/form-data">
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label">Gambar Banner
                            <i class="fas fa-question-circle text-dark cursor-pointer ms-1" data-bs-toggle="popover"
                                data-bs-trigger="hover focus" data-bs-placement="right" data-bs-html="true"
                                title="Rekomendasi Ukuran Banner"
                                data-bs-content="<ul><li><strong>Main Banner:</strong> 1920x700 piksel</li><li><strong>Promo Banner:</strong> 400x500 piksel</li><li><strong>Bestseller Banner:</strong> 600x300 piksel</li></ul>"></i>
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
                    <button type="submit" id="submitEditBtn" class="btn btn-outline-info btn-sm px-3">Simpan
                        Perubahan</button>
                    <button type="button" id="cancel-edit-button" class="btn btn-danger btn-sm px-3"
                        data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-3 mx-n5">
                <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                <p class="mb-0">Apakah Anda yakin ingin menghapus banner ini?</p>
                <h6 class="mt-2" id="delete-banner-title"></h6>
                <div class="mt-4">
                    <form id="deleteBannerForm" method="POST">
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

@section('page-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        // Fungsi untuk membuat baris tabel baru dari data banner
        function createTableRow(banner) {
            const imageUrl = banner.img_banner ? `{{ asset('storage') }}/${banner.img_banner}` :
                'https://via.placeholder.com/100x100';
            const statusBadge = banner.is_active ? '<span class="badge badge-success">Aktif</span>' :
                '<span class="badge badge-secondary">Tidak Aktif</span>';
            const urlDisplay = banner.url_tujuan ?
                `<a href="${banner.url_tujuan}" target="_blank" class="text-xs font-weight-bold mb-0 text-info">${banner.url_tujuan.length > 30 ? banner.url_tujuan.substring(0, 30) + '...' : banner.url_tujuan}</a>` :
                '<p class="text-xs text-secondary mb-0">-</p>';
            // Format tanggal menggunakan JS untuk konsistensi
            const createdAt = new Date(banner.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            return `
                    <tr id="banner-row-${banner.id}">
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                <div><img src="${imageUrl}" class="avatar avatar-lg me-3" alt="Banner Image"></div>
                                <div class="d-flex flex-column justify-content-center"><h6 class="mb-0 text-dark">${banner.judul || 'Tanpa Judul'}</h6></div>
                            </div>
                        </td>
                        <td>${urlDisplay}</td>
                        <td class="align-middle"><span class="text-secondary text-sm text-dark">${banner.posisi_label || banner.posisi}</span></td>
                        <td class="align-middle text-center text-sm">${statusBadge}</td>
                        <td class="align-middle text-center"><span class="text-secondary text-sm font-weight-bold">${createdAt}</span></td>
                        <td class="align-middle">
                            <a href="javascript:;" class="text-secondary font-weight-bold text-xs me-3" data-bs-toggle="modal" data-bs-target="#editModal" data-id="${banner.id}" title="Edit banner">
                                <i class="bx bx-pencil-square bi-lg text-dark"></i>
                            </a>
                            <a href="javascript:;" class="text-secondary font-weight-bold text-xs delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="${banner.id}" data-title="${banner.judul || 'Tanpa Judul'}" title="Hapus banner">
                                <i class="bx bx-trash bi-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                `;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Setup FilePond
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType
            );

            const csrfToken = "{{ csrf_token() }}";

            // FilePond untuk modal create
            const createPond = FilePond.create(document.querySelector('#create_img_banner'), {
                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                labelMaxFileSize: 'Ukuran file maksimum adalah 2MB',
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                labelFileTypeNotAllowed: 'Jenis file tidak valid. Hanya PNG, JPG, WEBP, dan SVG yang diizinkan.',
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
            });

            // AJAX untuk form create
            const createForm = document.getElementById('createBannerForm');
            const submitCreateBtn = document.getElementById('submitCreateBtn');

            submitCreateBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const formData = new FormData(createForm);
                const pondFile = createPond.getFile();
                if (pondFile) {
                    formData.set('img_banner', pondFile.serverId);
                }

                // Reset error states
                createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

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
                            const tableBody = document.getElementById('banner-table-body');
                            const newRowHtml = createTableRow(data.data);
                            tableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                            document.getElementById('banner-row-empty')?.remove();

                            const modalInstance = bootstrap.Modal.getInstance(document.getElementById(
                                'createModal'));
                            modalInstance.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }).catch(error => {
                        // Coba parse error sebagai JSON
                        error.json().then(errorData => {
                            if (errorData && errorData.errors) {
                                Object.keys(errorData.errors).forEach(key => {
                                    const input = createForm.querySelector(
                                        `[name="${key}"]`);
                                    // Gunakan selector yang lebih umum untuk error div
                                    const errorDiv = createForm.querySelector(
                                        `#${key}-error`);
                                    if (input) input.classList.add('is-invalid');
                                    if (errorDiv) errorDiv.textContent = errorData
                                        .errors[key][0];
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errorData.message ||
                                        'Terjadi kesalahan server.'
                                });
                            }
                        }).catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan yang tidak diketahui.'
                            });
                        });
                    });
            });

            // Logika tombol batalkan di modal create
            const cancelCreateBtn = document.getElementById('cancel-create-button');
            cancelCreateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('createModal'));
                createForm.reset();
                createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                createForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                createPond.removeFiles().then(() => {
                    modalInstance.hide();
                });
            });

            // FilePond untuk modal edit
            let editPond;
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editBannerForm');

            // Tambahkan event listener untuk membersihkan FilePond saat modal ditutup.
            editModal.addEventListener('hidden.bs.modal', function() {
                if (editPond) {
                    editPond.destroy();
                    editPond = null;
                }
                // Reset form dan error states
                editForm.reset();
                editForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                editForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            });

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const bannerId = button.getAttribute('data-id');
                editForm.dataset.updateUrl = `/banner/${bannerId}`;

                fetch(`/banner/${bannerId}/json`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_title').value = data.judul;
                        document.getElementById('edit_url_tujuan').value = data.url_tujuan;
                        document.getElementById('edit_is_active').checked = data.is_active;
                        document.getElementById('edit_posisi').value = data.posisi;
                        document.getElementById('edit_urutan').value = data.urutan;

                        const pondFiles = [];
                        if (data.img_banner) {
                            pondFiles.push(`/storage/${data.img_banner}`);
                        }


                        editPond = FilePond.create(document.querySelector('#edit_img_banner'), {
                            labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                            allowImagePreview: true,
                            acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp',
                                'image/svg+xml'
                            ],
                            files: pondFiles, // Muat file yang sudah ada
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
                                }
                            }
                        });

                        // --- Fitur Keamanan: Nonaktifkan tombol simpan saat upload ---
                        const submitEditBtn = editForm.querySelector('#submitEditBtn');
                        const pondEditInput = document.querySelector('#edit_img_banner');

                        pondEditInput.addEventListener('FilePond:addfile', (e) => {
                            // Nonaktifkan tombol saat file ditambahkan dan mulai diunggah
                            submitEditBtn.disabled = true;
                            submitEditBtn.innerHTML =
                                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...`;
                        });

                        pondEditInput.addEventListener('FilePond:processfile', (e) => {
                            // Aktifkan kembali setelah proses selesai
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML = 'Simpan Perubahan';
                        });

                        pondEditInput.addEventListener('FilePond:removefile', (e) => {
                            // Aktifkan kembali jika file dibatalkan/dihapus
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML = 'Simpan Perubahan';
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching banner data:', error);
                        bootstrap.Modal.getInstance(editModal).hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Data',
                            text: 'Tidak dapat mengambil data banner. Silakan coba lagi.'
                        });
                    });
            });

            // AJAX untuk form edit
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(editForm);
                formData.append('_method', 'PUT'); // Method spoofing
                const updateUrl = editForm.dataset.updateUrl;

                // Dapatkan file yang diproses oleh FilePond
                if (editPond) {
                    const pondFile = editPond.getFile();
                    if (pondFile && pondFile.status === FilePond.FileStatus.PROCESSING_COMPLETE) {
                        // Jika ada file baru yang diunggah, tambahkan serverId-nya ke FormData
                        formData.set('img_banner', pondFile.serverId);
                    } else if (!pondFile) {
                        // Jika tidak ada file sama sekali di FilePond (dihapus), kirim nilai kosong
                        formData.set('img_banner', null);
                    } else {
                        // Jika file ada tapi tidak diubah, hapus dari form data agar backend tidak mengubahnya
                        formData.delete('img_banner');
                    }
                }


                fetch(updateUrl, {
                        method: 'POST', // Form method spoofing
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const updatedRow = createTableRow(data.data);
                            const oldRow = document.getElementById(`banner-row-${data.data.id}`);
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
                            // Handle validation errors
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    const input = editForm.querySelector(`[name="${key}"]`);
                                    const errorDiv = editForm.querySelector(
                                        `#edit-${key}-error`);
                                    if (input) input.classList.add('is-invalid');
                                    if (errorDiv) errorDiv.textContent = data.errors[key][0];
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan.'
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
                    });
            });

            // --- MODAL DELETE ---
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteBannerForm');
            const deleteModalInstance = new bootstrap.Modal(deleteModal);
            let bannerIdToDelete = null;

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                bannerIdToDelete = button.getAttribute('data-id');
                const bannerTitle = button.getAttribute('data-title');
                document.getElementById('delete-banner-title').textContent = bannerTitle || 'banner ini';
            });

            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!bannerIdToDelete) return;

                const url = `/banner/${bannerIdToDelete}`;

                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        deleteModalInstance.hide();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            document.getElementById(`banner-row-${bannerIdToDelete}`).remove();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan.'
                            });
                        }
                    }).catch(error => {
                        deleteModalInstance.hide();
                        Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
                    });
            });

            // Inisialisasi semua popover di halaman
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl)
            })
        });
    </script>
@endsection
@endsection
