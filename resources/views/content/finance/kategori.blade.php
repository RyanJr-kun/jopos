@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endsection


<div class="row g-3 align-items-stretch">
    <div class="col-12 col-md-4 col-xl-3 mb-md-0">
        <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                        <i class="bx bx-category fs-4"></i>
                    </span>
                </div>
                <div>
                    <p class="text-white mb-0 text-sm">Total Kategori</p>
                    <h3 id="resumeTotaluser" class="mb-0 text-white">
                        {{ method_exists($kategoris, 'total') ? $kategoris->total() : $kategoris->count() }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
    {{-- Filter & Tombol Tambah --}}
    <div class="col-12 col-md-8 col-xl-9">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="row g-3 d-flex align-items-center justify-content-start w-100 m-0">
                    <div class="col-md-4">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Cari Kategori..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label">Filter Type</label>
                        <select id="typeFilter" name="type" class="form-select select2"
                            data-placeholder="Semua type">
                            <option value="">Semua type</option>
                            @foreach ($types as $typeName)
                                <option value="{{ $typeName }}"
                                    {{ request('type') == $typeName ? 'selected' : '' }}>
                                    {{ ucfirst($typeName) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Filter Status</label>
                        <select name="status" id="statusFilter" class="form-select select2"
                            data-placeholder="Semua Status">
                            <option value="">Semua Status</option>
                            <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                            <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-auto ms-md-auto">
                        <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import">
                            <i class="bx bx-plus fixed-plugin-button-nav cursor-pointer pe-2"></i> Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <h5 class="mb-n1 fw-bolder">Kategori Transaksi</h6>
                    <p class="text-sm mb-0">Kelola data Kategorimu</p>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div id="kategori-table-container">
                    @fragment('kategori-table-area')
                        <div class="table-responsive p-0 mt-2">
                            <table class="table table-hover align-items-center justify-content-start mb-0" id="tableData">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kategori</th>
                                        <th>Jenis</th>
                                        <th>Description</th>
                                        <th class="text-center ">Status</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody id="isiTable">
                                    @forelse ($kategoris as $key => $kategori)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                <small title="kategori">{{ $kategori->name }}</small>
                                            </td>
                                            <td>
                                                @if ($kategori->type == 'income')
                                                    <span
                                                        class="badge bg-label-success text-capitalize">{{ $kategori->type }}</span>
                                                @elseif ($kategori->type == 'expense')
                                                    <span
                                                        class="badge bg-label-warning text-capitalize">{{ $kategori->type }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small title="Description">
                                                    {{ Str::limit(strip_tags($kategori->description), 60) }}</small>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($kategori->status)
                                                    <span class="badge bg-label-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-nowrap">
                                                <div class="d-flex justify-content-center align-items-center gap-3">
                                                    <a href="#" class="text-dark fw-bold text-xs"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-url="{{ route('kategoritransaksi.getjson', $kategori->slug) }}"
                                                        data-update-url="{{ route('kategoritransaksi.update', $kategori->slug) }}"
                                                        title="Edit kategori">
                                                        <i class="bx bx-edit text-dark fs-6 opacity-10"></i>
                                                    </a>
                                                    <a href="#" class="text-danger delete-user-btn"
                                                        data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                        data-kategori-slug="{{ $kategori->slug }}"
                                                        data-kategori-name="{{ $kategori->name }}" title="Hapus kategori">
                                                        <i class="bx bx-trash fs-6"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 ">
                                                <p class=" text-dark text-sm fw-bold mb-0">Belum ada data Kategori
                                                    Transaksi.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="my-3 ms-3">
                                {{ $kategoris->onEachSide(1)->links() }}
                            </div>
                        </div>
                    @endfragment
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
                <h6 class="modal-title" id="ModalLabel">Buat kategori Baru</h6>
                <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('kategoritransaksi.store') }}" method="post">
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

                    <div class="mb-3">
                        <label for="type" class="form-label">Jenis Kategori</label>
                        <select class="select2 @error('type') is-invalid @enderror" id="type"
                            data-placeholder="Pilih Jenis Kategori" name="type" required>
                            @foreach ($types as $type)
                                <option value=""></option>
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editKategoriForm" method="post">
                    @method('put')
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama</label>
                                <input id="edit_name" name="name" type="text" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_slug" class="form-label">Slug</label>
                                <input id="edit_slug" name="slug" type="text" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_type" class="form-label">Jenis Kategori</label>
                                <select class="form-select select2" id="edit_type" name="type" required>
                                    <option value="" disabled>Pilih Jenis...</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
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
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
    aria-hidden="true">
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
        const hiddenInputCreate = document.getElementById('description-create');
        quillCreate = new Quill('#quill-editor-create', {
            theme: 'snow',
            placeholder: 'Tulis description di sini...',
        });
        quillCreate.on('text-change', () => handleTextChange(quillCreate, counterCreate, hiddenInputCreate));
        if (hiddenInputCreate.value) {
            quillCreate.root.innerHTML = hiddenInputCreate.value;
        }

        // Inisialisasi Quill untuk modal EDIT
        const counterEdit = document.getElementById('counter-edit');
        const hiddenInputEdit = document.getElementById('description-edit');
        quillEdit = new Quill('#quill-editor-edit', {
            theme: 'snow',
            placeholder: 'Tulis description di sini...',
        });
        quillEdit.on('text-change', () => handleTextChange(quillEdit, counterEdit, hiddenInputEdit));


        // --- MODAL CREATE ---
        const name = document.querySelector('#name ')
        const slug = document.querySelector('#slug')

        name.addEventListener('change', function() {
            fetch('/dashboard/kategoritransaksi/chekSlug?name=' + name.value)
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
                const inputNama = document.getElementById('edit_name');
                const inputSlug = document.getElementById('edit_slug');
                const inputStatus = document.getElementById('edit_status');
                const selectJenis = document.getElementById('edit_type');

                editForm.action = updateUrl;

                fetch(dataUrl)
                    .then(response => response.json())
                    .then(data => {
                        inputNama.value = data.name;
                        inputSlug.value = data.slug;
                        selectJenis.value = data.type;
                        inputStatus.checked = data.status == 1;

                        // FIX: Isi editor Quill dan input hidden secara eksplisit
                        quillEdit.root.innerHTML = data.description || '';
                        hiddenInputEdit.value = data.description ||
                            ''; // Baris ini memastikan data tersalin
                        handleTextChange(quillEdit, counterEdit,
                            hiddenInputEdit); // Panggil fungsi untuk update counter
                    })
                    .catch(error => console.error('Error fetching category data:', error));
            });

            const editNama = document.querySelector('#edit_name');
            const editSlug = document.querySelector('#edit_slug');
            editNama.addEventListener('change', function() {
                fetch(`/dashboard/kategoritransaksi/chekSlug?name=${this.value}`)
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

        // --- AJAX PRO: FILTER, SEARCH, & PAGINATION ---
        const searchInput = $('#searchInput');
        const typeFilter = $('#typeFilter');
        const statusFilter = $('#statusFilter');
        const tableContainer = $('#kategori-table-container'); // Container pembungkus tabel & pagination
        const totalCounter = $('#resumeTotaluser');

        let typingTimer;
        const doneTypingInterval = 500;

        // Fungsi fetch data utama
        function fetchKategori(targetUrl = null) {
            // Jika url kosong (dari ketikan/filter), buat ulang parameter URL-nya
            let url = targetUrl;
            if (!url) {
                url = "{{ route('kategoritransaksi.index') }}?" + $.param({
                    search: searchInput.val(),
                    type: typeFilter.val(),
                    status: statusFilter.val()
                });
            }

            $.ajax({
                url: url,
                type: "GET",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    tableContainer.css('opacity', '0.4');
                },
                success: function(response) {
                    // Inject Fragment
                    tableContainer.html(response.html);
                    // Update Angka Total di Header
                    totalCounter.text(response.total);
                    tableContainer.css('opacity', '1');

                    // Modifikasi URL Bar (agar saat direfresh tetap di halaman/filter yang sama)
                    window.history.pushState(null, '', url);
                },
                error: function(xhr) {
                    console.error("Gagal mengambil data AJAX", xhr);
                    tableContainer.css('opacity', '1');
                }
            });
        }

        // Trigger Pencarian (Debounce)
        searchInput.on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => fetchKategori(), doneTypingInterval);
        });

        // Trigger Dropdown (Langsung Fetch)
        typeFilter.on('change', () => fetchKategori());
        statusFilter.on('change', () => fetchKategori());

        // Trigger Pagination Klik (Ajax Pagination Interceptor)
        $(document).on('click', '#kategori-table-container .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href'); // Ambil link halaman tujuan (contoh: ?page=2)
            fetchKategori(url);
        });
    });
</script>
@endsection
