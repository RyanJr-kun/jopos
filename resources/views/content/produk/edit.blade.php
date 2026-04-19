@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Produk - ' . $produk->name_product)

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')

    <form id="editProductForm" method="POST" action="{{ route('produk.update', $produk->slug) }}"
        enctype="multipart/form-data">
        @method('PUT')
        @csrf

        {{-- ============================================================ --}}
        {{-- CARD 1: INFORMASI PRODUK --}}
        {{-- ============================================================ --}}
        <div class="card rounded-2">
            <div class="card-header pt-3 pb-0">
                <h6>Informasi Produk</h6>
            </div>
            <div class="card-body px-4 pt-0">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name_product') is-invalid @enderror"
                            id="name_product" name="name_product" value="{{ old('name_product', $produk->name_product) }}"
                            required autofocus>
                        @error('name_product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                            name="barcode" value="{{ old('barcode', $produk->barcode) }}">
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                            name="slug" value="{{ old('slug', $produk->slug) }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                            name="sku" value="{{ old('sku', $produk->sku) }}" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori bertingkat --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('kategori') is-invalid @enderror" name="kategori"
                            data-placeholder="Pilih Kategori" required>
                            <option value="" disabled>Pilih Kategori</option>
                            @foreach ($kategoris as $parent)
                                @if ($parent->children->isEmpty())
                                    <option value="{{ $parent->id }}" @selected(old('kategori', $produk->category_id) == $parent->id)>
                                        {{ $parent->name }}
                                    </option>
                                @else
                                    <optgroup label="{{ $parent->name }}">
                                        @foreach ($parent->children as $child)
                                            <option value="{{ $child->id }}" @selected(old('kategori', $produk->category_id) == $child->id)>
                                                {{ $child->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('brand') is-invalid @enderror" name="brand"
                            data-placeholder="Pilih Brand" required>
                            <option value="" disabled>Pilih Brand</option>
                            @foreach ($brands as $item)
                                <option value="{{ $item->id }}" @selected(old('brand', $produk->brand_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('unit') is-invalid @enderror" name="unit"
                            data-placeholder="Pilih Unit" required>
                            <option value="" disabled>Pilih Unit</option>
                            @foreach ($units as $item)
                                <option value="{{ $item->id }}" @selected(old('unit', $produk->unit_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Garansi</label>
                        <select class="form-select select2 @error('garansi') is-invalid @enderror" name="garansi"
                            data-placeholder="Pilih Garansi">
                            <option value="">Tidak Ada Garansi</option>
                            @foreach ($warranties as $item)
                                <option value="{{ $item->id }}" @selected(old('garansi', $produk->warrantie_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('garansi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Spesifikasi (BARU) --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Spesifikasi Teknis</label>
                        <div id="specification-container">
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btn-add-spec">
                            <i class="bx bx-plus"></i> Tambah Baris Spesifikasi
                        </button>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Deskripsi</label>
                        <div id="quill-description" style="min-height:120px;">{!! old('description', $produk->description) !!}</div>
                        <input type="hidden" name="description" id="description"
                            value="{{ old('description', $produk->description) }}">
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 2: STOK & HARGA --}}
        {{-- ============================================================ --}}
        <div class="card mt-3 rounded-2">
            <div class="card-header pt-3 pb-0">
                <h6>Stok &amp; Harga (Produk Utama)</h6>
            </div>
            <div class="card-body px-4 pt-0">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('qty') is-invalid @enderror" name="qty"
                            value="{{ old('qty', $produk->qty) }}" required>
                        @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_jual') is-invalid @enderror"
                                name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" required>
                            @error('harga_jual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_beli') is-invalid @enderror"
                                name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}" required>
                            @error('harga_beli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror"
                            name="stok_minimum" value="{{ old('stok_minimum', $produk->stok_minimum) }}" required>
                        @error('stok_minimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Pajak</label>
                        <select class="form-select select2 @error('pajak') is-invalid @enderror" name="pajak"
                            data-placeholder="Tidak Ada Pajak">
                            <option value="">Tidak Ada Pajak</option>
                            @foreach ($pajak as $item)
                                <option value="{{ $item->id }}" @selected(old('pajak', $produk->taxe_id) == $item->id)>{{ $item->name_taxe }}
                                </option>
                            @endforeach
                        </select>
                        @error('pajak')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="wajib_seri" name="wajib_seri"
                                value="1" @checked(old('wajib_seri', $produk->wajib_seri))>
                            <label class="form-check-label fw-bold" for="wajib_seri">
                                Produk ini memiliki <u class="text-warning">Nomor Seri</u>
                            </label>
                        </div>
                        <small id="serial-info" class="text-muted"
                            style="display:{{ $produk->wajib_seri ? 'block' : 'none' }};opacity:{{ $produk->wajib_seri ? '1' : '0' }};transition:opacity .3s">
                            Jika dicentang, Anda harus memasukkan nomor seri saat pembelian.
                        </small>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 3: GALERI FOTO --}}
        {{-- ============================================================ --}}
        <div class="card mt-3 rounded-2">
            <div class="card-header pt-3 pb-0">
                <h6>Galeri Foto Produk</h6>
            </div>
            <div class="card-body">

                {{-- Foto yang sudah ada --}}
                @if ($produk->images->count())
                    <div class="mb-3">
                        <label class="form-label">Foto yang ada sekarang</label>
                        <p class="text-muted small">Klik X untuk menghapus foto. Foto pertama otomatis jadi thumbnail.</p>
                        <div class="d-flex flex-wrap gap-2" id="existing-gallery">
                            @foreach ($produk->images as $img)
                                <div class="position-relative existing-img-wrapper" data-id="{{ $img->id }}">
                                    <img src="{{ asset('storage/' . $img->path) }}" alt=""
                                        style="width:100px;height:100px;object-fit:cover;border-radius:8px;
                            {{ $img->is_primary ? 'border:3px solid #696cff;' : '' }}">
                                    @if ($img->is_primary)
                                        <span class="badge bg-primary position-absolute bottom-0 start-0 m-1"
                                            style="font-size:9px;">Utama</span>
                                    @endif
                                    <button type="button"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-existing-img"
                                        style="width:22px;height:22px;padding:0;line-height:1;border-radius:50%;"
                                        data-id="{{ $img->id }}">×</button>
                                    <input type="hidden" name="existing_images[]" value="{{ $img->id }}"
                                        class="existing-img-input">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Upload foto baru --}}
                <label class="form-label">Tambah Foto Baru</label>
                <input type="file" id="gallery-pond" name="gallery_files" multiple accept="image/*">
                <div id="gallery-hidden-inputs"></div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 4: VARIASI PRODUK --}}
        {{-- ============================================================ --}}
        <div class="card mt-3 rounded-2">
            <div class="card-header pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Variasi Produk</h6>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="toggle-variants" @checked($produk->variantTypes->count() > 0)>
                    <label class="form-check-label" for="toggle-variants">Produk ini punya variasi</label>
                </div>
            </div>
            <div class="card-body" id="variant-section"
                style="display:{{ $produk->variantTypes->count() ? 'block' : 'none' }};">

                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Variasi</label>
                    <div id="variant-types-container">
                        {{-- Isi dari JS berdasarkan data existing --}}
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-variant-type">
                        <i class="bx bx-plus"></i> Tambah Tipe Variasi
                    </button>
                </div>

                <hr>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info btn-sm" id="generate-combinations">
                        <i class="bx bx-refresh"></i> Generate / Refresh Kombinasi
                    </button>
                    <small class="text-muted ms-2">Klik setelah mengubah tipe &amp; opsi.</small>
                </div>

                <div id="combinations-container" style="display:{{ $produk->variants->count() ? 'block' : 'none' }};">
                    <label class="form-label fw-bold">Detail per Kombinasi</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="combinations-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Variasi</th>
                                    <th>Foto</th>
                                    <th>SKU <span class="text-danger">*</span></th>
                                    <th>Barcode</th>
                                    <th>Harga Jual <span class="text-danger">*</span></th>
                                    <th>Harga Beli <span class="text-danger">*</span></th>
                                    <th>Stok <span class="text-danger">*</span></th>
                                </tr>
                            </thead>
                            <tbody id="combinations-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="d-flex justify-content-end mt-3 mb-4 me-4 gap-2">
            <a href="{{ route('produk.index') }}" id="cancel-button" class="btn btn-danger">Batalkan</a>
            <button id="submit-edit-produk" type="submit" class="btn btn-outline-info">Simpan Perubahan</button>
        </div>

    </form>
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ============================================================
            // SLUG
            // ============================================================
            document.getElementById('name_product').addEventListener('change', function() {
                fetch(`/produk/checkSlug?name_product=${this.value}`)
                    .then(r => r.json())
                    .then(d => document.getElementById('slug').value = d.slug);
            });

            // ============================================================
            // QUILL
            // ============================================================
            const quillDesc = new Quill('#quill-description', {
                theme: 'snow',
                placeholder: 'Deskripsi produk...'
            });
            const hiddenDesc = document.getElementById('description');
            quillDesc.on('text-change', () => hiddenDesc.value = quillDesc.root.innerHTML);
            if (hiddenDesc.value) quillDesc.root.innerHTML = hiddenDesc.value;

            const specContainer = document.getElementById('specification-container');
            document.getElementById('btn-add-spec').addEventListener('click', addSpecRow);

            function addSpecRow(key = '', value = '') {
                const row = document.createElement('div');
                row.className = 'd-flex gap-2 mb-2 align-items-center spec-row';
                row.innerHTML = `
        <input type="text" name="spec_keys[]" class="form-control" placeholder="Cth: Warna" value="${key}" required>
        <input type="text" name="spec_values[]" class="form-control" placeholder="Cth: Merah Merona" value="${value}" required>
        <button type="button" class="btn btn-icon btn-danger btn-sm remove-spec" title="Hapus">
            <i class="bx bx-trash"></i>
        </button>
    `;
                specContainer.appendChild(row);

                // Fungsi hapus
                row.querySelector('.remove-spec').addEventListener('click', function() {
                    row.remove();
                });
            }

            // KHUSUS EDIT.BLADE.PHP: Untuk me-load data spesifikasi yang sudah ada
            // Parse JSON dari database jika ada
            const existingSpecs = {!! $produk->specification ? $produk->specification : '[]' !!};
            if (existingSpecs.length > 0) {
                existingSpecs.forEach(spec => addSpecRow(spec.key, spec.value));
            } else {
                addSpecRow(); // Tampilkan 1 baris kosong sebagai default
            }

            // ============================================================
            // SERIAL CHECKBOX
            // ============================================================
            const serialCb = document.getElementById('wajib_seri');
            const serialInfo = document.getElementById('serial-info');

            function toggleSerial() {
                if (serialCb.checked) {
                    serialInfo.style.display = 'block';
                    setTimeout(() => serialInfo.style.opacity = 1, 10);
                } else {
                    serialInfo.style.opacity = 0;
                    setTimeout(() => serialInfo.style.display = 'none', 300);
                }
            }
            serialCb.addEventListener('change', toggleSerial);

            // ============================================================
            // EXISTING GALLERY – HAPUS FOTO LAMA
            // ============================================================
            document.querySelectorAll('.remove-existing-img').forEach(btn => {
                btn.addEventListener('click', function() {
                    const wrapper = this.closest('.existing-img-wrapper');
                    const id = this.dataset.id;
                    // Hapus hidden input sehingga tidak dikirim ke server
                    wrapper.querySelector('.existing-img-input').remove();
                    wrapper.remove();
                });
            });

            // ============================================================
            // FILEPOND – GALERI MULTI (FOTO BARU)
            // ============================================================
            FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType);

            const galleryInput = document.getElementById('gallery-pond');
            const hiddenGallery = document.getElementById('gallery-hidden-inputs');
            const saveBtn = document.getElementById('submit-edit-produk');
            let uploadingCount = 0;

            const galleryPond = FilePond.create(galleryInput, {
                allowMultiple: true,
                labelIdle: `Seret &amp; Lepas atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                imagePreviewHeight: 160,
                maxFileSize: '2MB',
                allowFileSizeValidation: true,
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
                server: {
                    process: {
                        url: '/produk/upload',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                    revert: {
                        url: '/produk/revert',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                },
            });

            galleryPond.on('addfile', () => {
                uploadingCount++;
                updateSaveBtn();
            });
            galleryPond.on('processfile', (error, fileItem) => {
                if (error) return;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'gallery[]';
                input.value = fileItem.serverId;
                input.dataset.fileId = fileItem.id;
                hiddenGallery.appendChild(input);
                uploadingCount = Math.max(0, uploadingCount - 1);
                updateSaveBtn();
            });
            galleryPond.on('removefile', (error, fileItem) => {
                const input = hiddenGallery.querySelector(`[data-file-id="${fileItem.id}"]`);
                if (input) input.remove();
                updateSaveBtn();
            });

            function updateSaveBtn() {
                if (uploadingCount > 0) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengunggah...`;
                } else {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Simpan Perubahan';
                }
            }

            // ============================================================
            // VARIASI – TOGGLE SECTION
            // ============================================================
            const toggleVariants = document.getElementById('toggle-variants');
            const variantSection = document.getElementById('variant-section');
            toggleVariants.addEventListener('change', function() {
                variantSection.style.display = this.checked ? 'block' : 'none';
            });

            // ============================================================
            // VARIASI – TIPE & OPSI
            // ============================================================
            const typesContainer = document.getElementById('variant-types-container');
            let typeCount = 0;

            function createTypeRow(existingName = '', existingOptions = []) {
                const idx = typeCount++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 position-relative';
                div.dataset.typeIndex = idx;
                div.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-type-btn"></button>
            <div class="row g-2 align-items-start">
                <div class="col-md-3">
                    <label class="form-label">Nama Tipe <span class="text-danger">*</span></label>
                    <input type="text" class="form-control type-name-input" name="variant_types[${idx}][name]"
                        placeholder="cth: Warna" value="${existingName}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Opsi</label>
                    <input type="text" class="form-control option-input" placeholder="Ketik lalu Enter" data-type-idx="${idx}">
                    <div class="option-tags mt-2 d-flex flex-wrap gap-1" data-type-idx="${idx}"></div>
                    <div class="option-hiddens" data-type-idx="${idx}"></div>
                </div>
            </div>`;
                typesContainer.appendChild(div);

                // Isi opsi yang sudah ada
                existingOptions.forEach(opt => addOptionTag(idx, opt));

                const optionInput = div.querySelector('.option-input');
                optionInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        addOptionTag(idx, this.value.trim().replace(/,$/, ''));
                        this.value = '';
                    }
                });
                optionInput.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        addOptionTag(idx, this.value.trim());
                        this.value = '';
                    }
                });
                div.querySelector('.remove-type-btn').addEventListener('click', () => div.remove());
            }

            function addOptionTag(typeIdx, value) {
                if (!value) return;
                const tagsEl = typesContainer.querySelector(`.option-tags[data-type-idx="${typeIdx}"]`);
                const hiddenEl = typesContainer.querySelector(`.option-hiddens[data-type-idx="${typeIdx}"]`);

                const tag = document.createElement('span');
                tag.className = 'badge bg-label-primary d-inline-flex align-items-center gap-1 px-2 py-1';
                tag.innerHTML =
                    `${value} <button type="button" class="btn-close btn-close-sm" aria-label="Hapus"></button>`;
                tag.querySelector('.btn-close').addEventListener('click', () => {
                    tag.remove();
                    Array.from(hiddenEl.children).forEach(inp => {
                        if (inp.value === value) inp.remove();
                    });
                });
                tagsEl.appendChild(tag);

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `variant_types[${typeIdx}][options][]`;
                hidden.value = value;
                hiddenEl.appendChild(hidden);
            }

            document.getElementById('add-variant-type').addEventListener('click', () => createTypeRow());

            // ---- Load existing variant types dari server ----
            const existingTypes = @json(
                $produk->variantTypes->map(fn($t) => [
                        'name' => $t->name,
                        'options' => $t->options->pluck('value')->toArray(),
                    ]));

            existingTypes.forEach(type => createTypeRow(type.name, type.options));

            // ============================================================
            // VARIASI – GENERATE KOMBINASI
            // ============================================================
            const existingVariants = {!! $produk->variants->map(
                    fn($v) => [
                        'id' => $v->id,
                        'sku' => $v->sku,
                        'barcode' => $v->barcode,
                        'harga_jual' => $v->harga_jual,
                        'harga_beli' => $v->harga_beli,
                        'qty' => $v->qty,
                        'img_variant' => $v->img_variant,
                        'label' => $v->options->pluck('value')->join(' / '),
                    ],
                )->toJson() !!};

            document.getElementById('generate-combinations').addEventListener('click', generateCombinations);

            // Auto-generate jika ada variasi existing
            if (existingVariants.length > 0) generateCombinations();

            function generateCombinations() {
                const types = [];
                typesContainer.querySelectorAll('[data-type-index]').forEach(typeDiv => {
                    const idx = typeDiv.dataset.typeIndex;
                    const name = typeDiv.querySelector('.type-name-input').value.trim();
                    const options = Array.from(typeDiv.querySelectorAll(
                        `.option-hiddens[data-type-idx="${idx}"] input`)).map(i => i.value);
                    if (name && options.length) types.push({
                        name,
                        options
                    });
                });

                if (!types.length) {
                    alert('Tambahkan minimal 1 tipe variasi dengan opsi.');
                    return;
                }

                const combinations = types.reduce((acc, type) => {
                    if (!acc.length) return type.options.map(o => [{
                        type: type.name,
                        value: o
                    }]);
                    return acc.flatMap(combo => type.options.map(o => [...combo, {
                        type: type.name,
                        value: o
                    }]));
                }, []);

                renderCombinations(combinations);
            }

            function renderCombinations(combinations) {
                const tbody = document.getElementById('combinations-tbody');
                tbody.innerHTML = '';

                combinations.forEach((combo, i) => {
                    const label = combo.map(c => c.value).join(' / ');
                    // Cari data existing berdasarkan label
                    const existing = existingVariants.find(v => v.label === label) || {};

                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>
                    <strong>${label}</strong>
                    ${existing.id ? `<input type="hidden" name="variants[${i}][id]" value="${existing.id}">` : ''}
                </td>
                <td>
                    <input type="file" class="variant-img-input" id="edit-variant-img-${i}" accept="image/*" style="display:none;">
                    <label for="edit-variant-img-${i}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-image-add"></i>
                    </label>
                    ${existing.img_variant
                        ? `<img src="/storage/${existing.img_variant}" alt="" id="edit-variant-img-preview-${i}"
                                                                  style="max-height:48px;border-radius:4px;" class="ms-1">`
                        : `<img id="edit-variant-img-preview-${i}" src="" alt="" style="max-height:48px;display:none;border-radius:4px;" class="ms-1">`
                    }
                    <input type="hidden" name="variants[${i}][img_variant]" id="edit-variant-img-path-${i}" value="${existing.img_variant ?? ''}">
                </td>
                <td><input type="text" class="form-control form-control-sm" name="variants[${i}][sku]"
                    value="${existing.sku ?? ''}" required></td>
                <td><input type="text" class="form-control form-control-sm" name="variants[${i}][barcode]"
                    value="${existing.barcode ?? ''}"></td>
                <td>
                    <div class="input-group input-group-sm"><span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" name="variants[${i}][harga_jual]"
                        value="${existing.harga_jual ?? ''}" required></div>
                </td>
                <td>
                    <div class="input-group input-group-sm"><span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" name="variants[${i}][harga_beli]"
                        value="${existing.harga_beli ?? ''}" required></div>
                </td>
                <td><input type="number" class="form-control form-control-sm" name="variants[${i}][qty]"
                    value="${existing.qty ?? 0}" required></td>`;
                    tbody.appendChild(row);

                    // Upload foto variant
                    const imgInput = row.querySelector(`#edit-variant-img-${i}`);
                    imgInput.addEventListener('change', function() {
                        if (!this.files[0]) return;
                        const fd = new FormData();
                        fd.append('file', this.files[0]);
                        fetch('/produk/upload', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: fd
                            })
                            .then(r => r.text()).then(path => {
                                document.getElementById(`edit-variant-img-path-${i}`).value =
                                    path;
                                const preview = document.getElementById(
                                    `edit-variant-img-preview-${i}`);
                                preview.src = `/storage/${path}`;
                                preview.style.display = 'inline-block';
                            });
                    });
                });

                document.getElementById('combinations-container').style.display = 'block';
            }

            // ============================================================
            // SUBMIT – SYNC QUILL
            // ============================================================
            document.getElementById('editProductForm').addEventListener('submit', function() {
                hiddenDesc.value = quillDesc.root.innerHTML;
                hiddenSpec.value = quillSpec.root.innerHTML;
            });

            // ============================================================
            // CANCEL – Revert foto baru yang belum disimpan
            // ============================================================
            document.getElementById('cancel-button').addEventListener('click', function(e) {
                e.preventDefault();
                const newFiles = galleryPond.getFiles().filter(f =>
                    f.origin === FilePond.FileOrigin.INPUT && f.serverId
                );
                const reverts = newFiles.map(f =>
                    fetch('/produk/revert', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: f.serverId
                    })
                );
                Promise.allSettled(reverts).finally(() => window.location.href = this.href);
            });

        });
    </script>
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length >
                            0,
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
@endsection
