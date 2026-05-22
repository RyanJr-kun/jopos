@extends('layouts/contentNavbarLayout')

@section('title', 'Buat Promo Baru')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible d-flex align-items-start gap-2 mb-4 shadow-sm border-0"
            role="alert">
            <i class="bx bx-error-circle fs-5 mt-1 flex-shrink-0"></i>
            <div>
                <strong class="d-block mb-1">Oops! Ada kesalahan:</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('promo.store') }}" method="POST" id="promoForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 d-flex flex-column gap-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center gap-2">
                        <span
                            class="avatar avatar-sm d-flex align-items-center justify-content-center bg-label-primary rounded">
                            <i class="bx bx-info-circle"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Informasi Dasar</h6>
                            <small class="text-muted">Nama dan kode identifikasi promo</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Promotion <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="cth: Promo Lebaran 2025" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="code" class="form-label fw-semibold">
                                    Kode Promo
                                    <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="bx bx-barcode text-muted"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control text-uppercase @error('code') is-invalid @enderror"
                                        id="code" name="code" value="{{ old('code') }}" placeholder="LEBARAN25"
                                        style="letter-spacing:.5px;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Kosongkan jika tidak butuh kode.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Pengaturan Diskon --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center gap-2">
                        <span
                            class="avatar avatar-sm d-flex align-items-center justify-content-center bg-label-warning rounded">
                            <i class="bx bx-purchase-tag text-warning"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Pengaturan Diskon</h6>
                            <small class="text-muted">Tipe, nilai, dan batasan diskon</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Tipe --}}
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">
                                    Tipe Diskon <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="percentage" @selected(old('type', 'percentage') == 'percentage')>
                                        Persentase (%)
                                    </option>
                                    <option value="fixed" @selected(old('type') == 'fixed')>
                                        Nominal Tetap (Rp)
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nilai Diskon --}}
                            <div class="col-md-6">
                                <label for="nilai_diskon" class="form-label fw-semibold">
                                    Nilai Diskon <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text fw-semibold" id="nilai-diskon-addon">%</span>
                                    <input type="number" step="0.01"
                                        class="form-control @error('nilai_diskon') is-invalid @enderror" id="nilai_diskon"
                                        name="nilai_diskon" value="{{ old('nilai_diskon') }}" required min="0"
                                        placeholder="0">
                                    @error('nilai_diskon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted" id="nilai-diskon-hint">
                                    Masukkan nilai persentase (0–100).
                                </small>
                            </div>

                            {{-- Min Pembelian --}}
                            <div class="col-md-6">
                                <label for="min_pembelian" class="form-label fw-semibold">
                                    Minimum Pembelian
                                    <span class="text-muted fw-normal">(Rp)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted">Rp</span>
                                    <input type="number" step="1"
                                        class="form-control @error('min_pembelian') is-invalid @enderror" id="min_pembelian"
                                        name="min_pembelian" value="{{ old('min_pembelian', 0) }}" min="0">
                                    @error('min_pembelian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Isi 0 jika tidak ada minimum.</small>
                            </div>

                            {{-- Maks Diskon (hanya untuk persentase) --}}
                            <div class="col-md-6" id="max-diskon-wrapper">
                                <label for="max_diskon" class="form-label fw-semibold">
                                    Maks. Diskon
                                    <span class="text-muted fw-normal">(Rp)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted">Rp</span>
                                    <input type="number" step="1"
                                        class="form-control @error('max_diskon') is-invalid @enderror" id="max_diskon"
                                        name="max_diskon" value="{{ old('max_diskon') }}" min="0"
                                        placeholder="Tidak terbatas">
                                    @error('max_diskon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Kosongkan jika tidak ada batas.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Periode --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center gap-2">
                        <span
                            class="avatar avatar-sm d-flex align-items-center justify-content-center bg-label-success rounded">
                            <i class="bx bx-calendar text-success"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Periode Promo</h6>
                            <small class="text-muted">Tanggal mulai dan berakhirnya promo</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label fw-semibold">
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                    class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai"
                                    name="tanggal_mulai" value="{{ old('tanggal_mulai', now()->format('Y-m-d\TH:i')) }}"
                                    required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_berakhir" class="form-label fw-semibold">
                                    Tanggal Berakhir <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                    class="form-control @error('tanggal_berakhir') is-invalid @enderror"
                                    id="tanggal_berakhir" name="tanggal_berakhir"
                                    value="{{ old('tanggal_berakhir', now()->addMonth()->format('Y-m-d\TH:i')) }}"
                                    required>
                                @error('tanggal_berakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Deskripsi --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center gap-2">
                        <span
                            class="avatar avatar-sm d-flex align-items-center justify-content-center bg-label-info rounded">
                            <i class="bx bx-file-blank text-info"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Deskripsi</h6>
                            <small class="text-muted">Keterangan tambahan (opsional)</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="quill-description" style="min-height: 150px;">
                            {!! old('description', '') !!}
                        </div>
                        <input type="hidden" name="description" id="description" value="{{ old('description', '') }}">
                        @error('description')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="col-lg-4 d-flex flex-column gap-4">

                {{-- Card: Status --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-2 bg-label-primary">
                            <div>
                                <p class="mb-0 fw-semibold text-sm">Aktifkan Promo</p>
                                <small class="text-muted">Promo langsung dapat digunakan</small>
                            </div>
                            <div class="form-check form-switch ms-2 mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="status"
                                    name="status" value="1" @checked(old('status', true))
                                    style="width:2.5em; height:1.4em; cursor:pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Cakupan Produk --}}
                <div class="card border-0 shadow-sm flex-grow-1">
                    <div class="card-header py-3 d-flex align-items-center gap-2">
                        <span
                            class="avatar avatar-sm d-flex align-items-center justify-content-center bg-label-primary rounded">
                            <i class="bx bx-package text-primary"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Cakupan Produk</h6>
                            <small class="text-muted">Tentukan produk yang mendapat diskon</small>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column gap-3">
                        {{-- Toggle Semua Produk --}}
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-2 bg-label-blue">
                            <div>
                                <p class="mb-0 fw-semibold text-sm">Semua Produk</p>
                                <small class="text-muted">Berlaku untuk seluruh katalog</small>
                            </div>
                            <div class="form-check form-switch ms-2 mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_all_products"
                                    name="is_all_products" value="1" @checked(old('is_all_products', false))
                                    style="width:2.5em; height:1.4em; cursor:pointer;">
                            </div>
                        </div>

                        {{-- Select Produk Tertentu --}}
                        <div id="products-wrapper">
                            <label for="products" class="form-label fw-semibold text-sm mb-1">
                                Pilih Produk Tertentu
                            </label>
                            <select class="select2 form-select" id="products" name="products[]" multiple>
                                @if (old('products'))
                                    @foreach (Modules\Inventory\Models\Product::whereIn('id', old('products'))->get() as $produk)
                                        <option value="{{ $produk->id }}" selected>
                                            {{ $produk->name_product }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text text-muted">
                                Kosongkan jika ingin berlaku untuk semua produk.
                            </small>
                            @error('products')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="submit"
                            class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bx bx-save"></i>
                            <span>Simpan Promo</span>
                        </button>
                        <a href="{{ route('promo.index') }}" class="btn btn-outline-secondary w-100">
                            Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </form>

@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script type="module">
        const ROUTE_PRODUK = "{{ route('get-data.produk') }}";
        const ASSET_DEFAULT = "{{ asset('assets/img/produk.png') }}";
        const ASSET_STORAGE = "{{ asset('storage') }}";

        /* ── 1. TOGGLE TYPE UI ── */
        function toggleTypeUI() {
            const type = document.getElementById('type').value;
            const isPct = type === 'percentage';
            const maxWrapper = document.getElementById('max-diskon-wrapper');
            const addon = document.getElementById('nilai-diskon-addon');
            const input = document.getElementById('nilai_diskon');
            const hint = document.getElementById('nilai-diskon-hint');

            maxWrapper.style.display = isPct ? '' : 'none';
            addon.textContent = isPct ? '%' : 'Rp';

            if (isPct) {
                input.setAttribute('max', '100');
                hint.textContent = 'Masukkan nilai persentase (0–100).';
            } else {
                input.removeAttribute('max');
                hint.textContent = 'Masukkan nominal diskon dalam Rupiah.';
            }
        }

        document.getElementById('type').addEventListener('change', toggleTypeUI);
        toggleTypeUI();

        /* ── 2. TOGGLE PRODUCTS WRAPPER ── */
        function toggleProductsWrapper() {
            const isAll = document.getElementById('is_all_products').checked;
            const wrapper = document.getElementById('products-wrapper');
            const sel = document.getElementById('products');

            wrapper.style.display = isAll ? 'none' : '';

            if (isAll && sel) {
                Array.from(sel.options).forEach(o => o.selected = false);
                if (typeof $ !== 'undefined' && $.fn.select2) $(sel).trigger('change');
            }
        }

        document.getElementById('is_all_products').addEventListener('change', toggleProductsWrapper);
        toggleProductsWrapper();

        /* ── 3. SELECT2 WITH AJAX ── */
        function formatProduct(produk) {
            if (!produk.id) return produk.text;
            const imageUrl = produk.img_produk ?
                `${ASSET_STORAGE}/${produk.img_produk}` :
                ASSET_DEFAULT;
            const harga = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(produk.harga_jual);

            return $(`
                <div class="d-flex align-items-center gap-2 py-1">
                    <img src="${imageUrl}" class="rounded" style="width:36px;height:36px;object-fit:cover;"
                         onerror="this.src='${ASSET_DEFAULT}'">
                    <div>
                        <div class="fw-semibold" style="font-size:.85rem;">${produk.text}</div>
                        <div class="text-muted" style="font-size:.75rem;">${harga}</div>
                    </div>
                </div>
            `);
        }

        function initSelect2() {
            if (typeof $ === 'undefined' || !$.fn.select2) {
                setTimeout(initSelect2, 100);
                return;
            }
            $('#products').select2({
                placeholder: 'Cari dan pilih produk...',
                allowClear: true,
                width: '100%',
                templateResult: formatProduct,
                ajax: {
                    url: ROUTE_PRODUK,
                    dataType: 'json',
                    delay: 250,
                    data: p => ({
                        search: p.term,
                        page: p.page || 1
                    }),
                    processResults(data, p) {
                        p.page = p.page || 1;
                        return {
                            results: data.data.map(i => ({
                                id: i.id,
                                text: i.name_product,
                                img_produk: i.img_produk,
                                harga_jual: i.harga_jual,
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            },
                        };
                    },
                    cache: true,
                },
            });
        }

        initSelect2();

        /* ── 4. QUILL EDITOR ── */
        function initQuill() {
            if (typeof Quill === 'undefined') {
                setTimeout(initQuill, 100);
                return;
            }
            const editorEl = document.getElementById('quill-description');
            const hiddenInput = document.getElementById('description');
            if (!editorEl) return;

            const quill = new Quill('#quill-description', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi promo di sini...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['link'],
                        ['clean'],
                    ],
                },
            });

            if (hiddenInput.value) quill.root.innerHTML = hiddenInput.value;
            quill.on('text-change', () => hiddenInput.value = quill.root.innerHTML);
        }

        initQuill();
    </script>
@endsection
