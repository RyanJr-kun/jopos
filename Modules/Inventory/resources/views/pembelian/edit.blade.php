@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Purchase - ' . $pembelian->referensi)
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endsection

@section('content')
    <form action="{{ route('pembelian.update', $pembelian->referensi) }}" method="post" id="form-pembelian-edit">
        @method('PUT')
        @csrf

        <div class="card rounded-3 shadow-sm border-0">
            <div
                class="card-header bg-transparent border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Edit Purchase <span class="text-muted fs-6 ms-1">#{{ $pembelian->referensi }}</span>
                </h5>
                <a href="{{ route('pembelian.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body p-4">
                <!-- HEADER INFO -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="Supplier" class="form-label fw-semibold">Supplier <span
                                class="text-danger">*</span></label>
                        <select class="form-select  select2 @error('supplier_id') is-invalid @enderror" name="supplier_id"
                            id="Supplier" required>
                            <option value="" disabled>Pilih Supplier</option>
                            {{-- Perhatikan variabel $pemasok atau $supplier tergantung dari controller Anda, saya gunakan $pemasok sesuai file asli Anda --}}
                            @foreach ($pemasok as $item)
                                <option value="{{ $item->id }}" @selected(old('supplier_id', $pembelian->supplier_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback d-block text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal <span
                                class="text-danger">*</span></label>
                        <input id="tanggal" name="tanggal" type="date"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('Y-m-d')) }}"
                            required>
                        @error('tanggal')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
                        <input id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" type="date"
                            class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror"
                            value="{{ old('tanggal_jatuh_tempo', \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->format('Y-m-d')) }}">
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                    </div>



                    <div class="col-12 col-md-12 col-lg-4">
                        <label for="referensi" class="form-label fw-semibold">No Invoice</label>
                        <input type="text" class="form-control bg-label-light" id="referensi" name="referensi"
                            value="{{ $pembelian->referensi }}" readonly>
                    </div>
                </div>

                <!-- PRODUCT SEARCH PANEL -->
                <div class="bg-label-light p-3 rounded-3 border mb-4">
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

                <!-- TABLET & DESKTOP TABLE -->
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
                            <!-- POPULASI DATA LAMA DARI DATABASE -->
                            @foreach ($pembelian->details as $index => $detail)
                                @php
                                    $pajak_rate = $detail->pajak->rate ?? 0;
                                    $subtotal_item = $detail->harga_beli * $detail->qty - ($detail->diskon ?? 0);
                                    $pajak_amount = $subtotal_item * ($pajak_rate / 100);
                                    $subtotal_with_tax = $detail->subtotal;

                                    // Pembentukan ID Unik agar script JS mengenali perbedaannya
                                    $rowId = $detail->product_variant_id
                                        ? "{$detail->product_id}-{$detail->product_variant_id}"
                                        : $detail->product_id;

                                    // Set fallback default
                                    $imageUrl = asset('assets/img/produk.png');
                                    $namaVarian = '';

                                    if ($detail->product_variant_id && $detail->varian) {
                                        // 1. Cek gambar varian
                                        if (!empty($detail->varian->img_variant)) {
                                            $imageUrl = asset('storage/' . $detail->varian->img_variant);
                                        }
                                        // 2. Jika kosong, pakai cara jitu kamu: bungkus path primaryImage dengan asset(storage)
                                        elseif (!empty($detail->produk->primaryImage->path)) {
                                            $imageUrl = asset('storage/' . $detail->produk->primaryImage->path);
                                        }

                                        // Susun nama varian jika ada relasi opsi
                                        $variantOpts = [];
                                        if ($detail->varian->relationLoaded('options')) {
                                            foreach ($detail->varian->options as $opt) {
                                                $variantOpts[] = $opt->value;
                                            }
                                        }
                                        $namaVarian = !empty($variantOpts)
                                            ? implode(' / ', $variantOpts)
                                            : 'SKU: ' . $detail->varian->sku;
                                    } else {
                                        // Jika produk simple, sama: bungkus path primaryImage dengan asset(storage)
                                        if (!empty($detail->produk->primaryImage->path)) {
                                            $imageUrl = asset('storage/' . $detail->produk->primaryImage->path);
                                        }
                                    }
                                @endphp
                                <tr data-row-id="{{ $rowId }}">
                                    <input type="hidden" name="items[{{ $index }}][product_id]"
                                        value="{{ $detail->product_id }}">
                                    <input type="hidden" name="items[{{ $index }}][product_variant_id]"
                                        value="{{ $detail->product_variant_id ?? '' }}">
                                    <input type="hidden" name="items[{{ $index }}][qty]" class="item-qty-hidden"
                                        value="{{ $detail->qty }}">
                                    <input type="hidden" name="items[{{ $index }}][harga_beli]"
                                        class="item-harga-hidden" value="{{ $detail->harga_beli }}">
                                    <input type="hidden" name="items[{{ $index }}][diskon]"
                                        class="item-diskon-hidden" value="{{ $detail->diskon ?? 0 }}">
                                    <input type="hidden" name="items[{{ $index }}][taxe_id]"
                                        class="item-pajak-id-hidden" value="{{ $detail->taxe_id ?? '' }}">
                                    <input type="hidden" class="item-pajak-rate-hidden" value="{{ $pajak_rate }}">

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $imageUrl }}" class="avatar avatar-sm me-3" alt="Produk">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm item-name">
                                                    {{ $detail->produk->name_product ?? 'Produk Dihapus' }}</h6>
                                                @if ($namaVarian)
                                                    <small class="text-muted"><i
                                                            class="bx bx-list-ul text-xs me-1"></i>{{ $namaVarian }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center"><span
                                            class="item-qty">{{ $detail->qty }}</span></td>
                                    <td class="align-middle"><span
                                            class="item-harga">{{ 'Rp ' . number_format($detail->harga_beli, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="align-middle"><span
                                            class="item-pajak">{{ 'Rp ' . number_format($pajak_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="align-middle"><span
                                            class="item-diskon">{{ 'Rp ' . number_format($detail->diskon ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="subtotal-item text-start text-sm fw-bold">
                                        {{ 'Rp ' . number_format($subtotal_with_tax, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-link text-info p-0 m-0 me-2 btn-edit"
                                                title="Edit Item"><i class="bx bx-edit"></i></button>
                                            <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove"
                                                title="Hapus Item"><i class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- BOTTOM SECTION: SETTINGS & TOTALS -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-xl-7">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="ongkir" class="form-label fw-semibold">Ongkos Kirim</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-label-light">Rp</span>
                                    <input type="text" name="ongkir" id="ongkir" class="form-control text-end"
                                        value="{{ old('ongkir', $pembelian->ongkir) }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="diskon-tambahan" class="form-label fw-semibold">Diskon Tambahan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-label-light">Rp</span>
                                    <input type="text" name="diskon_tambahan" id="diskon-tambahan"
                                        class="form-control text-end" value="{{ old('diskon', $pembelian->diskon) }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="statusBarang" class="form-label fw-semibold">Status Barang <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('status_barang') is-invalid @enderror"
                                    name="status_barang" id="statusBarang" required>
                                    <option value="Diterima" @selected(old('status_barang', $pembelian->status_barang) == 'Diterima')>Diterima</option>
                                    <option value="Belum Diterima" @selected(old('status_barang', $pembelian->status_barang) == 'Belum Diterima')>Belum Diterima</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="statusBayar" class="form-label fw-semibold">Status Pembayaran <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('status_pembayaran') is-invalid @enderror"
                                    name="status_pembayaran" id="statusBayar" required>
                                    <option value="Lunas" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'Lunas')>Lunas</option>
                                    <option value="Belum Lunas" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'Belum Lunas')>Belum Lunas</option>
                                    <option value="Dibatalkan" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'Dibatalkan')>Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-5">
                        <div class="bg-label-light p-4 rounded-3 border h-100">
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
                                        value="{{ old('jumlah_dibayar', $pembelian->jumlah_dibayar) }}">
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
                        <div id="quill-editor-catatan" style="min-height: 120px; border: none;">{!! old('catatan', $pembelian->catatan) !!}
                        </div>
                    </div>
                    <input type="hidden" name="catatan" id="catatan"
                        value="{{ old('catatan', $pembelian->catatan) }}">
                    @error('catatan')
                        <div class="invalid-feedback d-block text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ACTIONS -->
                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('pembelian.index') }}" id="cancel-button"
                        class="btn btn-outline-secondary w-100 w-sm-auto order-2 order-sm-1">Batalkan</a>
                    <button id="saveBtn" type="submit"
                        class="btn btn-info w-100 w-sm-auto order-1 order-sm-2 px-4">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal Edit Item --}}
    <div class="modal rounded-3 fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="editItemModalLabel">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm" onsubmit="return false;">
                        <input type="hidden" id="edit-item-id">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Product</label>
                                <input type="text" class="form-control bg-label-light" id="edit-item-name" readonly
                                    disabled>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="edit-item-qty" class="form-label fw-semibold">Qty <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit-item-qty" min="1" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="edit-item-harga" class="form-label fw-semibold">Harga Beli <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-label-light">Rp</span>
                                    <input type="text" class="form-control text-end" id="edit-item-harga"
                                        placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="edit-item-pajak-id" class="form-label fw-semibold">Pajak (Taxe)</label>
                                <select class="form-select select2" id="edit-item-pajak-id">
                                    <option value="" data-rate="0" selected>Tidak ada</option>
                                    @foreach ($taxes as $pajak)
                                        <option value="{{ $pajak->id }}" data-rate="{{ $pajak->rate }}">
                                            {{ $pajak->name_taxe }} ({{ $pajak->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="edit-item-diskon" class="form-label fw-semibold">Diskon Item</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-label-light">Rp</span>
                                    <input type="text" class="form-control text-end text-danger" id="edit-item-diskon"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="saveItemChangesBtn">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script type="module">
        // 1. FUNGSI PENUNGGU JQUERY & SELECT2 (Menghindari "Uncaught ReferenceError: $ is not defined")
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

            // --- A. INISIALISASI QUILL (Bisa jalan tanpa jQuery) ---
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

            // --- B. LOGIKA KASIR MENGGUNAKAN JQUERY ---
            waitForDependencies(function() {

                // --- Inisialisasi Default Select2 (Status, dll) ---
                $('.select2:not(#select2)').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                    width: '100%'
                });

                // Utilitas Format Mata Uang
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

                // Ambil nilai tertinggi counter dari backend agar item baru tidak menimpa array yang lama
                let itemCounter =
                    {{ $pembelian->details->count() > 0 ? collect($pembelian->details->keys())->max() + 1 : 0 }};
                const editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));

                // Select2 untuk Cari Produk
                function formatProduct(produk) {
                    if (!produk.id) return produk.text;
                    var defaultImage = "{{ asset('assets/img/produk.png') }}";
                    var imageUrl = produk.img_produk ? `{{ asset('storage/') }}/${produk.img_produk}` :
                        defaultImage;
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

                // Tambah Item ke Tabel
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
                        const imageUrl = produkImg ? `{{ asset('storage/') }}/${produkImg}` :
                            defaultImage;

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
                                    <img src="${imageUrl}" class="avatar avatar-sm me-3" alt="Produk">
                                    <h6 class="mb-0 text-sm item-name">${produkNama}</h6>
                                </div>
                            </td>
                            <td class="align-middle text-center"><span class="item-qty">${qtyToAdd}</span></td>
                            <td class="align-middle"><span class="item-harga">${formatCurrency(hargaBeli)}</span></td>
                            <td class="align-middle"><span class="item-pajak">${formatCurrency(pajakAwal)}</span></td>
                            <td class="align-middle"><span class="item-diskon">Rp 0</span></td>
                            <td class="subtotal-item text-start text-sm fw-bold">${formatCurrency(subtotalDenganTaxe)}</td>
                            <td>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-link text-info p-0 m-0 me-2 btn-edit" title="Edit Item"><i class="bx bx-edit"></i></button>
                                    <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove" title="Hapus Item"><i class="bx bx-trash"></i></button>
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
                    calculateChange();
                });

                // Kalkulasi Dinamis
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

                    const ongkir = parseCurrency($("#ongkir").val());
                    const diskonTambahan = parseCurrency($("#diskon-tambahan").val());
                    const totalAkhir = subtotalKeseluruhan - diskonTambahan + ongkir;

                    $("#total-akhir").text(formatCurrency(totalAkhir < 0 ? 0 : totalAkhir));
                    return totalAkhir < 0 ? 0 : totalAkhir;
                }

                function calculateChange() {
                    const totalAkhir = calculateGrandTotal();
                    const bayar = parseCurrency($("#bayar").val());

                    $("#jumlah_dibayar_hidden").val(bayar);
                    const sisa = bayar - totalAkhir;

                    $("#kembalian").text(formatCurrency(sisa));
                    if (sisa < 0) {
                        $("#kembalian").removeClass('text-dark').addClass('text-danger');
                    } else {
                        $("#kembalian").removeClass('text-danger').addClass('text-dark');
                    }
                }

                // Event Listener Hapus & Update Data Tabel
                $("#ongkir, #diskon-tambahan, #bayar").on("input", calculateChange);

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
                    // Ambil gabungan nama + varian untuk modal agar user jelas
                    let fullName = row.find('.item-name').text();
                    let variantName = row.find('small.text-muted').text();
                    if (variantName) fullName += " " + variantName;

                    $("#edit-item-name").val(fullName);
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

                function updateRowDisplay(row) {
                    row.find('.item-qty').text(row.find('.item-qty-hidden').val());
                    row.find('.item-harga').text(formatCurrency(row.find('.item-harga-hidden').val()));
                    row.find('.item-diskon').text(formatCurrency(row.find('.item-diskon-hidden').val()));
                    calculateRow(row);
                    calculateChange();
                }

                // Update Status Bayar
                $("#statusBayar").on("change", function() {
                    const status = $(this).val();
                    if (status === 'Lunas') {
                        const totalAkhir = calculateGrandTotal();
                        $("#bayar").val(totalAkhir).trigger('input');
                    } else if (status === 'Belum Lunas') {
                        $("#bayar").val(0).trigger('input');
                    }
                });

                // Format Currency saat input
                $('#edit-item-harga, #edit-item-diskon, #ongkir, #diskon-tambahan, #bayar').on(
                    'blur focusout',
                    function() {
                        formatInputAsCurrency($(this));
                    });

                // Validasi saat form disubmit
                $("#form-pembelian-edit").on("submit", function(e) {
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
                        '<i class="bx bx-loader bx-spin me-1"></i> Menyimpan...');
                });

                // --- TRIGGER AWAL SAAT HALAMAN DILAKUKAN REFRESH ---
                // 1. Kalkulasi ulang setiap baris yang sudah ada dari database
                $('#table-pembelian tbody tr').each(function() {
                    calculateRow($(this));
                });

                // 2. Format nilai bayar, ongkir, dan diskon
                $('#bayar').val($('#jumlah_dibayar_hidden').val());
                $("#ongkir, #diskon-tambahan, #bayar").each(function() {
                    formatInputAsCurrency($(this));
                });

                // 3. Kalkulasi Grand Total dan Kembalian
                calculateChange();
            });
        });
    </script>
@endsection
