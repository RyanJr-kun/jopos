@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
    <style>
        .disabled-form-section {
            pointer-events: none;
            opacity: 0.6;
            user-select: none;
        }
    </style>
@endsection

@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '#'],
            ['name' => 'Daftar Invoice Penjualan', 'url' => route('penjualan.index')],
            ['name' => 'Edit Invoice', 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-header pb-0 px-3 pt-2 mb-3">
            <h6 class="mb-0">Edit Transaksi Penjualan</h6>
            <p class="text-sm">Invoice: <span class="fw-bold">{{ $penjualan->referensi }}</span></p>
        </div>
        <div class="card-body pt-0">
            <form id="editPenjualanForm" action="{{ route('penjualan.update', $penjualan->referensi) }}" method="POST">
                @method('put')
                @csrf
                {{-- Display All Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger text-white mt-3" role="alert">
                        <strong class="font-weight-bold">Oops! Terjadi kesalahan:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    {{-- Informasi Dasar --}}
                    <div class="col-md-3">
                        <label for="pelanggan_id" class="form-label">Pelanggan</label>
                        <select name="pelanggan_id" id="pelanggan_id" class="form-select" required>
                            @foreach ($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}" @selected($penjualan->pelanggan_id == $pelanggan->id)>{{ $pelanggan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_penjualan" class="form-label">Tanggal</label>
                        <input type="datetime-local" name="tanggal_penjualan" id="tanggal_penjualan"
                            class="form-control"
                            value="{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('Y-m-d\TH:i') }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                            <option value="TUNAI" @selected($penjualan->metode_pembayaran == 'TUNAI')>Tunai</option>
                            <option value="TRANSFER" @selected($penjualan->metode_pembayaran == 'TRANSFER')>Transfer</option>
                            <option value="QRIS" @selected($penjualan->metode_pembayaran == 'QRIS')>QRIS</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                        <select name="status_pembayaran" id="status_pembayaran" class="form-select" required>
                            <option value="Lunas" @selected($penjualan->status_pembayaran == 'Lunas')>Lunas</option>
                            <option value="Belum Lunas" @selected($penjualan->status_pembayaran == 'Belum Lunas')>Belum Lunas</option>
                            <option value="Dibatalkan" @selected($penjualan->status_pembayaran == 'Dibatalkan')>Dibatalkan</option>
                        </select>
                    </div>

                    {{-- Pencarian Produk --}}
                    <div class="col-12">
                        <label for="produk_search" class="form-label">Cari & Tambah Produk</label>
                        <select id="produk_search" class="form-control"></select>
                    </div>

                    {{-- Tabel Item --}}
                    <div class="col-12 table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-items-center" id="items_table">
                            <thead class="table-secondary sticky-top">
                                <tr>
                                    <th class="text-dark text-xs font-weight-bolder">Produk</th>
                                    <th class="text-dark text-xs font-weight-bolder text-center">Qty</th>
                                    <th class="text-dark text-xs font-weight-bolder ps-2">Harga Jual</th>
                                    <th class="text-dark text-xs font-weight-bolder text-center">Pajak</th>
                                    <th class="text-dark text-xs font-weight-bolder ps-2">Diskon (Rp)</th>
                                    <th class="text-dark text-xs font-weight-bolder ps-2">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Items will be injected here by JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Rincian Biaya & Total --}}
                    <div class="col-md-6">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <div id="quill_catatan" style="height: 120px;">{!! $penjualan->catatan !!}</div>
                        <input type="hidden" name="catatan" id="catatan_hidden" value="{{ $penjualan->catatan }}">
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <p class="text-sm">Subtotal (DPP)</p>
                            <p class="text-sm font-weight-bold" id="subtotal_dpp_display">Rp 0</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-sm">PPN</p>
                            <p class="text-sm font-weight-bold" id="pajak_total_display">Rp 0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                            data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="service"
                            data-label="Service">
                            <p class="text-sm mb-0">Service</p>
                            <p class="text-sm font-weight-bold mb-0" id="service-display">
                                {{ 'Rp ' . number_format($penjualan->service, 0, ',', '.') }}</p>
                            <input type="hidden" name="service" id="service-input" value="{{ $penjualan->service }}">
                        </div>
                        <div class="d-flex justify-content-between align-items-center my-3" style="cursor: pointer;"
                            data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="ongkir"
                            data-label="Ongkos Kirim">
                            <p class="text-sm mb-0">Ongkir</p>
                            <p class="text-sm font-weight-bold mb-0" id="ongkir-display">
                                {{ 'Rp ' . number_format($penjualan->ongkir, 0, ',', '.') }}</p>
                            <input type="hidden" name="ongkir" id="ongkir-input"
                                value="{{ $penjualan->ongkir }}">
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;"
                            data-bs-toggle="modal" data-bs-target="#editExtraCostModal" data-type="diskon"
                            data-label="Diskon">
                            <p class="text-sm mb-0">Diskon (Rp)</p>
                            <p class="text-sm font-weight-bold mb-0" id="diskon-display">
                                {{ 'Rp ' . number_format($penjualan->diskon, 0, ',', '.') }}</p>
                            <input type="hidden" name="diskon" id="diskon-input"
                                value="{{ $penjualan->diskon }}">
                        </div>
                        <hr class="horizontal dark my-2">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bolder">Total</h6>
                            <h6 class="fw-bolder" id="total_akhir">Rp 0</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <label for="jumlah_dibayar" class="form-label text-sm mb-0">Jumlah Dibayar</label>
                            <div class="input-group w-50">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah_dibayar" id="jumlah_dibayar"
                                    class="form-control text-end"
                                    value="{{ number_format($penjualan->jumlah_dibayar, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <h6 class="text-sm">Sisa/Kembalian</h6>
                            <h6 class="text-sm" id="kembalian_display">Rp 0</h6>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-4">
                        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Item Detail --}}
<div class="modal fade" id="editItemDetailModal" tabindex="-1" aria-labelledby="editItemDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemDetailModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editItemDetailForm" onsubmit="return false;">
                    <input type="hidden" id="edit_item_produk_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="edit_item_nama" readonly disabled>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="edit_item_qty" class="form-label">Qty</label>
                            <input type="number" class="form-control" id="edit_item_qty" min="1" required>
                        </div>
                        <div class="col-6">
                            <label for="edit_item_harga" class="form-label">Harga Jual</label>
                            <input type="text" class="form-control" id="edit_item_harga" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label for="edit_item_pajak_id" class="form-label">Pajak</label>
                            <select class="form-select" id="edit_item_pajak_id">
                                <option value="" data-rate="0" selected>Tidak Ada</option>
                                @foreach ($pajaks as $pajak)
                                    <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                        {{ $pajak->nama_pajak }} ({{ $pajak->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="edit_item_diskon" class="form-label">Diskon (Rp)</label>
                            <input type="text" class="form-control" id="edit_item_diskon" placeholder="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" id="saveItemDetailChangesBtn">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Biaya Tambahan (Service, Ongkir, Diskon) --}}
<div class="modal fade" id="editExtraCostModal" tabindex="-1" aria-labelledby="editExtraCostModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="editExtraCostModalLabel">Edit Biaya</h6>
                <button type="button" class="btn bg-dark btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editExtraCostForm" onsubmit="return false;">
                    <input type="hidden" id="extra-cost-type">
                    <div class="form-group">
                        <label for="extra-cost-value" class="form-control-label" id="extra-cost-label">Jumlah</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control" id="extra-cost-value" min="0"
                                required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" id="saveExtraCostBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Pass initial data from PHP to JS
        const initialCartItems = @json($penjualan->items);
        const allPajak = @json($pajaks);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- STATE & UTILITIES ---
            const cart = new Map();
            let itemCounter = 0;
            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
            const parseCurrency = (string) => parseFloat(String(string).replace(/[^0-9]/g, '')) || 0;

            function formatInputAsCurrency(input) {
                let value = input.val();
                let number = parseCurrency(value);
                input.val(new Intl.NumberFormat('id-ID').format(number));
            }

            // --- QUILL INITIALIZATION ---
            const quill = new Quill('#quill_catatan', {
                theme: 'snow',
                placeholder: 'Tulis catatan di sini...'
            });
            quill.on('text-change', function() {
                document.getElementById('catatan_hidden').value = quill.root.innerHTML;
            });

            // --- MODAL & FORM ELEMENTS ---
            const editItemDetailModal = new bootstrap.Modal(document.getElementById('editItemDetailModal'));
            const editExtraCostModal = new bootstrap.Modal(document.getElementById('editExtraCostModal'));
            // --- SELECT2 INITIALIZATION ---
            // Fungsi untuk format tampilan hasil pencarian produk di Select2
            const productSearch = $('#produk_search').select2({
                theme: "bootstrap-5",
                placeholder: 'Ketik untuk mencari produk...',
                ajax: {
                    url: "{{ route('get-data.produk') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        search: params.term,
                        page: params.page || 1
                    }),
                    processResults: function(response) {
                        // Menyesuaikan dengan struktur data paginasi Laravel
                        return {
                            results: response.data.map(item => ({
                                id: item.id,
                                text: item.nama_produk,
                                harga_jual: item.harga_jual,
                                stok: item.qty,
                                pajak_id: item.pajak_id,
                                img_produk: item.img_produk, // Ambil data gambar
                            })),
                            pagination: {
                                more: response.next_page_url !== null
                            }
                        };
                    },
                    cache: true,
                },
                templateResult: function(produk) { // Menggunakan fungsi langsung di sini
                    if (produk.loading) {
                        return produk.text;
                    }
                    const defaultImage = "{{ asset('assets/img/produk.webp') }}";
                    const imageUrl = produk.img_produk ? `/storage/${produk.img_produk}` : defaultImage;
                    const markup = `
                        <div class="d-flex align-items-center">
                            <img src="${imageUrl}" class="avatar avatar-sm me-3" onerror="this.onerror=null;this.src='${defaultImage}';" />
                            <div>
                                <h6 class="mb-0 text-sm">${produk.text}</h6>
                                <p class="text-xs text-muted mb-0">Stok: ${produk.stok || 'N/A'}</p>
                            </div>
                        </div>`;
                    return $(markup);
                },
                escapeMarkup: markup => markup // Izinkan HTML pada template
            });

            // --- CART & TABLE LOGIC ---
            function renderTable() {
                const tableBody = $('#items_table tbody');
                tableBody.empty();
                itemCounter = 0;

                if (cart.size === 0) {
                    tableBody.html(
                        '<tr><td colspan="7" class="text-center text-muted py-4">Belum ada item.</td></tr>');
                    return;
                }

                cart.forEach(item => {
                    const pajakInfo = allPajak.find(p => p.id === item.pajak_id);
                    const pajakRate = pajakInfo ? pajakInfo.rate : 0;

                    const hargaTotalItem = item.harga_jual * item.jumlah;
                    const dpp = hargaTotalItem / (1 + pajakRate / 100);
                    const subtotal = dpp - item.diskon;

                    const rowHtml = `
                        <tr data-produk-id="${item.id}">
                            <input type="hidden" name="items[${itemCounter}][produk_id]" value="${item.id}">
                            <input type="hidden" name="items[${itemCounter}][jumlah]" value="${item.jumlah}">
                            <input type="hidden" name="items[${itemCounter}][harga_jual]" value="${item.harga_jual}">
                            <input type="hidden" name="items[${itemCounter}][diskon]" value="${item.diskon}">
                            <input type="hidden" name="items[${itemCounter}][pajak_id]" value="${item.pajak_id || ''}">

                            <td>

                                <p class="text-sm fw-bold ms-3 mb-0">${item.nama}</p>
                            </td>
                            <td class="text-center text-sm">${item.jumlah}</td>
                            <td class="text-sm">${formatCurrency(item.harga_jual)}</td>
                            <td class="text-sm text-center">${pajakInfo ? pajakInfo.nama_pajak : 'Bebas Pajak'}</td>
                            <td class="text-sm">${formatCurrency(item.diskon)}</td>
                            <td class="text-sm fw-bold">${formatCurrency(subtotal)}</td>
                            <td class="text-center">
                                <div class="d-flex">
                                    <button type="button" class="btn btn-link text-dark p-0 m-0 me-2 btn-edit-item" title="Edit Item"><i class="bx bx-pencil-square"></i></button>
                                    <button type="button" class="btn btn-link text-danger  p-0 m-0 btn-remove-item" title="Hapus Item"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.append(rowHtml);
                    itemCounter++;
                });
                calculateTotals();
            }

            function calculateTotals() {
                let subtotalDpp = 0;
                let totalPajak = 0;

                cart.forEach(item => {
                    const pajakInfo = allPajak.find(p => p.id === item.pajak_id);
                    const pajakRate = pajakInfo ? pajakInfo.rate : 0;

                    const hargaTotalItem = item.harga_jual * item.jumlah;
                    const dppItem = hargaTotalItem / (1 + pajakRate / 100);
                    const pajakItem = hargaTotalItem - dppItem;

                    subtotalDpp += (dppItem - item.diskon);
                    totalPajak += pajakItem;
                });

                const service = parseCurrency($('#service-input').val());
                const ongkir = parseCurrency($('#ongkir-input').val());
                const diskonGlobal = parseCurrency($('#diskon-input').val());

                const totalAkhir = subtotalDpp + totalPajak + service + ongkir - diskonGlobal;

                $('#subtotal_dpp_display').text(formatCurrency(subtotalDpp));
                $('#pajak_total_display').text(formatCurrency(totalPajak));
                $('#total_akhir').text(formatCurrency(totalAkhir));
                calculateChange();
            }

            function calculateChange() {
                const totalAkhir = parseCurrency($('#total_akhir').text());
                const jumlahDibayar = parseCurrency($('#jumlah_dibayar').val());
                const kembalian = jumlahDibayar - totalAkhir;

                $('#kembalian_display').text(formatCurrency(kembalian));
                $('#kembalian_display').toggleClass('text-danger', kembalian < 0);
            }

            function initializeCart() {
                initialCartItems.forEach(item => {
                    cart.set(item.produk_id, {
                        id: item.produk_id,
                        nama: item.produk.nama_produk,
                        jumlah: item.jumlah,
                        harga_jual: item.harga_jual,
                        diskon: item.diskon_item,
                        pajak_id: item.pajak_id,
                    });
                });
                renderTable();
            }

            // --- UI LOCKING FOR 'DIBATALKAN' STATUS ---
            function toggleFormControls(enabled) {
                const isCancelled = !enabled;

                // Product search
                $('#produk_search').prop('disabled', isCancelled).trigger('change');

                // Item action buttons
                $('#items_table .btn-edit-item, #items_table .btn-remove-item').css('pointer-events', enabled ?
                    'auto' : 'none');

                // Extra cost modals
                $('[data-bs-toggle="modal"][data-type]').css('pointer-events', enabled ? 'auto' : 'none');

                // Payment input
                $('#jumlah_dibayar').prop('readonly', isCancelled);

                // Other fields
                // We remove .prop('disabled') and rely on the parent's 'pointer-events: none'
                // to prevent interaction while still allowing form submission.
                $('#tanggal_penjualan').prop('readonly', isCancelled);

                // Quill editor
                quill.enable(enabled);

                // Add/remove disabled class for visual feedback
                const sectionsToToggle = ['#produk_search', '#items_table', '[data-bs-toggle="modal"][data-type]'];
                sectionsToToggle.forEach(selector => {
                    $(selector).closest('.row > div, div.table-responsive').toggleClass(
                        'disabled-form-section', isCancelled);
                });
            }

            // --- EVENT LISTENERS ---
            productSearch.on('select2:select', function(e) {
                const data = e.params.data;
                const defaultPajak = allPajak.find(p => p.id === data.pajak_id);

                cart.set(data.id, {
                    id: data.id,
                    nama: data.text,
                    jumlah: 1,
                    harga_jual: data.harga_jual,
                    diskon: 0,
                    pajak_id: data.pajak_id || null,
                });

                renderTable();
                $(this).val(null).trigger('change');
            });

            $('#items_table').on('click', '.btn-remove-item', function() {
                const row = $(this).closest('tr');
                const produkId = row.data('produk-id');
                cart.delete(produkId);
                renderTable();
            });

            $('#items_table').on('click', '.btn-edit-item', function() {
                const row = $(this).closest('tr');
                const produkId = row.data('produk-id');
                const item = cart.get(produkId);

                $('#edit_item_produk_id').val(item.id);
                $('#edit_item_nama').val(item.nama);
                $('#edit_item_qty').val(item.jumlah);
                $('#edit_item_harga').val(item.harga_jual);
                $('#edit_item_diskon').val(item.diskon);
                $('#edit_item_pajak_id').val(item.pajak_id || '');

                editItemDetailModal.show();
            });

            $('#saveItemDetailChangesBtn').on('click', function() {
                const produkId = parseInt($('#edit_item_produk_id').val());
                const item = cart.get(produkId);

                if (item) {
                    item.jumlah = parseInt($('#edit_item_qty').val()) || 1;
                    item.harga_jual = parseCurrency($('#edit_item_harga').val() || '0');
                    item.diskon = parseCurrency($('#edit_item_diskon').val() || '0');
                    item.pajak_id = parseInt($('#edit_item_pajak_id').val()) ||
                        null; // Tetap null jika tidak ada
                }

                renderTable();
                editItemDetailModal.hide();
            });

            // Format currency inputs
            $('#jumlah_dibayar, #edit_item_harga, #edit_item_diskon, #extra-cost-value').on('input', function() {
                // No action on input, only on blur, to allow easier typing
            }).on('blur', function() {
                formatInputAsCurrency($(this));
            });

            // Recalculate on change
            $('#jumlah_dibayar').on('input', calculateChange);

            $('#status_pembayaran').on('change', function() {
                if ($(this).val() === 'Lunas') {
                    const totalAkhir = parseCurrency($('#total_akhir').text());
                    $('#jumlah_dibayar').val(new Intl.NumberFormat('id-ID').format(totalAkhir)).trigger(
                        'input');
                    toggleFormControls(true); // Pastikan form aktif
                } else if ($(this).val() === 'Dibatalkan') {
                    $('#jumlah_dibayar').val('0').trigger('input');
                    toggleFormControls(false); // Kunci form
                } else { // Belum Lunas
                    toggleFormControls(true); // Pastikan form aktif
                }
            });

            // --- EXTRA COST MODAL LOGIC ---
            $('#editExtraCostModal').on('show.bs.modal', function(event) {
                const triggerElement = $(event.relatedTarget);
                const type = triggerElement.data('type');
                const label = triggerElement.data('label');

                const targetInput = $(`#${type}-input`);
                const currentValue = targetInput.val();

                $('#extra-cost-type').val(type);
                $('#editExtraCostModalLabel').text(`Edit ${label}`);
                $('#extra-cost-label').text(label);
                $('#extra-cost-value').val(new Intl.NumberFormat('id-ID').format(currentValue || 0));
                setTimeout(() => $('#extra-cost-value').focus(), 500);
            });

            $('#saveExtraCostBtn').on('click', function() {
                const type = $('#extra-cost-type').val();
                const newValue = parseCurrency($('#extra-cost-value').val());

                const targetInput = $(`#${type}-input`);
                const targetDisplay = $(`#${type}-display`);

                targetInput.val(newValue);
                targetDisplay.text(formatCurrency(newValue));

                calculateTotals();
                editExtraCostModal.hide();
            });

            // --- INITIALIZATION ---
            initializeCart();

            // Set initial form state based on status
            if ($('#status_pembayaran').val() === 'Dibatalkan') {
                toggleFormControls(false);
            }
        });

        // --- FORM SUBMISSION ---
        $('#editPenjualanForm').on('submit', function(e) {
            // Bersihkan format angka dari semua input yang relevan sebelum submit
            const inputsToClean = [
                '#jumlah_dibayar',
                '#service-input',
                '#ongkir-input',
                '#diskon-input'
            ];

            inputsToClean.forEach(selector => {
                const input = $(selector);
                if (input.length) input.val(parseCurrency(input.val()));
            });
        });
    </script>
@endsection
@endsection
