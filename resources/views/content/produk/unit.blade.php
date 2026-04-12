@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <div class="container-fluid p-3 ">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-n1">Data Satuan</h6>
                        <p class="text-sm mb-0">Kelola Data Satuan Productmu</p>
                    </div>
                    <div class="ms-auto mt-2">
                        {{-- triger-modal --}}
                        <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#import">
                            <i class="bx bx-plus cursor-pointer pe-2"></i>Satuan
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="filter-container">
                    <div class="row g-3 align-items-center justify-content-between">
                        <!-- Filter Pencarian Satuan -->
                        <div class="col-5 col-lg-3 ms-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari Satuan..."
                                value="{{ request('search') }}">
                        </div>
                        <!-- Filter Dropdown Status -->
                        <div class="col-5 col-lg-2 me-3">
                            <select id="statusFilter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1" @selected(request('status') == '1')>Aktif</option>
                                <option value="0" @selected(request('status') == '0')>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{-- Container untuk tabel yang akan di-update oleh AJAX --}}
                <div id="unit-table-container">
                    {{-- Memuat tabel parsial untuk tampilan awal --}}
                    @include('content.produk._unit_table', ['units' => $units])
                </div>
            </div>
        </div>

        {{-- modal-create --}}
        <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title" id="ModalLabel">Buat Satuan Baru</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('unit.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group">

                                    <div class="mb-3">
                                        <label for="name" class="form-label ">Nama Satuan</label>
                                        <input id="name" name="name" type="string"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input id="slug" name="slug" type="string"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            value="{{ old('slug') }}" required>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="singkat" class="form-label">Nama Pendek</label>
                                        <input id="singkat" name="singkat" type="string"
                                            class="form-control @error('singkat') is-invalid @enderror"
                                            value="{{ old('singkat') }}" required>
                                        @error('singkat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="justify-content-end form-check form-switch form-check-reverse">
                                        <label class="me-auto form-check-label" for="status">Status</label>
                                        <input id="status" class="form-check-input" type="checkbox" name="status"
                                            value="1" checked>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer border-0 pb-0">
                                <button type="submit" class="btn btn-outline-info btn-sm">Buat Satuan</button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-dismiss="modal">Batalkan</button>
                            </div>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const hasError = document.querySelector('.is-invalid');
                                if (hasError) {
                                    var importModal = new bootstrap.Modal(document.getElementById('import'));
                                    importModal.show();
                                }
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
        {{-- modal edit --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title" id="editModalLabel">Edit Satuan Product</h6>
                        <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUnitForm" method="post">
                            @method('put')
                            @csrf
                            <div class="row">
                                <div class="form-group">
                                    <div class="mb-3">
                                        <label for="edit_name" class="form-label">Nama</label>
                                        <input id="edit_name" name="name" type="text" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_slug" class="form-label">Slug</label>
                                        <input id="edit_slug" name="slug" type="text" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_singkat" class="form-label">Nama Pendek</label>
                                        <input id="edit_singkat" name="singkat" type="text" class="form-control"
                                            required>
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
                        <p class="mb-0">Apakah Anda yakin ingin menghapus Satuan ini?</p>
                        <h6 class="mt-2" id="unitNameToDelete"></h6>
                        <div class="mt-4">
                            <form id="deleteUnitForm" method="POST" action="#">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';

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
                    let url = '{{ route('unit.index') }}';

                    $('#unit-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            status: status,
                            page: page
                        },
                        success: function(data) {
                            $('#unit-table-container').html(data).css('opacity', 1);
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&status=' + status
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&status=' + status);
                        },
                        error: function() {
                            $('#unit-table-container').css('opacity', 1);
                            window.showToast('error', 'Gagal memuat data. Silakan coba lagi.');
                        }
                    });
                }

                $('#searchInput').on('keyup', debounce(function() {
                    fetchData(1);
                }, 500));
                $('#statusFilter').on('change', function() {
                    fetchData(1);
                });
                $(document).on('click', '#unit-table-container .pagination a', function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    if (page) {
                        fetchData(page);
                    }
                });
            });


            // slug
            const name = document.querySelector('#name ')
            const slug = document.querySelector('#slug')

            name.addEventListener('change', function() {
                fetch('/dashboard/unit/chekSlug?name=' + name.value)
                    .then(response => response.json())
                    .then(data => slug.value = data.slug)
            });

            // delete
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    // Ambil 'slug' dari atribut data-*
                    const unitSlug = button.getAttribute('data-unit-slug');
                    const unitName = button.getAttribute('data-unit-name');
                    const modalBodyName = deleteModal.querySelector('#unitNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deleteUnitForm');
                    modalBodyName.textContent = unitName;
                    // Atur action form menggunakan slug
                    deleteForm.action = `{{ url('unit') }}/${unitSlug}`;
                });
            }
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget; // Tombol yang di-klik

                    // Ambil URL dari atribut data-*
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    // Ambil elemen form dan input di dalam modal edit
                    const editForm = document.getElementById('editUnitForm');
                    const inputNama = document.getElementById('edit_name');
                    const inputSlug = document.getElementById('edit_slug');
                    const inputSingkat = document.getElementById(
                        'edit_singkat'); // Pastikan ID ini ada di HTML
                    const inputStatus = document.getElementById('edit_status');

                    // Atur action form untuk update
                    editForm.action = updateUrl;

                    // Ambil data Unit dari server
                    fetch(dataUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Isi form modal dengan data yang diterima
                            inputNama.value = data.name;
                            inputSlug.value = data.slug;
                            inputSingkat.value = data.singkat;
                            inputStatus.checked = data.status == 1;
                        })
                        .catch(error => {
                            console.error('Error fetching unit data:', error);
                            // Opsional: tampilkan pesan error kepada pengguna
                        });
                });

                // Slug generator untuk form edit
                const editNama = document.querySelector('#edit_name');
                const editSlug = document.querySelector('#edit_slug');
                editNama.addEventListener('change', function() {
                    fetch('/dashboard/unit/chekSlug?name=' + editNama.value)
                        .then(response => response.json())
                        .then(data => editSlug.value = data.slug)
                });
            }
        });
    </script>
@endsection
@endsection
