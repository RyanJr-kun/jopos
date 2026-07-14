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
                                <th class="py-3" width="8%"></th>
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

    {{-- MODAL PILIH NOMOR SERI --}}
    <div class="modal fade" id="serialNumberModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title fw-semibold"><i class="bx bx-barcode-reader me-2 text-primary"></i>Pilih Nomor
                        Seri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="d-flex align-items-center mb-3 bg-light p-2 rounded-3 border">
                        <img id="sn-image-produk" src="" class="rounded me-3"
                            style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0 fw-bold" id="sn-name-produk">Nama Produk</h6>
                            <small class="text-muted">Dibutuhkan: <span id="sn-required-count"
                                    class="fw-bold text-danger fs-6">0</span> SN</small>
                        </div>
                    </div>
                    <div id="sn-error-message" class="text-danger small mb-2 fw-medium"></div>

                    {{-- Daftar Checkbox SN akan di-render disini --}}
                    <div class="list-group list-group-flush border rounded-3" id="sn-list-container"
                        style="max-height: 250px; overflow-y: auto;">
                    </div>
                </div>
                <div class="modal-footer justify-content-between pt-2">
                    <span class="text-muted small"><i class="bx bx-info-circle me-1"></i>Pilih sesuai jumlah Qty
                        kirim.</span>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn-confirm-sn">Konfirmasi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    if ($this.attr('id') === 'select-produk') return;
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

                // Variabel Modal SN
                const serialNumberModalEl = document.getElementById('serialNumberModal');
                const serialNumberModal = serialNumberModalEl ? new bootstrap.Modal(serialNumberModalEl) :
                    null;
                const snErrorMessage = document.getElementById('sn-error-message');
                const snListContainer = document.getElementById('sn-list-container');
                const snRequiredCount = document.getElementById('sn-required-count');
                const snConfirmBtn = document.getElementById('btn-confirm-sn');
                let tempRowDataForSN = null;
                let isConfirmingSN =
                    false; // flag biar handler 'hidden.bs.modal' tau ini konfirmasi, bukan batal

                function currentStoreAsalId() {
                    return $storeAsal.val();
                }

                function initProdukSelect2() {
                    if (produkSelect2Initialized) {
                        $selectProduk.empty();
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
                                    store_id: currentStoreAsalId()
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
                                            sku: item.sku,
                                            wajib_seri: item.wajib_seri ||
                                                false // MENGAMBIL STATUS WAJIB SERI
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

                    // Toko asal berubah -> reset pencarian produk. Qty & pilihan
                    // sebelumnya milik toko lama, jadi tidak relevan lagi di toko baru.
                    if (produkSelect2Initialized) {
                        $selectProduk.val(null).trigger('change');
                    }
                    $("#sisa_stok").val('');
                    $("#qty").val('');

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

                if (currentStoreAsalId()) {
                    $storeAsal.trigger('change');
                }

                function updateSubmitButtonState() {
                    $('#btn-submit-form').prop('disabled', $('#table-transfer-items tbody tr[data-row-id]')
                        .length === 0);
                }

                // ==========================
                // FUNGSI BUKA MODAL SN
                // ==========================
                function openSerialNumberModal(rowId, productId, variantId, productName, requiredQty,
                    imgUrl, indexCounter, existingSerials = []) {
                    tempRowDataForSN = {
                        rowId,
                        indexCounter
                    };
                    isConfirmingSN = false;
                    document.getElementById('sn-name-produk').textContent = productName;
                    document.getElementById('sn-required-count').textContent = requiredQty;
                    document.getElementById('sn-image-produk').src = imgUrl;
                    snErrorMessage.textContent = '';
                    snListContainer.innerHTML =
                        `<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>`;

                    serialNumberModal.show();

                    // Endpoint menggunakan route yg sama dengan kasir (samakan dengan edit penjualan)
                    const url = new URL(`{{ route('serialNumber.getProduct') }}`);
                    url.searchParams.set('product_id', productId);
                    if (variantId) url.searchParams.set('variant_id', variantId);

                    // Filter berdasarkan Toko Asal transfer
                    const storeAsal = currentStoreAsalId();
                    if (storeAsal) url.searchParams.set('store_id', storeAsal);

                    // SN yang sudah dipilih sebelumnya (kalau lagi ubah pilihan), biar tetap
                    // muncul & tercentang meski statusnya bukan 'Tersedia' lagi
                    if (existingSerials.length > 0) {
                        url.searchParams.set('existing_sns', existingSerials.join(','));
                    }

                    fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            snListContainer.innerHTML = '';
                            if (!data.serial_numbers || data.serial_numbers.length === 0) {
                                snListContainer.innerHTML =
                                    '<p class="text-center text-muted py-3">Tidak ada nomor seri tersedia di toko ini.</p>';
                                return;
                            }
                            data.serial_numbers.forEach(sn => {
                                const isChecked = existingSerials.includes(sn.serial_number);
                                const isDisabled = sn.status !== 'Tersedia' && !isChecked;
                                snListContainer.insertAdjacentHTML('beforeend', `
                                <label class="list-group-item list-group-item-action d-flex gap-2 align-items-center ${isDisabled ? 'text-muted bg-light' : 'cursor-pointer'}">
                                    <input class="form-check-input flex-shrink-0 sn-checkbox" type="checkbox" value="${sn.serial_number}" ${isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}>
                                    <span class="d-flex justify-content-between align-items-center w-100">
                                        <span class="fw-mono">${sn.serial_number}</span>
                                        <small class="badge bg-label-${sn.status === 'Tersedia' ? 'success' : 'secondary'}">${sn.status}</small>
                                    </span>
                                </label>`);
                            });
                        }).catch(() => {
                            snListContainer.innerHTML =
                                '<p class="text-center text-danger py-3">Gagal memuat nomor seri.</p>';
                        });
                }

                // Batal pilih SN (tutup modal tanpa konfirmasi) -> kalau item ini baru
                // (belum pernah punya SN tersimpan), batalkan juga penambahan itemnya
                // supaya tidak ada baris "wajib SN" yang lolos tanpa nomor seri.
                if (serialNumberModalEl) {
                    serialNumberModalEl.addEventListener('hidden.bs.modal', function() {
                        if (isConfirmingSN || !tempRowDataForSN) {
                            isConfirmingSN = false;
                            tempRowDataForSN = null;
                            return;
                        }

                        const row = $(
                            `#table-transfer-items tbody tr[data-row-id="${tempRowDataForSN.rowId}"]`
                        );
                        const hasExistingSN = row.find('.item-sn-hidden').length > 0;

                        if (row.length > 0 && !hasExistingSN) {
                            row.remove();
                            if ($('#table-transfer-items tbody tr[data-row-id]').length === 0) {
                                $('#table-transfer-items tbody').append(
                                    `<tr id="no-items-row"><td colspan="4" class="text-center py-4"><p class="text-sm fw-bold mb-0">Belum ada item yang ditambahkan.</p></td></tr>`
                                );
                            }
                            window.showToast('warning',
                                'Pemilihan nomor seri dibatalkan, item tidak ditambahkan.');
                            updateSubmitButtonState();
                        }

                        tempRowDataForSN = null;
                    });
                }

                // ==========================
                // KONFIRMASI SN MODAL
                // ==========================
                if (snConfirmBtn) {
                    snConfirmBtn.addEventListener('click', () => {
                        const requiredQty = parseInt(snRequiredCount.textContent);
                        const selected = [...document.querySelectorAll('.sn-checkbox:checked')].map(
                            cb => cb.value);

                        if (selected.length !== requiredQty) {
                            snErrorMessage.textContent =
                                `Anda harus memilih tepat ${requiredQty} nomor seri (Baru memilih: ${selected.length}).`;
                            return;
                        }

                        const row = $(
                            `#table-transfer-items tbody tr[data-row-id="${tempRowDataForSN.rowId}"]`
                        );
                        const idx = tempRowDataForSN.indexCounter;

                        if (row.length > 0) {
                            // Bersihkan data SN sebelumnya jika ada
                            row.find('.item-sn-hidden').remove();

                            // Inject array SN ke form
                            let hiddenInputsHtml = '';
                            selected.forEach(sn => {
                                hiddenInputsHtml +=
                                    `<input type="hidden" name="items[${idx}][serial_numbers][]" class="item-sn-hidden" value="${sn}">`;
                            });
                            row.append(hiddenInputsHtml);

                            // Menampilkan UI badge biru SN di bawah nama produk
                            let snDisplayHtml =
                                `<small class="text-info d-block mt-1 fw-medium"><i class="bx bx-barcode text-xs me-1"></i>SN: ${selected.join(', ')}</small>`;
                            row.find('.sn-display-container').html(snDisplayHtml);
                        }
                        isConfirmingSN = true;
                        serialNumberModal.hide();
                    });
                }

                // ==========================
                // ADD ITEM TO TABLE
                // ==========================
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

                    const wajibSeri = selectedData.wajib_seri ? 1 : 0;

                    const newRow = `
                        <tr data-row-id="${rowId}" data-wajib-seri="${wajibSeri}">
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${selectedData.product_id}">
                            <input type="hidden" name="items[${itemCounter}][product_variant_id]" value="${selectedData.variant_id || ''}">
                            <input type="hidden" name="items[${itemCounter}][qty_kirim]" class="item-qty-hidden" value="${qty}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${imageUrl}" class="rounded rounded-2 me-3" style="width:40px; height:40px; object-fit:cover;">
                                    <div>
                                        <h6 class="mb-0 text-sm">${selectedData.text}</h6>
                                        ${variantHtml}
                                        <div class="sn-display-container"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center text-sm">${stokTersedia}</td>
                            <td class="align-middle text-center"><span class="fw-bold">${qty}</span></td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center">
                                    ${wajibSeri ? `<button type="button" class="btn btn-link text-primary p-0 m-0 me-2 btn-edit-sn" title="Ubah Nomor Seri"><i class="bx bx-barcode-reader"></i></button>` : ''}
                                    <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove-item" title="Hapus Item"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#no-items-row').remove();
                    $("#table-transfer-items tbody").append(newRow);

                    // TRIGGER JIKA WAJIB SERI
                    if (selectedData.wajib_seri) {
                        openSerialNumberModal(rowId, selectedData.product_id, selectedData
                            .variant_id, selectedData.text, qty, imageUrl, itemCounter);
                    }

                    itemCounter++;
                    $selectProduk.val(null).trigger("change");
                    $("#qty").val('');
                    $("#sisa_stok").val('');
                    updateSubmitButtonState();
                });

                // UBAH PILIHAN SN UNTUK ITEM YANG SUDAH DITAMBAHKAN
                $("#table-transfer-items").on("click", ".btn-edit-sn", function() {
                    const row = $(this).closest('tr');
                    const rowId = row.data('row-id');
                    const productId = row.find('input[name$="[product_id]"]').val();
                    const variantId = row.find('input[name$="[product_variant_id]"]').val() || null;
                    const productName = row.find('h6').first().text().trim();
                    const imageUrl = row.find('img').attr('src');
                    const requiredQty = parseInt(row.find('.item-qty-hidden').val()) || 0;

                    const nameAttr = row.find('.item-qty-hidden').attr('name');
                    const match = nameAttr ? nameAttr.match(/items\[(\d+)\]/) : null;
                    const indexCounter = match ? match[1] : itemCounter;

                    const existingSerials = row.find('.item-sn-hidden').map(function() {
                        return $(this).val();
                    }).get();

                    openSerialNumberModal(rowId, productId, variantId, productName, requiredQty,
                        imageUrl, indexCounter, existingSerials);
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
                        return;
                    }

                    // Pastikan setiap item yang wajib SN sudah punya nomor seri lengkap
                    // sebelum transfer dikirim.
                    let snIncomplete = false;
                    $('#table-transfer-items tbody tr[data-row-id]').each(function() {
                        const $row = $(this);
                        $row.removeClass('table-danger');

                        if ($row.attr('data-wajib-seri') !== '1') return;

                        const requiredQty = parseInt($row.find('.item-qty-hidden').val()) ||
                            0;
                        const snCount = $row.find('.item-sn-hidden').length;

                        if (snCount !== requiredQty) {
                            snIncomplete = true;
                            $row.addClass('table-danger');
                        }
                    });

                    if (snIncomplete) {
                        e.preventDefault();
                        window.showToast('warning',
                            'Ada item wajib nomor seri yang pemilihannya belum lengkap. Silakan klik ikon barcode untuk melengkapinya.'
                        );
                    }
                });
            });
        });
    </script>
@endsection
