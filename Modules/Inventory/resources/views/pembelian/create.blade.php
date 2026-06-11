@extends('layouts/contentNavbarLayout')
@section('title', 'Purchase Order - Buat Pembelian')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-purchase.scss'])
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endsection

@section('content')

    <form action="{{ route('pembelian.store') }}" method="post" id="formPurchase">
        @csrf
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-header border-bottom py-3 justify-content-between align-items-center d-flex">
                <h5 class="mb-0 fw-bold">Informasi Purchase</h5>
                <a href="{{ route('pembelian.index') }}" class="btn btn-icon btn-outline-secondary btn-sm"
                    data-bs-toggle="tooltip" aria-label="Kembali" data-bs-original-title="Kembali">
                    <i class="bx bx-arrow-back"></i>
                </a>
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
                <div class="row g-4">
                    <!-- Left: Additional Charges & Status -->
                    {{-- <div class="col-12 col-xl-7">
                        <div class="row g-3">
                        </div>
                    </div> --}}

                    <!-- Right: Calculation Summary Panel -->
                    <div class="totals">
                        <div class="totals-row">
                            <span class="totals-label">Subtotal Keseluruhan</span>
                            <span class="totals-value" id="subtotal">Rp 0</span>
                        </div>
                        <div class="totals-row">
                            <span class="totals-label">PPN</span>
                            <span class="totals-value" id="pajak-total-display">Rp 0</span>
                        </div>
                        <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                            data-type="ongkir" data-label="Ongkos Kirim" role="button" tabindex="0"
                            aria-label="Edit ongkos kirim">
                            <span class="totals-label">Ongkir</span>
                            <span class="totals-value" id="ongkir-display">Rp 0</span>
                            <input type="hidden" name="ongkir" id="ongkir-input" value="0">
                        </div>
                        <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                            data-type="diskon" data-label="Diskon" role="button" tabindex="0"
                            aria-label="Edit diskon">
                            <span class="totals-label">Diskon (Rp)</span>
                            <span class="totals-value" id="diskon-display">Rp 0</span>
                            <input type="hidden" name="diskon" id="diskon-tambahan" value="0">
                        </div>
                        <div class="totals-grand">
                            <span class="grand-label">Total</span>
                            <span class="grand-value" id="total-akhir" aria-live="polite">Rp 0</span>
                        </div>
                        <hr class="border-secondary opacity-25">
                        <div class="d-flex ">
                            <button type="button"
                                class="btn btn-primary w-100 justify-content-between align-items-center"
                                id="btn-open-payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <span class="text-muted">Bayar Sekarang</span>
                                <span class="text-muted" id="cart-total-btn-display">Rp 0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </form>

    @include('inventory::pembelian.partials._modal_purchase')

@endsection

@section('page-script')
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                const formatBankLogo = (state) => {
                    if (!state.id) {
                        return state.text;
                    }
                    const logoUrl = $(state.element).data('logo');
                    if (!logoUrl) {
                        return state.text;
                    }
                    const $state = $(
                        '<span class="d-flex align-items-center">' +
                        '<img src="' + logoUrl +
                        '" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;" alt="logo" />' +
                        '<span>' + state.text + '</span>' +
                        '</span>'
                    );

                    return $state;
                };

                $('.select2').each(function() {
                    const $this = $(this);
                    const isBankSelect = $this.hasClass('select2-bank');
                    let select2Options = {
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10
                    };

                    if (isBankSelect) {
                        select2Options.templateResult = formatBankLogo;
                        select2Options.templateSelection = formatBankLogo;
                    }

                    $this.select2(select2Options);
                });
            } else {
                setTimeout(initSelect2, 100);
            }
        };

        initSelect2();
    </script>
    <script type="module">
        // 1. FUNGSI PENUNGGU JQUERY & SELECT2
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

            // --- A. LOGIKA NON-JQUERY (Quill & Supplier) ---
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
                                if (typeof window.showToast !== 'undefined') window.showToast('success',
                                    data.message);
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
                            }
                        });
                });
            }

            // --- B. LOGIKA JQUERY & KASIR ---
            waitForDependencies(function() {
                // Utilitas Format
                $("#qty").removeAttr("onfocus");
                const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);

                const parseCurrency = (string) => {
                    // Format ID: titik = pemisah ribuan, koma = desimal
                    // Contoh: "50.000" → 50000 | "1.500,50" → 1500.50
                    const cleaned = String(string)
                        .replace(/\./g, '') // hapus titik ribuan
                        .replace(',', '.'); // ganti koma desimal ke titik
                    return parseFloat(cleaned) || 0;
                };

                function formatInputAsCurrency(input) {
                    let value = parseCurrency(input.val());
                    input.val(new Intl.NumberFormat('id-ID').format(value));
                }

                let itemCounter = 0;
                const editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));

                // Inisialisasi Select2 di dalam modal Edit Item (harus pakai dropdownParent agar dropdown tidak terpotong)
                $('#edit-item-pajak-id').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#editItemModal'),
                });

                function formatProduct(produk) {
                    if (!produk.id) return produk.text;
                    const imageUrl = produk.image_url || "{{ asset('assets/img/produk.png') }}";
                    var variantBadge = produk.variant_id ?
                        `<span class="badge bg-label-info text-xs mt-1"><i class="bx bx-list-ul ms-n1 me-1"></i>Varian</span>` :
                        '';

                    return $(`
                        <div class="d-flex align-items-center">
                            <img src="${imageUrl}" class="rounded rounded-2 me-3" style="width:40px; height:40px; object-fit:cover;" />
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
                                        image_url: item.image_url,
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

                // ADD ITEM TO TABLE
                $("#btn-add").on("click", function() {
                    const selectedData = $("#select2").select2('data')[0];
                    const qty = $("#qty").val();

                    if (!selectedData || !selectedData.id || !qty || parseInt(qty) <= 0) {
                        if (typeof window.showToast !== 'undefined') window.showToast('warning',
                            'Harap pilih produk dan tentukan jumlah yang valid.');
                        else alert('Harap pilih produk dan tentukan jumlah yang valid.');
                        return;
                    }

                    const rowId = selectedData.id;
                    const produkId = selectedData.product_id;
                    const variantId = selectedData.variant_id;
                    const produkNama = selectedData.text;
                    const imageUrl = selectedData.image_url ||
                        "{{ asset('assets/img/produk.png') }}";
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
                                    <img src="${imageUrl}" class="rounded rounded-2 me-3" style="width:40px; height:40px; object-fit:cover;" alt="${produkNama}">
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

                // CALCULATIONS
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
                    let totalPajakKeseluruhan = 0;

                    $('#table-pembelian tbody tr').each(function() {
                        const qty = parseFloat($(this).find(".item-qty-hidden").val()) || 0;
                        const hargaBeli = parseFloat($(this).find(".item-harga-hidden").val()) || 0;
                        const diskon = parseFloat($(this).find(".item-diskon-hidden").val()) || 0;
                        const pajakRate = parseFloat($(this).find(".item-pajak-rate-hidden")
                            .val()) || 0;

                        const subtotalItem = (qty * hargaBeli) - diskon;
                        const pajakItem = subtotalItem * (pajakRate / 100);

                        subtotalKeseluruhan += subtotalItem;
                        totalPajakKeseluruhan += pajakItem;
                    });

                    $("#subtotal").text(formatCurrency(subtotalKeseluruhan));
                    $("#pajak-total-display").text(formatCurrency(totalPajakKeseluruhan));

                    const ongkir = parseFloat($("#ongkir-input").val()) || 0;
                    const diskonTambahan = parseFloat($("#diskon-tambahan").val()) || 0;
                    const totalAkhir = subtotalKeseluruhan + totalPajakKeseluruhan - diskonTambahan +
                        ongkir;
                    const finalTotal = totalAkhir < 0 ? 0 : totalAkhir;

                    $("#total-akhir").text(formatCurrency(finalTotal));
                    $("#cart-total-btn-display").text(formatCurrency(finalTotal));

                    // Disable tombol bayar jika keranjang kosong
                    const isCartEmpty = $('#table-pembelian tbody tr').length === 0;
                    $('#btn-open-payment').prop('disabled', isCartEmpty);

                    return finalTotal;
                }

                $("#table-pembelian").on("click", ".btn-remove", function() {
                    const row = $(this).closest("tr");
                    if (confirm(`Hapus item dari daftar?`)) {
                        row.remove();
                        calculateGrandTotal();
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
                    $("#edit-item-pajak-id").val(row.find(".item-pajak-id-hidden").val()).trigger(
                        'change');

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
                    row.find(".item-pajak-rate-hidden").val(selectedTaxe.data('rate') || 0);

                    updateRowDisplay(row);
                    editItemModal.hide();
                });

                $('#edit-item-harga, #edit-item-diskon').on('input', function() {
                    formatInputAsCurrency($(this));
                });

                function updateRowDisplay(row) {
                    row.find('.item-qty').text(row.find('.item-qty-hidden').val());
                    row.find('.item-harga').text(formatCurrency(row.find('.item-harga-hidden').val()));
                    row.find('.item-diskon').text(formatCurrency(row.find('.item-diskon-hidden').val()));
                    calculateRow(row);
                    calculateGrandTotal();
                }

                // ==========================================
                //  LOGIKA MODAL EXTRA COST (Ongkir/Diskon)
                // ==========================================
                const editExtraCostModal = document.getElementById('editExtraCostModal');
                if (editExtraCostModal) {
                    // Update tampilan awal sebelum modal terbuka
                    editExtraCostModal.addEventListener('show.bs.modal', function(event) {
                        const trigger = event.relatedTarget;
                        const type = trigger.getAttribute('data-type');
                        const label = trigger.getAttribute('data-label');

                        document.getElementById('extra-cost-type').value = type;
                        document.getElementById('editExtraCostModalLabel').innerText = 'Edit ' +
                            label;
                        document.getElementById('extra-cost-label').innerText = label;

                        let currentValue = 0;
                        if (type === 'ongkir') currentValue = document.getElementById(
                            'ongkir-input').value;
                        if (type === 'diskon') currentValue = document.getElementById(
                            'diskon-tambahan').value;

                        document.getElementById('extra-cost-value').value = new Intl.NumberFormat(
                            'id-ID').format(currentValue);
                    });

                    // Simpan perubahan ke form
                    document.getElementById('saveExtraCostBtn').addEventListener('click', function() {
                        const type = document.getElementById('extra-cost-type').value;
                        const value = parseCurrency(document.getElementById('extra-cost-value')
                            .value);

                        if (type === 'ongkir') {
                            document.getElementById('ongkir-input').value = value;
                            document.getElementById('ongkir-display').innerText = formatCurrency(
                                value);
                        } else if (type === 'diskon') {
                            document.getElementById('diskon-tambahan').value = value;
                            document.getElementById('diskon-display').innerText = formatCurrency(
                                value);
                        }

                        bootstrap.Modal.getInstance(editExtraCostModal).hide();
                        calculateGrandTotal(); // Panggil kalkulasi ulang
                    });

                    // Format input text jadi mata uang
                    $('#extra-cost-value').on('input', function() {
                        let value = parseCurrency($(this).val());
                        $(this).val(new Intl.NumberFormat('id-ID').format(value));
                    });
                }

                // ==========================================
                //  LOGIKA MODAL PAYMENT
                // ==========================================
                const paymentModalElement = document.getElementById('paymentModal');
                if (paymentModalElement) {
                    const inputJumlah = document.getElementById('jumlah-dibayar-input');
                    const displayChange = document.getElementById('change-display');

                    paymentModalElement.addEventListener('show.bs.modal', function() {
                        const total = calculateGrandTotal();
                        document.getElementById('payment-modal-total').innerText = formatCurrency(
                            total);
                        inputJumlah.value = ''; // Reset input setiap kali modal dibuka
                        calculateModalChange(); // Hitung kembalian awal (dari 0)
                        toggleTransferDetails();
                    });

                    // Tombol Quick Pay (50rb, 100rb)
                    document.querySelectorAll('.quick-pay-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const amount = parseFloat(this.getAttribute('data-amount'));
                            let currentVal = parseCurrency(inputJumlah.value);
                            inputJumlah.value = new Intl.NumberFormat('id-ID').format(
                                currentVal + amount);
                            calculateModalChange();
                        });
                    });

                    // Tombol Uang Pas
                    const btnPayExact = document.getElementById('btn-pay-exact');
                    if (btnPayExact) {
                        btnPayExact.addEventListener('click', function() {
                            const total = calculateGrandTotal();
                            inputJumlah.value = new Intl.NumberFormat('id-ID').format(total);
                            calculateModalChange();
                        });
                    }

                    // Format input dan hitung kembalian real-time
                    $(inputJumlah).on('input', function() {
                        let val = parseCurrency($(this).val());
                        $(this).val(new Intl.NumberFormat('id-ID').format(val));
                        calculateModalChange();
                    });

                    function calculateModalChange() {
                        const total = calculateGrandTotal();
                        const bayar = parseCurrency(inputJumlah.value);
                        const change = bayar - total;

                        displayChange.innerText = formatCurrency(change);
                        if (change < 0) {
                            displayChange.classList.remove('text-success');
                            displayChange.classList.add('text-danger');
                        } else {
                            displayChange.classList.remove('text-danger');
                            displayChange.classList.add('text-success');
                        }
                    }

                    // Toggle rekening transfer
                    const paymentRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
                    const transferDetails = document.getElementById('transfer-details');
                    const bankTujuanSelect = document.getElementById('bank_id');

                    function toggleTransferDetails() {
                        const isTransfer = document.getElementById('pay-transfer').checked;
                        if (isTransfer) {
                            transferDetails.classList.remove('d-none');
                            bankTujuanSelect.setAttribute('required', 'required');
                        } else {
                            transferDetails.classList.add('d-none');
                            bankTujuanSelect.removeAttribute('required');
                            bankTujuanSelect.value = '';
                            $(bankTujuanSelect).trigger('change');
                        }
                    }

                    paymentRadios.forEach(radio => radio.addEventListener('change', toggleTransferDetails));
                }

                // ==========================================
                //  FORM SUBMISSION (Validasi Akhir)
                // ==========================================
                $("#formPurchase").on("submit", function(e) {
                    const itemCount = $("#table-pembelian tbody tr").length;

                    if (itemCount === 0) {
                        e.preventDefault();
                        if (typeof window.showToast !== 'undefined') window.showToast('warning',
                            'Harap tambahkan minimal satu produk.');
                        else alert('Harap tambahkan minimal satu produk.');
                        return;
                    }

                    // Strip format currency dari jumlah_dibayar (misal "50.000" → "50000")
                    const inputJumlahEl = document.getElementById('jumlah-dibayar-input');
                    if (inputJumlahEl) {
                        inputJumlahEl.value = parseCurrency(inputJumlahEl.value);
                    }

                    // Hitung status pembayaran di sisi client (fallback, server tetap override)
                    const totalAkhir = calculateGrandTotal();
                    const bayar = parseFloat(inputJumlahEl ? inputJumlahEl.value : 0) || 0;
                    let statusPembayaran = bayar >= totalAkhir ? 'Lunas' : 'Hutang';

                    if (!document.querySelector('input[name="status_pembayaran"]')) {
                        $(this).append(
                            `<input type="hidden" name="status_pembayaran" value="${statusPembayaran}">`
                        );
                    } else {
                        $('input[name="status_pembayaran"]').val(statusPembayaran);
                    }

                    if (!document.querySelector('select[name="status_barang"]') && !document
                        .querySelector('input[name="status_barang"]')) {
                        $(this).append(
                            `<input type="hidden" name="status_barang" value="Diterima">`);
                    }

                    $("#saveBtn").prop('disabled', true).html(
                        '<i class="bx bx-loader bx-spin me-1"></i> Memproses...');
                });
            });
        });
    </script>
@endsection
