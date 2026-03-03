@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    {{-- Select2 untuk pencarian produk --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('breadcrumb')
    @php
        $breadcrumbItems = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Inventaris', 'url' => '#'],
            ['name' => 'Riwayat Penyesuaian', 'url' => route('stok-penyesuaian.index')],
            ['name' => 'Buat Penyesuaian', 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
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
                        <label for="select-produk" class="form-label">Pilih Produk</label>
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
                        <button type="button" id="btn-add-item" class="btn btn-outline-info"><i
                                class="bx bx-plus-lg"></i> Tambah</button>
                    </div>
                </div>

                {{-- Tabel untuk menampilkan item yang ditambahkan --}}
                <div class="table-responsive p-0">
                    <table class="table table-hover align-items-center mb-0" id="adjustment-items-table">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-uppercase text-dark text-xs font-weight-bolder ps-4">Produk</th>
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
</div>

@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                text: `${item.nama_produk} (Stok: ${item.qty})`,
                                nama_produk: item.nama_produk,
                                sku: item.sku,
                                img_url: item.img_produk ?
                                    `{{ asset('storage/') }}/${item.img_produk}` :
                                    `{{ asset('assets/img/produk.webp') }}`
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatProduk,
                templateSelection: (data) => data.text
            });

            function formatProduk(produk) {
                if (!produk.id) {
                    return produk.text;
                }
                var $container = $(
                    `<div class='select2-result-repository d-flex clearfix'>
                        <div class='select2-result-repository__avatar'><img src='${produk.img_url}' class='avatar avatar-sm me-3' /></div>
                        <div class='select2-result-repository__meta'>
                            <div class='select2-result-repository__title'>${produk.nama_produk}</div>
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
                    Swal.fire('Peringatan', 'Silakan pilih produk terlebih dahulu.', 'warning');
                    return;
                }
                if (isNaN(qty) || qty < 1) {
                    Swal.fire('Peringatan', 'Jumlah harus berupa angka dan minimal 1.', 'warning');
                    return;
                }
                if (reason === '') {
                    Swal.fire('Peringatan', 'Alasan penyesuaian harus diisi.', 'warning');
                    return;
                }
                if (addedProducts.has(selectedProduct.id.toString())) {
                    Swal.fire('Peringatan', 'Produk ini sudah ada dalam daftar.', 'warning');
                    return;
                }

                // Tambahkan item ke tabel
                const typeBadge = type === 'IN' ?
                    '<span class="badge badge-sm badge-success">Masuk</span>' :
                    '<span class="badge badge-sm badge-danger">Keluar</span>';

                const newRow = `
                    <tr class="adjustment-item-row" data-product-id="${selectedProduct.id}">
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div>
                                    <img src="${selectedProduct.img_url}" class="avatar avatar-sm me-3" alt="product image">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${selectedProduct.nama_produk}</h6>
                                    <p class="text-xs text-secondary mb-0">${selectedProduct.sku}</p>
                                </div>
                            </div>
                            <input type="hidden" name="items[${itemCounter}][produk_id]" value="${selectedProduct.id}">
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
@endsection
