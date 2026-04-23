@extends('layouts/contentNavbarLayout')
@section('title', 'Pemasukan')

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
                            <i class="bx bx-wallet fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Data Pemasukan</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotaluser">
                            {{ $incomes->total() }}
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
                        <div class="col-md-5">
                            <label class="form-label">Pencarian</label>
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Cari keterangan/referensi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filter Kategori</label>
                            <select id="kategoriFilter" class="form-select select2" data-placeholder="Semua Kategori">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoriFilters as $kategori)
                                    <option value="{{ $kategori->id }}" @selected(request('kategori_id') == $kategori->id)>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto ms-md-auto">
                            <button class="btn btn-outline-info mt-md-4 mb-0" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bx bx-plus me-2"></i>Income
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3 pt-2 mb-3">
                    <h5 class="mb-n1 fw-bold">Data Pemasukan</h5>
                    <p class="text-sm mb-0">Kelola riwayat pemasukanmu</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    {{-- Container target AJAX --}}
                    <div id="income-table-container">
                        @fragment('income-table-area')
                        <div class="table-responsive p-0">
                            <table class="table table-hover align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal & Ref</th>
                                        <th>Keterangan</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($incomes as $key => $income)
                                        <tr>
                                            <td>{{++$key}}</td>
                                            <td class="d-flex flex-column">
                                                <small>{{ \Carbon\Carbon::parse($income->tanggal)->format('d M Y') }}</small>
                                                <small>{{ $income->referensi }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $income->keterangan }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-success">{{ $income->transaction_category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <small class="text-success">Rp {{ number_format($income->jumlah, 0, ',', '.') }}</small>
                                            </td>
                                            {{-- Fix Tombol Aksi Tumpang Tindih (Flexbox & nowrap) --}}
                                            <td class="align-middle text-center text-nowrap">
                                                <div class="d-flex justify-content-center align-items-center gap-3">
                                                    <a href="#" class="text-info fw-bold text-xs" data-bs-toggle="modal"
                                                        data-bs-target="#viewModal" data-url="{{ route('income.getjson', $income->referensi) }}" title="Lihat Detail">
                                                        <i class="bx bx-show fs-6"></i>
                                                    </a>
                                                    <a href="#" class="text-dark fw-bold text-xs" data-bs-toggle="modal"
                                                        data-bs-target="#editModal" data-url="{{ route('income.getjson', $income->referensi) }}" 
                                                        data-update-url="{{ route('income.update', $income->referensi) }}" title="Edit Income">
                                                        <i class="bx bx-edit fs-6 opacity-10"></i>
                                                    </a>
                                                    <a href="#" class="text-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteConfirmationModal" data-income-referensi="{{ $income->referensi }}"
                                                        data-income-name="{{ $income->keterangan }}" title="Hapus Income">
                                                        <i class="bx bx-trash fs-6"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <span class="text-muted text-sm">Data income tidak ditemukan.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="my-3 ms-3">
                                {{ $incomes->onEachSide(1)->links() }}
                            </div>
                        </div>
                        @endfragment
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal-create --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="ModalLabel">Buat Income Baru</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('income.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="transaction_category_id" class="form-label">Kategori</label>
                                <select name="transaction_category_id" id="transaction_category_id"
                                    class="form-select select2 @error('transaction_category_id') is-invalid @enderror" required>
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
                            <label for="keterangan" class="form-label">Income Untuk</label>
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
                    <h6 class="modal-title" id="editModalLabel">Edit Income</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editIncomeForm" method="post">
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
                                <label for="edit_referensi" class="form-label">Referensi (Opsional)</label>
                                <input id="edit_referensi" name="referensi" type="text" class="form-control"
                                    readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Income Untuk</label>
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
                    <h6 class="modal-title" id="viewModalLabel">Detail Income</h6>
                    <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-6">
                            <p id="view_referensi" class="mb-0 fw-bolder"></p>
                            <p id="view_tanggal" class="text-xs"></p>
                        </div>
                        <div class="col-md-4 col-6">
                            <p class="text-sm mb-1"><strong>Kategori:</strong></p>
                            <p id="view_kategori"></p>
                        </div>
                        <div class="col-md-4 col-6">
                            <p class="text-sm mb-1"><strong>Jumlah Income:</strong></p>
                            <p id="view_jumlah" class=" text-success"></p>
                        </div>
                        <div class="col-md-12 col-6">
                            <p class="text-sm mb-1"><strong>Income Untuk:</strong></p>
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
                    <p class="mb-0">Apakah Anda yakin ingin menghapus income ini?</p>
                    <h6 class="mt-2" id="incomeNameToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteIncomeForm" method="POST" action="#">
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
                // --- INISIALISASI QUILL ---
                let quillCreate, quillEdit;
    
                // Inisialisasi Quill untuk modal CREATE
                const hiddenInputCreate = document.getElementById('description-create');
                quillCreate = new Quill('#quill-editor-create', {
                    theme: 'snow',
                    placeholder: 'Tulis detail income di sini...',
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
                    placeholder: 'Tulis detail income di sini...',
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
    
                        const editForm = document.getElementById('editIncomeForm');
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
                            .catch(error => console.error('Error fetching income data:', error));
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
                                const userNama = data.user?.name || 'Tidak diketahui';
                                const userImg = data.user?.img_user ?
                                    `{{ asset('storage') }}/${data.user.img_user}` :
                                    `{{ asset('assets/img/user.webp') }}`;
    
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
                            .catch(error => console.error('Error fetching income data for view:', error));
                    });
                }
    
                // --- MODAL DELETE ---
                const deleteModal = document.getElementById('deleteConfirmationModal');
                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const incomeReferensi = button.getAttribute('data-income-referensi');
                        const incomeName = button.getAttribute('data-income-name');
    
                        const modalBodyName = deleteModal.querySelector('#incomeNameToDelete');
                        const deleteForm = deleteModal.querySelector('#deleteIncomeForm');
    
                        modalBodyName.textContent = incomeName;
                        deleteForm.action = `/income/${incomeReferensi}`;
                    });
                }
    
                // --- AJAX PRO: FILTER, SEARCH, & PAGINATION ---
                const searchInput = $('#searchInput');
                const kategoriFilter = $('#kategoriFilter');
                const tableContainer = $('#income-table-container');
                const totalCounter = $('#resumeTotaluser');
                
                let typingTimer;
                const doneTypingInterval = 500;
    
                function fetchIncome(targetUrl = null) {
                    let url = targetUrl;
                    if (!url) {
                        url = "{{ route('income.index') }}?" + $.param({
                            search: searchInput.val(),
                            kategori_id: kategoriFilter.val()
                        });
                    }
    
                    $.ajax({
                        url: url,
                        type: "GET",
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        beforeSend: function() {
                            tableContainer.css('opacity', '0.4');
                        },
                        success: function(response) {
                            tableContainer.html(response.html);
                            totalCounter.text(response.total);
                            tableContainer.css('opacity', '1');
                            window.history.pushState(null, '', url);
                        },
                        error: function(xhr) {
                            console.error("Gagal mengambil data AJAX", xhr);
                            tableContainer.css('opacity', '1');
                        }
                    });
                }
    
                searchInput.on('keyup', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => fetchIncome(), doneTypingInterval);
                });
    
                kategoriFilter.on('change', () => fetchIncome());
    
                // Hijack Pagination Clicks
                $(document).on('click', '#income-table-container .pagination a', function(e) {
                    e.preventDefault();
                    fetchIncome($(this).attr('href'));
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
