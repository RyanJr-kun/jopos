@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endsection

@section('content')

    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-view-list fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Garansi</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalUnit">
                            {{ method_exists($warranties, 'total') ? $warranties->total() : $warranties->count() }}
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
                            {{-- triger-modal-create --}}
                            <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import"><i
                                    class="bx bx-plus fixed-plugin-button-nav cursor-pointer pe-2"></i>Garansi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3 pt-2 mb-3">

                    <h5 class="mb-n1 fw-bolder">Data Warrantie</h5>
                    <p class="text-sm mb-0">
                        Kelola data garansimu
                    </p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div id="garansi-table-container">
                        @include('inventory::master.garansi._garansi_table', ['warranties' => $warranties])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal-create --}}
    <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n2">
                    <h6 class="modal-title" id="ModalLabel">Buat Warrantie Baru</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createWarrantieForm" action="{{ route('garansi.store') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label ">Nama</label>
                            <input id="name" name="name" type="string"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
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
                                    <label for="duration" class="form-label">Durasi</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                        id="duration" name="duration" value="{{ old('duration') }}" min="0">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="period" class="form-label"> Periode </label>
                                    <select class="form-select select2" id="period" name="period" required>
                                        <option value="Day">Hari</option>
                                        <option value="Week">Minggu</option>
                                        <option value="Month">Bulan</option>
                                        <option value="Year">Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <div id="quill-editor-create" style="min-height: 100px;">{!! old('description') !!}</div>
                            <div class="text-end text-muted small" id="counter-create">0/60</div>
                            <input type="hidden" name="description" id="description-create"
                                value="{{ old('description') }}">
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-2 justify-content-end form-check form-switch form-check-reverse">
                            <label class="me-auto form-check-label" for="status">Status</label>
                            <input id="status" class="form-check-input" type="checkbox" name="status"
                                value="1" checked>
                        </div>
                        <div class="modal-footer border-0 pb-0">
                            <button type="submit" id="submit-create-button" class="btn btn-outline-info btn-sm">Buat
                                Warrantie</button>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Warrantie </h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editWarrantieForm" method="post" action="">
                        @method('put')
                        @csrf
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama</label>
                            <input id="edit_name" name="name" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_slug" class="form-label">Slug</label>
                            <input id="edit_slug" name="slug" type="text" class="form-control" required readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="edit_duration" class="form-label">Durasi</label>
                                    <input type="number" class="form-control" id="edit_duration" name="duration"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="edit_period" class="form-label">Periode</label>
                                    <select class="form-select select2" id="edit_period" name="period" required>
                                        <option value="Day">Hari</option>
                                        <option value="Week">Minggu</option>
                                        <option value="Month">Bulan</option>
                                        <option value="Year">Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <div id="quill-editor-edit" style="min-height: 100px;"></div>
                            <div class="text-end text-muted small" id="counter-edit">0/60</div>
                            <input type="hidden" name="description" id="description-edit">
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
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center mt-3 mx-n5">
                    <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                    <p class="mb-0">Apakah Anda yakin ingin menghapus garansi ini?</p>
                    <h6 class="mt-2" id="garansiNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteWarrantieForm" method="POST" action="#">
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
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
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
            // --- QUILL EDITOR SETUP ---
            const maxLength = 60;
            let quillCreate, quillEdit;

            function handleTextChange(quill, counterElement, hiddenInputElement) {
                const length = quill.getText().length - 1;
                counterElement.textContent = `${length}/${maxLength}`;

                if (length > maxLength) {
                    quill.deleteText(maxLength, length);
                    counterElement.classList.add('text-danger');
                } else {
                    counterElement.classList.remove('text-danger');
                }
                hiddenInputElement.value = quill.root.innerHTML;
            }

            // Init Quill Create
            const counterCreate = document.getElementById('counter-create');
            const hiddenInputCreate = document.getElementById('description-create');
            quillCreate = new Quill('#quill-editor-create', {
                theme: 'snow',
                placeholder: 'Tulis description di sini...',
            });
            quillCreate.on('text-change', () => handleTextChange(quillCreate, counterCreate, hiddenInputCreate));
            if (hiddenInputCreate.value) {
                quillCreate.root.innerHTML = hiddenInputCreate.value;
            }

            // Init Quill Edit
            const counterEdit = document.getElementById('counter-edit');
            const hiddenInputEdit = document.getElementById('description-edit');
            quillEdit = new Quill('#quill-editor-edit', {
                theme: 'snow',
                placeholder: 'Tulis description di sini...',
            });
            quillEdit.on('text-change', () => handleTextChange(quillEdit, counterEdit, hiddenInputEdit));


            // --- AUTO SLUG & MODAL RE-OPEN ---
            const createModalEl = document.getElementById('import');
            if (createModalEl) {
                const createNamaInput = document.getElementById('name');
                const createSlugInput = document.getElementById('slug');

                createNamaInput.addEventListener('change', function() {
                    fetch(`/dashboard/garansi/chekSlug?name=${this.value}`)
                        .then(response => response.json())
                        .then(data => createSlugInput.value = data.slug);
                });

                // Buka kembali modal jika ada error validasi saat reload
                if (document.querySelector('#createWarrantieForm .is-invalid')) {
                    new bootstrap.Modal(createModalEl).show();
                }
            }

            // --- MODAL DELETE LOGIC ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) return;
                    const garansiSlug = button.getAttribute('data-garansi-slug');
                    const garansiName = button.getAttribute('data-garansi-name');

                    this.querySelector('#garansiNameToDelete').textContent = garansiName;
                    this.querySelector('#deleteWarrantieForm').action = `/garansi/${garansiSlug}`;
                });
            }

            // --- MODAL EDIT LOGIC ---
            const editModal = document.getElementById('editModal');
            if (editModal) {
                const editForm = editModal.querySelector('#editWarrantieForm');
                const inputNama = editModal.querySelector('#edit_name');
                const inputSlug = editModal.querySelector('#edit_slug');
                const inputDurasi = editModal.querySelector('#edit_duration');
                const selectPeriod = editModal.querySelector('#edit_period');
                const inputStatus = editModal.querySelector('#edit_status');

                // Buka kembali modal edit jika ada error validasi
                if (document.querySelector('#editWarrantieForm .is-invalid')) {
                    new bootstrap.Modal(editModal).show();
                }

                // Auto slug untuk Edit
                inputNama.addEventListener('change', function() {
                    fetch(`/dashboard/garansi/chekSlug?name=${this.value}`)
                        .then(response => response.json())
                        .then(data => inputSlug.value = data.slug);
                });

                // Fetch data saat modal edit dibuka
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    // Set action form untuk submit biasa
                    editForm.action = updateUrl;

                    fetch(dataUrl)
                        .then(response => response.json())
                        .then(data => {
                            inputNama.value = data.name;
                            inputSlug.value = data.slug;
                            inputStatus.checked = data.status == 1;

                            const totalDays = data.duration;
                            if (totalDays && totalDays > 0) {
                                if (totalDays % 360 === 0) {
                                    $(selectPeriod).val('Year').trigger('change');
                                    inputDurasi.value = totalDays / 360;
                                } else if (totalDays % 30 === 0) {
                                    $(selectPeriod).val('Month').trigger('change');
                                    inputDurasi.value = totalDays / 30;
                                } else if (totalDays % 7 === 0) {
                                    $(selectPeriod).val('Week').trigger('change');
                                    inputDurasi.value = totalDays / 7;
                                } else {
                                    $(selectPeriod).val('Day').trigger('change');
                                    inputDurasi.value = totalDays;
                                }
                            }

                            quillEdit.root.innerHTML = data.description || '';
                            handleTextChange(quillEdit, counterEdit, hiddenInputEdit);
                        })
                        .catch(error => console.error('Error fetching garansi data:', error));
                });
            }

            // --- AJAX FILTER & SEARCH ---
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
                        let url = '{{ route('garansi.index') }}';

                        $('#garansi-table-container').css('opacity', 0.5);

                        $.ajax({
                            url: url,
                            type: 'GET',
                            data: {
                                search: search,
                                status: status,
                                page: page
                            },
                            success: function(data) {
                                $('#garansi-table-container').html(data).css('opacity', 1);

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
                        let urlObj = new URL($(this).attr('href'));
                        let page = urlObj.searchParams.get('page');
                        if (page) fetchData(page);
                    });
                });
            }
        });
    </script>
@endsection
