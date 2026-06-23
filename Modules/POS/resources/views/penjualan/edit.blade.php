@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-purchase.scss'])
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endsection

<form id="editSaleForm" action="{{ route('penjualan.update', $penjualan->referensi) }}" method="POST">
    @method('put')
    @csrf
    <div class="card">
        <div class="card-header border-bottom py-3 justify-content-between align-items-center d-flex">
                <h5 class="mb-0 fw-bold">Edit Penjualan <span class="text-muted fs-6 ms-1">#{{ $penjualan->referensi }}</span>
                </h5>
                <a href="{{ route('penjualan.index') }}" class="btn btn-icon btn-outline-secondary btn-sm"
                    data-bs-toggle="tooltip" aria-label="Kembali" data-bs-original-title="Kembali">
                    <i class="bx bx-arrow-back"></i>
                </a>
            </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                {{-- Informasi Dasar --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="Customer" class="form-label fw-semibold">Customer <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <select name="customer_id" id="customer_id" class="form-select select2" required>
                                @foreach ($customers as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" @selected($penjualan->customer_id == $pelanggan->id)>{{ $pelanggan->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-info rounded-2 px-3" data-bs-toggle="modal"
                                data-bs-target="#createCustomerModal" title="Tambah Customer Baru">
                                <i class="bx bx-plus"></i>
                            </button>
                            @error('customer_id')
                            <div class="invalid-feedback d-block text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="tanggal_penjualan" class="form-label fw-semibold">Tanggal <span
                                class="text-danger">*</span></label>
                    <input id="tanggal_penjualan" name="tanggal_penjualan" type="datetime-local"
                        class="form-control @error('tanggal_penjualan') is-invalid @enderror"
                        value="{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('Y-m-d\TH:i') }}"
                        required>
                    @error('tanggal_penjualan')
                        <div class="invalid-feedback text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
                        <input id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" type="date"
                            class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror"
                            value="{{ old('tanggal_jatuh_tempo', \Carbon\Carbon::parse($penjualan->tanggal_jatuh_tempo)->format('Y-m-d')) }}">
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback text-sm">{{ $message }}</div>
                        @enderror
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="referensi" class="form-label fw-semibold">No Invoice</label>
                        {{-- ✅ Disesuaikan: bg-light seperti create --}}
                    <input type="text" class="form-control bg-light" id="referensi" name="referensi"
                            value="{{ $penjualan->referensi }}" readonly>
                </div>

                {{-- Pencarian Product --}}
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

                {{-- Tabel Item --}}
                <div class="table-responsive text-nowrap mb-4 border rounded-3">
                    <table class="table table-hover align-middle mb-0" id="table-penjualan">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th class="text-xs font-weight-bolder py-3">Nama Product</th>
                                <th class="text-xs font-weight-bolder text-center py-3" width="10%">Qty</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Harga Jual</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Taxe (Rp)</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Diskon (Rp)</th>
                                <th class="text-xs font-weight-bolder py-3" width="15%">Subtotal</th>
                                <th class="py-3" width="5%"></th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach ($penjualan->items as $index => $detail)
                                @php
                                    $pajak_rate = $detail->pajak->rate ?? 0;
                                    // FIX: Ubah qty menjadi jumlah, diskon menjadi diskon_item
                                    $subtotal_item = $detail->harga_jual * $detail->jumlah - ($detail->diskon_item ?? 0); 
                                    $pajak_amount = $detail->pajak_item ?? ($subtotal_item * ($pajak_rate / 100));
                                    // FIX: Tambahkan pajak ke subtotal agar render awal sesuai dengan perhitungan JS
                                    $subtotal_with_tax = $detail->subtotal + $pajak_amount; 

                                    $rowId = $detail->product_variant_id
                                        ? "{$detail->product_id}-{$detail->product_variant_id}"
                                        : $detail->product_id;

                                    $imageUrl = $detail->product->image_url;
                                    $namaVarian = '';


                                    if ($detail->product_variant_id && $detail->varian) {
                                        if (!empty($detail->varian->img_variant)) {
                                            $imageUrl = \Illuminate\Support\Facades\Storage::url($detail->varian->img_variant);
                                        }

                                        $variantOpts = [];
                                        if ($detail->varian->relationLoaded('options')) {
                                            foreach ($detail->varian->options as $opt) {
                                                $variantOpts[] = $opt->value;
                                            }
                                        }
                                        $namaVarian = !empty($variantOpts)
                                            ? implode(' / ', $variantOpts)
                                            : 'SKU: ' . $detail->varian->sku;
                                    }
                                @endphp
                                <tr data-row-id="{{ $rowId }}">
                                    <input type="hidden" name="items[{{ $index }}][product_id]"
                                        value="{{ $detail->product_id }}">
                                    <input type="hidden" name="items[{{ $index }}][product_variant_id]"
                                        value="{{ $detail->product_variant_id ?? '' }}">
                                        
                                    <input type="hidden" name="items[{{ $index }}][jumlah]" class="item-qty-hidden" value="{{ $detail->jumlah }}">
                                    <input type="hidden" name="items[{{ $index }}][harga_jual]"
                                        class="item-harga-hidden" value="{{ $detail->harga_jual }}">
                                    <input type="hidden" name="items[{{ $index }}][diskon]"
                                        class="item-diskon-hidden" value="{{ $detail->diskon ?? 0 }}">
                                    <input type="hidden" name="items[{{ $index }}][taxe_id]"
                                        class="item-pajak-id-hidden" value="{{ $detail->taxe_id ?? '' }}">
                                    <input type="hidden" class="item-pajak-rate-hidden" value="{{ $pajak_rate }}">

                                    <td>
                                        {{-- ✅ Disesuaikan: style img sama dengan create (rounded rounded-2, me-3) --}}
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $imageUrl }}" class="rounded rounded-2 me-3"
                                                style="width:40px; height:40px; object-fit:cover;" alt="Produk">
                                            <div>
                                                <h6 class="mb-0 text-sm item-name">
                                                    {{ $detail->product->name_product ?? 'Produk Dihapus' }}</h6>
                                                @if ($namaVarian)
                                                    <small class="text-muted"><i
                                                            class="bx bx-list-ul text-xs me-1"></i>{{ $namaVarian }}</small>
                                                @endif
                                                @if ($detail->product && $detail->product->wajib_seri && $detail->serialNumbers->isNotEmpty())
                                                    @php
                                                        $snList = $detail->serialNumbers->pluck('nomor_seri')->toArray();
                                                    @endphp
                                                    <small class="text-info d-block mt-1">
                                                        <i class="bx bx-barcode text-xs me-1"></i>SN: {{ implode(', ', $snList) }}
                                                    </small>

                                                    {{-- Looping Hidden Input untuk masing-masing SN --}}
                                                    <div class="sn-hidden-container">
                                                        @foreach ($snList as $sn)
                                                            <input type="hidden" name="items[{{ $index }}][serial_numbers][]" class="item-sn-hidden" value="{{ $sn }}">
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center"><span
                                            class="item-qty">{{ $detail->jumlah }}</span></td>
                                    <td class="align-middle"><span
                                            class="item-harga">{{ 'Rp ' . number_format($detail->harga_jual, 0, ',', '.') }}</span>
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

                <div class="row g-4">
                    <div class="col-12 col-xl-7">
                        {{-- Kosong / bisa diisi konten lain jika diperlukan --}}
                    </div>

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
                            data-type="service" data-label="Service" role="button" tabindex="0"
                            aria-label="Edit Biaya Service">
                            <span class="totals-label">Service</span>
                            <span class="totals-value" id="service-display">Rp 0</span>
                            <input type="hidden" name="service" id="service-input"
                                value="{{ old('service', $penjualan->service) }}">
                        </div>
                        <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                            data-type="ongkir" data-label="Ongkos Kirim" role="button" tabindex="0"
                            aria-label="Edit ongkos kirim">
                            <span class="totals-label">Ongkir</span>
                            <span class="totals-value" id="ongkir-display">Rp 0</span>
                            <input type="hidden" name="ongkir" id="ongkir-input"
                                value="{{ old('ongkir', $penjualan->ongkir) }}">
                        </div>
                        <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                            data-type="diskon" data-label="Diskon" role="button" tabindex="0"
                            aria-label="Edit diskon">
                            <span class="totals-label">Diskon (Rp)</span>
                            <span class="totals-value" id="diskon-display">Rp 0</span>
                            <input type="hidden" name="diskon_tambahan" id="diskon-tambahan"
                                value="{{ old('diskon', $penjualan->diskon) }}">
                        </div>
                        <div class="totals-grand">
                            <span class="grand-label">Total</span>
                            <span class="grand-value" id="total-akhir" aria-live="polite">Rp 0</span>
                        </div>
                        <hr class="border-secondary opacity-25">
                        <div class="d-flex">
                            <button type="button"
                                class="btn btn-primary w-100 justify-content-between align-items-center"
                                id="btn-open-payment" data-bs-toggle="modal" data-bs-target="#paymentModalEdit">
                                <span class="text-muted">Simpan Perubahan</span>
                                <span class="text-muted" id="cart-total-btn-display">Rp 0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@include('pos::penjualan.partials._model')


@endsection

@section('page-script')
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
            const defaultHeaders = {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Pastikan token CSRF tersedia
            };
            let tempRowDataForSN = null; // Menyimpan konteks baris tabel saat modal SN aktif
            const serialNumberModalEl = document.getElementById('serialNumberModal');
            const serialNumberModal = serialNumberModalEl ? new bootstrap.Modal(serialNumberModalEl) : null;
            const snErrorMessage = document.getElementById('sn-error-message');
            const snListContainer = document.getElementById('sn-list-container');
            const snRequiredCount = document.getElementById('sn-required-count');
            const snConfirmBtn = document.getElementById('btn-confirm-sn');

            const openSerialNumberModal = (rowId, productId, variantId, productName, requiredQty, imgUrl, existingSerials = []) => {
            tempRowDataForSN = rowId; // Simpan rowId

            document.getElementById('sn-produk-id').value = productId;
            document.getElementById('sn-variant-id').value = variantId || '';

            document.getElementById('sn-name-produk').textContent = productName;
            document.getElementById('sn-required-count').textContent = requiredQty;
            document.getElementById('sn-image-produk').src = imgUrl || '/assets/img/produk.png';
            // Catatan: Stok bisa diambil via AJAX atau dilempar sebagai parameter jika diperlukan, 
            // untuk saat ini saya hilangkan sementara agar tidak error undefined.
            document.getElementById('sn-stok-produk').textContent = "-"; 

            snErrorMessage.textContent = '';
            snListContainer.innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                </div>`;

            serialNumberModal.show();

            const url = new URL(`{{ route('serialNumber.getProduct') }}`);
            url.searchParams.set('product_id', productId);
            if (variantId) url.searchParams.set('variant_id', variantId);

            fetch(url.toString(), { headers: defaultHeaders })
                .then(r => r.json())
                .then(data => {
                    snListContainer.innerHTML = '';
                    if (!data.serial_numbers || data.serial_numbers.length === 0) {
                        snListContainer.innerHTML = '<p class="text-center text-muted py-3">Tidak ada nomor seri tersedia.</p>';
                        return;
                    }
                    data.serial_numbers.forEach(sn => {
                        const isChecked  = existingSerials.includes(sn.serial_number);
                        // Biarkan SN yang sudah terikat pada transaksi ini bisa dipilih (checked)
                        const isDisabled = sn.status !== 'Tersedia' && !isChecked; 
                        snListContainer.insertAdjacentHTML('beforeend', `
                            <label class="list-group-item list-group-item-action d-flex gap-2 align-items-center
                                        ${isDisabled ? 'text-muted disabled' : ''}">
                                <input class="form-check-input flex-shrink-0 sn-checkbox" type="checkbox"
                                    value="${sn.serial_number}"
                                    ${isChecked  ? 'checked'  : ''}
                                    ${isDisabled ? 'disabled' : ''}>
                                <span class="d-flex justify-content-between align-items-center w-100">
                                    <span>${sn.serial_number}</span>
                                    <small class="badge bg-label-${sn.status === 'Tersedia' ? 'success' : 'secondary'}">
                                        ${sn.status}
                                    </small>
                                </span>
                            </label>`);
                    });
                })
                .catch(() => {
                    snListContainer.innerHTML = '<p class="text-center text-danger py-3">Gagal memuat nomor seri.</p>';
                });
        };
                    
            const ASSET_STORAGE = "{{ config('filesystems.disks.r2.url') }}";
            const createCustomerForm = document.getElementById('createCustomerForm');
            if (createCustomerForm) {
                const pelangganSelect = document.getElementById('customer_id');
                const createCustomerModal = new bootstrap.Modal(document.getElementById('createCustomerModal'));

                createCustomerForm.addEventListener('submit', function(e) {
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
                                // ✅ Tambah option baru ke Select2 customer
                                const newOption = new Option(data.data.name, data.data.id, true, true);
                                $(pelangganSelect).append(newOption).trigger('change');
                                createCustomerForm.reset();
                                createCustomerModal.hide();
                                if (typeof window.showToast !== 'undefined') window.showToast('success',
                                    data.message);
                            }
                        })
                        .catch(error => {
                            if (error.errors) {
                                Object.keys(error.errors).forEach(key => {
                                    const input = createCustomerForm.querySelector(
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

                // Inisialisasi Default Select2
                // ✅ FIX: exclude .select2-bank-edit agar tidak bentrok dengan init khusus di payment modal
                $('.select2:not(#select2):not(.select2-bank):not(.select2-bank-edit)').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                    width: '100%',
                });

                // Utilitas Format
                $("#qty").removeAttr("onfocus");
                const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);

                // ✅ FIX: parseCurrency yang benar untuk format Rupiah (titik = ribuan, koma = desimal)
                const parseCurrency = (string) => {
                    const cleaned = String(string)
                        .replace(/\./g, '') // hapus titik ribuan
                        .replace(',', '.'); // ganti koma desimal ke titik
                    return parseFloat(cleaned) || 0;
                };

                function formatInputAsCurrency(input) {
                    let value = parseCurrency(input.val());
                    input.val(new Intl.NumberFormat('id-ID').format(value));
                }

                // Ambil nilai tertinggi counter dari backend
                let itemCounter =
                    {{ $penjualan->items->count() > 0 ? collect($penjualan->items->keys())->max() + 1 : 0 }};

                if (snConfirmBtn) {
                        snConfirmBtn.addEventListener('click', () => {
                            const requiredQty = parseInt(snRequiredCount.textContent);
                            const selected = [...document.querySelectorAll('.sn-checkbox:checked')].map(cb => cb.value);

                            if (selected.length !== requiredQty) {
                                snErrorMessage.textContent = `Pilih tepat ${requiredQty} nomor seri (dipilih: ${selected.length}).`;
                                return;
                            }
                            
                            snErrorMessage.textContent = '';
                            
                            // --- LOGIKA UPDATE BARIS TABEL ---
                            const row = $(`#table-penjualan tbody tr[data-row-id="${tempRowDataForSN}"]`);
                            if (row.length > 0) {
                                // Hapus input hidden SN lama yang ada di row ini
                                row.find('.item-sn-hidden').remove();
                                
                                // Ambil index array name (contoh: items[0][jumlah], kita butuh angka '0')
                                const nameAttr = row.find('.item-qty-hidden').attr('name');
                                const match = nameAttr.match(/items\[(\d+)\]/);
                                const index = match ? match[1] : itemCounter;

                                // Generate ulang info SN di layar & input hidden baru
                                let snDisplayHtml = `<small class="text-info d-block mt-1 sn-display"><i class="bx bx-barcode text-xs me-1"></i>SN: ${selected.join(', ')}</small>`;
                                row.find('.sn-display').remove(); // Hapus text info lama
                                row.find('.item-name').parent().append(snDisplayHtml);

                                // Sisipkan input hidden SN baru
                                let hiddenInputsHtml = '';
                                selected.forEach(sn => {
                                    hiddenInputsHtml += `<input type="hidden" name="items[${index}][serial_numbers][]" class="item-sn-hidden" value="${sn}">`;
                                });
                                row.append(hiddenInputsHtml);
                            } else {
                                // Jika baris belum ada (kasus nambah produk baru dari Select2), maka jalankan addProductToCart
                                // (Pastikan fungsi addProductToCart bisa menangani SN, mirip dengan di create.blade.php)
                                // addProductToCart(tempProductDataForSN, selected); 
                                console.error("Baris tidak ditemukan untuk SN");
                            }

                            serialNumberModal.hide();
                        });
                    }
                const editItemModal = new bootstrap.Modal(document.getElementById('editCartItemModal'));

                // Select2 di dalam modal Edit Item
                $('#edit-item-pajak-id').select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#editCartItemModal'),
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
                        url: "{{ route('getDataProduct') }}",
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
                                        harga_jual: item.harga_jual,
                                        taxe_id: item.taxe_id,
                                        pajak_rate: item.pajak ? item.pajak.rate : 0,
                                        wajib_seri: item.wajib_seri || false
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
                    const hargaJual = selectedData.harga_jual || 0;
                    const pajakId = selectedData.taxe_id || null;
                    const pajakRate = selectedData.pajak_rate || 0;
                    const qtyToAdd = parseInt(qty);

                    const wajibSeri = selectedData.wajib_seri || false;

                    let existingRow = $(`#table-penjualan tbody tr[data-row-id="${rowId}"]`);

                    if (existingRow.length > 0) {
                        let currentQtyInput = existingRow.find(".qty-penjualan, .item-qty-hidden");
                        let newQty = parseInt(currentQtyInput.val()) + qtyToAdd;
                        currentQtyInput.val(newQty);
                        updateRowDisplay(existingRow);
                    } else {
                        const subtotalAwal = (hargaJual * qtyToAdd);
                        const pajakAwal = subtotalAwal * (pajakRate / 100);
                        const subtotalDenganTaxe = subtotalAwal + pajakAwal;
                        const trClass = wajibSeri ? 'is-wajib-seri' : '';

                        const newRow = `
                        <tr data-row-id="${rowId}">
                            <input type="hidden" name="items[${itemCounter}][product_id]" value="${produkId}">
                            <input type="hidden" name="items[${itemCounter}][product_variant_id]" value="${variantId || ''}">
                            <input type="hidden" name="items[${itemCounter}][jumlah]" class="item-qty-hidden" value="${qtyToAdd}">
                            <input type="hidden" name="items[${itemCounter}][harga_jual]" class="item-harga-hidden" value="${hargaJual}">
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
                            <td class="align-middle"><span class="item-harga">${formatCurrency(hargaJual)}</span></td>
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
                        $("#table-penjualan tbody").append(newRow);
                        itemCounter++;
                    }

                    $("#select2").val(null).trigger("change");
                    $("#qty").val("");
                    $("#sisa_stok").val("");
                    calculateGrandTotal();

                    if (wajibSeri) {
                        let targetRow = $(`#table-penjualan tbody tr[data-row-id="${rowId}"]`);
                        
                        // Ambil SN yang sudah tersimpan (jika item sudah ada sebelumnya lalu ditambah qty)
                        const existingSerials = [];
                        targetRow.find('.item-sn-hidden').each(function() {
                            existingSerials.push($(this).val());
                        });

                        // Pastikan mengambil qty terbaru setelah ditambahkan
                        const currentQty = parseInt(targetRow.find('.item-qty-hidden').val()) || qtyToAdd;

                        openSerialNumberModal(rowId, produkId, variantId, produkNama, currentQty, imageUrl, existingSerials);
                    }
                });

                // CALCULATIONS
                function calculateRow(row) {
                    const qty = parseFloat(row.find(".item-qty-hidden").val()) || 0;
                    const hargaJual = parseFloat(row.find(".item-harga-hidden").val()) || 0;
                    const diskon = parseFloat(row.find(".item-diskon-hidden").val()) || 0;
                    const pajakRate = parseFloat(row.find(".item-pajak-rate-hidden").val()) || 0;

                    const subtotalSebelumTaxe = (qty * hargaJual) - diskon;
                    const pajakAmount = subtotalSebelumTaxe * (pajakRate / 100);
                    const subtotalDenganTaxe = subtotalSebelumTaxe + pajakAmount;

                    row.find(".item-pajak").text(formatCurrency(pajakAmount));
                    row.find(".subtotal-item").text(formatCurrency(subtotalDenganTaxe));
                }

                function calculateGrandTotal() {
                    let subtotalKeseluruhan = 0;
                    let totalPajakKeseluruhan = 0;

                    $('#table-penjualan tbody tr').each(function() {
                        const qty = parseFloat($(this).find(".item-qty-hidden").val()) || 0;
                        const hargaJual = parseFloat($(this).find(".item-harga-hidden").val()) || 0;
                        const diskon = parseFloat($(this).find(".item-diskon-hidden").val()) || 0;
                        const pajakRate = parseFloat($(this).find(".item-pajak-rate-hidden")
                            .val()) || 0;

                        const subtotalItem = (qty * hargaJual) - diskon;
                        const pajakItem = subtotalItem * (pajakRate / 100);

                        subtotalKeseluruhan += subtotalItem;
                        totalPajakKeseluruhan += pajakItem;
                    });

                    $("#subtotal").text(formatCurrency(subtotalKeseluruhan));
                    $("#pajak-total-display").text(formatCurrency(totalPajakKeseluruhan));

                    const service = parseFloat($("#service-input").val()) || 0; // ✅ FIX Bug #6: include service
                    const ongkir = parseFloat($("#ongkir-input").val()) || 0;
                    const diskonTambahan = parseFloat($("#diskon-tambahan").val()) || 0;
                    const totalAkhir = subtotalKeseluruhan + totalPajakKeseluruhan + service - diskonTambahan +
                        ongkir;
                    const finalTotal = totalAkhir < 0 ? 0 : totalAkhir;

                    $("#total-akhir").text(formatCurrency(finalTotal));
                    $("#cart-total-btn-display").text(formatCurrency(finalTotal));

                    const isCartEmpty = $('#table-penjualan tbody tr').length === 0;
                    $('#btn-open-payment').prop('disabled', isCartEmpty);

                    return finalTotal;
                }

                $("#table-penjualan").on("click", ".btn-remove", function() {
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
                                calculateGrandTotal();
                            }
                        });
                    } else {
                        if (confirm(`Hapus ${productName} dari daftar?`)) {
                            row.remove();
                            calculateGrandTotal();
                        }
                    }
                });

                $("#table-penjualan").on("click", ".btn-edit", function() {
                    const row = $(this).closest("tr");
                    const rowId = row.data('row-id');

                    let fullName = row.find('.item-name').text();
                    let variantName = row.find('small.text-muted').text();
                    if (variantName) fullName += " " + variantName;

                    $("#edit-item-id").val(rowId);
                    $("#edit-item-name").val(fullName);
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
                    const row = $(`#table-penjualan tbody tr[data-row-id="${rowId}"]`);

                    const newQty = parseInt($("#edit-item-qty").val()) || 1;
                    row.find(".item-qty, .item-qty-hidden").val(newQty);
                    row.find(".item-harga-hidden").val(parseCurrency($("#edit-item-harga").val()));
                    row.find(".item-diskon-hidden").val(parseCurrency($("#edit-item-diskon").val()));

                    const selectedTaxe = $("#edit-item-pajak-id option:selected");
                    row.find(".item-pajak-id-hidden").val(selectedTaxe.val());
                    row.find(".item-pajak-rate-hidden").val(selectedTaxe.data('rate') || 0);

                    calculateRow(row);
                    calculateGrandTotal(); 
                    editItemModal.hide();

                    // --- CEK APAKAH WAJIB SERI ---
                    const hasSN = row.hasClass('is-wajib-seri') || row.find('.item-sn-hidden').length > 0;
                    
                    if (hasSN) {
                        // Ambil data yang dibutuhkan untuk memanggil modal SN
                        const productId = row.find('input[name*="[product_id]"]').val();
                        const variantId = row.find('input[name*="[product_variant_id]"]').val();
                        const productName = row.find('.item-name').text();
                        const imgUrl = row.find('img').attr('src');
                        
                        // Ambil SN yang sudah tersimpan sebelumnya sebagai pre-selection
                        const existingSerials = [];
                        row.find('.item-sn-hidden').each(function() {
                            existingSerials.push($(this).val());
                        });

                        // Buka Modal SN dengan meminta jumlah SN sesuai dengan qty baru
                        openSerialNumberModal(rowId, productId, variantId, productName, newQty, imgUrl, existingSerials);
                    }
                });

                // ==========================================
                //  LOGIKA MODAL EXTRA COST (Ongkir/Diskon)
                // ==========================================
                const editExtraCostModal = document.getElementById('editExtraCostModal');
                if (editExtraCostModal) {
                    editExtraCostModal.addEventListener('show.bs.modal', function(event) {
                        const trigger = event.relatedTarget;
                        const type = trigger.getAttribute('data-type');
                        const label = trigger.getAttribute('data-label');

                        document.getElementById('extra-cost-type').value = type;
                        document.getElementById('editExtraCostModalLabel').innerText = 'Edit ' +
                            label;
                        document.getElementById('extra-cost-label').innerText = label;

                        let currentValue = 0;
                        if (type === 'service') currentValue = document.getElementById(
                            'service-input').value;
                        if (type === 'ongkir') currentValue = document.getElementById(
                            'ongkir-input').value;
                        if (type === 'diskon') currentValue = document.getElementById(
                            'diskon-tambahan').value;

                        document.getElementById('extra-cost-value').value = new Intl.NumberFormat(
                            'id-ID').format(currentValue);
                    });

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
                        } else if (type === 'service') {
                            document.getElementById('service-input').value = value;
                            document.getElementById('service-display').innerText = formatCurrency(
                                value);
                        }

                        bootstrap.Modal.getInstance(editExtraCostModal).hide();
                        calculateGrandTotal();
                    });

                    $('#extra-cost-value').on('input', function() {
                        let value = parseCurrency($(this).val());
                        $(this).val(new Intl.NumberFormat('id-ID').format(value));
                    });
                }

                // ==========================================
                //  LOGIKA MODAL PAYMENT EDIT
                // ==========================================
                // ✅ FIX Bug #3: target #paymentModalEdit (bukan #paymentModal yang dipakai create)
                const paymentModalElement = document.getElementById('paymentModalEdit');
                if (paymentModalElement) {
                    // ✅ FIX: pakai class .select2-bank-edit agar tidak bentrok dengan modal create
                    $('.select2-bank-edit').select2({
                        placeholder: "Pilih Rekening Bank...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#paymentModalEdit'),
                    });

                    // ✅ FIX: pakai id unik -edit
                    const inputJumlah = document.getElementById('jumlah-dibayar-input-edit');
                    const displayChange = document.getElementById('change-display-edit');

                    paymentModalElement.addEventListener('show.bs.modal', function() {
                        const total = calculateGrandTotal();
                        document.getElementById('payment-modal-total-edit').innerText = formatCurrency(
                            total);
                        // ✅ Edit: pre-fill dengan jumlah_dibayar yang sudah ada
                        if (inputJumlah && !inputJumlah.value) {
                            inputJumlah.value = new Intl.NumberFormat('id-ID').format(
                                {{ old('jumlah_dibayar', $penjualan->jumlah_dibayar ?? 0) }});
                        }
                        calculateModalChange();
                        toggleTransferDetails();
                    });

                    document.querySelectorAll('.quick-pay-btn-edit').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const amount = parseFloat(this.getAttribute('data-amount'));
                            let currentVal = parseCurrency(inputJumlah.value);
                            inputJumlah.value = new Intl.NumberFormat('id-ID').format(
                                currentVal + amount);
                            calculateModalChange();
                        });
                    });

                    // ✅ FIX: pakai id unik btn-pay-exact-edit
                    const btnPayExact = document.getElementById('btn-pay-exact-edit');
                    if (btnPayExact) {
                        btnPayExact.addEventListener('click', function() {
                            const total = calculateGrandTotal();
                            inputJumlah.value = new Intl.NumberFormat('id-ID').format(total);
                            calculateModalChange();
                        });
                    }

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

                    // ✅ FIX: pakai id unik -edit
                    const paymentRadios = document.querySelectorAll('#paymentModalEdit input[name="metode_pembayaran"]');
                    const transferDetails = document.getElementById('transfer-details-edit');
                    const bankTujuanSelect = document.getElementById('bank_id_edit');

                    function toggleTransferDetails() {
                        const isTransfer = document.getElementById('pay-transfer-edit').checked;
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

                    // ✅ Edit: pre-select metode pembayaran yang sudah tersimpan
                    @if ($penjualan->metode_pembayaran)
                        const savedMetode = "{{ $penjualan->metode_pembayaran }}";
                        // ✅ FIX: cari dalam scope #paymentModalEdit saja, pakai selector -edit
                        const savedRadio = document.querySelector(
                            `#paymentModalEdit input[name="metode_pembayaran"][value="${savedMetode}"]`);
                        if (savedRadio) {
                            savedRadio.checked = true;
                        }
                    @endif

                    // ✅ Edit: pre-select bank jika metode transfer
                    @if ($penjualan->bank_id)
                        $(bankTujuanSelect).val("{{ $penjualan->bank_id }}").trigger('change');
                        toggleTransferDetails();
                    @endif

                    // ✅ FIX: Init Quill untuk catatan edit (id unik -edit)
                    if (document.getElementById('quill-editor-catatan-edit')) {
                        const hiddenInputCatatanEdit = document.getElementById('catatan-edit');
                        const quillEdit = new Quill('#quill-editor-catatan-edit', {
                            theme: 'snow',
                            placeholder: 'Tulis catatan penjualan di sini...',
                        });
                        quillEdit.on('text-change', function() {
                            hiddenInputCatatanEdit.value = quillEdit.root.innerHTML;
                        });
                        if (hiddenInputCatatanEdit.value) {
                            quillEdit.root.innerHTML = hiddenInputCatatanEdit.value;
                        }
                    }
                }

                // ==========================================
                //  FORM SUBMISSION (Validasi Akhir)
                // ==========================================
                // ✅ FIX Bug #4: nama form yang benar adalah #editSaleForm
                $("#editSaleForm").on("submit", function(e) {
                    const itemCount = $("#table-penjualan tbody tr").length;

                    if (itemCount === 0) {
                        e.preventDefault();
                        if (typeof window.showToast !== 'undefined') window.showToast('warning',
                            'Harap tambahkan minimal satu produk.');
                        else alert('Harap tambahkan minimal satu produk.');
                        return;
                    }

                    // ✅ FIX: pakai id unik -edit
                    const inputJumlahEl = document.getElementById('jumlah-dibayar-input-edit');
                    if (inputJumlahEl) {
                        inputJumlahEl.value = parseCurrency(inputJumlahEl.value);
                    }

                    const totalAkhir = calculateGrandTotal();
                    const bayar = parseFloat(inputJumlahEl ? inputJumlahEl.value : 0) || 0;
                    let statusPembayaran = bayar >= totalAkhir ? 'Lunas' : 'Piutang';

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
                        '<i class="bx bx-loader bx-spin me-1"></i> Menyimpan...');
                });

                // --- TRIGGER AWAL: Kalkulasi ulang data dari DB ---
                // 1. Tampilkan ongkir dan diskon dari DB ke display
                const initService = parseFloat($("#service-input").val()) || 0;
                const initOngkir = parseFloat($("#ongkir-input").val()) || 0;
                const initDiskon = parseFloat($("#diskon-tambahan").val()) || 0;
                $("#service-display").text(formatCurrency(initService));
                $("#ongkir-display").text(formatCurrency(initOngkir));
                $("#diskon-display").text(formatCurrency(initDiskon));

                // 2. Kalkulasi ulang setiap baris yang sudah ada
                $('#table-penjualan tbody tr').each(function() {
                    calculateRow($(this));
                });

                // 3. Kalkulasi Grand Total
                calculateGrandTotal();
            });
        });
    </script>
@endsection