@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')

@section('content')
    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-group fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white mb-0 text-sm">Total Pemasok</p>
                        <h3 class="text-white mb-0 fw-bold" id="resumeTotalPemasok">
                            {{ method_exists($suppliers, 'total') ? $suppliers->total() : $suppliers->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="row g-3 align-items-center justify-content-start w-100 m-0">
                        <div class="col-12 col-sm-6 col-md-5">
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="Cari invoice atau pelanggan..." value="{{ request('search') }}">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <select name="status" id="statusFilter" class="form-select select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(request('status') == $status)>
                                        {{ $status == 1 ? 'Aktif' : 'Tidak Aktif' }}</option>
                                @endforeach
                            </select>
                        </div>
                        @can('create-pemasok')
                            <div class="col-12 col-md-auto ms-md-auto">
                                <button class="btn btn-info w-100 w-md-auto" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <i class="bx bx-plus cursor-pointer pe-1"></i>Pemasok
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-n1 fw-bolder">List Supplier</h5>
                            <p class="text-sm mb-0">Kelola data pemasokmu.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-4">
                    <div id="pemasok-table-container">
                        @include('inventory::pembelian.partials._pemasok_table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal-create --}}
    @can('create-pemasok')
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title">Tambah Supplier Baru</h6>
                        <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- PERBAIKAN: Form ini sekarang akan melakukan submit standar (full page refresh) --}}
                        <form action="{{ route('pemasok.store') }}" method="post">
                            @csrf
                            <x-pemasok-form-fields />
                            <div class="justify-content-end form-check form-switch form-check-reverse my-2">
                                <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                                <input id="status" class="form-check-input" type="checkbox" name="status" value="1"
                                    checked>
                            </div>
                            <div class="modal-footer border-0 pb-0">
                                <button type="submit" class="btn btn-outline-info btn-sm">Buat Supplier</button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Batalkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- modal edit --}}
    @can('edit-pemasok')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 mb-n3">
                        <h6 class="modal-title" id="editModalLabel">Edit Supplier</h6>
                        <button type="button" class="btn btn-close rounded-3 me-1" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editSupplierForm" method="post">
                            @method('put')
                            @csrf
                            <x-pemasok-form-fields prefix="edit_" :pemasok="new \Modules\Inventory\Models\Supplier()" />
                            <div class="justify-content-end form-check form-switch form-check-reverse mt-3">
                                <label class="me-auto form-check-label" for="edit_status">Status</label>
                                <input id="edit_status" class="form-check-input" type="checkbox" name="status" value="1">
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
    @endcan

    {{-- modal delete --}}
    @can('delete-pemasok')
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center mt-3 mx-n5">
                        <i class="bx bx-trash fa-2x text-danger mb-3"></i>
                        <p class="mb-0">Apakah Anda yakin ingin menghapus pemasok ini?</p>
                        <h6 class="mt-2" id="pemasokNameToDelete"></h6>
                        <div class="mt-4">
                            <form id="deleteSupplierForm" method="POST" action="#">
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
    @endcan

@endsection

@section('page-script')
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
            // Inisialisasi semua popover di halaman
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    sanitize: false // Penting: Agar ikon Boxicons (<i>) dan elemen layout HTML tidak dihapus oleh sistem keamanan Bootstrap
                })
            });

            // (Opsional) Menutup popover lain saat satu popover diklik
            $('body').on('click', function(e) {
                $('[data-bs-toggle="popover"]').each(function() {
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover')
                        .has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });

            // --- MODAL EDIT ---
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const dataUrl = button.getAttribute('data-url');
                    const updateUrl = button.getAttribute('data-update-url');

                    const editForm = document.getElementById('editSupplierForm');
                    const inputNama = document.getElementById('edit_name');
                    const inputPerusahaan = document.getElementById('edit_perusahaan');
                    const inputKontak = document.getElementById('edit_kontak');
                    const inputEmail = document.getElementById('edit_email');
                    const inputAlamat = document.getElementById('edit_alamat');
                    const inputNote = document.getElementById('edit_note');
                    const inputStatus = document.getElementById('edit_status');

                    editForm.action = updateUrl;

                    fetch(dataUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            inputNama.value = data.name;
                            inputPerusahaan.value = data.perusahaan;
                            inputKontak.value = data.kontak;
                            inputEmail.value = data.email;
                            inputAlamat.value = data.alamat;
                            inputNote.value = data.note;
                            inputStatus.checked = data.status == 1;
                        })
                        .catch(error => console.error('Error fetching pemasok data:', error));
                });
            }

            // --- MODAL DELETE ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const pemasokId = button.getAttribute('data-pemasok-id');
                    const pemasokName = button.getAttribute('data-pemasok-name');
                    const modalBodyName = deleteModal.querySelector('#pemasokNameToDelete');
                    const deleteForm = deleteModal.querySelector('#deleteSupplierForm');

                    modalBodyName.textContent = pemasokName;
                    // Pastikan route untuk delete sudah benar, contoh: /pemasok/{id}
                    deleteForm.action = `{{ url('pemasok') }}/${pemasokId}`;
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
                    let url = '{{ route('pemasok.index') }}';

                    $('#pemasok-table-container').css('opacity', 0.5); // Efek loading

                    $.ajax({
                        url: url,
                        data: {
                            search: search,
                            status: status,
                            page: page
                        },
                        success: function(data) {
                            $('#pemasok-table-container').html(data).css('opacity', 1);
                            // Update URL di browser
                            window.history.pushState({
                                    path: url + '?page=' + page + '&search=' + search +
                                        '&status=' + status
                                }, '', url + '?page=' + page + '&search=' + search +
                                '&status=' + status);
                        },
                        error: function() {
                            $('#pemasok-table-container').css('opacity', 1);
                            window.showToast('error', 'Gagal memuat data. Silakan coba lagi.');
                        }
                    });
                }

                // Event listener untuk input pencarian dengan debounce
                $('#searchInput').on('keyup', debounce(function() {
                    fetchData(1); // Selalu kembali ke halaman 1 saat melakukan pencarian baru
                }, 500));

                // Event listener untuk filter status
                $('#statusFilter').on('change', function() {
                    fetchData(1); // Selalu kembali ke halaman 1 saat mengubah filter
                });

                // Event listener untuk klik pada link pagination
                $(document).on('click', '#pemasok-table-container .pagination a', function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    if (page) {
                        fetchData(page);
                    }
                });
            });

            // --- SHOW CREATE MODAL ON VALIDATION ERROR ---
            const hasError = document.querySelector('.is-invalid');
            @if ($errors->any())
                var createModal = new bootstrap.Modal(document.getElementById('createModal'));
                createModal.show();
            @endif

        });
    </script>
@endsection
