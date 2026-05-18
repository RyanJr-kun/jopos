@extends('layouts/contentNavbarLayout')
@section('title', 'Catatan Penyesuaian Stok - Inventory')


@section('content')
    <form action="{{ route('stok-penyesuaian.store') }}" method="POST" id="adjustmentForm">
        @csrf
        <div class="card rounded-2 mb-4">
            <div class="card-header pb-0 px-3 pt-2">
                <h6 class="mb-0">Informasi Umum</h6>
            </div>
            <div class="card-body pt-2">
                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Umum (Opsional)</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="2"
                        placeholder="Contoh: Penyesuaian stok karena barang rusak">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2">
                <h6 class="mb-0">Item Penyesuaian</h6>
                <p class="text-sm mb-0">Tambahkan produk yang akan disesuaikan stoknya.</p>
            </div>
            <div class="card-body pt-2">
                {{-- Form untuk menambah item --}}
                <div class="row g-3 align-items-end border-bottom pb-3 mb-3">
                    <div class="col-md-4">
                        <label for="select-produk" class="form-label">Pilih Product</label>
                        <select id="select-produk" class="form-control"></select>
                    </div>
                    <div class="col-md-2">
                        <label for="adjustment-type" class="form-label">Tipe</label>
                        <select id="adjustment-type" class="form-select">
                            <option value="IN">Masuk (IN)</option>
                            <option value="OUT">Keluar (OUT)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="adjustment-qty" class="form-label">Jumlah</label>
                        <input type="number" id="adjustment-qty" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-3">
                        <label for="adjustment-reason" class="form-label">Alasan</label>
                        <input type="text" id="adjustment-reason" class="form-control"
                            placeholder="Contoh: Barang rusak">
                    </div>
                    <div class="col-md-1 d-grid mb-n3">
                        <button type="button" id="btn-add-item" class="btn btn-outline-info"><i class="bx bx-plus-lg"></i>
                            Tambah</button>
                    </div>
                </div>

                {{-- Tabel untuk menampilkan item yang ditambahkan --}}
                <div class="table-responsive p-0">
                    <table class="table table-hover align-items-center mb-0" id="adjustment-items-table">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Product</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Tipe</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Jumlah</th>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder">Alasan</th>
                                <th class="text-center text-uppercase text-dark text-xs font-weight-bolder">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="no-items-row">
                                <td colspan="5" class="text-center py-4">
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
    <script>
        $(document).ready(function() {
            let itemCounter = 0;
            let addedProducts = new Set();

            // Inisialisasi Select2 untuk pencarian produk
            $('#select-produk').select2({
                theme: "bootstrap-5",
                placeholder: 'Ketik untuk mencari produk...',
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
                        return {
                            results: data.data.map(item => ({
                                id: item.id,
                                text: `${item.name_product} (Stock: ${item.qty})`,
                                name_product: item.name_product,
                                sku: item.sku,
                                img_url: item.img_produk ?
                                    `{{ asset('storage/') }}/${item.img_produk}` :
                                    `{{ asset('assets/img/produk.png') }}`
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatProduct,
                templateSelection: (data) => data.text
            });

            function formatProduct(produk) {
                if (!produk.id) {
                    return produk.text;
                }
                var $container = $(
                    `<div class='select2-result-repository d-flex clearfix'>
                        <div class='select2-result-repository__avatar'><img src='${produk.img_url}' class='avatar avatar-sm me-3' /></div>
                        <div class='select2-result-repository__meta'>
                            <div class='select2-result-repository__title'>${produk.name_product}</div>
                            <div class='select2-result-repository__description text-xs'>SKU: ${produk.sku}</div>
                        </div>
                    </div>`
                );
                return $container;
            }

            // Fungsi untuk mengupdate status tombol simpan
            function updateSubmitButtonState() {
                if (addedProducts.size > 0) {
                    $('#btn-submit-form').prop('disabled', false);
                } else {
                    $('#btn-submit-form').prop('disabled', true);
                }
            }

            // Event handler untuk tombol "Tambah Item"
            $('#btn-add-item').on('click', function() {
                const selectedProduct = $('#select-produk').select2('data')[0];
                const type = $('#adjustment-type').val();
                const qty = parseInt($('#adjustment-qty').val());
                const reason = $('#adjustment-reason').val().trim();

                // Validasi
                if (!selectedProduct || !selectedProduct.id) {
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
                if (addedProducts.has(selectedProduct.id.toString())) {
                    window.showToast('warning', 'Product ini sudah ada dalam daftar.');
                    return;
                }

                // Tambahkan item ke tabel
                const typeBadge = type === 'IN' ?
                    '<span class="badge badge-sm bg-label-success">Masuk</span>' :
                    '<span class="badge badge-sm bg-label-danger">Keluar</span>';

                const newRow = `
                    <tr class="adjustment-item-row" data-product-id="${selectedProduct.id}">
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div>
                                    <img src="${selectedProduct.img_url}" class="avatar avatar-sm me-3" alt="product image">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${selectedProduct.name_product}</h6>
                                    <p class="text-xs text-secondary mb-0">${selectedProduct.sku}</p>
                                </div>
                            </div>
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${selectedProduct.id}">
                        </td>
                        <td class="align-middle text-center text-sm">
                            ${typeBadge}
                            <input type="hidden" name="items[${itemCounter}][tipe]" value="${type}">
                        </td>
                        <td class="align-middle text-center text-sm">
                            <span class="fw-bold">${qty}</span>
                            <input type="hidden" name="items[${itemCounter}][jumlah]" value="${qty}">
                        </td>
                        <td class="align-middle text-sm">
                            ${reason}
                            <input type="hidden" name="items[${itemCounter}][alasan]" value="${reason}">
                        </td>
                        <td class="align-middle text-center">
                            <button type="button" class="btn btn-link text-danger p-0 m-0 btn-remove-item">
                                <i class="bx bx-trash bi-lg"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#no-items-row').hide();
                $('#adjustment-items-table tbody').append(newRow);
                addedProducts.add(selectedProduct.id.toString());
                itemCounter++;

                // Reset form input
                $('#select-produk').val(null).trigger('change');
                $('#adjustment-qty').val(1);
                $('#adjustment-reason').val('');

                updateSubmitButtonState();
            });

            // Event handler untuk menghapus item dari tabel
            $('#adjustment-items-table').on('click', '.btn-remove-item', function() {
                const row = $(this).closest('tr');
                const productId = row.data('product-id').toString();

                addedProducts.delete(productId);
                row.remove();

                if (addedProducts.size === 0) {
                    $('#no-items-row').show();
                }
                updateSubmitButtonState();
            });

        });
    </script>
@endsection
