@extends('layouts/contentNavbarLayout')
@section('title', 'Buat Penyesuaian Stok - Inventory')

@section('content')
    <form action="{{ route('stok-penyesuaian.store') }}" method="POST" id="adjustmentForm">
        @csrf

        <div class="card rounded-2 mb-4">
            <div class="card-header pb-0 px-3 pt-2">
                <h6 class="mb-0">Informasi Umum</h6>
            </div>
            <div class="card-body pt-2">
                <div class="row g-3">
                    @can('view-toko-gudang')
                        <div class="col-md-4">
                            <label for="store_id" class="form-label fw-semibold">Toko <span class="text-danger">*</span></label>
                            <select name="store_id" id="store_id" class="form-select select2" required>
                                <option value="" disabled selected>Pilih Toko</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>
                                        {{ $store->name_toko }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                    <div class="col-md-{{ auth()->user()->can('view-toko-gudang') ? 8 : 12 }}">
                        <label for="catatan" class="form-label fw-semibold">Catatan Umum (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="1"
                            placeholder="Contoh: Penyesuaian stok akhir bulan">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2">
                <h6 class="mb-0">Item Penyesuaian</h6>
                <p class="text-sm mb-0">Tambahkan produk yang akan disesuaikan stoknya.</p>
            </div>
            <div class="card-body pt-2">
                {{-- PRODUCT SEARCH PANEL --}}
                <div class="p-3 rounded-3 border mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label for="select-produk" class="form-label fw-semibold">Cari Produk</label>
                            <select name="select-produk" id="select-produk" class="form-control"></select>
                        </div>
                        <div class="col-4 col-md-3 col-lg-1">
                            <label for="sisa_stok" class="form-label fw-semibold">Stok Saat Ini</label>
                            <input type="number" id="sisa_stok" class="form-control bg-white" readonly>
                        </div>
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="adjustment-category" class="form-label fw-semibold">Kategori</label>
                            <select id="adjustment-category" class="form-select select2" data-placeholder="Pilih Kategori">
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="adjustment-arah" class="form-label fw-semibold">Arah</label>
                            <select id="adjustment-arah" class="form-select select2" data-placeholder="Pilih Arah">
                                <option value="in">Masuk</option>
                                <option value="out">Keluar</option>
                            </select>
                        </div>
                        <div class="col-4 col-md-3 col-lg-1">
                            <label for="qty" class="form-label fw-semibold">Qty</label>
                            <input type="number" id="qty" class="form-control" min="1" placeholder="0">
                        </div>
                        <div class="col-8 col-md-6 col-lg-2">
                            <label for="adjustment-reason" class="form-label fw-semibold">Alasan</label>
                            <input type="text" id="adjustment-reason" class="form-control"
                                placeholder="Contoh: 2 unit pecah">
                        </div>
                        <div class="col-12 col-lg-12 mt-2">
                            <button type="button" class="btn btn-info w-100" id="btn-add">
                                <i class="bx bx-plus"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive text-nowrap mb-0 border rounded-3">
                    <table class="table table-hover align-middle mb-0" id="table-adjustment">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th class="text-xs font-weight-bolder py-3">Produk</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="12%">Kategori</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="10%">Arah</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="8%">Jumlah</th>
                                <th class="text-xs font-weight-bolder py-3" width="25%">Alasan</th>
                                <th class="py-3" width="5%"></th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0" id="no-items-body">
                            <tr id="no-items-row">
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-sm fw-bold mb-0">Belum ada item yang ditambahkan.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('stok-penyesuaian.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" id="btn-submit-form" class="btn btn-info" disabled>Simpan Penyesuaian</button>
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

    <script type="module">
        function waitForDependencies(callback) {
            if (window.$ && window.$.fn && window.$.fn.select2) {
                callback();
            } else {
                setTimeout(function() {
                    waitForDependencies(callback);
                }, 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            waitForDependencies(function() {
                const defaultImage = "{{ asset('assets/img/produk.png') }}";
                const typeLabels = @json($types);
                let itemCounter = 0;

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

                $('#select-produk').select2({
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
                        processResults: function(data) {
                            let items = data.data ? data.data : (Array.isArray(data) ? data :
                            []);
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
                        cache: true
                    }
                });

                function updateSubmitButtonState() {
                    $('#btn-submit-form').prop('disabled', $('#table-adjustment tbody tr[data-row-id]')
                        .length === 0);
                }

                // ADD ITEM TO TABLE
                $("#btn-add").on("click", function() {
                    const selectedData = $("#select-produk").select2('data')[0];
                    const category = $('#adjustment-category').val();
                    const arah = $('#adjustment-arah').val();
                    const qty = parseInt($("#qty").val());
                    const reason = $('#adjustment-reason').val().trim();

                    if (!selectedData || !selectedData.id) {
                        window.showToast('warning', 'Silakan pilih produk terlebih dahulu.');
                        return;
                    }
                    if (isNaN(qty) || qty < 1) {
                        window.showToast('warning', 'Jumlah harus berupa angka dan minimal 1.');
                        return;
                    }
                    if (reason === '') {
                        window.showToast('warning', 'Alasan penyesuaian harus diisi.');
                        return;
                    }
                    if (arah === 'out' && qty > parseInt($("#sisa_stok").val() || 0)) {
                        window.showToast('warning', 'Jumlah keluar melebihi stok saat ini.');
                        return;
                    }

                    const rowId = selectedData.id;
                    if ($(`#table-adjustment tbody tr[data-row-id="${rowId}"]`).length > 0) {
                        window.showToast('warning', 'Produk/varian ini sudah ada dalam daftar.');
                        return;
                    }

                    const produkId = selectedData.product_id;
                    const variantId = selectedData.variant_id;
                    const produkNama = selectedData.text;
                    const imageUrl = selectedData.img_produk ? selectedData.img_produk :
                        defaultImage;
                    const variantHtml = selectedData.text2 ?
                        `<small class="text-muted d-block"><i class="bx bx-list-ul text-xs me-1"></i>${selectedData.text2}</small>` :
                        '';
                    const arahBadge = arah === 'in' ?
                        '<span class="badge badge-sm bg-label-success">Masuk</span>' :
                        '<span class="badge badge-sm bg-label-danger">Keluar</span>';

                    const newRow = `
                        <tr data-row-id="${rowId}">
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${produkId}">
                            <input type="hidden" name="items[${itemCounter}][product_variant_id]" value="${variantId || ''}">
                            <input type="hidden" name="items[${itemCounter}][type]" value="${category}">
                            <input type="hidden" name="items[${itemCounter}][arah]" value="${arah}">
                            <input type="hidden" name="items[${itemCounter}][jumlah]" value="${qty}">
                            <input type="hidden" name="items[${itemCounter}][alasan]" value="${reason}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${imageUrl}" class="rounded rounded-2 me-3" style="width:40px; height:40px; object-fit:cover;" alt="${produkNama}">
                                    <div>
                                        <h6 class="mb-0 text-sm">${produkNama}</h6>
                                        ${variantHtml}
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center text-sm">${typeLabels[category]}</td>
                            <td class="align-middle text-center">${arahBadge}</td>
                            <td class="align-middle text-center"><span class="fw-bold">${qty}</span></td>
                            <td class="align-middle text-sm">${reason}</td>
                            <td class="align-middle text-center">
                                <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove-item" title="Hapus Item">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#no-items-row').remove();
                    $("#table-adjustment tbody").append(newRow);
                    itemCounter++;

                    $("#select-produk").val(null).trigger("change");
                    $("#qty").val('');
                    $("#sisa_stok").val('');
                    $('#adjustment-reason').val('');
                    updateSubmitButtonState();
                });

                // REMOVE ITEM
                $("#table-adjustment").on("click", ".btn-remove-item", function() {
                    $(this).closest('tr').remove();
                    if ($('#table-adjustment tbody tr[data-row-id]').length === 0) {
                        $('#table-adjustment tbody').append(
                            `<tr id="no-items-row"><td colspan="6" class="text-center py-4"><p class="text-sm fw-bold mb-0">Belum ada item yang ditambahkan.</p></td></tr>`
                        );
                    }
                    updateSubmitButtonState();
                });

                // FINAL VALIDATION
                $("#adjustmentForm").on("submit", function(e) {
                    const itemCount = $("#table-adjustment tbody tr[data-row-id]").length;
                    if (itemCount === 0) {
                        e.preventDefault();
                        window.showToast('warning',
                            'Harap tambahkan minimal satu item penyesuaian.');
                    }
                });
            });
        });
    </script>
@endsection
