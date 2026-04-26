@extends('layouts/contentNavbarLayout')

@section('title', 'Buat Promotion Baru')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid p-3">
        <div class="card rounded-2">
            <div class="card-header pb-0 px-3 pt-2 mb-3">
                <h6 class="mb-0">Buat Promotion Baru</h6>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('promo.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger text-white mt-3" role="alert">
                            <strong class="font-weight-bold">Oops! Terjadi kesalahan:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ================================================================
                         FORM FIELDS (merged dari _form.blade.php)
                         ================================================================ --}}
                    <div class="row g-3">

                        {{-- Nama Promotion --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Nama Promotion <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kode Promotion --}}
                        <div class="col-md-6">
                            <label for="code" class="form-label">Kode Promotion (Opsional)</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code') }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipe Diskon --}}
                        <div class="col-md-6">
                            <label for="type" class="form-label">
                                Tipe Diskon <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="percentage" @selected(old('type') == 'percentage')>
                                    Persentase (%)
                                </option>
                                <option value="fixed" @selected(old('type') == 'fixed')>
                                    Jumlah Tetap (Rp)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nilai Diskon --}}
                        <div class="col-md-6">
                            <label for="nilai_diskon" class="form-label">
                                Nilai Diskon <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" id="nilai-diskon-addon">%</span>
                                <input type="number" step="0.01"
                                    class="form-control @error('nilai_diskon') is-invalid @enderror" id="nilai_diskon"
                                    name="nilai_diskon" value="{{ old('nilai_diskon') }}" required min="0">
                                @error('nilai_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted" id="nilai-diskon-hint">
                                Masukkan nilai persentase (0–100).
                            </small>
                        </div>

                        {{-- Minimum Purchase --}}
                        <div class="col-md-6">
                            <label for="min_pembelian" class="form-label">Minimum Purchase (Rp)</label>
                            <input type="number" step="0.01"
                                class="form-control @error('min_pembelian') is-invalid @enderror" id="min_pembelian"
                                name="min_pembelian" value="{{ old('min_pembelian', 0) }}" min="0">
                            @error('min_pembelian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Maksimal Diskon (hanya relevan untuk persentase) --}}
                        <div class="col-md-6" id="max-diskon-wrapper">
                            <label for="max_diskon" class="form-label">
                                Maksimal Diskon (Rp)
                                <span class="text-muted fw-normal">— untuk tipe persentase</span>
                            </label>
                            <input type="number" step="0.01"
                                class="form-control @error('max_diskon') is-invalid @enderror" id="max_diskon"
                                name="max_diskon" value="{{ old('max_diskon') }}" min="0">
                            @error('max_diskon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Berakhir --}}
                        <div class="col-md-6">
                            <label for="tanggal_berakhir" class="form-label">
                                Tanggal Berakhir <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                class="form-control @error('tanggal_berakhir') is-invalid @enderror" id="tanggal_berakhir"
                                name="tanggal_berakhir"
                                value="{{ old('tanggal_berakhir', now()->addMonth()->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description (Quill) --}}
                        <div class="col-12 mb-12">
                            <label for="quill-description" class="form-label">
                                Description (Opsional)
                            </label>
                            <div id="quill-description" style="min-height: 120px;">
                                {!! old('description', '') !!}
                            </div>
                            <input type="hidden" name="description" id="description"
                                value="{{ old('description', '') }}">
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Checkbox: Berlaku untuk Semua Produk --}}
                        <div class="col-12 form-check form-switch ms-2 mt-12 ">
                            <input class="form-check-input" type="checkbox" id="is_all_products" name="is_all_products"
                                value="1" @checked(old('is_all_products', false))>
                            <label class="form-check-label" for="is_all_products">
                                Berlaku untuk <strong>semua produk</strong>
                            </label>
                        </div>

                        {{-- Pilih Produk (Select2 via NPM global) --}}
                        <div class="col-12" id="products-wrapper">
                            <label for="products" class="form-label">
                                Berlaku untuk Produk Tertentu (Opsional)
                            </label>
                            <select class="select2 form-select" id="products" name="products[]" multiple>
                                {{-- Opsi pre-populated untuk old() value saat validasi gagal --}}
                                @if (old('products'))
                                    @foreach (App\Models\Product::whereIn('id', old('products'))->get() as $produk)
                                        <option value="{{ $produk->id }}" selected>
                                            {{ $produk->name_product }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text text-muted">
                                Pilih satu atau lebih produk. Field ini diabaikan jika
                                <em>Berlaku untuk semua produk</em> dicentang.
                            </small>
                            @error('products')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('products.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Aktif --}}
                        <div class="col-12 form-check form-switch ms-2 mt-1">
                            <input class="form-check-input" type="checkbox" id="status" name="status"
                                value="1" @checked(old('status', true))>
                            <label class="form-check-label" for="status">Status Aktif</label>
                        </div>

                    </div>
                    {{-- END FORM FIELDS --}}

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-outline-info me-2">
                            Simpan Promotion
                        </button>
                        <a href="{{ route('promo.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script type="module">
        const ROUTE_PRODUK = "{{ route('get-data.produk') }}";
        const ASSET_DEFAULT = "{{ asset('assets/img/produk.png') }}";
        const ASSET_STORAGE = "{{ asset('storage') }}";

        /* ================================================================
           1. TOGGLE TYPE UI
           Sembunyikan/tampilkan max_diskon, ubah addon & hint nilai_diskon
        ================================================================ */
        function toggleTypeUI() {
            const type = document.getElementById('type').value;
            const isPct = type === 'percentage';
            const maxWrapper = document.getElementById('max-diskon-wrapper');
            const addon = document.getElementById('nilai-diskon-addon');
            const input = document.getElementById('nilai_diskon');
            const hint = document.getElementById('nilai-diskon-hint');

            maxWrapper.style.display = isPct ? '' : 'none';

            if (isPct) {
                addon.textContent = '%';
                input.setAttribute('max', '100');
                hint.textContent = 'Masukkan nilai persentase (0–100).';
            } else {
                addon.textContent = 'Rp';
                input.removeAttribute('max');
                hint.textContent = 'Masukkan jumlah tetap dalam Rupiah.';
            }
        }

        document.getElementById('type').addEventListener('change', toggleTypeUI);
        toggleTypeUI();

        /* ================================================================
           2. TOGGLE PRODUCTS WRAPPER
           Sembunyikan dropdown produk jika "semua produk" dicentang
        ================================================================ */
        function toggleProductsWrapper() {
            const isAll = document.getElementById('is_all_products').checked;
            const wrapper = document.getElementById('products-wrapper');
            const productsSelect = document.getElementById('products');

            wrapper.style.display = isAll ? 'none' : '';

            // Kosongkan pilihan agar tidak ikut tersubmit
            if (isAll && productsSelect) {
                Array.from(productsSelect.options).forEach(opt => opt.selected = false);
                // Beritahu Select2 supaya UI-nya juga update
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(productsSelect).trigger('change');
                }
            }
        }

        document.getElementById('is_all_products').addEventListener('change', toggleProductsWrapper);
        toggleProductsWrapper();

        /* ================================================================
           3. SELECT2 — pakai pola retry agar tidak race dengan bundle
        ================================================================ */
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

            // Select2 templateResult butuh jQuery element atau plain string
            return $(`
                <div class="d-flex align-items-center gap-2">
                    <img src="${imageUrl}" class="rounded-2"
                         style="width:36px;height:36px;object-fit:cover;"
                         onerror="this.src='${ASSET_DEFAULT}'">
                    <div>
                        <div class="fw-semibold" style="font-size:.875rem;">${produk.text}</div>
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
                theme: 'bootstrap-5',
                placeholder: 'Cari dan pilih produk...',
                allowClear: true,
                width: '100%',
                templateResult: formatProduct,
                ajax: {
                    url: ROUTE_PRODUK,
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
                                text: item.name_product,
                                img_produk: item.img_produk,
                                harga_jual: item.harga_jual,
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

        /* ================================================================
           4. QUILL — pakai pola retry agar tidak race dengan CDN
        ================================================================ */
        function initQuill() {
            if (typeof Quill === 'undefined') {
                setTimeout(initQuill, 100);
                return;
            }

            const editorEl = document.getElementById('quill-description');
            if (!editorEl) return;

            const hiddenInput = document.getElementById('description');

            const quill = new Quill('#quill-description', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi promo di sini...',
            });

            if (hiddenInput.value) {
                quill.root.innerHTML = hiddenInput.value;
            }

            quill.on('text-change', () => {
                hiddenInput.value = quill.root.innerHTML;
            });
        }

        initQuill();
    </script>
@endsection
