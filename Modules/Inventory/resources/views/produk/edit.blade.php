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

        {{-- Peringatan Error (Langkah 1 sebelumnya) --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <h6 class="alert-heading fw-bold mb-1">Gagal Menyimpan!</h6>
                <ul class="mb-0">
                    @foreach ($errors->getMessages() as $field => $messages)
                        <li><strong>Kolom ({{ $field }})</strong>: Data ini sudah digunakan / duplikat.</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- CARD 1: INFORMASI DASAR PRODUK --}}
        {{-- ============================================================ --}}
        <div class="card rounded-2 mb-4">
            <div class="card-header pt-3 pb-0">
                <h6 class="m-0 font-weight-bold">Informasi Utama Produk</h6>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    {{-- Nama --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name_product') is-invalid @enderror"
                            id="name_product" name="name_product" value="{{ old('name_product', $produk->name_product) }}"
                            required autofocus>
                        @error('name_product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="col-md-6">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('kategori') is-invalid @enderror" name="kategori"
                            id="kategori" data-placeholder="Pilih Kategori..." required>
                            <option value="" disabled>Pilih Kategori...</option>
                            @foreach ($kategoris as $parent)
                                @if ($parent->children->isEmpty())
                                    <option value="{{ $parent->id }}" @selected(old('kategori', $produk->category_id) == $parent->id)>{{ $parent->name }}
                                    </option>
                                @else
                                    <optgroup label="{{ $parent->name }}">
                                        @foreach ($parent->children as $child)
                                            <option value="{{ $child->id }}" @selected(old('kategori', $produk->category_id) == $child->id)>
                                                {{ $child->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Brand --}}
                    <div class="col-md-6">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('brand') is-invalid @enderror" name="brand"
                            data-placeholder="Pilih Brand..." required>
                            <option value="" disabled>Pilih Brand...</option>
                            @foreach ($brands as $item)
                                <option value="{{ $item->id }}" @selected(old('brand', $produk->brand_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Unit --}}
                    <div class="col-md-6">
                        <label class="form-label">Unit / Satuan <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('unit') is-invalid @enderror" name="unit"
                            data-placeholder="Pilih Unit..." required>
                            <option value="" disabled>Pilih Unit...</option>
                            @foreach ($units as $item)
                                <option value="{{ $item->id }}" @selected(old('unit', $produk->unit_id) == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Garansi --}}
                    <div class="col-md-6">
                        <label class="form-label">Garansi <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('garansi') is-invalid @enderror" name="garansi"
                            data-placeholder="Pilih Garansi...">
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

                    {{-- Spesifikasi Teknis --}}
                    <div class="col-md-6">
                        <label class="form-label">Spesifikasi Teknis</label>
                        <div id="specification-container"></div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btn-add-spec">
                            <i class="bx bx-plus"></i> Tambah Baris Spesifikasi
                        </button>
                    </div>

                    {{-- Deskripsi Pakai Quill --}}
                    <div class="col-md-12">
                        <label class="form-label">Deskripsi Lengkap</label>
                        <div class="quill-wrapper">
                            <div id="quill-description">{!! old('description', $produk->description) !!}</div>
                        </div>
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
        {{-- CARD 2: STOK, HARGA & KODE --}}
        {{-- ============================================================ --}}
        <div class="card mb-4 rounded-2">
            <div class="card-header pt-3 pb-0">
                <h6 class="m-0 font-weight-bold">Stok, Harga & Identitas</h6>
            </div>
            <div class="card-body pt-3">

                {{-- Toggle Jika Punya Varian --}}
                <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="toggle-variants" @checked($produk->variantTypes->count() > 0)>
                        <label class="form-check-label fw-bold" for="toggle-variants">Aktifkan Varian Produk (Warna,
                            Ukuran, dll)</label>
                    </div>
                    <small class="ms-auto">Centang ini jika produk memiliki variasi harga/stok.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number"
                                class="form-control base-price-input @error('harga_beli') is-invalid @enderror"
                                name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}" required>
                            @error('harga_beli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number"
                                class="form-control base-price-input @error('harga_jual') is-invalid @enderror"
                                name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" required>
                            @error('harga_jual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror"
                            name="stok_minimum" value="{{ old('stok_minimum', $produk->stok_minimum) }}" required>
                        @error('stok_minimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-3">

                    <div class="col-md-4">
                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                            name="sku" value="{{ old('sku', $produk->sku) }}" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barcode</label>
                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                            name="barcode" value="{{ old('barcode', $produk->barcode) }}">
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light @error('slug') is-invalid @enderror"
                            id="slug" name="slug" value="{{ old('slug', $produk->slug) }}" readonly required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pajak --}}
                    <div class="col-md-4">
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

                    <div class="col-md-12 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="wajib_seri" name="wajib_seri"
                                value="1" @checked(old('wajib_seri', $produk->wajib_seri))>
                            <label class="form-check-label fw-bold text-warning" for="wajib_seri">
                                Produk ini melacak Nomor Seri (Serial Number)
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
        {{-- CARD 3: VARIASI PRODUK --}}
        {{-- ============================================================ --}}
        <div class="card mb-4 rounded-2" id="variant-section"
            style="display:{{ $produk->variantTypes->count() ? 'block' : 'none' }}; border: 2px solid #696cff;">
            <div class="card-header pt-3 pb-0 bg-label-primary">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Variasi</h6>
            </div>
            <div class="card-body pt-3">
                <div class="mb-3">
                    <div id="variant-types-container">
                        {{-- Diisi oleh JS berdasarkan data existing --}}
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-variant-type">
                        <i class="bx bx-plus"></i> Tambah Tipe (Warna, RAM, dll)
                    </button>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-primary btn-sm w-100" id="generate-combinations">
                        <i class="bx bx-refresh"></i> Generate / Refresh Kombinasi
                    </button>
                    <small class="text-muted ms-2">Klik setelah mengubah tipe &amp; opsi.</small>
                </div>

                <div id="combinations-container" style="display:{{ $produk->variants->count() ? 'block' : 'none' }};">
                    <label class="form-label fw-bold">Detail per Kombinasi</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="combinations-table">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Varian</th>
                                    <th width="10%">Foto</th>
                                    <th width="15%">SKU <span class="text-danger">*</span></th>
                                    <th width="15%">Barcode</th>
                                    <th width="15%">Hrg Jual</th>
                                    <th width="15%">Hrg Beli</th>
                                </tr>
                            </thead>
                            <tbody id="combinations-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 4: GALERI FOTO (FILEPOND) --}}
        {{-- ============================================================ --}}
        <div class="card mb-4 rounded-2">
            <div class="card-header pt-3 pb-0">
                <h6 class="m-0 font-weight-bold">Galeri Foto Produk Utama</h6>
            </div>
            <div class="card-body pt-3">

                {{-- Foto yang sudah ada --}}
                @if ($produk->images->count())
                    <div class="mb-3">
                        <label class="form-label">Foto Saat Ini</label>
                        <p class="text-muted small">Klik × untuk menghapus foto. Foto pertama otomatis jadi thumbnail.</p>
                        <div class="d-flex flex-wrap gap-2" id="existing-gallery">
                            @foreach ($produk->images as $img)
                                <div class="position-relative existing-img-wrapper" data-id="{{ $img->id }}">
                                    <img src="{{ $img->path ? Storage::url($img->path) : asset('assets/img/produk.png') }}"
                                        alt="GambarProduk"
                                        style="width:100px; height:100px; object-fit:cover; border-radius:8px;">
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

        {{-- TOMBOL SUBMIT --}}
        <div class="d-flex justify-content-end mb-5 gap-2">
            <a href="{{ route('produk.index') }}" id="cancel-button" class="btn btn-secondary">Batal</a>
            <button id="submit-edit-produk" type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Simpan Perubahan
            </button>
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
        const storageBaseUrl = "{{ rtrim(Storage::disk('r2')->url(''), '/') }}";
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ============================================================
            // 1. AUTO SLUG dari Nama Produk
            // ============================================================
            document.getElementById('name_product').addEventListener('change', function() {
                fetch(`/produk/checkSlug?name_product=${this.value}`)
                    .then(r => r.json())
                    .then(d => document.getElementById('slug').value = d.slug);
            });

            // ============================================================
            // 2. INIT QUILL EDITOR
            // ============================================================
            const quillDesc = new Quill('#quill-description', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi produk yang menarik...',
            });
            const hiddenDesc = document.getElementById('description');
            quillDesc.on('text-change', () => hiddenDesc.value = quillDesc.root.innerHTML);
            if (hiddenDesc.value) quillDesc.root.innerHTML = hiddenDesc.value;

            // ============================================================
            // 3. SPESIFIKASI TEKNIS
            // ============================================================
            const specContainer = document.getElementById('specification-container');
            document.getElementById('btn-add-spec').addEventListener('click', () => addSpecRow());

            function addSpecRow(key = '', value = '') {
                const row = document.createElement('div');
                row.className = 'd-flex gap-2 mb-2 align-items-center spec-row';
                row.innerHTML = `
                    <input type="text" name="spec_keys[]" class="form-control" placeholder="Cth: Warna" value="${key}" required>
                    <input type="text" name="spec_values[]" class="form-control" placeholder="Cth: Merah Merona" value="${value}" required>
                    <button type="button" class="btn btn-icon btn-danger btn-sm remove-spec" title="Hapus">
                        <i class="bx bx-trash"></i>
                    </button>`;
                specContainer.appendChild(row);
                row.querySelector('.remove-spec').addEventListener('click', () => row.remove());
            }

            const existingSpecs = @js($produk->specification ? json_decode($produk->specification, true) : []);
            if (Array.isArray(existingSpecs) && existingSpecs.length > 0) {
                existingSpecs.forEach(spec => addSpecRow(spec.key, spec.value));
            } else {
                addSpecRow(); // Tambah 1 row kosong default
            }

            // ============================================================
            // 4. SERIAL NUMBER CHECKBOX
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
            // 5. HAPUS FOTO LAMA (existing gallery)
            // ============================================================
            document.querySelectorAll('.remove-existing-img').forEach(btn => {
                btn.addEventListener('click', function() {
                    const wrapper = this.closest('.existing-img-wrapper');
                    wrapper.querySelector('.existing-img-input').remove();
                    wrapper.remove();
                });
            });

            // ============================================================
            // 6. INIT FILEPOND
            // ============================================================
            FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType);

            const galleryInput = document.getElementById('gallery-pond');
            const hiddenGallery = document.getElementById('gallery-hidden-inputs');
            const saveBtn = document.getElementById('submit-edit-produk');
            let uploadingCount = 0;

            const galleryPond = FilePond.create(galleryInput, {
                allowMultiple: true,
                labelIdle: `Seret & Lepas gambar ke sini atau <span class="filepond--label-action">Browse</span>`,
                allowImagePreview: true,
                imagePreviewHeight: 160,
                maxFileSize: '2MB',
                allowFileSizeValidation: true,
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/jpg', 'image/svg'],
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
                    saveBtn.innerHTML = '<i class="bx bx-save me-1"></i> Simpan Perubahan';
                }
            }

            // ============================================================
            // 7. LOGIKA VARIAN vs HARGA DEFAULT
            // ============================================================
            const toggleVariants = document.getElementById('toggle-variants');
            const variantSection = document.getElementById('variant-section');
            const baseInputs = document.querySelectorAll('.base-price-input');

            toggleVariants.addEventListener('change', function() {
                if (this.checked) {
                    variantSection.style.display = 'block';
                    baseInputs.forEach(inp => {
                        inp.removeAttribute('required');
                        inp.parentElement.parentElement.style.opacity = '0.5';
                    });
                } else {
                    variantSection.style.display = 'none';
                    baseInputs.forEach(inp => {
                        inp.setAttribute('required', 'required');
                        inp.parentElement.parentElement.style.opacity = '1';
                    });
                }
            });

            // Jalankan sekali saat load jika varian sudah aktif
            if (toggleVariants.checked) {
                baseInputs.forEach(inp => {
                    inp.removeAttribute('required');
                    inp.parentElement.parentElement.style.opacity = '0.5';
                });
            }

            // ============================================================
            // 8. VARIASI – TIPE & OPSI
            // ============================================================
            const typesContainer = document.getElementById('variant-types-container');
            let typeCount = 0;

            function createTypeRow(existingName = '', existingOptions = []) {
                const idx = typeCount++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-2 position-relative bg-white';
                div.dataset.typeIndex = idx;
                div.innerHTML = `
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-type-btn"></button>
                    <div class="row g-2 align-items-start">
                        <div class="col-md-4">
                            <input type="text" class="form-control type-name-input" name="variant_types[${idx}][name]"
                                placeholder="Nama Tipe (Misal: Warna)" value="${existingName}" required>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control option-input" placeholder="Ketik opsi lalu Enter (Merah, Biru)" data-type-idx="${idx}">
                            <div class="option-tags mt-2 d-flex flex-wrap gap-1" data-type-idx="${idx}"></div>
                            <div class="option-hiddens" data-type-idx="${idx}"></div>
                        </div>
                    </div>`;
                typesContainer.appendChild(div);

                existingOptions.forEach(opt => addOptionTag(idx, opt, div));

                const optionInput = div.querySelector('.option-input');
                optionInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        addOptionTag(idx, this.value.trim().replace(/,$/, ''), div);
                        this.value = '';
                    }
                });
                optionInput.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        addOptionTag(idx, this.value.trim(), div);
                        this.value = '';
                    }
                });
                div.querySelector('.remove-type-btn').addEventListener('click', () => div.remove());
            }

            function addOptionTag(typeIdx, value, containerDiv) {
                if (!value) return;
                const tagsEl = containerDiv ?
                    containerDiv.querySelector('.option-tags') :
                    typesContainer.querySelector(`.option-tags[data-type-idx="${typeIdx}"]`);
                const hiddenEl = containerDiv ?
                    containerDiv.querySelector('.option-hiddens') :
                    typesContainer.querySelector(`.option-hiddens[data-type-idx="${typeIdx}"]`);

                const tag = document.createElement('span');
                tag.className = 'badge bg-secondary d-inline-flex align-items-center gap-1';
                tag.innerHTML =
                    `${value} <i class="bx bx-x text-white" style="cursor:pointer"></i>`;
                tag.querySelector('i').addEventListener('click', () => {
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

            // ============================================================
            // 8. Load Tipe & Opsi Existing 
            // ============================================================
            const existingTypes = @json(
                $produk->variantTypes->map(fn($t) => [
                        'name' => $t->name,
                        'options' => $t->options->pluck('value')->toArray(),
                    ]));
            existingTypes.forEach(type => createTypeRow(type.name, type.options));

            // ============================================================
            // 9. VARIASI – GENERATE & RENDER KOMBINASI (PENCOCOKAN ABSOLUT)
            // ============================================================

            const existingVariants = {!! $produk->variants->map(function ($v) {
                    // Ambil opsi, urutkan A-Z, lalu gabungkan dengan |
                    $opts = $v->options->pluck('value')->toArray();
                    sort($opts);
            
                    return [
                        'id' => $v->id,
                        'sku' => $v->sku,
                        'barcode' => $v->barcode,
                        'harga_jual' => $v->harga_jual,
                        'harga_beli' => $v->harga_beli,
                        'img_variant' => $v->img_variant,
                        'match_key' => implode('|', $opts), // Contoh output: "Biru|L"
                    ];
                })->values()->toJson() !!};

            document.getElementById('generate-combinations').addEventListener('click', generateCombinations);

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
                const baseSku = document.getElementById('sku').value || 'SKU';

                combinations.forEach((combo, i) => {
                    const label = combo.map(c => c.value).join(' / '); // Label tampilan (Merah / L)

                    // BUAT MATCH KEY DI JAVASCRIPT
                    const currentVals = combo.map(c => c.value);
                    currentVals.sort(); // Urutkan A-Z
                    const formMatchKey = currentVals.join('|'); // Output: "Biru|L"

                    // CARI MATCH KEY YANG SAMA PERSIS DI DATA DATABASE
                    const existing = existingVariants.find(v => v.match_key === formMatchKey) || {};

                    const row = document.createElement('tr');
                    row.innerHTML =
                        `
                        <td>
                            <span class="badge bg-label-info">${label}</span>
                            ${existing.id ? `<input type="hidden" name="variants[${i}][id]" value="${existing.id}">` : ''}
                            ${combo.map(c => `<input type="hidden" name="variants[${i}][option_ids][]" data-type="${c.type}" value="${c.value}">`).join('')}
                        </td>
                        <td class="text-center">
                            <input type="file" class="variant-img-input" id="v-img-${i}" accept="image/*" style="display:none;">
                            <label for="v-img-${i}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-camera"></i></label>
                            ${existing.img_variant
                                ? `<img src="${storageBaseUrl}/${existing.img_variant}" id="v-preview-${i}" style="max-height:48px;border-radius:4px;" class="ms-1">`
                                : `<img id="v-preview-${i}" src="" style="display:none;max-height:48px;border-radius:4px;" class="ms-1">`}
                            <input type="hidden" name="variants[${i}][img_variant]" id="v-path-${i}" value="${existing.img_variant ?? ''}">
                        </td>
                        <td><input type="text" class="form-control form-control-sm" name="variants[${i}][sku]" value="${existing.sku ?? baseSku + '-' + label.replace(/ \/ /g, '-')}" required></td>
                        <td><input type="text" class="form-control form-control-sm" name="variants[${i}][barcode]" value="${existing.barcode ?? ''}"></td>
                        <td>
                            <div class="input-group input-group-sm"><span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="variants[${i}][harga_jual]" value="${existing.harga_jual ?? ''}" required></div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm"><span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="variants[${i}][harga_beli]" value="${existing.harga_beli ?? ''}" required></div>
                        </td>
                        `;
                    tbody.appendChild(row);

                    row.querySelector(`#v-img-${i}`).addEventListener('change', function() {
                        if (!this.files[0]) return;

                        const fileInput = this;
                        const label = row.querySelector(`label[for="v-img-${i}"]`);
                        const preview = document.getElementById(`v-preview-${i}`);
                        const pathInput = document.getElementById(`v-path-${i}`);

                        // Tampilkan loading di label tombol kamera
                        label.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;
                        label.disabled = true;

                        const fd = new FormData();
                        fd.append('file', fileInput.files[0]);

                        fetch('/produk/upload', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: fd
                            })
                            .then(r => {
                                if (!r.ok) return r.json().then(err => {
                                    throw new Error(err.error || 'Upload gagal');
                                });
                                return r.text();
                            })
                            .then(path => {
                                // Simpan path tmp ke hidden input agar bisa dikirim saat form submit
                                pathInput.value = path.trim();
                                // Update preview menggunakan storageBaseUrl (R2 URL)
                                // path = 'tmp/filename.jpg', storageBaseUrl sudah tanpa trailing slash
                                preview.src = `${storageBaseUrl}/${path.trim()}`;
                                preview.style.display = 'inline-block';
                            })
                            .catch(err => {
                                alert('Gagal upload gambar varian: ' + err.message);
                            })
                            .finally(() => {
                                // Kembalikan ikon kamera
                                label.innerHTML = `<i class="bx bx-camera"></i>`;
                                label.disabled = false;
                            });
                    });

                });

                document.getElementById('combinations-container').style.display = 'block';
            }

            // ============================================================
            // 10. SUBMIT – SYNC QUILL
            // ============================================================
            document.getElementById('editProductForm').addEventListener('submit', function() {
                hiddenDesc.value = quillDesc.root.innerHTML;
            });

            // ============================================================
            // 11. CANCEL – Revert foto baru yang belum disimpan
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
@endsection
