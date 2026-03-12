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
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Data Expense', 'url' => route('expense.index')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3 ">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Data Expense</h6>
                    <p class="text-sm mb-0">
                        Kelola expensemu
                    </p>
                </div>
                <div class="ms-md-auto mt-2">
                    {{-- triger-modal-create --}}
                    <button class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fa fa-plus fixed-plugin-button-nav cursor-pointer pe-2"></i> Expense
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="filter-container">
                <div class="row g-3 align-items-center justify-content-between">
                    <div class="col-5 col-lg-3 ms-3">
                        <input type="text" id="searchInput" name="search" class="form-control"
                            placeholder="Cari keterangan/referensi..." value="{{ request('search') }}">
                    </div>
                    <div class="col-5 col-lg-2 me-3">
                        <select id="kategoriFilter" name="kategori_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriFilters as $kategori)
                                <option value="{{ $kategori->id }}" @selected(request('kategori_id') == $kategori->id)>{{ $kategori->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div id="expense-table-container">
                @include('content.keuangan._expense_table', ['expenses' => $expenses])
            </div>
        </div>
    </div>

    {{-- modal-create --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="ModalLabel">Buat Expense Baru</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('expense.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="transaction_category_id" class="form-label">Kategori</label>
                                <select name="transaction_category_id" id="transaction_category_id"
                                    class="form-select @error('transaction_category_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($allKategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ old('transaction_category_id') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->name }}</option>
                                    @endforeach
                                </select>
                                @error('transaction_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input id="tanggal" name="tanggal" type="date"
                                    class="form-control @error('tanggal') is-invalid @enderror"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input id="jumlah" name="jumlah" type="number"
                                        class="form-control @error('jumlah') is-invalid @enderror"
                                        value="{{ old('jumlah') }}" required min="0">
                                </div>
                                @error('jumlah')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="referensi" class="form-label">Referensi</label>
                                <input id="referensi" name="referensi" type="text"
                                    class="form-control @error('referensi') is-invalid @enderror"
                                    value="{{ old('referensi', $referensi_otomatis) }}" readonly>
                                @error('referensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Expense Untuk</label>
                            <input id="keterangan" name="keterangan" type="text"
                                class="form-control @error('keterangan') is-invalid @enderror"
                                value="{{ old('keterangan') }}" required>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Detail</label>
                            <div id="quill-editor-create" style="min-height: 100px;">{!! old('description') !!}</div>
                            <input type="hidden" name="description" id="description-create"
                                value="{{ old('description') }}">
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer border-0 pb-0">
                            <button type="submit" class="btn btn-outline-info btn-sm">Simpan</button>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Expense</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editExpenseForm" method="post">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_transaction_category_id" class="form-label">Kategori</label>
                                <select name="transaction_category_id" id="edit_transaction_category_id"
                                    class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($allKategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_tanggal" class="form-label">Tanggal</label>
                                <input id="edit_tanggal" name="tanggal" type="date" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_jumlah" class="form-label">Jumlah</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input id="edit_jumlah" name="jumlah" type="number" class="form-control"
                                        required min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_referensi" class="form-label">Referensi</label>
                                <input id="edit_referensi" name="referensi" type="text" class="form-control"
                                    readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Expense Untuk</label>
                            <input id="edit_keterangan" name="keterangan" type="text" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Detail</label>
                            <div id="quill-editor-edit" style="min-height: 100px;"></div>
                            <input type="hidden" name="description" id="description-edit">
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

    {{-- modal view --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="viewModalLabel">Detail Expense</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 col-6">
                            <p id="view_referensi" class="mb-0 fw-bolder"></p>
                            <p id="view_tanggal" class="text-xs"></p>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="text-sm mb-1"><strong>Kategori:</strong></p>
                            <p id="view_kategori"></p>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="text-sm mb-1"><strong>Jumlah Expense:</strong></p>
                            <p id="view_jumlah" class=" text-danger"></p>
                        </div>
                        <div class="col-md-12 col-6">
                            <p class="text-sm mb-1"><strong>Expense Untuk:</strong></p>
                            <p id="view_keterangan" class=""></p>
                        </div>
                        <div class="col-12">
                            <p class="text-sm mb-1"><strong>Detail:</strong></p>
                            <div id="view_description" class="p-2 border rounded" style="min-height: 80px;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
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
                    <p class="mb-0">Apakah Anda yakin ingin menghapus expense ini?</p>
                    <h6 class="mt-2" id="expenseNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteExpenseForm" method="POST" action="#">
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
            // --- INISIALISASI QUILL ---
            let quillCreate, quillEdit;

            // Inisialisasi Quill untuk modal CREATE
            const hiddenInputCreate = document.getElementById('description-create');
            quillCreate = new Quill('#quill-editor-create', {
                theme: 'snow',
                placeholder: 'Tulis detail expense di sini...',
            });
            quillCreate.on('text-change', () => {
                hiddenInputCreate.value = quillCreate.root.innerHTML;
            });
            if (hiddenInputCreate.value) {
                quillCreate.root.innerHTML = hiddenInputCreate.value;
            }

            // Inisialisasi Quill untuk modal EDIT
            const hiddenInputEdit = document.getElementById('description-edit');
            quillEdit = new Quill('#quill-editor-edit', {
                theme: 'snow',
                placeholder: 'Tulis detail expense di sini...',
            });
            quillEdit.on('text-change', () => {
                hiddenInputEdit.value = quillEdit.root.innerHTML;
            });

            // --- MODAL EDIT ---
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    const editForm = document.getElementById('editExpenseForm');
                    editForm.action = updateUrl;

                    fetch(dataUrl)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('edit_keterangan').value = data.keterangan;
                            document.getElementById('edit_transaction_category_id').value = data
                                .transaction_category_id;
                            document.getElementById('edit_tanggal').value = data.tanggal;
                            document.getElementById('edit_jumlah').value = data.jumlah;
                            document.getElementById('edit_referensi').value = data.referensi;

                            // Isi editor Quill dan input hidden
                            quillEdit.root.innerHTML = data.description || '';
                            hiddenInputEdit.value = data.description || '';
                        })
                        .catch(error => console.error('Error fetching expense data:', error));
                });
            }

            // --- MODAL VIEW ---
            const viewModal = document.getElementById('viewModal');
            if (viewModal) {
                viewModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');

                    // Fungsi untuk memformat mata uang
                    const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(number);

                    // Fungsi untuk memformat tanggal
                    const formatDate = (dateString) => {
                        const options = {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        };
                        return new Date(dateString).toLocaleDateString('id-ID', options);
                    };

                    fetch(dataUrl)
                        .then(response => response.json())
                        .then(data => {
                            // Memuat relasi dari controller sudah memastikan data ini ada
                            const kategoriNama = data.transaction_category?.name ||
                            'Tidak ada kategori';

                            document.getElementById('view_referensi').textContent = data.referensi ||
                                '-';
                            document.getElementById('view_tanggal').textContent = formatDate(data
                                .tanggal);
                            document.getElementById('view_kategori').textContent = kategoriNama;
                            document.getElementById('view_jumlah').textContent = formatCurrency(data
                                .jumlah);
                            document.getElementById('view_keterangan').textContent = data.keterangan;
                            document.getElementById('view_description').innerHTML = data.description ||
                                '<p class="text-muted">Tidak ada detail.</p>';
                        })
                        .catch(error => console.error('Error fetching expense data for view:', error));
                });
            }


            // --- MODAL DELETE ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const expenseReferensi = button.getAttribute('data-expense-referensi');
                    const expenseName = button.getAttribute('data-expense-name');

                    const modalBodyName = deleteModal.querySelector('#expenseNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deleteExpenseForm');

                    modalBodyName.textContent = expenseName;
                    deleteForm.action = `/expense/${expenseReferensi}`;
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
                    let kategori_id = $('#kategoriFilter').val();
                    let url = '{{ route('expense.index') }}';

                    $('#expense-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            kategori_id: kategori_id,
                            page: page
                        },
                        success: function(data) {
                            $('#expense-table-container').html(data).css('opacity', 1);
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&kategori_id=' + kategori_id
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&kategori_id=' + kategori_id);
                        },
                        error: function() {
                            $('#expense-table-container').css('opacity', 1);
                            Swal.fire('Gagal', 'Gagal memuat data. Silakan coba lagi.',
                                'error');
                        }
                    });
                }

                $('#searchInput').on('keyup', debounce(function() {
                    fetchData(1);
                }, 500));
                $('#kategoriFilter').on('change', function() {
                    fetchData(1);
                });
                $(document).on('click', '#expense-table-container .pagination a', function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    if (page) {
                        fetchData(page);
                    }
                });
            });

            // --- SHOW CREATE MODAL ON VALIDATION ERROR ---
            const hasError = document.querySelector('.is-invalid');
            if (hasError) {
                var createModal = new bootstrap.Modal(document.getElementById('createModal'));
                createModal.show();
            }

        });
    </script>
@endsection
@endsection
