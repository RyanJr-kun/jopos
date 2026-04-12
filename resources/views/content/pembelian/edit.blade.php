@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

<form action="{{ route('pembelian.update', $pembelian->referensi) }}" method="post" id="form-pembelian-edit">
    @method('PUT')
    @csrf
    <div class="container-fluid p-3">
        <div class="card rounded-2">
            <div class="card-header pt-3 pb-0 mb-0 ">
                <h6 class="">Informasi Purchase</h6>
            </div>
            <div class="card-body px-4 pt-0">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label for="Supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select me-2 @error('supplier_id') is-invalid @enderror" name="supplier_id"
                            id="Supplier" required>
                            <option value="" disabled>Pilih Supplier</option>
                            @foreach ($pemasok as $item)
                                <option value="{{ $item->id }}" @selected(old('supplier_id', $pembelian->supplier_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 ">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input id="tanggal" name="tanggal" type="date"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('Y-m-d')) }}"
                            required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 ms-auto">
                        <label for="referensi" class="form-label">No Invoice</label>
                        <input type="text" class="form-control" id="referensi" name="referensi"
                            value="{{ $pembelian->referensi }}" readonly>
                    </div>
                    <div class="col-md-12">
                        <div class="row g-md-3 g-3">
                            <div class="col-md-9 col-12">
                                <label for="select2" class="form-label">Cari Product:</label>
                                <select name="select2" id="select2" class="form-control"></select>
                            </div>
                            <div class="col-md-1 col-4">
                                <label for="sisa_stok" class="form-label">Stock:</label>
                                <input type="number" id="sisa_stok" class="form-control" readonly>
                            </div>
                            <div class="col-md-1 col-4">
                                <label for="qty" class="form-label">Qty:</label>
                                <input type="number" id="qty" class="form-control" min="1">
                            </div>
                            <div class="col-md-1 col-4" style="padding-top: 30px">
                                <button type="button" class="btn btn-outline-info" id="btn-add">Tambah</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 table-responsive p-2">
                        <table class="table table-hover align-items-center" id="table-pembelian">
                            <thead class="table-secondary">
                                <tr class="table-secondary">
                                    <th class="text-dark text-xs font-weight-bolder">Nama Product</th>
                                    <th class="text-dark text-xs font-weight-bolder text-center">Qty</th>
                                    <th class="text-dark text-xs font-weight-bolder">Harga Beli</th>
                                    <th class="text-dark text-xs font-weight-bolder">Taxe (Rp)</th>
                                    <th class="text-dark text-xs font-weight-bolder">Diskon (Rp)</th>
                                    <th class="text-dark text-xs font-weight-bolder ps-2">Subtotal</th>
                                    <th class="text-dark"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembelian->details as $index => $detail)
                                    @php
                                        // Ambil pajak dari relasi pajak di detail. Fallback ke 0 jika tidak ada.
                                        $pajak_rate = $detail->pajak->rate ?? 0;
                                        $subtotal_item = $detail->harga_beli * $detail->qty - ($detail->diskon ?? 0);
                                        $pajak_amount = $subtotal_item * ($pajak_rate / 100);
                                        $subtotal_with_tax = $detail->subtotal; // subtotal dari DB sudah termasuk pajak
                                    @endphp
                                    <tr data-produk-id="{{ $detail->product_id }}">
                                        <input type="hidden" name="items[{{ $index }}][product_id]"
                                            value="{{ $detail->product_id }}">
                                        <input type="hidden" name="items[{{ $index }}][qty]"
                                            class="item-qty-hidden" value="{{ $detail->qty }}">
                                        <input type="hidden" name="items[{{ $index }}][harga_beli]"
                                            class="item-harga-hidden" value="{{ $detail->harga_beli }}">
                                        <input type="hidden" name="items[{{ $index }}][diskon]"
                                            class="item-diskon-hidden" value="{{ $detail->diskon ?? 0 }}">
                                        <input type="hidden" name="items[{{ $index }}][taxe_id]"
                                            class="item-pajak-id-hidden" value="{{ $detail->taxe_id ?? '' }}">
                                        <input type="hidden" class="item-pajak-rate-hidden"
                                            value="{{ $pajak_rate }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $detail->produk->img_produk ? asset('storage/' . $detail->produk->img_produk) : asset('assets/img/produk.webp') }}"
                                                    class="avatar avatar-sm me-3"
                                                    alt="{{ $detail->produk->name_product }}">
                                                <h6 class="mb-0 text-sm item-name">{{ $detail->produk->name_product }}
                                                </h6>
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
                                        <td class="subtotal-item text-start text-sm">
                                            {{ 'Rp ' . number_format($subtotal_with_tax, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-link text-info p-0 m-0 me-2 btn-edit"
                                                    title="Edit Item"><i class="bx bx-edit"></i></button>
                                                <button type="button"
                                                    class="btn btn-link text-danger p-0 m-0 btn-remove"
                                                    title="Hapus Item"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-md-flex justify-items-between w-100">
                        <div class="row w-100">
                            <div class="col-md-3 form-group">
                                <label for="ongkir" class="form-control-label fw-bolder">Ongkos Kirim</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="ongkir" id="ongkir"
                                        class="form-control form-control-sm text-end"
                                        value="{{ old('ongkir', $pembelian->ongkir) }}">
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="diskon-tambahan" class="form-control-label fw-bolder">Diskon
                                    Tambahan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="diskon_tambahan" id="diskon-tambahan"
                                        class="form-control form-control-sm text-end"
                                        value="{{ old('diskon', $pembelian->diskon) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="statusBarang" class="form-label">Status Barang:</label>
                                <select class="form-select me-2 @error('status_barang') is-invalid @enderror"
                                    name="status_barang" id="statusBarang" required>
                                    <option value="" disabled>Pilih</option>
                                    <option value="Diterima" @selected(old('status_barang', $pembelian->status_barang) == 'Diterima')>Diterima</option>
                                    <option value="Belum Diterima" @selected(old('status_barang', $pembelian->status_barang) == 'Belum Diterima')>Belum Diterima</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-md-0 mb-3">
                                <label for="statusBayar" class="form-label">Status Pembayaran:</label>
                                <select class="form-select me-2 @error('status_pembayaran') is-invalid @enderror"
                                    name="status_pembayaran" id="statusBayar" required>
                                    <option value="Lunas" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'Lunas')>Lunas</option>
                                    <option value="Belum Lunas" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'Belum Lunas')>Belum Lunas</option>
                                    <option value="Dibatalkan" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'Dibatalkan')>Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div class="row w-100 g-3 align-items-center justify-content-end">
                            <div class="col-md-5">
                                <div class="row mb-2">
                                    <div class="col-7 text-end"><span class="fw-bolder text-sm">Subtotal
                                            Keseluruhan</span></div>
                                    <div class="col-5 text-start"><span id="subtotal-keseluruhan"
                                            class="fw-bold text-sm">Rp 0</span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-7 text-end"><span class="fw-bolder text-sm text-dark">TOTAL
                                            AKHIR</span></div>
                                    <div class="col-5 text-start"><span id="total-akhir" class="fw-bolder text-sm">Rp
                                            0</span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-7 text-end"><span class="fw-bolder text-sm text-dark">Bayar</span>
                                    </div>
                                    <div class="col-5 text-start">
                                        <input type="text" id="bayar"
                                            class="text-dark text-end form-control form-control-sm text-sm">
                                        <input type="hidden" name="jumlah_dibayar" id="jumlah_dibayar_hidden"
                                            value="{{ old('jumlah_dibayar', $pembelian->jumlah_dibayar) }}">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-7 text-end"><span class="fw-bolder text-sm text-dark">Sisa /
                                            Kembalian</span></div>
                                    <div class="col-5 text-start"><span id="kembalian"
                                            class="fw-bolder text-sm text-dark">Rp 0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <div id="quill-editor-catatan" style="min-height: 100px;">{!! old('catatan', $pembelian->catatan) !!}</div>
                            <input type="hidden" name="catatan" id="catatan"
                                value="{{ old('catatan', $pembelian->catatan) }}">
                            @error('catatan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <button id="saveBtn" type="submit" class="btn btn-outline-info">Simpan Perubahan</button>
                    <a href="{{ route('pembelian.index') }}" id="cancel-button"
                        class="btn btn-danger ms-3">Batalkan</a>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal Edit Item --}}
<div class="modal rounded-2 fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel"
    aria-hidden="true">
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
                            <select class="form-select" id="edit-item-pajak-id">
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

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- UTILITIES ---
            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
            const parseCurrency = (string) => parseFloat(String(string).replace(/[^0-9]/g, '')) || 0;

            function formatInputAsCurrency(input) {
                let value = input.val();
                let number = parseCurrency(value);
                input.val(new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(number));
            }

            let itemCounter = {{ $pembelian->details->count() }};
            const editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));


            // --- SELECT2 INITIALIZATION ---
            function formatProduct(produk) {
                if (!produk.id) return produk.text;
                var defaultImage = "{{ asset('assets/img/produk.webp') }}";
                var imageUrl = produk.img_produk ? `{{ asset('storage/') }}/${produk.img_produk}` : defaultImage;
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

            $('#select2').select2({
                theme: "bootstrap-5",
                placeholder: 'Ketik untuk mencari produk...',
                templateResult: formatProduct,
                templateSelection: function(produk) {
                    if (produk.id) {
                        $("#sisa_stok").val(produk.qty);
                        $("#qty").val(1).focus();
                    }
                    return produk.text;
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
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name_product,
                                    img_produk: item.img_produk,
                                    qty: item.qty,
                                    harga_beli: item.harga_beli,
                                    harga_jual: item.harga_jual,
                                    // Menambahkan data pajak untuk konsistensi
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
                    window.showToast('warning', 'Harap pilih produk dan tentukan jumlah yang valid.');
                    return;
                }

                const produkId = selectedData.id;
                const produkNama = selectedData.text;
                const produkImg = selectedData.img_produk;
                const hargaBeli = selectedData.harga_beli || 0;
                const hargaJual = selectedData.harga_jual || 0;
                // Ambil data pajak dari produk yang dipilih
                const pajakId = selectedData.taxe_id || null;
                const pajakRate = selectedData.pajak_rate || 0;
                const qtyToAdd = parseInt(qty);

                let existingRow = $(`#table-pembelian tbody tr[data-produk-id="${produkId}"]`);
                if (existingRow.length > 0) {
                    let currentQtyInput = existingRow.find(".qty-pembelian");
                    let newQty = parseInt(currentQtyInput.val()) + qtyToAdd;
                    currentQtyInput.val(newQty).trigger('input');
                } else {
                    const defaultImage = "{{ asset('assets/img/produk.webp') }}";
                    const imageUrl = produkImg ? `{{ asset('storage/') }}/${produkImg}` : defaultImage;

                    // Hitung nilai awal untuk pajak dan subtotal (termasuk pajak)
                    const subtotalItem = hargaBeli * qtyToAdd;
                    const pajakAmount = subtotalItem * (pajakRate / 100);
                    const subtotalWithTax = subtotalItem + pajakAmount;

                    const newRow = `
                        <tr data-produk-id="${produkId}">
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${produkId}">
                            <input type="hidden" name="items[${itemCounter}][qty]" class="item-qty-hidden" value="${qtyToAdd}">
                            <input type="hidden" name="items[${itemCounter}][harga_beli]" class="item-harga-hidden" value="${hargaBeli}">
                            <input type="hidden" name="items[${itemCounter}][diskon]" class="item-diskon-hidden" value="0">
                            <input type="hidden" name="items[${itemCounter}][taxe_id]" class="item-pajak-id-hidden" value="${pajakId || ''}">
                            <input type="hidden" class="item-pajak-rate-hidden" value="${pajakRate}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${imageUrl}" class="avatar avatar-sm me-3" alt="${produkNama}">
                                    <h6 class="mb-0 text-sm item-name">${produkNama}</h6>
                                </div>
                            </td>
                            <td class="align-middle text-center"><span class="item-qty">${qtyToAdd}</span></td>
                            <td class="align-middle"><span class="item-harga">${formatCurrency(hargaBeli)}</span></td>
                            <td class="align-middle"><span class="item-pajak">${formatCurrency(pajakAmount)}</span></td>
                            <td class="align-middle"><span class="item-diskon">Rp 0</span></td>
                            <td class="subtotal-item text-start text-sm">${formatCurrency(subtotalWithTax)}</td>
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
                calculateChange(); // Panggil calculateChange agar total akhir juga terupdate
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
                    const pajakRate = parseFloat($(this).find(".item-pajak-rate-hidden").val()) || 0;

                    const subtotalItem = (qty * hargaBeli) - diskon;
                    const pajakItem = subtotalItem * (pajakRate / 100);
                    subtotalKeseluruhan += subtotalItem +
                        pajakItem; // Subtotal keseluruhan SEKARANG termasuk pajak
                });

                $("#subtotal-keseluruhan").text(formatCurrency(subtotalKeseluruhan));

                const ongkir = parseCurrency($("#ongkir").val()); // Ini sudah benar
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
                $("#kembalian").toggleClass('text-danger', sisa < 0).toggleClass('text-dark', sisa >= 0);
            }

            // --- EVENT LISTENERS ---
            $("#ongkir, #diskon-tambahan").on("input", function() {
                calculateChange();
            });
            $("#bayar").on("input", function() {
                calculateChange();
            });

            $("#table-pembelian").on("click", ".btn-remove", function() {
                const row = $(this).closest("tr");
                const productName = row.find('.item-name').text();
                Swal.fire({
                    title: 'Hapus Product?',
                    text: `Anda yakin ingin menghapus ${productName} dari daftar?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        calculateChange();
                    }
                });
            });

            // Open Edit Modal
            $("#table-pembelian").on("click", ".btn-edit", function() {
                const row = $(this).closest("tr");
                const produkId = row.data('produk-id');

                // Populate modal
                $("#edit-item-id").val(produkId);
                $("#edit-item-name").val(row.find(".item-name").text());
                $("#edit-item-qty").val(row.find(".item-qty-hidden").val());
                $("#edit-item-harga").val(new Intl.NumberFormat('id-ID').format(row.find(
                    ".item-harga-hidden").val()));
                $("#edit-item-diskon").val(new Intl.NumberFormat('id-ID').format(row.find(
                    ".item-diskon-hidden").val()));
                $("#edit-item-pajak-id").val(row.find(".item-pajak-id-hidden").val());

                editItemModal.show();
            });

            // Save changes from modal
            $("#saveItemChangesBtn").on("click", function() {
                const produkId = $("#edit-item-id").val();
                const row = $(`#table-pembelian tbody tr[data-produk-id="${produkId}"]`);

                row.find(".item-qty, .item-qty-hidden").val($("#edit-item-qty").val());
                row.find(".item-harga-hidden").val(parseCurrency($("#edit-item-harga").val()));
                row.find(".item-diskon-hidden").val(parseCurrency($("#edit-item-diskon").val()));

                const selectedTaxe = $("#edit-item-pajak-id option:selected");
                row.find(".item-pajak-id-hidden").val(selectedTaxe.val());
                row.find(".item-pajak-rate-hidden").val(selectedTaxe.data('rate'));

                updateRowDisplay(row);
                editItemModal.hide();
            });

            $("#statusBayar").on("change", function() {
                const status = $(this).val();
                if (status === 'Lunas') {
                    const totalAkhir = calculateGrandTotal();
                    $("#bayar").val(totalAkhir).trigger('input');
                    // Pastikan form aktif
                } else if (status === 'Belum Lunas') {
                    $("#bayar").val(0).trigger('input');
                    // Pastikan form aktif
                } else if (status === 'Dibatalkan') {
                    // Kunci form jika dibatalkan
                }
            });

            // --- CURRENCY FORMATTING ---
            $('#edit-item-harga, #edit-item-diskon, #ongkir, #diskon-tambahan, #bayar').on('input', function() {
                formatInputAsCurrency($(this));
            });


            function updateRowDisplay(row) {
                row.find('.item-qty').text(row.find('.item-qty-hidden').val());
                row.find('.item-harga').text(formatCurrency(row.find('.item-harga-hidden').val()));
                row.find('.item-diskon').text(formatCurrency(row.find('.item-diskon-hidden').val()));

                calculateRow(row);
                calculateChange();
            }


            // --- FORM SUBMISSION VALIDATION ---
            $("#form-pembelian-edit").on("submit", function(e) {
                const totalAkhir = calculateGrandTotal();
                const bayar = parseCurrency($("#bayar").val());
                const statusBayar = $("#statusBayar").val();

                if (bayar < totalAkhir && statusBayar === 'Lunas') {
                    e.preventDefault();
                    window.showToast('warning',
                        'Pembayaran kurang dari total akhir. Mohon ubah status pembayaran Anda atau lunasi pembayaran.'
                        );
                    return;
                }

                // Validasi baru: Jika pembayaran lunas, status tidak boleh 'Belum Lunas'
                if (statusBayar === 'Belum Lunas' && bayar >= totalAkhir) {
                    e.preventDefault();
                    window.showToast('warning',
                        'Pembayaran sudah lunas. Mohon ubah status pembayaran menjadi "Lunas".');
                    return;
                }

                if ($("#table-pembelian tbody tr").length === 0) {
                    e.preventDefault();
                    window.showToast('warning', 'Harap tambahkan minimal satu produk.');
                }
            });

            // --- INITIAL CALCULATIONS ON PAGE LOAD ---
            // 1. Set nilai awal dari DB ke input yang terlihat
            $('#bayar').val($('#jumlah_dibayar_hidden').val());

            // 2. Format semua input mata uang saat halaman dimuat
            $("#ongkir, #diskon-tambahan, #bayar").each(function() {
                formatInputAsCurrency($(this));
            });

            // 3. Jalankan kalkulasi total setelah semua nilai terformat
            calculateChange();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endsection
@endsection
