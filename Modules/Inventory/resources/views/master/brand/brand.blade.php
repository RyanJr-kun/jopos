@extends('layouts/contentNavbarLayout')
@section('title', 'Cards basic - UI elements')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection

@section('content')

    <div class="row g-3 align-items-stretch">

        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-brand fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Brand</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalBrand">
                            {{ method_exists($brands, 'total') ? $brands->total() : $brands->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Tombol Tambah --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="row g-3 align-items-center justify-content-start w-100 m-0">
                        <div class="col-md-4">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari kategori..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 me-3">
                            <select name="status" id="statusFilter" class="form-select select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-auto ms-md-auto">
                            <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import">
                                <i class="bx bx-plus cursor-pointer pe-2"></i>Brand
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header p-4">
                    <h5 class="mb-n1 fw-bolder">List Brand</h5>
                    <p class="text-sm mb-0">Kelola Data Brandmu</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div id="brand-table-container">
                        @include('inventory::master.brand._brand_table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal-create --}}
    <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="ModalLabel">Buat Data Brand Baru</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createBrandForm" method="POST" action="{{ route('brand.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <p class="text-dark fw-bold ">Gambar Brand:</p>
                                <input type="file" class="filepond" name="img_brand" id="img_brand_create">
                            </div>
                            <div class="col-md-6">
                                <div class="mt-md-4">
                                    <label for="name" class="form-label">Brand</label>
                                    <input id="name" name="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input id="slug" name="slug" type="text"
                                        class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}"
                                        required>
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
                            <button type="submit" id="submit-create-button" class="btn btn-outline-info btn-sm">Buat
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
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBrandForm" method="POST" action="" enctype="multipart/form-data">
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
                                    <label for="edit_name" class="form-label">Nama</label>
                                    <input id="edit_name" name="name" type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_slug" class="form-label">Slug</label>
                                    <input id="edit_slug" name="slug" type="text" class="form-control" required>
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
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
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

@endsection
@section('page-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            // --- FILEPOND SETUP ---
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType,
                FilePondPluginImageTransform
            );

            // Setup FilePond Modal Create
            const createPond = FilePond.create(document.querySelector('#img_brand_create'), {
                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                imageCropAspectRatio: '1:1',
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                server: {
                    process: {
                        url: '/dashboard/brand/upload',
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

            // --- MODAL CREATE LOGIC ---
            const createModal = document.getElementById('import');
            if (createModal) {
                const nameInput = createModal.querySelector('#name');
                const slugInput = createModal.querySelector('#slug');

                // Auto-slug
                nameInput.addEventListener('change', function() {
                    fetch(`/dashboard/brand/chekSlug?name=${nameInput.value}`)
                        .then(response => response.json())
                        .then(data => slugInput.value = data.slug);
                });

                // Re-open modal jika ada error validasi dari controller
                const hasError = document.querySelector('.is-invalid');
                if (hasError) {
                    var createModalInstance = new bootstrap.Modal(createModal);
                    createModalInstance.show();
                }

                // Bersihkan filepond jika modal ditutup/dibatalkan
                const cancelCreateBtn = createModal.querySelector('#cancel-create-button');
                if (cancelCreateBtn) {
                    cancelCreateBtn.addEventListener('click', function(e) {
                        const createForm = document.getElementById('createBrandForm');
                        createForm.reset();
                        createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                            'is-invalid'));
                        createPond.removeFiles().then(() => {
                            bootstrap.Modal.getInstance(createModal).hide();
                        });
                    });
                }
            }

            // --- MODAL EDIT LOGIC ---
            const editModal = document.getElementById('editModal');
            let editPond = null;

            if (editModal) {
                const editForm = editModal.querySelector('#editBrandForm');
                const inputNama = editModal.querySelector('#edit_name');
                const inputSlug = editModal.querySelector('#edit_slug');
                const inputStatus = editModal.querySelector('#edit_status');

                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    // Set Action Form untuk submit biasa
                    editForm.action = updateUrl;

                    // Ambil data untuk mengisi form
                    fetch(dataUrl)
                        .then(response => response.json())
                        .then(data => {
                            inputNama.value = data.name;
                            inputSlug.value = data.slug;
                            inputStatus.checked = data.status == 1;

                            const pondFiles = [];
                            if (data.img_brand) {
                                pondFiles.push(`/storage/${data.img_brand}`);
                            }

                            editPond = FilePond.create(document.querySelector('#img_brand_edit'), {
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
                                server: {
                                    process: {
                                        url: '/dashboard/brand/upload',
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

                            // Disable tombol submit saat upload berjalan
                            const submitEditBtn = editForm.querySelector('#submit-edit-button');
                            const pondEditInput = document.querySelector('#img_brand_edit');

                            pondEditInput.addEventListener('FilePond:addfile', () => {
                                submitEditBtn.disabled = true;
                                submitEditBtn.innerHTML =
                                    `<span class="spinner-border spinner-border-sm"></span> Mengunggah...`;
                            });
                            pondEditInput.addEventListener('FilePond:processfile', () => {
                                submitEditBtn.disabled = false;
                                submitEditBtn.innerHTML = 'Simpan Perubahan';
                            });
                        });
                });

                // Hancurkan instance filepond edit saat modal ditutup agar tidak duplikat
                editModal.addEventListener('hidden.bs.modal', function() {
                    if (editPond) {
                        editPond.destroy();
                        editPond = null;
                    }
                });

                // Auto-slug edit
                inputNama.addEventListener('change', function() {
                    fetch(`/dashboard/brand/chekSlug?name=${inputNama.value}`)
                        .then(response => response.json())
                        .then(data => inputSlug.value = data.slug);
                });
            }

            // --- AJAX FILTER & SEARCH ---
            // (Pastikan jQuery diload sebelum script ini)
            if (typeof $ !== 'undefined') {
                $(document).ready(function() {
                    function debounce(func, delay) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), delay);
                        };
                    }

                    function fetchData(page = 1) {
                        let search = $('#searchInput').val();
                        let status = $('#statusFilter').val();
                        let url = '{{ route('brand.index') }}';

                        $('#brand-table-container').css('opacity', 0.5);

                        $.ajax({
                            url: url,
                            type: 'GET',
                            data: {
                                search: search,
                                status: status,
                                page: page
                            },
                            success: function(data) {
                                $('#brand-table-container').html(data).css('opacity', 1);

                                // Update URL browser tanpa refresh
                                let newParams = new URLSearchParams();
                                if (page > 1) newParams.append('page', page);
                                if (search) newParams.append('search', search);
                                if (status) newParams.append('status', status);

                                let newUrl = url + (newParams.toString() ? '?' + newParams
                                    .toString() : '');
                                window.history.pushState({
                                    path: newUrl
                                }, '', newUrl);
                            },
                            error: function() {
                                $('#brand-table-container').css('opacity', 1);
                                alert('Gagal memuat data. Silakan coba lagi.');
                            }
                        });
                    }

                    // Trigger saat ketik di kolom pencarian
                    $('#searchInput').on('keyup', debounce(function() {
                        fetchData(1);
                    }, 500));

                    // Trigger saat Select2 berubah nilainya
                    $('#statusFilter').on('change', function() {
                        fetchData(1);
                    });

                    // Memastikan klik pagination juga memakai AJAX
                    $(document).on('click', '#brand-table-container .pagination a', function(e) {
                        e.preventDefault();
                        let urlObj = new URL($(this).attr('href'));
                        let page = urlObj.searchParams.get('page');
                        if (page) fetchData(page);
                    });
                });
            }
        });
    </script>
@endsection
