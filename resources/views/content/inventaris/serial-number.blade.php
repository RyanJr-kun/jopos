@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection
<div class="container-fluid p-3">
    {{-- Card 1: Form Pendaftaran Nomor Seri --}}
    <div class="card rounded-2 mb-4">
        <div class="card-header pb-0 px-3 pt-2">
            <h6 class="mb-0">Pendaftaran Nomor Seri</h6>
            <p class="text-sm mb-0">Tambah nomor seri untuk produk yang membutuhkannya.</p>
        </div>
        <div class="card-body pt-2">
            <form id="addMultipleSerialsForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="product_id" id="selected_product_id">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="select-produk" class="form-label">Pilih Product:</label>
                        <select id="select-produk" class="form-control"></select>
                    </div>
                    <div class="col-md-5 d-flex">
                        <div class="w-100 me-3">
                            <label for="input-serial" class="form-label">Input Nomor Seri:</label>
                            <input type="text" id="input-serial" class="form-control"
                                placeholder="Pilih produk terlebih dahulu" disabled>
                        </div>
                        <div>
                            <button type="button" id="btn-add-to-table" class="btn btn-outline-info"
                                style="margin-top: 30px;" disabled>Tambah</button>
                        </div>
                    </div>
                    <div class="col-md-5 d" id="product-info-container" style="display: none;">
                        <div class="card bg-gray-100 rounded-2 shadow-none">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-around text-center">
                                    <div>
                                        <h6 class="mb-0" id="info-stok">0</h6>
                                        <p class="text-xs fw-bold mb-0">Stock</p>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" id="info-sn-tercatat">0</h6>
                                        <p class="text-xs fw-bold mb-0">SN Tercatat</p>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-danger" id="info-sn-butuh">0</h6>
                                        <p class="text-xs fw-bold mb-0">Butuh SN</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="serial-input-section" style="display: none;">

                    <div class="table-responsive p-0 mt-3" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm w-md-60 align-items-center" id="temp-serial-table">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="text-dark text-xs font-weight-bolder">No.</th>
                                    <th class="text-dark text-xs font-weight-bolder ps-2">Nomor Seri</th>
                                    <th class="text-dark"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- New serials will be added here by JS --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-start mt-3">
                        <button type="button" id="btn-submit-serials" class="btn btn-outline-info" disabled>Simpan
                            Nomor Seri</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Card 2: Daftar Nomor Seri (Existing Table) --}}
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-n1">Daftar Nomor Seri</h6>
                    <p class="text-sm mb-0">Kelola semua nomor seri produk Anda.</p>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <form method="GET" action="{{ route('serialNumber.index') }}">
                <div class="row g-3 px-3 align-items-center justify-content-start">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Nomor Seri</label>
                        <input type="text" id="search" name="search" class="form-control"
                            placeholder="Ketik nomor seri..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="product_id_filter" class="form-label">Filter Product</label>
                        <select id="product_id_filter" name="product_id" class="form-select" style="width: 100%;">
                            <option value="">Semua Product</option>
                            @foreach ($products as $produk)
                                <option value="{{ $produk->id }}" @selected(request('product_id') == $produk->id)>
                                    {{ $produk->name_product }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status_filter" class="form-label">Filter Status</label>
                        <select id="status_filter" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Tersedia" @selected(request('status') == 'Tersedia')>Tersedia</option>
                            <option value="Terjual" @selected(request('status') == 'Terjual')>Terjual</option>
                            <option value="Rusak" @selected(request('status') == 'Rusak')>Rusak</option>
                            <option value="Hilang" @selected(request('status') == 'Hilang')>Hilang</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end" style="padding-top: 45px;">
                        <button type="submit" class="btn btn-outline-info me-3 py-2">Filter</button>
                        <a href="{{ route('serialNumber.index') }}" class="btn btn-outline-dark py-2">Reset</a>
                    </div>
                </div>
            </form>
            <div class="table-responsive p-0 mt-3">
                <table class="table table-hover align-items-center mb-0" id="tableData">
                    <thead>
                        <tr class="table-secondary">
                            <th class="text-uppercase text-dark text-xs font-weight-bolder">Product</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Nomor Seri</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Status</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Tgl. Masuk</th>
                            <th class="text-uppercase text-dark text-xs font-weight-bolder ps-2">Info Sale</th>
                            <th class="text-dark">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="isiTable">
                        @forelse ($serialNumbers as $sn)
                            <tr>
                                <td>
                                    <div class="d-flex ms-2 px-2 py-1 align-items-center">
                                        @if ($sn->produk->img_produk)
                                            <img src="{{ asset('storage/' . $sn->produk->img_produk) }}"
                                                class="avatar avatar-sm me-3" alt="{{ $sn->produk->name_product }}">
                                        @else
                                            <img src="{{ asset('assets/img/produk.webp') }}"
                                                class="avatar avatar-sm me-3" alt="Gambar produk default">
                                        @endif
                                        <h6 class="mb-0 text-sm">{{ $sn->produk->name_product }}</h6>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs text-dark fw-bold mb-0">{{ $sn->nomor_seri }}</p>
                                </td>
                                <td class="align-middle text-sm">
                                    @php
                                        $statusClass = '';
                                        switch ($sn->status) {
                                            case 'Tersedia':
                                                $statusClass = 'bg-label-success';
                                                break;
                                            case 'Terjual':
                                                $statusClass = 'bg-label-info';
                                                break;
                                            case 'Rusak':
                                                $statusClass = 'bg-label-danger';
                                                break;
                                            case 'Hilang':
                                                $statusClass = 'bg-label-warning';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $sn->status }}</span>
                                </td>
                                <td>
                                    <p class="text-xs text-dark fw-bold mb-0">
                                        {{ $sn->created_at->translatedFormat('d M Y') }}</p>
                                </td>
                                <td>
                                    @if ($sn->penjualan)
                                        <a href="{{ route('penjualan.show', $sn->penjualan->referensi) }}"
                                            class="text-info fw-bold text-xs" data-bs-toggle="tooltip"
                                            title="Lihat Invoice Sale">
                                            {{ $sn->penjualan->referensi }}
                                        </a>
                                    @else
                                        <p class="text-xs text-dark fw-bold mb-0">-</p>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <button type="button" class="btn btn-link text-dark p-0 m-0 btn-edit"
                                        data-id="{{ $sn->id }}" data-serial="{{ $sn->nomor_seri }}"
                                        data-status="{{ $sn->status }}" title="Edit SN">
                                        <i class="bx bx-edit bi-sm text-dark text-sm opacity-10"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-danger p-0 m-0 ms-2 btn-delete"
                                        data-id="{{ $sn->id }}" data-serial="{{ $sn->nomor_seri }}"
                                        title="Hapus SN">
                                        <i class="bx bx-trash bi-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada data nomor seri yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="my-3 ms-3">{{ $serialNumbers->links() }}</div>
            </div>
        </div>
    </div>

    {{-- modal edit --}}
    <div class="modal fade" id="editSerialModal" tabindex="-1" aria-labelledby="editSerialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="editSerialModalLabel">Edit Nomor Seri</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSerialForm" method="post">
                        @method('put')
                        @csrf
                        <div class="mb-3">
                            <label for="edit_serial_number" class="form-label">Nomor Seri</label>
                            <input id="edit_serial_number" name="serial_number" type="text" class="form-control"
                                required>
                            <div class="invalid-feedback" id="edit_serial_number-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Terjual">Terjual</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Hilang">Hilang</option>
                            </select>
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
                    <p class="mb-0">Apakah Anda yakin ingin menghapus Unit ini?</p>
                    <h6 class="mt-2" id="serialNumberToDelete"></h6>
                    <div class="mt-4">
                        <form id="deleteSerialForm" method="POST" action="#">
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
    <script>
        $(document).ready(function() {

            // --- GLOBAL VARIABLES & INITIALIZATION ---
            let tempSerials = new Set();
            let selectedProductData = null;
            const editSerialModal = new bootstrap.Modal(document.getElementById('editSerialModal'));
            const deleteSerialModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));

            $('#product_id_filter').select2({
                theme: "bootstrap-5",
                placeholder: 'Semua Product'
            });

            // --- UTILITY FUNCTIONS ---
            function showSuccess(message) {
                window.showToast('success', message);.then(() => location.reload());
            }

            function showError(message, errors = {}) {
                let errorText = message;
                if (Object.keys(errors).length > 0) {
                    errorText = Object.values(errors).flat()[0];
                }
                window.showToast('error', errorText);
            }

            // --- FUNGSI UTAMA (SUMBER KEBENARAN TUNGGAL) ---

            /**
             * Mengupdate status input serial berdasarkan data produk dan daftar serial sementara.
             * Ini adalah satu-satunya fungsi yang boleh mengaktifkan/menonaktifkan input.
             */
            function updateInputState() {
                if (!selectedProductData) return;

                const butuhAwal = selectedProductData.qty - selectedProductData.sn_count;
                const sisaButuh = butuhAwal - tempSerials.size;

                if (sisaButuh > 0) {
                    $('#input-serial').prop('disabled', false).attr('placeholder', `Butuh ${sisaButuh} SN lagi...`)
                        .focus();
                    $('#btn-add-to-table').prop('disabled', false);
                } else {
                    $('#input-serial').prop('disabled', true).attr('placeholder', 'Jumlah SN sudah cukup');
                    $('#btn-add-to-table').prop('disabled', true);
                }

                $('#btn-submit-serials').prop('disabled', tempSerials.size === 0);
            }

            /**
             * Memuat data produk, memperbarui UI, dan memanggil updateInputState.
             */
            function updateProductInfo(productId, productSlug) {
                if (!productId) {
                    $('#product-info-container').hide();
                    return;
                }
                const url = "{{ route('get-data.serial-product-info', ['produk' => ':id']) }}".replace(':id',
                    productId);
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        selectedProductData = {
                            id: productId,
                            qty: data.qty,
                            sn_count: data.sn_tercatat_count,
                            slug: productSlug
                        };
                        $('#selected_product_id').val(productId);
                        $('#info-stok').text(data.qty);
                        $('#info-sn-tercatat').text(data.sn_tercatat_count);
                        $('#info-sn-butuh').text(data.butuh_sn);
                        $('#product-info-container').show();
                        $('#serial-input-section').show();

                        const newUrl = `/serialNumber/${productSlug}`;
                        history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        tempSerials.clear();
                        renderTempTable();
                        updateInputState(); // Panggil fungsi utama untuk atur input
                    },
                    error: function() {
                        showError('Gagal memuat info produk.');
                    }
                });
            }


            // --- SELECT2 & EVENT HANDLING ---
            function formatProduct(produk) {
                if (!produk.id) return produk.text;
                const defaultImage = "{{ asset('assets/img/produk.webp') }}";
                const imageUrl = produk.img_produk ? `{{ asset('storage/') }}/${produk.img_produk}` : defaultImage;
                return $(
                    `<div class="d-flex align-items-center">
                    <img src="${imageUrl}" class="avatar avatar-sm me-3" />
                    <div>
                        <h6 class="mb-0 text-sm">${produk.text}</h6>
                        <p class="text-xs text-muted mb-0">Stock: ${produk.qty}</p>
                    </div>
                </div>`
                );
            }

            $('#select-produk').select2({
                theme: "bootstrap-5",
                placeholder: 'Ketik untuk mencari produk',
                templateResult: formatProduct,
                templateSelection: (produk) => produk.text,
                ajax: {
                    url: "{{ route('get-data.produk') }}",
                    dataType: 'json',
                    delay: 250,
                    data: (params) => ({
                        search: params.term,
                        page: params.page || 1,
                        wajib_seri: 1
                    }),
                    processResults: function(data) {
                        return {
                            results: data.data.map(item => ({
                                id: item.id,
                                text: item.name_product,
                                slug: item.slug,
                                qty: item.qty,
                                img_produk: item.img_produk
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    }
                }
            });

            $('#select-produk').on('select2:select', function(e) {
                const data = e.params.data;
                updateProductInfo(data.id, data.slug);
            });


            // --- LOGIKA PENDAFTARAN SN ---
            function addSerialToTempTable() {
                if (!selectedProductData) return;
                const serialInput = $('#input-serial');
                const serialValue = serialInput.val().trim();
                if (!serialValue) return;

                const sisaButuh = (selectedProductData.qty - selectedProductData.sn_count) - tempSerials.size;
                if (sisaButuh <= 0) {
                    window.showToast('warning', 'Jumlah nomor seri yang ditambahkan sudah mencukupi.');
                    return;
                }
                if (tempSerials.has(serialValue)) {
                    window.showToast('warning', 'Nomor seri sudah ada di dalam daftar.');
                    return;
                }

                tempSerials.add(serialValue);
                renderTempTable();
                serialInput.val('').focus();
                updateInputState(); // Panggil fungsi utama untuk update state
            }

            $('#btn-add-to-table').on('click', addSerialToTempTable);
            $('#input-serial').on('keypress', (e) => {
                if (e.which === 13) {
                    e.preventDefault();
                    addSerialToTempTable();
                }
            });

            function renderTempTable() {
                const tableBody = $('#temp-serial-table tbody');
                tableBody.empty();
                let counter = 1;
                tempSerials.forEach(serial => {
                    tableBody.append(`  <tr>
                                        <td class="ps-4">${counter++}</td>
                                        <td class="text-start">${serial}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove-temp" data-serial="${serial}"><i class="bx bx-trash bi-sm"></i></button>
                                        </td>
                                    </tr>`);
                });
                updateInputState(); // Panggil fungsi utama untuk update state
            }

            $('#temp-serial-table').on('click', '.btn-remove-temp', function() {
                tempSerials.delete($(this).data('serial'));
                renderTempTable(); // Render ulang tabel dan panggil updateInputState di dalamnya
            });

            $('#btn-submit-serials').on('click', function() {
                $.ajax({
                    url: "{{ route('serialNumber.store') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: $('#selected_product_id').val(),
                        serial_numbers: Array.from(tempSerials)
                    },
                    success: (response) => showSuccess(response.message),
                    error: (xhr) => showError(xhr.responseJSON.message || 'Terjadi kesalahan.', xhr
                        .responseJSON.errors || {})
                });
            });

            // --- LOGIKA AWAL SAAT HALAMAN DIMUAT ---
            @if ($produkDipilih)
                var produkOption = new Option("{{ $produkDipilih->name_product }}", "{{ $produkDipilih->id }}",
                    true, true);
                $('#select-produk').append(produkOption).trigger('change');
                updateProductInfo("{{ $produkDipilih->id }}", "{{ $produkDipilih->slug }}");
                $('#product_id_filter').val("{{ $produkDipilih->id }}").trigger('change');
            @endif

            // --- LOGIKA MODAL EDIT & DELETE ---
            $('#tableData').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const serial = $(this).data('serial');
                const status = $(this).data('status');
                const url = "{{ route('serialNumber.update', ':id') }}".replace(':id', id);

                $('#editSerialForm').attr('action', url);
                $('#edit_serial_number').val(serial);
                $('#edit_status').val(status);
                editSerialModal.show();
            });

            $('#editSerialForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: (response) => showSuccess(response.message),
                    error: (xhr) => {
                        const error = xhr.responseJSON.errors?.serial_number[0];
                        $('#edit_serial_number').addClass('is-invalid').focus();
                        $('#edit_serial_number-error').text(error || 'Terjadi kesalahan');
                    }
                });
            });

            $('#tableData').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const serial = $(this).data('serial');
                const url = "{{ route('serialNumber.destroy', ':id') }}".replace(':id', id);
                $('#deleteSerialForm').attr('action', url);
                $('#serialNumberToDelete').text(`"${serial}"`);
                deleteSerialModal.show();
            });

            $('#deleteSerialForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: (response) => showSuccess(response.message),
                    error: (xhr) => showError(xhr.responseJSON.message || 'Terjadi kesalahan.')
                });
            });

        });
    </script>
@endsection
@endsection
