@extends('layouts/contentNavbarLayout')
@section('title', 'Buat Transfer Stok - Inventory')

@section('content')
    <form action="{{ route('stok-transfer.store') }}" method="POST" id="transferForm">
        @csrf

        <div class="card rounded-2 mb-4">
            <div class="card-header pb-0 px-3 pt-2">
                <h6 class="mb-0">Informasi Transfer</h6>
            </div>
            <div class="card-body pt-2">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="store_asal_id" class="form-label fw-semibold">Toko Asal <span
                                class="text-danger">*</span></label>
                        <select name="store_asal_id" id="store_asal_id" class="form-select select2" required
                            {{ count($storesAsal) === 1 ? 'disabled' : '' }}>
                            <option value="" disabled {{ !$defaultStoreAsalId ? 'selected' : '' }}>Pilih Toko Asal
                            </option>
                            @foreach ($storesAsal as $store)
                                <option value="{{ $store->id }}" @selected($defaultStoreAsalId == $store->id)>
                                    {{ $store->name_toko }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Kalau di-disable, tetap kirim value-nya lewat hidden input --}}
                        @if (count($storesAsal) === 1)
                            <input type="hidden" name="store_asal_id" value="{{ $storesAsal->first()->id }}">
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="store_tujuan_id" class="form-label fw-semibold">Toko Tujuan <span
                                class="text-danger">*</span></label>
                        <select name="store_tujuan_id" id="store_tujuan_id" class="form-select select2" required>
                            <option value="" disabled selected>Pilih Toko Tujuan</option>
                            @foreach ($storesTujuan as $store)
                                <option value="{{ $store->id }}">{{ $store->name_toko }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="catatan_kirim" class="form-label fw-semibold">Catatan Pengiriman</label>
                        <input type="text" id="catatan_kirim" name="catatan_kirim" class="form-control"
                            placeholder="Opsional">
                    </div>
                </div>
                <div class="alert alert-warning d-flex align-items-center mt-3 mb-0" role="alert">
                    <i class="bx bx-info-circle fs-5 me-2"></i>
                    <div class="text-sm">
                        Stok toko asal akan langsung berkurang begitu transfer dikirim. Barang dianggap "dalam
                        perjalanan" sampai toko tujuan mengonfirmasi penerimaan.
                    </div>
                </div>
            </div>
        </div>

        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2">
                <h6 class="mb-0">Item yang Dikirim</h6>
                <p class="text-sm mb-0" id="itemHint">Pilih toko asal terlebih dahulu untuk mencari produk.</p>
            </div>
            <div class="card-body pt-2">
                <div class="p-3 rounded-3 border mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label for="select-produk" class="form-label fw-semibold">Cari Produk</label>
                            <select name="select-produk" id="select-produk" class="form-control" disabled></select>
                        </div>
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="sisa_stok" class="form-label fw-semibold">Stok Tersedia</label>
                            <input type="number" id="sisa_stok" class="form-control bg-white" readonly>
                        </div>
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="qty" class="form-label fw-semibold">Qty Kirim</label>
                            <input type="number" id="qty" class="form-control" min="1" placeholder="0">
                        </div>
                        <div class="col-4 col-md-6 col-lg-3">
                            <button type="button" class="btn btn-info w-100" id="btn-add">
                                <i class="bx bx-plus"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive text-nowrap mb-0 border rounded-3">
                    <table class="table table-hover align-middle mb-0" id="table-transfer-items">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th class="text-xs font-weight-bolder py-3">Produk</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="15%">Stok Tersedia</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="12%">Qty Kirim</th>
                                <th class="py-3" width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="no-items-body">
                            <tr id="no-items-row">
                                <td colspan="4" class="text-center py-4">
                                    <p class="text-sm fw-bold mb-0">Belum ada item yang ditambahkan.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('stok-transfer.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" id="btn-submit-form" class="btn btn-info" disabled>
                    <i class="bx bx-send me-1"></i> Kirim Transfer
                </button>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    if ($this.attr('id') === 'select-produk') return; // di-handle terpisah
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: false,
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

    <script type="module">
        function waitForDependencies(callback) {
            if (window.$ && window.$.fn && window.$.fn.select2) {
                callback();
            } else {
                setTimeout(() => waitForDependencies(callback), 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            waitForDependencies(function() {
                const defaultImage = "{{ asset('assets/img/produk.png') }}";
                let itemCounter = 0;
                let produkSelect2Initialized = false;

                const $storeAsal = $('#store_asal_id');
                const $selectProduk = $('#select-produk');

                function currentStoreAsalId() {
                    return $storeAsal.val();
                }

                function initProdukSelect2() {
                    if (produkSelect2Initialized) {
                        $selectProduk.select2('destroy');
                    }

                    $selectProduk.select2({
                        placeholder: 'Ketik untuk mencari produk atau varian...',
                        minimumInputLength: 0,
                        language: 'id',
                        templateResult: formatProduct,
                        templateSelection: function(produk) {
                            if (produk.id) {
                                $("#sisa_stok").val(produk.qty);
                                setTimeout(() => $("#qty").val(1).focus(), 150);
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
                                    page: params.page || 1,
                                    store_id: currentStoreAsalId() // stok difilter sesuai toko asal
                                };
                            },
                            processResults: function(data) {
                                let items = data.data ? data.data : (Array.isArray(data) ?
                                    data : []);
                                return {
                                    results: items.map(function(item) {
                                        let combinedId = item.variant_id ?
                                            `${item.id}-${item.variant_id}` : item.id;
                                        return {
                                            id: combinedId,
                                            product_id: item.id,
                                            variant_id: item.variant_id || null,
                                            text: item.variant_name ?
                                                `${item.name_product} - ${item.variant_name}` :
                                                item.name_product,
                                            text2: item.variant_name,
                                            img_produk: item.img_produk || null,
                                            qty: item.qty,
                                            sku: item.sku
                                        };
                                    }),
                                    pagination: {
                                        more: data.next_page_url !== null
                                    }
                                };
                            },
                            cache: false
                        }
                    });
                    produkSelect2Initialized = true;
                }

                function formatProduct(produk) {
                    if (!produk.id) return produk.text;
                    const imageUrl = produk.img_produk ? produk.img_produk : defaultImage;
                    const variantBadge = produk.variant_id ?
                        `<span class="badge bg-label-info text-xs mt-1"><i class="bx bx-list-ul ms-n1 me-1"></i>${produk.text2}</span>` :
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

                // Aktifkan pencarian produk hanya setelah toko asal dipilih
                $storeAsal.on('change', function() {
                    const storeId = $(this).val();
                    $('#table-transfer-items tbody tr[data-row-id]').remove();
                    if ($('#no-items-row').length === 0) {
                        $('#table-transfer-items tbody').append(
                            `<tr id="no-items-row"><td colspan="4" class="text-center py-4"><p class="text-sm fw-bold mb-0">Belum ada item yang ditambahkan.</p></td></tr>`
                        );
                    }
                    itemCounter = 0;
                    updateSubmitButtonState();

                    if (storeId) {
                        $selectProduk.prop('disabled', false);
                        $('#itemHint').text('Cari produk yang tersedia di toko asal terpilih.');
                        initProdukSelect2();
                    } else {
                        $selectProduk.prop('disabled', true);
                        $('#itemHint').text(
                            'Pilih toko asal terlebih dahulu untuk mencari produk.');
                    }
                });

                // Kalau toko asal udah otomatis terisi dari awal (staff non-view-toko-gudang)
                if (currentStoreAsalId()) {
                    $storeAsal.trigger('change');
                }

                function updateSubmitButtonState() {
                    $('#btn-submit-form').prop('disabled', $('#table-transfer-items tbody tr[data-row-id]')
                        .length === 0);
                }

                $("#btn-add").on("click", function() {
                    const selectedData = $selectProduk.select2('data')[0];
                    const qty = parseInt($("#qty").val());
                    const stokTersedia = parseInt($("#sisa_stok").val() || 0);

                    if (!currentStoreAsalId()) {
                        window.showToast('warning', 'Pilih toko asal terlebih dahulu.');
                        return;
                    }
                    if (!selectedData || !selectedData.id) {
                        window.showToast('warning', 'Silakan pilih produk terlebih dahulu.');
                        return;
                    }
                    if (isNaN(qty) || qty < 1) {
                        window.showToast('warning', 'Qty kirim harus berupa angka dan minimal 1.');
                        return;
                    }
                    if (qty > stokTersedia) {
                        window.showToast('warning',
                            'Qty kirim melebihi stok tersedia di toko asal.');
                        return;
                    }

                    const rowId = selectedData.id;
                    if ($(`#table-transfer-items tbody tr[data-row-id="${rowId}"]`).length > 0) {
                        window.showToast('warning', 'Produk/varian ini sudah ada dalam daftar.');
                        return;
                    }

                    const imageUrl = selectedData.img_produk ? selectedData.img_produk :
                        defaultImage;
                    const variantHtml = selectedData.text2 ?
                        `<small class="text-muted d-block"><i class="bx bx-list-ul text-xs me-1"></i>${selectedData.text2}</small>` :
                        '';

                    const newRow = `
                        <tr data-row-id="${rowId}">
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${selectedData.product_id}">
                            <input type="hidden" name="items[${itemCounter}][product_variant_id]" value="${selectedData.variant_id || ''}">
                            <input type="hidden" name="items[${itemCounter}][qty_kirim]" value="${qty}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${imageUrl}" class="rounded rounded-2 me-3" style="width:40px; height:40px; object-fit:cover;">
                                    <div>
                                        <h6 class="mb-0 text-sm">${selectedData.text}</h6>
                                        ${variantHtml}
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center text-sm">${stokTersedia}</td>
                            <td class="align-middle text-center"><span class="fw-bold">${qty}</span></td>
                            <td class="align-middle text-center">
                                <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove-item" title="Hapus Item">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#no-items-row').remove();
                    $("#table-transfer-items tbody").append(newRow);
                    itemCounter++;

                    $selectProduk.val(null).trigger("change");
                    $("#qty").val('');
                    $("#sisa_stok").val('');
                    updateSubmitButtonState();
                });

                $("#table-transfer-items").on("click", ".btn-remove-item", function() {
                    $(this).closest('tr').remove();
                    if ($('#table-transfer-items tbody tr[data-row-id]').length === 0) {
                        $('#table-transfer-items tbody').append(
                            `<tr id="no-items-row"><td colspan="4" class="text-center py-4"><p class="text-sm fw-bold mb-0">Belum ada item yang ditambahkan.</p></td></tr>`
                        );
                    }
                    updateSubmitButtonState();
                });

                $("#transferForm").on("submit", function(e) {
                    const itemCount = $("#table-transfer-items tbody tr[data-row-id]").length;
                    if (itemCount === 0) {
                        e.preventDefault();
                        window.showToast('warning',
                            'Harap tambahkan minimal satu item untuk dikirim.');
                    }
                });
            });
        });
    </script>
@endsection
