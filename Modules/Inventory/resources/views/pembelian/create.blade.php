@extends('layouts/contentNavbarLayout')
@section('title', 'Purchase Order - Buat Pembelian')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endsection

@section('content')

    <form action="{{ route('pembelian.store') }}" method="post" id="form-pembelian">
        @csrf
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                <h5 class="mb-0 fw-bold">Informasi Purchase</h5>
            </div>

            <div class="card-body p-4">
                <!-- HEADER INFO -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="Supplier" class="form-label fw-semibold">Supplier <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <select class="form-select select2 @error('supplier_id') is-invalid @enderror"
                                name="supplier_id" id="Supplier" required>
                                <option value="" disabled selected>Pilih Supplier</option>
                                @foreach ($supplier as $item)
                                    <option value="{{ $item->id }}" @selected(old('supplier_id') == $item->id)>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-info rounded-2 px-3" data-bs-toggle="modal"
                                data-bs-target="#createSupplierModal" title="Tambah Supplier Baru">
                                <i class="bx bx-plus"></i>
                            </button>
                        </div>
                        @error('supplier_id')
                            <div class="invalid-feedback d-block text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal <span
                                class="text-danger">*</span></label>
                        <input id="tanggal" name="tanggal" type="date"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
                        <input id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" type="date"
                            class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror"
                            value="{{ old('tanggal_jatuh_tempo') }}">
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-12 col-lg-3">
                        <label for="referensi" class="form-label fw-semibold">No Invoice <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light @error('referensi') is-invalid @enderror"
                            id="referensi" name="referensi" value="{{ old('referensi', $nomer_referensi) }}" required
                            readonly>
                        @error('referensi')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- PRODUCT SEARCH PANEL -->
                <div class="p-3 rounded-3 border mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-7">
                            <label for="select2" class="form-label fw-semibold">Cari Product</label>
                            <select name="select2" id="select2" class="form-control"></select>
                        </div>
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="sisa_stok" class="form-label fw-semibold">Stock</label>
                            <input type="number" id="sisa_stok" class="form-control bg-white" readonly>
                        </div>
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="qty" class="form-label fw-semibold">Qty</label>
                            <input type="number" id="qty" class="form-control" min="1" placeholder="0">
                        </div>
                        <div class="col-4 col-md-6 col-lg-1">
                            <button type="button" class="btn btn-info w-100" id="btn-add">
                                <i class="bx bx-plus d-lg-none"></i> <span class="d-none d-lg-inline">Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABLET & DESKTOP TABLE (Scrollable on Mobile) -->
                <div class="table-responsive text-nowrap mb-4 border rounded-3">
                    <table class="table table-hover align-middle mb-0" id="table-pembelian">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th class="text-xs font-weight-bolder py-3">Nama Product</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="10%">Qty</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Harga Beli</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Taxe (Rp)</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Diskon (Rp)</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Subtotal</th>
                                <th class="py-3" width="5%"></th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <!-- Items appended via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- BOTTOM SECTION: SETTINGS & TOTALS -->
                <div class="row g-4 mb-4">
                    <!-- Left: Additional Charges & Status -->
                    <div class="col-12 col-xl-7">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="ongkir" class="form-label fw-semibold">Ongkos Kirim</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="ongkir" id="ongkir" class="form-control text-end"
                                        value="0" min="0">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="diskon-tambahan" class="form-label fw-semibold">Diskon Tambahan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="diskon_tambahan" id="diskon-tambahan"
                                        class="form-control text-end" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="statusBarang" class="form-label fw-semibold">Status Barang <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('status_barang') is-invalid @enderror"
                                    name="status_barang" id="statusBarang" required>
                                    <option value="" disabled selected>Pilih Status</option>
                                    <option value="Diterima" @selected(old('status_barang') == 'Diterima')>Diterima</option>
                                    <option value="Belum Diterima" @selected(old('status_barang') == 'Belum Diterima')>Belum Diterima</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="statusBayar" class="form-label fw-semibold">Status Pembayaran <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('status_pembayaran') is-invalid @enderror"
                                    name="status_pembayaran" id="statusBayar" required>
                                    <option value="" disabled selected>Pilih Status</option>
                                    <option value="Lunas" @selected(old('status_pembayaran') == 'Lunas')>Lunas</option>
                                    <option value="Belum Lunas" @selected(old('status_pembayaran') == 'Belum Lunas')>Belum Lunas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Calculation Summary Panel -->
                    <div class="col-12 col-xl-5">
                        <div class="bg-label-light p-4 rounded-3 border border-primary h-100">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-semibold">Subtotal Keseluruhan</span>
                                <span id="subtotal-keseluruhan" class="fw-bold">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <span class="text-dark fw-bold text-uppercase">Total Akhir</span>
                                <span id="total-akhir" class="fw-bolder fs-5 text-primary">Rp 0</span>
                            </div>
                            <hr class="border-secondary opacity-25">
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <span class="text-dark fw-semibold">Nominal Bayar</span>
                                <div class="w-50">
                                    <input type="text" id="bayar" class="form-control text-end fw-bold">
                                    <input type="hidden" name="jumlah_dibayar" id="jumlah_dibayar_hidden"
                                        value="0">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark fw-semibold">Sisa / Kembalian</span>
                                <span id="kembalian" class="fw-bold fs-6">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NOTES -->
                <div class="mb-4">
                    <label for="catatan" class="form-label fw-semibold">Catatan</label>
                    <div class="border rounded-3 bg-white">
                        <div id="quill-editor-catatan" style="min-height: 120px; border: none;">{!! old('catatan') !!}
                        </div>
                    </div>
                    <input type="hidden" name="catatan" id="catatan" value="{{ old('catatan') }}">
                    @error('catatan')
                        <div class="invalid-feedback d-block text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ACTIONS -->
                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('pembelian.index') }}" id="cancel-button"
                        class="btn btn-outline-danger w-100 w-sm-auto order-2 order-sm-1">Batalkan</a>
                    <button id="saveBtn" type="submit"
                        class="btn btn-success w-100 w-sm-auto order-1 order-sm-2 px-4">Buat
                        Transaksi</button>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal Create Supplier --}}
    <div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 mb-n3">
                    <h6 class="modal-title" id="createSupplierModalLabel">Tambah Supplier Baru</h6>
                    <button type="button" class="btn btn-close bg-danger rounded-3 me-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createSupplierForm" action="{{ route('pemasok.store') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama</label>
                                <input id="name" name="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="perusahaan" class="form-label">Perusahaan</label>
                                <input id="perusahaan" name="perusahaan" type="text"
                                    class="form-control @error('perusahaan') is-invalid @enderror"
                                    value="{{ old('perusahaan') }}" required>
                                @error('perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="kontak" class="form-label">Kontak</label>
                                <input id="kontak" name="kontak" type="text"
                                    class="form-control @error('kontak') is-invalid @enderror"
                                    value="{{ old('kontak') }}" required>
                                @error('kontak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="example@gmail.com"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="note" class="form-label">Catatan (Opsional)</label>
                                <textarea id="note" name="note" class="form-control @error('note') is-invalid @enderror" rows="2">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="justify-content-end form-check form-switch form-check-reverse my-2">
                            <label class="me-auto fw-bold form-check-label" for="status">Status</label>
                            <input id="status" class="form-check-input" type="checkbox" name="status"
                                value="1" checked>
                        </div>
                        <div class="modal-footer border-0 pb-0">
                            <button type="submit" class="btn btn-info btn-sm">Buat Supplier</button>
                            <button type="button" class="btn btn-danger btn-sm"
                                data-bs-dismiss="modal">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Item --}}
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm" onsubmit="return false;">
                        <input type="hidden" id="edit-item-id">
                        <div class="row g-3 px-1">
                            <div class="col-12">
                                <label class="form-label">Nama Product</label>
                                <input type="text" class="form-control" id="edit-item-name" readonly disabled>
                            </div>
                            <div class="col-md-6 col-12 form-group">
                                <label for="edit-item-qty" class="form-control-label">Qty <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit-item-qty" min="1" required>
                            </div>
                            <div class="col-md-6 col-12 form-group">
                                <label for="edit-item-harga" class="form-control-label">Harga Beli <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-item-harga" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label for="edit-item-pajak-id" class="form-label">Taxe</label>
                                <select class="form-select select2" id="edit-item-pajak-id">
                                    <option value="" data-rate="0" selected>Tidak ada</option>
                                    @foreach ($taxes as $pajak)
                                        <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                            {{ $pajak->name_taxe }} ({{ $pajak->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="edit-item-diskon" class="form-label">Diskon (Rp)</label>
                                <input type="text" class="form-control" id="edit-item-diskon" placeholder="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" id="saveItemChangesBtn">Simpan Perubahan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script type="module">
        // 1. FUNGSI PENUNGGU JQUERY & SELECT2
        // Menahan script agar tidak error '$ is not defined' sebelum select2.js siap
        function waitForDependencies(callback) {
            if (window.$ && window.$.fn && window.$.fn.select2) {
                callback();
            } else {
                setTimeout(function() {
                    waitForDependencies(callback);
                }, 100);
            }
        }

        // 2. JALANKAN SAAT DOM SIAP
        document.addEventListener('DOMContentLoaded', function() {

            // --- A. LOGIKA NON-JQUERY (Bisa jalan duluan) ---

            // Inisialisasi Quill Editor
            if (document.getElementById('quill-editor-catatan')) {
                const hiddenInputCatatan = document.getElementById('catatan');
                const quillCatatan = new Quill('#quill-editor-catatan', {
                    theme: 'snow',
                    placeholder: 'Tulis catatan pembelian di sini...',
                });
                quillCatatan.on('text-change', function() {
                    hiddenInputCatatan.value = quillCatatan.root.innerHTML;
                });
                if (hiddenInputCatatan.value) {
                    quillCatatan.root.innerHTML = hiddenInputCatatan.value;
                }
            }

            // Form Create Supplier Modal
            const createSupplierForm = document.getElementById('createSupplierForm');
            if (createSupplierForm) {
                const pemasokSelect = document.getElementById('Supplier');
                const createSupplierModal = new bootstrap.Modal(document.getElementById('createSupplierModal'));

                createSupplierForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                    fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        })
                        .then(response => {
                            if (response.status === 422) {
                                return response.json().then(data => {
                                    throw {
                                        errors: data.errors
                                    };
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const newOption = new Option(data.data.name, data.data.id, true, true);
                                pemasokSelect.appendChild(newOption);
                                pemasokSelect.dispatchEvent(new Event('change'));
                                createSupplierForm.reset();
                                createSupplierModal.hide();

                                if (typeof Swal !== 'undefined') {
                                    window.showToast('success', data.message);
                                } else {
                                    alert(data.message);
                                }
                            }
                        })
                        .catch(error => {
                            if (error.errors) {
                                Object.keys(error.errors).forEach(key => {
                                    const input = createSupplierForm.querySelector(
                                        `[name="${key}"]`);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const errorFeedback = input.nextElementSibling;
                                        if (errorFeedback && errorFeedback.classList.contains(
                                                'invalid-feedback')) {
                                            errorFeedback.textContent = error.errors[key][0];
                                        }
                                    }
                                });
                            } else {
                                console.error('Error:', error);
                                if (typeof Swal !== 'undefined') {
                                    window.showToast('error', 'Terjadi kesalahan. Silakan coba lagi.');
                                } else {
                                    alert('Terjadi kesalahan. Silakan coba lagi.');
                                }
                            }
                        });
                });
            }

            // --- B. LOGIKA JQUERY & KASIR (Menunggu $ Siap) ---
            waitForDependencies(function() {

                // --- Inisialisasi Default Select2 (Status, dll) ---
                $('.select2:not(#select2)').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                    width: '100%'
                });

                // --- Utilitas Format ---
                $("#qty").removeAttr("onfocus");
                const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
                const parseCurrency = (string) => parseFloat(String(string).replace(/[^0-9]/g, '')) || 0;

                function formatInputAsCurrency(input) {
                    let value = parseCurrency(input.val());
                    input.val(new Intl.NumberFormat('id-ID').format(value));
                }

                let itemCounter = 0;
                const editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));

                function formatProduct(produk) {
                    if (!produk.id) return produk.text;
                    var defaultImage = "{{ asset('assets/img/produk.png') }}";

                    // Ambil URL dasar dari konfigurasi disk R2 Laravel
                    var r2BaseUrl = "{{ config('filesystems.disks.r2.url') }}";

                    // Gabungkan Base URL R2 dengan path gambar produk
                    var imageUrl = produk.img_produk ? `${r2BaseUrl}/${produk.img_produk}` : defaultImage;

                    var variantBadge = produk.variant_id ?
                        `<span class="badge bg-label-info text-xs mt-1"><i class="bx bx-list-ul ms-n1 me-1"></i>Varian</span>` :
                        '';
                    return $(`
                                <div class="d-flex align-items-center">
                                    <img src="${imageUrl}" class="avatar avatar-sm me-3" />
                                    <div>
                                        <h6 class="mb-0 text-sm">${produk.text}</h6>
                                        <p class="text-xs text-muted mb-0">Stock: ${produk.qty} ${variantBadge}</p>
                                    </div>
                                </div>
                            `);
                }

                $('#select2').select2({
                    // TANPA THEME: Agar murni menggunakan style dari select2.scss
                    placeholder: 'Ketik untuk mencari produk atau varian...',
                    minimumInputLength: 0,
                    language: 'id',
                    templateResult: formatProduct,
                    templateSelection: function(produk) {
                        if (produk.id) {
                            $("#sisa_stok").val(produk.qty);
                            setTimeout(function() {
                                $("#qty").val(1).focus();
                            }, 150);
                            return produk.text;
                        }
                        return produk.text || "Ketik untuk mencari...";
                    },
                    ajax: {
                        url: "{{ route('get-data.produk') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            let items = data.data ? data.data : (Array.isArray(data) ? data :
                            []);

                            return {
                                results: items.map(function(item) {
                                    let combinedId = item.variant_id ?
                                        `${item.id}-${item.variant_id}` : item.id;
                                    let displayName = item.variant_name ?
                                        `${item.name_product} - ${item.variant_name}` :
                                        item.name_product;

                                    return {
                                        id: combinedId,
                                        product_id: item.id,
                                        variant_id: item.variant_id || null,
                                        text: displayName,
                                        img_produk: item.img_produk,
                                        qty: item.qty,
                                        harga_beli: item.harga_beli,
                                        taxe_id: item.taxe_id,
                                        pajak_rate: item.pajak ? item.pajak.rate : 0
                                    };
                                }),
                                pagination: {
                                    more: data.next_page_url !== null
                                }
                            };
                        },
                        cache: true
                    }
                });

                // --- ADD ITEM TO TABLE ---
                $("#btn-add").on("click", function() {
                    const selectedData = $("#select2").select2('data')[0];
                    const qty = $("#qty").val();

                    if (!selectedData || !selectedData.id || !qty || parseInt(qty) <= 0) {
                        if (typeof Swal !== 'undefined') {
                            window.showToast('warning',
                                'Harap pilih produk dan tentukan jumlah yang valid.');
                        } else {
                            alert('Harap pilih produk dan tentukan jumlah yang valid.');
                        }
                        return;
                    }

                    const rowId = selectedData.id;
                    const produkId = selectedData.product_id;
                    const variantId = selectedData.variant_id;
                    const produkNama = selectedData.text;
                    const produkImg = selectedData.img_produk;
                    const hargaBeli = selectedData.harga_beli || 0;
                    const pajakId = selectedData.taxe_id || null;
                    const pajakRate = selectedData.pajak_rate || 0;
                    const qtyToAdd = parseInt(qty);

                    let existingRow = $(`#table-pembelian tbody tr[data-row-id="${rowId}"]`);

                    if (existingRow.length > 0) {
                        let currentQtyInput = existingRow.find(".qty-pembelian, .item-qty-hidden");
                        let newQty = parseInt(currentQtyInput.val()) + qtyToAdd;
                        currentQtyInput.val(newQty);
                        updateRowDisplay(existingRow);
                    } else {
                        const defaultImage = "{{ asset('assets/img/produk.png') }}";
                        const r2BaseUrl = "{{ config('filesystems.disks.r2.url') }}";
                        const imageUrl = produkImg ? `${r2BaseUrl}/${produkImg}` : defaultImage;

                        const subtotalAwal = (hargaBeli * qtyToAdd);
                        const pajakAwal = subtotalAwal * (pajakRate / 100);
                        const subtotalDenganTaxe = subtotalAwal + pajakAwal;

                        const newRow = `
                        <tr data-row-id="${rowId}">
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${produkId}">
                            <input type="hidden" name="items[${itemCounter}][product_variant_id]" value="${variantId || ''}">
                            <input type="hidden" name="items[${itemCounter}][qty]" class="item-qty-hidden" value="${qtyToAdd}">
                            <input type="hidden" name="items[${itemCounter}][harga_beli]" class="item-harga-hidden" value="${hargaBeli}">
                            <input type="hidden" name="items[${itemCounter}][diskon]" class="item-diskon-hidden" value="0">
                            <input type="hidden" name="items[${itemCounter}][taxe_id]" class="item-pajak-id-hidden" value="${pajakId || ''}">
                            <input type="hidden" class="item-pajak-rate-hidden" value="${pajakRate}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${imageUrl}" class="avatar avatar-sm me-3" alt="${produkNama}">
                                    <div>
                                        <h6 class="mb-0 text-sm item-name">${produkNama}</h6>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center"><span class="item-qty">${qtyToAdd}</span></td>
                            <td class="align-middle"><span class="item-harga">${formatCurrency(hargaBeli)}</span></td>
                            <td class="align-middle"><span class="item-pajak">${formatCurrency(pajakAwal)}</span></td>
                            <td class="align-middle"><span class="item-diskon">Rp 0</span></td>
                            <td class="subtotal-item text-start text-sm fw-bold">${formatCurrency(subtotalDenganTaxe)}</td>
                            <td>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-link text-info p-0 m-0 me-2 btn-edit" title="Edit Item">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove" title="Hapus Item">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;
                        $("#table-pembelian tbody").append(newRow);
                        itemCounter++;
                    }

                    $("#select2").val(null).trigger("change");
                    $("#qty").val("");
                    $("#sisa_stok").val("");
                    calculateGrandTotal();
                });

                // --- CALCULATIONS ---
                function calculateRow(row) {
                    const qty = parseFloat(row.find(".item-qty-hidden").val()) || 0;
                    const hargaBeli = parseFloat(row.find(".item-harga-hidden").val()) || 0;
                    const diskon = parseFloat(row.find(".item-diskon-hidden").val()) || 0;
                    const pajakRate = parseFloat(row.find(".item-pajak-rate-hidden").val()) || 0;

                    const subtotalSebelumTaxe = (qty * hargaBeli) - diskon;
                    const pajakAmount = subtotalSebelumTaxe * (pajakRate / 100);
                    const subtotalDenganTaxe = subtotalSebelumTaxe + pajakAmount;

                    row.find(".item-pajak").text(formatCurrency(pajakAmount));
                    row.find(".subtotal-item").text(formatCurrency(subtotalDenganTaxe));
                }

                function calculateGrandTotal() {
                    let subtotalKeseluruhan = 0;
                    $('#table-pembelian tbody tr').each(function() {
                        const qty = parseFloat($(this).find(".item-qty-hidden").val()) || 0;
                        const hargaBeli = parseFloat($(this).find(".item-harga-hidden").val()) || 0;
                        const diskon = parseFloat($(this).find(".item-diskon-hidden").val()) || 0;
                        const pajakRate = parseFloat($(this).find(".item-pajak-rate-hidden")
                            .val()) || 0;

                        const subtotalItem = (qty * hargaBeli) - diskon;
                        const pajakItem = subtotalItem * (pajakRate / 100);
                        subtotalKeseluruhan += subtotalItem + pajakItem;
                    });

                    $("#subtotal-keseluruhan").text(formatCurrency(subtotalKeseluruhan));
                    const ongkir = parseFloat($("#ongkir").val()) || 0;
                    const diskonTambahan = parseFloat($("#diskon-tambahan").val()) || 0;
                    const totalAkhir = subtotalKeseluruhan - diskonTambahan + ongkir;

                    $("#total-akhir").text(formatCurrency(totalAkhir < 0 ? 0 : totalAkhir));
                    return totalAkhir < 0 ? 0 : totalAkhir;
                }

                function calculateChange() {
                    const totalAkhir = calculateGrandTotal();
                    const bayarValue = $("#bayar").val().replace(/[^0-9]/g, '');
                    const bayar = parseFloat(bayarValue) || 0;

                    $("#jumlah_dibayar_hidden").val(bayar);
                    const sisa = bayar - totalAkhir;

                    $("#kembalian").text(formatCurrency(sisa));
                    if (sisa < 0) {
                        $("#kembalian").removeClass('text-dark').addClass('text-danger');
                    } else {
                        $("#kembalian").removeClass('text-danger').addClass('text-dark');
                    }
                }

                $("#ongkir, #diskon-tambahan").on("input", calculateChange);

                $("#table-pembelian").on("click", ".btn-remove", function() {
                    const row = $(this).closest("tr");
                    const productName = row.find('.item-name').text();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus Item?',
                            text: `Hapus ${productName} dari daftar?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();
                                calculateChange();
                            }
                        });
                    } else {
                        if (confirm(`Hapus ${productName} dari daftar?`)) {
                            row.remove();
                            calculateChange();
                        }
                    }
                });

                $("#table-pembelian").on("click", ".btn-edit", function() {
                    const row = $(this).closest("tr");
                    const rowId = row.data('row-id');

                    $("#edit-item-id").val(rowId);
                    $("#edit-item-name").val(row.find(".item-name").text());
                    $("#edit-item-qty").val(row.find(".item-qty-hidden").val());
                    $("#edit-item-harga").val(new Intl.NumberFormat('id-ID').format(row.find(
                        ".item-harga-hidden").val()));
                    $("#edit-item-diskon").val(new Intl.NumberFormat('id-ID').format(row.find(
                        ".item-diskon-hidden").val()));
                    $("#edit-item-pajak-id").val(row.find(".item-pajak-id-hidden").val());

                    editItemModal.show();
                });

                $("#saveItemChangesBtn").on("click", function() {
                    const rowId = $("#edit-item-id").val();
                    const row = $(`#table-pembelian tbody tr[data-row-id="${rowId}"]`);

                    row.find(".item-qty, .item-qty-hidden").val($("#edit-item-qty").val());
                    row.find(".item-harga-hidden").val(parseCurrency($("#edit-item-harga").val()));
                    row.find(".item-diskon-hidden").val(parseCurrency($("#edit-item-diskon")
                        .val()));

                    const selectedTaxe = $("#edit-item-pajak-id option:selected");
                    row.find(".item-pajak-id-hidden").val(selectedTaxe.val());
                    row.find(".item-pajak-rate-hidden").val(selectedTaxe.data('rate'));

                    updateRowDisplay(row);
                    editItemModal.hide();
                });

                $("#bayar").on("input", calculateChange);

                $("#statusBayar").on("change", function() {
                    if ($(this).val() === 'Lunas') {
                        const totalAkhir = calculateGrandTotal();
                        $("#bayar").val(totalAkhir).trigger('input');
                    } else {
                        $("#bayar").val(0).trigger('input');
                    }
                });

                $("#bayar").on("blur", function() {
                    const value = $(this).val().replace(/[^0-9]/g, '');
                    const number = parseFloat(value) || 0;
                    $(this).val(new Intl.NumberFormat('id-ID').format(number));
                });

                $('#edit-item-harga, #edit-item-diskon').on('input', function() {
                    formatInputAsCurrency($(this));
                });

                function updateRowDisplay(row) {
                    row.find('.item-qty').text(row.find('.item-qty-hidden').val());
                    row.find('.item-harga').text(formatCurrency(row.find('.item-harga-hidden').val()));
                    row.find('.item-diskon').text(formatCurrency(row.find('.item-diskon-hidden').val()));
                    calculateRow(row);
                    calculateChange();
                }

                $("#form-pembelian").on("submit", function(e) {
                    const totalAkhir = calculateGrandTotal();
                    const bayar = parseCurrency($("#bayar").val());
                    const statusBayar = $("#statusBayar").val();
                    const itemCount = $("#table-pembelian tbody tr").length;

                    if (itemCount === 0) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            window.showToast('warning', 'Harap tambahkan minimal satu produk.');
                        } else {
                            alert('Harap tambahkan minimal satu produk ke dalam daftar pembelian.');
                        }
                        return;
                    }
                    if (bayar < totalAkhir && statusBayar === 'Lunas') {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            window.showToast('warning',
                                'Pembayaran kurang dari total akhir. Mohon ubah status pembayaran Anda atau lunasi pembayaran.'
                            );
                        } else {
                            alert('Pembayaran kurang dari total akhir.');
                        }
                        return;
                    }
                    if (statusBayar === 'Belum Lunas' && bayar >= totalAkhir) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            window.showToast('warning',
                                'Pembayaran sudah lunas. Mohon ubah status pembayaran menjadi "Lunas".'
                            );
                        } else {
                            alert('Ubah status pembayaran menjadi "Lunas".');
                        }
                        return;
                    }

                    $("#saveBtn").prop('disabled', true).html(
                        '<i class="bx bx-loader bx-spin me-1"></i> Memproses...');
                });
            });
        });
    </script>
@endsection
