@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Nomor Seri')

{{-- Select2 CSS already loaded globally via Vite in commonMaster/styles.blade.php --}}

@section('content')
    <div class="card rounded-3 mb-4 shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between py-3 px-4 border-bottom">
            <div>
                <h5 class="mb-0 fw-semibold"><i class="bx bx-barcode me-2 text-primary"></i>Pendaftaran Nomor Seri</h5>
                <p class="text-muted mb-0 mt-1 small">Tambah nomor seri untuk produk yang membutuhkannya.</p>
            </div>
        </div>
        <div class="card-body p-4">
            <form id="addMultipleSerialsForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="purchase_id" id="selected_purchase_id" value="{{ $purchaseId }}">
                <input type="hidden" name="product_id" id="selected_product_id">
                <input type="hidden" name="product_variant_id" id="selected_variant_id">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="select-produk" class="form-label fw-medium">Pilih Produk</label>
                        <select id="select-produk" class="form-control" style="width:100%;"></select>
                    </div>
                    <div class="col-md-4">
                        <label for="input-serial" class="form-label fw-medium">Input Nomor Seri</label>
                        <div class="input-group">
                            <input type="text" id="input-serial" class="form-control"
                                placeholder="Pilih produk terlebih dahulu" disabled>
                            <button type="button" id="btn-add-to-table" class="btn btn-primary" disabled>
                                <i class="bx bx-plus me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2" id="product-info-container" style="display: none;">
                        <div class="card border-0 bg-light rounded-3 h-100">
                            <div class="card-body p-2 text-center">
                                <div class="d-flex justify-content-around">
                                    <div>
                                        <div class="fw-bold fs-6" id="info-stok">0</div>
                                        <div class="text-muted" style="font-size:0.7rem;">Stok</div>
                                    </div>
                                    <div class="vr mx-1"></div>
                                    <div>
                                        <div class="fw-bold fs-6" id="info-sn-tercatat">0</div>
                                        <div class="text-muted" style="font-size:0.7rem;">Tercatat</div>
                                    </div>
                                    <div class="vr mx-1"></div>
                                    <div>
                                        <div class="fw-bold fs-6 text-danger" id="info-sn-butuh">0</div>
                                        <div class="text-muted" style="font-size:0.7rem;">Butuh</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="serial-input-section" style="display: none;">
                    <div class="table-responsive mt-3 border rounded-3" style="max-height: 260px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" id="temp-serial-table">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-uppercase text-muted small ps-3" style="width:50px;">No.</th>
                                    <th class="text-uppercase text-muted small">Nomor Seri</th>
                                    <th class="text-uppercase text-muted small text-center" style="width:80px;">Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Diisi oleh JS --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <button type="button" id="btn-submit-serials" class="btn btn-success" disabled>
                            <i class="bx bx-save me-1"></i>Simpan Nomor Seri
                        </button>
                        <span class="text-muted small" id="sn-count-label"></span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Card 2: Daftar Nomor Seri --}}
    <div class="card rounded-3 shadow-sm">
        <div class="card-header py-3 px-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-semibold"><i class="bx bx-list-ul me-2 text-primary"></i>Daftar Nomor Seri</h5>
                    <p class="text-muted mb-0 mt-1 small">Kelola semua nomor seri produk Anda.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4 pb-2">
            <form method="GET" action="{{ route('serial-number.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-medium">Cari Nomor Seri</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="search" name="search" class="form-control"
                                placeholder="Ketik nomor seri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="product_id_filter" class="form-label fw-medium">Filter Produk</label>
                        <select id="product_id_filter" name="product_id" class="form-select select2 "
                            data-placeholder="pilih produk" style="width:100%;">
                            <option value="">Semua Produk</option>
                            @foreach ($products as $produk)
                                <option value="{{ $produk->id }}" @selected(request('product_id') == $produk->id)>
                                    {{ $produk->name_product }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status_filter" class="form-label fw-medium">Filter Status</label>
                        <select id="status_filter" name="status" class="form-select select2"
                            data-placeholder="pilih status">
                            <option value="">Semua Status</option>
                            <option value="Tersedia" @selected(request('status') == 'Tersedia')>Tersedia</option>
                            <option value="Terjual" @selected(request('status') == 'Terjual')>Terjual</option>
                            <option value="Rusak" @selected(request('status') == 'Rusak')>Rusak</option>
                            <option value="Hilang" @selected(request('status') == 'Hilang')>Hilang</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i
                                class="bx bx-filter me-1"></i>Filter</button>
                        <a href="{{ route('serial-number.index') }}" class="btn btn-outline-secondary px-3"><i
                                class="bx bx-reset me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle mb-0" id="tableData">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-muted small ps-4">Produk</th>
                        <th class="text-uppercase text-muted small">Varian</th>
                        <th class="text-uppercase text-muted small">Nomor Seri</th>
                        <th class="text-uppercase text-muted small">Status</th>
                        <th class="text-uppercase text-muted small">Tgl. Masuk</th>
                        <th class="text-uppercase text-muted small">Info Sale</th>
                        <th class="text-uppercase text-muted small text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="isiTable">
                    @forelse ($serialNumbers as $sn)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        // Prioritaskan gambar varian, fallback ke gambar produk
                                        $imgPath =
                                            $sn->variant?->img_variant ?? ($sn->produk->primaryImage?->path ?? null);
                                    @endphp
                                    @if ($imgPath)
                                        <img src="{{ Storage::url($imgPath) }}" class="rounded-2" width="36"
                                            height="36" style="object-fit:cover;"
                                            alt="{{ $sn->produk->name_product }}">
                                    @else
                                        <img src="{{ asset('assets/img/produk.png') }}" class="rounded-2" width="36"
                                            height="36" style="object-fit:cover;" alt="Produk">
                                    @endif
                                    <span class="fw-medium small">{{ $sn->produk->name_product }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($sn->variant)
                                    @php
                                        $variantLabel = $sn->variant->options->pluck('value')->implode(' / ');
                                    @endphp
                                    <span
                                        class="badge bg-label-info small">{{ $variantLabel ?: $sn->variant->sku }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-dark fw-mono text-sm px-2 py-1">{{ $sn->nomor_seri }}</span>
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'Tersedia' => 'success',
                                        'Terjual' => 'info',
                                        'Rusak' => 'danger',
                                        'Hilang' => 'warning',
                                    ];
                                    $color = $statusMap[$sn->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-label-{{ $color }}">{{ $sn->status }}</span>
                            </td>
                            <td class="small text-muted">{{ $sn->created_at->translatedFormat('d M Y') }}</td>
                            <td>
                                @if ($sn->penjualan)
                                    <a href="{{ route('penjualan.show', $sn->penjualan->referensi) }}"
                                        class="text-primary fw-medium small" data-bs-toggle="tooltip"
                                        title="Lihat Invoice Sale">
                                        {{ $sn->penjualan->referensi }}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="action-btn text-secondary btn-edit me-1"
                                    data-id="{{ $sn->id }}" data-serial="{{ $sn->nomor_seri }}"
                                    data-status="{{ $sn->status }}" title="Edit SN" data-bs-toggle="tooltip">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <button type="button" class="action-btn text-danger btn-delete"
                                    data-id="{{ $sn->id }}" data-serial="{{ $sn->nomor_seri }}" title="Hapus SN"
                                    data-bs-toggle="tooltip">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="bx bx-info-circle fs-3 mb-2"></i>
                                    <span class="">Tidak ada data nomor seri yang cocok.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="my-3 ms-3">{{ $serialNumbers->links() }}</div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editSerialModal" tabindex="-1" aria-labelledby="editSerialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header border-bottom pb-3">
                    <h6 class="modal-title fw-semibold" id="editSerialModalLabel">
                        <i class="bx bx-edit me-2 text-primary"></i>Edit Nomor Seri
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <form id="editSerialForm" method="post">
                        @method('put')
                        @csrf
                        <div class="mb-3">
                            <label for="edit_serial_number" class="form-label fw-medium">Nomor Seri</label>
                            <input id="edit_serial_number" name="serial_number" type="text" class="form-control"
                                required>
                            <div class="invalid-feedback" id="edit_serial_number-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label fw-medium">Status</label>
                            <select id="edit_status" name="status" class="form-select select2"
                                data-placeholder="pilih status ..." required>
                                <option value="" class=""></option>
                                @foreach ($status as $s)
                                    <option value="{{ $s }}" @selected(old('status_barang') == $s)>
                                        {{ $s }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-warning"><i class="bx bx-info-circle me-1"></i>Status "Terjual"
                                diatur otomatis oleh sistem.</div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-3 text-center">
                <div class="modal-body py-4 px-4">
                    <div class="mb-3">
                        <span class="avatar avatar-lg rounded-circle bg-label-danger">
                            <i class="bx bx-trash fs-4"></i>
                        </span>
                    </div>
                    <h6 class="fw-semibold mb-1">Hapus Nomor Seri?</h6>
                    <p class="text-muted small mb-3">Tindakan ini tidak dapat dibatalkan.</p>
                    <p class="fw-bold text-dark mb-4" id="serialNumberToDelete"></p>
                    <form id="deleteSerialForm" method="POST" action="#">
                        @method('delete')
                        @csrf
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                        </div>
                    </form>
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

        $(document).ready(function() {
            setTimeout(function() {
                // --- INISIALISASI GLOBAL ---
                let tempSerials = new Set();
                let selectedProductData = null;
                const editSerialModal = new bootstrap.Modal(document.getElementById('editSerialModal'));
                const deleteSerialModal = new bootstrap.Modal(document.getElementById(
                    'deleteConfirmationModal'));

                // Inisialisasi tooltip Bootstrap
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(
                    el));

                // --- SELECT2: Filter Produk (tidak perlu AJAX, data sudah ada) ---
                $('#product_id_filter').select2({
                    placeholder: 'Semua Produk',
                    allowClear: true,
                    width: '100%'
                });

                // --- FUNGSI UTILITY ---
                function showSuccess(message) {
                    window.showToast('success', message);
                    setTimeout(() => location.reload(), 1500);
                }

                function showError(message, errors = {}) {
                    let errorText = message;
                    if (Object.keys(errors).length > 0) {
                        errorText = Object.values(errors).flat()[0];
                    }
                    window.showToast('error', errorText);
                }

                // --- FUNGSI UTAMA: Update State Input ---
                function updateInputState() {
                    if (!selectedProductData) return;

                    const butuhAwal = selectedProductData.qty - selectedProductData.sn_count;
                    const sisaButuh = butuhAwal - tempSerials.size;
                    const total = tempSerials.size;

                    if (sisaButuh > 0) {
                        $('#input-serial').prop('disabled', false)
                            .attr('placeholder', `Butuh ${sisaButuh} SN lagi...`);
                        $('#btn-add-to-table').prop('disabled', false);
                    } else {
                        $('#input-serial').prop('disabled', true)
                            .attr('placeholder', 'Jumlah SN sudah cukup');
                        $('#btn-add-to-table').prop('disabled', true);
                    }

                    $('#btn-submit-serials').prop('disabled', total === 0);
                    $('#sn-count-label').text(total > 0 ? `${total} SN siap disimpan` : '');
                }

                // --- FUNGSI: Muat Info Produk ---
                function updateProductInfo(productId, variantId, productSlug) {
                    if (!productId) {
                        $('#product-info-container').hide();
                        return;
                    }
                    const url = "{{ route('get-data.serial-product-info', ['produk' => ':id']) }}"
                        .replace(':id', productId);
                    $.ajax({
                        url: url,
                        method: 'GET',
                        data: variantId ? {
                            variant_id: variantId
                        } : {},
                        success: function(data) {
                            selectedProductData = {
                                id: productId,
                                variant_id: variantId || null,
                                qty: data.qty,
                                sn_count: data.sn_tercatat_count,
                                slug: productSlug
                            };
                            $('#selected_product_id').val(productId);
                            $('#selected_variant_id').val(variantId || '');
                            $('#info-stok').text(data.qty);
                            $('#info-sn-tercatat').text(data.sn_tercatat_count);
                            $('#info-sn-butuh').text(data.butuh_sn);
                            $('#product-info-container').show();
                            $('#serial-input-section').show();

                            if (productSlug) {
                                const newUrl = `/serial-number/${productSlug}`;
                                history.pushState({
                                    path: newUrl
                                }, '', newUrl);
                            }

                            tempSerials.clear();
                            renderTempTable();
                            updateInputState();
                        },
                        error: function() {
                            showError('Gagal memuat info produk.');
                        }
                    });
                }

                // --- SELECT2: Pilih Produk (AJAX) ---
                function formatProduct(produk) {
                    if (!produk.id) return produk.text;

                    // JS tinggal pakai img_produk karena URL-nya sudah matang dari server
                    const imageUrl = produk.img_produk;

                    const variantBadge = produk.variant_id ?
                        `<span class="badge bg-label-info ms-1" style="font-size:0.65rem;">${produk.variant_name}</span>` :
                        '';

                    return $(
                        `<div class="d-flex align-items-center gap-2 py-1">
                            <img src="${imageUrl}" class="rounded-2" width="32" height="32" style="object-fit:cover;" />
                            <div>
                                <div class="fw-medium small">${produk.name_product}${variantBadge}</div>
                                <div class="text-muted" style="font-size:0.72rem;">Stok: ${produk.qty ?? '-'}</div>
                            </div>
                        </div>`
                    );
                }

                $('#select-produk').select2({
                    placeholder: 'Ketik untuk mencari produk...',
                    allowClear: true,
                    width: '100%',
                    templateResult: formatProduct,
                    templateSelection: function(p) {
                        if (!p.id) return p.text || 'Pilih Produk';
                        // Tampilkan "Nama Produk — Varian" di field setelah dipilih
                        return p.variant_name ?
                            `${p.name_product} — ${p.variant_name}` :
                            p.name_product || p.text;
                    },
                    ajax: {
                        url: "{{ route('get-data.produk') }}",
                        dataType: 'json',
                        delay: 250,
                        data: (params) => ({
                            search: params.term,
                            page: params.page || 1,
                            wajib_seri: 1,
                            hide_fulfilled: 1
                        }),
                        processResults: function(data) {
                            return {
                                results: data.data.map(item => {
                                    // Buat ID unik: "productId-variantId" untuk varian, atau "productId" untuk simple
                                    const combinedId = item.variant_id ?
                                        `${item.id}-${item.variant_id}` :
                                        String(item.id);
                                    // Teks tampilan: "Nama Produk - Warna / Ukuran" atau hanya "Nama Produk"
                                    const displayName = item.variant_name ?
                                        `${item.name_product} - ${item.variant_name}` :
                                        item.name_product;
                                    return {
                                        id: combinedId,
                                        product_id: item.id,
                                        variant_id: item.variant_id || null,
                                        variant_name: item.variant_name || null,
                                        name_product: item.name_product,
                                        text: displayName,
                                        slug: item.slug,
                                        qty: item.qty,
                                        img_produk: item.img_produk || null,
                                    };
                                }),
                                pagination: {
                                    more: data.next_page_url !== null
                                }
                            };
                        }
                    }
                });

                $('#select-produk').on('select2:select', function(e) {
                    const data = e.params.data;
                    updateProductInfo(data.product_id, data.variant_id, data.slug);
                });

                $('#select-produk').on('select2:clear', function() {
                    selectedProductData = null;
                    $('#product-info-container').hide();
                    $('#serial-input-section').hide();
                    $('#selected_product_id').val('');
                    $('#selected_variant_id').val('');
                    tempSerials.clear();
                });

                // --- LOGIKA PENDAFTARAN SN ---
                function addSerialToTempTable() {
                    if (!selectedProductData) return;
                    const serialInput = $('#input-serial');
                    const serialValue = serialInput.val().trim().toUpperCase();
                    if (!serialValue) return;

                    const sisaButuh = (selectedProductData.qty - selectedProductData.sn_count) - tempSerials
                        .size;
                    if (sisaButuh <= 0) {
                        window.showToast('warning', 'Jumlah nomor seri yang ditambahkan sudah mencukupi.');
                        return;
                    }
                    if (tempSerials.has(serialValue)) {
                        window.showToast('warning', 'Nomor seri sudah ada di dalam daftar.');
                        return;
                    }

                    tempSerials.add(serialValue);
                    renderTempTable();
                    serialInput.val('').focus();
                }

                $('#btn-add-to-table').on('click', addSerialToTempTable);
                $('#input-serial').on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        addSerialToTempTable();
                    }
                });

                function renderTempTable() {
                    const tableBody = $('#temp-serial-table tbody');
                    tableBody.empty();
                    let counter = 1;
                    tempSerials.forEach(serial => {
                        tableBody.append(
                            `<tr>
                        <td class="ps-3 text-muted small">${counter++}</td>
                        <td class="fw-medium small">${serial}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-temp"
                                data-serial="${serial}" title="Hapus dari daftar">
                                <i class="bx bx-x"></i>
                            </button>
                        </td>
                    </tr>`
                        );
                    });
                    updateInputState();
                }

                $('#temp-serial-table').on('click', '.btn-remove-temp', function() {
                    tempSerials.delete($(this).data('serial'));
                    renderTempTable();
                });

                $('#btn-submit-serials').on('click', function() {
                    const btn = $(this);
                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...'
                    );
                    $.ajax({
                        url: "{{ route('serial-number.store') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            purchase_id: $('#selected_purchase_id').val() || null,
                            product_id: $('#selected_product_id').val(),
                            product_variant_id: $('#selected_variant_id').val() || null,
                            serial_numbers: Array.from(tempSerials)
                        },
                        success: (response) => showSuccess(response.message),
                        error: (xhr) => {
                            showError(
                                xhr.responseJSON?.message || 'Terjadi kesalahan.',
                                xhr.responseJSON?.errors || {}
                            );
                            btn.prop('disabled', false).html(
                                '<i class="bx bx-save me-1"></i>Simpan Nomor Seri');
                        }
                    });
                });

                @if ($produkDipilih)
                    $('#product_id_filter').val("{{ $produkDipilih->id }}").trigger('change');

                    @if ($produkDipilih->has_variants)
                        {{-- Mengecek apakah ada data varian spesifik yang dikirim dari controller --}}
                        @if (isset($varianDipilih) && $varianDipilih)
                            var combinedId = "{{ $produkDipilih->id }}-{{ $varianDipilih->id }}";

                            var variantName =
                                "{{ $varianDipilih->options->pluck('value')->implode(' / ') ?: $varianDipilih->sku }}";
                            var displayName = "{{ $produkDipilih->name_product }} — " + variantName;

                            var produkOption = new Option(displayName, combinedId, true, true);
                            $('#select-produk').append(produkOption).trigger('change');

                            updateProductInfo("{{ $produkDipilih->id }}", "{{ $varianDipilih->id }}",
                                "{{ $produkDipilih->slug }}");
                        @else
                            window.showToast('info',
                                'Silakan pilih varian produk secara spesifik di form pendaftaran untuk menambah SN baru.'
                            );
                        @endif
                    @else
                        var produkOption = new Option(
                            "{{ $produkDipilih->name_product }}",
                            "{{ $produkDipilih->id }}",
                            true, true
                        );
                        $('#select-produk').append(produkOption).trigger('change');
                        updateProductInfo("{{ $produkDipilih->id }}", null, "{{ $produkDipilih->slug }}");
                    @endif
                @endif

                // --- MODAL EDIT ---
                $('#tableData').on('click', '.btn-edit', function() {
                    const id = $(this).data('id');
                    const serial = $(this).data('serial');
                    const status = $(this).data('status');
                    const url = "{{ route('serial-number.update', ':id') }}".replace(':id', id);

                    $('#editSerialForm').attr('action', url);
                    $('#edit_serial_number').val(serial).removeClass('is-invalid');
                    $('#edit_serial_number-error').text('');
                    $('#edit_status').val(status);
                    editSerialModal.show();
                });

                $('#editSerialForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const btn = form.find('[type=submit]');
                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...'
                    );
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: (response) => {
                            editSerialModal.hide();
                            showSuccess(response.message);
                        },
                        error: (xhr) => {
                            const error = xhr.responseJSON?.errors?.serial_number?.[0];
                            $('#edit_serial_number').addClass('is-invalid').focus();
                            $('#edit_serial_number-error').text(error ||
                                'Terjadi kesalahan.');
                            btn.prop('disabled', false).text('Simpan Perubahan');
                        }
                    });
                });

                // --- MODAL DELETE ---
                $('#tableData').on('click', '.btn-delete', function() {
                    const id = $(this).data('id');
                    const serial = $(this).data('serial');
                    const url = "{{ route('serial-number.destroy', ':id') }}".replace(':id', id);
                    $('#deleteSerialForm').attr('action', url);
                    $('#serialNumberToDelete').text(`"${serial}"`);
                    deleteSerialModal.show();
                });

                $('#deleteSerialForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const btn = form.find('[type=submit]');
                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...'
                    );
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: (response) => {
                            deleteSerialModal.hide();
                            showSuccess(response.message);
                        },
                        error: (xhr) => {
                            deleteSerialModal.hide();
                            showError(xhr.responseJSON?.message ||
                                'Gagal menghapus nomor seri.');
                        }
                    });
                });
            }, 500);
        });
    </script>
@endsection
