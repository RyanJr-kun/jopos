@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Product')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')

    <form id="addform" method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card rounded-2">
            <div class="card-header pt-3 pb-0">
                <h6>Informasi Produk</h6>
            </div>
            <div class="card-body px-4 pt-0">
                <div class="row">

                    {{-- Nama --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name_product') is-invalid @enderror"
                            id="name_product" name="name_product" value="{{ old('name_product') }}" required autofocus>
                        @error('name_product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Barcode --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                            name="barcode" value="{{ old('barcode') }}">
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                            name="slug" value="{{ old('slug') }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SKU --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                            name="sku" value="{{ old('sku') }}" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('kategori') is-invalid @enderror" name="kategori"
                            id="kategori" data-placeholder="Pilih Kategori" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($kategoris as $parent)
                                @if ($parent->children->isEmpty())
                                    {{-- Kategori tanpa sub --}}
                                    <option value="{{ $parent->id }}" @selected(old('kategori') == $parent->id)>
                                        {{ $parent->name }}
                                    </option>
                                @else
                                    {{-- Group dengan sub-kategori --}}
                                    <optgroup label="{{ $parent->name }}">
                                        @foreach ($parent->children as $child)
                                            <option value="{{ $child->id }}" @selected(old('kategori') == $child->id)>
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

                    {{-- Brand --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('brand') is-invalid @enderror" id="brand"
                            name="brand" data-placeholder="Pilih Brand" required>
                            <option value="" disabled selected>Pilih Brand</option>
                            @foreach ($brand as $item)
                                <option value="{{ $item->id }}" @selected(old('brand') == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('unit') is-invalid @enderror" id="unit"
                            name="unit" data-placeholder="Pilih Unit" required>
                            <option value="" disabled selected>Pilih Unit</option>
                            @foreach ($unit as $item)
                                <option value="{{ $item->id }}" @selected(old('unit') == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Garansi --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Garansi <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('garansi') is-invalid @enderror" name="garansi"
                            data-placeholder="Pilih Garansi" required>
                            <option value="" disabled selected>Pilih Garansi</option>
                            @foreach ($garansi as $item)
                                <option value="{{ $item->id }}" @selected(old('garansi') == $item->id)>{{ $item->name }}
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
                        <div id="quill-description" style="min-height:120px;">{!! old('description') !!}</div>
                        <input type="hidden" name="description" id="description" value="{{ old('description') }}">
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
                <p class="text-muted small mb-3">
                    Isi harga &amp; stok di bawah sebagai <strong>default</strong>.
                    Jika produk punya variasi, stok &amp; harga per variasi dapat diatur di bagian Variasi.
                </p>
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('qty') is-invalid @enderror" name="qty"
                            value="{{ old('qty', 0) }}" required>
                        @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('harga_jual') is-invalid @enderror"
                                name="harga_jual" value="{{ old('harga_jual') }}" required>
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
                                name="harga_beli" value="{{ old('harga_beli') }}" required>
                            @error('harga_beli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror"
                            name="stok_minimum" value="{{ old('stok_minimum', 0) }}" required>
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
                                <option value="{{ $item->id }}" @selected(old('pajak') == $item->id)>{{ $item->name_taxe }}
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
                                value="1" @checked(old('wajib_seri'))>
                            <label class="form-check-label fw-bold" for="wajib_seri">
                                Produk ini memiliki <u class="text-warning">Nomor Seri</u>
                            </label>
                        </div>
                        <small id="serial-info" class="text-muted" style="display:none;opacity:0;transition:opacity .3s">
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
                <p class="text-muted small">Upload beberapa foto. Foto pertama yang diupload otomatis jadi foto utama
                    (thumbnail).</p>
                {{-- FilePond multi upload --}}
                <input type="file" id="gallery-pond" name="gallery_files" multiple accept="image/*">
                {{-- Hidden inputs akan ditulis oleh JS setelah upload --}}
                <div id="gallery-hidden-inputs"></div>
                @error('gallery')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 4: VARIASI PRODUK --}}
        {{-- ============================================================ --}}
        <div class="card mt-3 rounded-2">
            <div class="card-header pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Variasi Produk</h6>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="toggle-variants">
                    <label class="form-check-label" for="toggle-variants">Produk ini punya variasi</label>
                </div>
            </div>
            <div class="card-body" id="variant-section" style="display:none;">

                {{-- Tipe Variasi --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Variasi</label>
                    <p class="text-muted small">Contoh: Warna, RAM, Storage. Tambah sebanyak yang dibutuhkan.</p>
                    <div id="variant-types-container"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-variant-type">
                        <i class="bx bx-plus"></i> Tambah Tipe Variasi
                    </button>
                </div>

                <hr>

                {{-- Generate Kombinasi --}}
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info btn-sm" id="generate-combinations">
                        <i class="bx bx-refresh"></i> Generate Kombinasi Variasi
                    </button>
                    <small class="text-muted ms-2">Klik setelah mengisi semua tipe &amp; opsi di atas.</small>
                </div>

                {{-- Tabel Kombinasi --}}
                <div id="combinations-container" style="display:none;">
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

        {{-- TOMBOL SUBMIT --}}
        <div class="d-flex justify-content-end mt-3 mb-4 me-4 gap-2">
            <a href="{{ route('produk.index') }}" class="btn btn-danger">Batalkan</a>
            <button id="saveBtn" type="submit" class="btn btn-outline-info">Buat Produk</button>
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
            // SLUG AUTO-GENERATE
            // ============================================================
            document.getElementById('name_product').addEventListener('change', function() {
                fetch(`/produk/checkSlug?name_product=${this.value}`)
                    .then(r => r.json())
                    .then(d => document.getElementById('slug').value = d.slug);
            });

            // ============================================================
            // QUILL – DESKRIPSI
            // ============================================================
            const quillDesc = new Quill('#quill-description', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi produk...',
            });
            const hiddenDesc = document.getElementById('description');
            quillDesc.on('text-change', () => hiddenDesc.value = quillDesc.root.innerHTML);
            if (hiddenDesc.value) quillDesc.root.innerHTML = hiddenDesc.value;

            // ============================================================
            // SPESIFIKASI
            // ============================================================
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
            // FILEPOND – GALERI MULTI FOTO
            // ============================================================
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType
            );

            const galleryInput = document.getElementById('gallery-pond');
            const hiddenGallery = document.getElementById('gallery-hidden-inputs');
            const saveBtn = document.getElementById('saveBtn');
            let uploadingCount = 0;

            const galleryPond = FilePond.create(galleryInput, {
                allowMultiple: true,
                labelIdle: `Seret &amp; Lepas beberapa gambar atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                imagePreviewHeight: 160,
                maxFileSize: '2MB',
                allowFileSizeValidation: true,
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
                labelFileTypeNotAllowed: 'Format tidak didukung.',
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

            // Saat file berhasil diupload, tambahkan hidden input
            galleryPond.on('processfile', (error, fileItem) => {
                if (error) return;
                const idx = galleryPond.getFiles().indexOf(fileItem);
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'gallery[]';
                input.value = fileItem.serverId;
                input.dataset.fileId = fileItem.id;

                // Tandai yang pertama sebagai primary_image
                if (hiddenGallery.children.length === 0) {
                    const primaryInput = document.createElement('input');
                    primaryInput.type = 'hidden';
                    primaryInput.name = 'primary_image';
                    primaryInput.value = fileItem.serverId;
                    primaryInput.id = 'primary_image_input';
                    hiddenGallery.appendChild(primaryInput);
                }

                hiddenGallery.appendChild(input);
                uploadingCount = Math.max(0, uploadingCount - 1);
                updateSaveBtn();
            });

            galleryPond.on('addfile', () => {
                uploadingCount++;
                updateSaveBtn();
            });

            galleryPond.on('removefile', (error, fileItem) => {
                // Hapus hidden input yang sesuai
                const input = hiddenGallery.querySelector(`[data-file-id="${fileItem.id}"]`);
                if (input) input.remove();

                // Jika primary dihapus, ganti dengan yang pertama yang tersisa
                const primaryInput = document.getElementById('primary_image_input');
                if (primaryInput && primaryInput.value === fileItem.serverId) {
                    const next = hiddenGallery.querySelector('input[name="gallery[]"]');
                    if (next) primaryInput.value = next.value;
                    else primaryInput.remove();
                }
                updateSaveBtn();
            });

            function updateSaveBtn() {
                if (uploadingCount > 0) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengunggah...`;
                } else {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Buat Produk';
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

            function createTypeRow() {
                const idx = typeCount++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 position-relative';
                div.dataset.typeIndex = idx;
                div.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-type-btn" aria-label="Hapus"></button>
            <div class="row g-2 align-items-start">
                <div class="col-md-3">
                    <label class="form-label">Nama Tipe <span class="text-danger">*</span></label>
                    <input type="text" class="form-control type-name-input" name="variant_types[${idx}][name]"
                        placeholder="cth: Warna" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Opsi (pisah dengan koma)</label>
                    <input type="text" class="form-control option-input"
                        placeholder="cth: Merah, Biru, Hitam"
                        data-type-idx="${idx}">
                    <div class="option-tags mt-2 d-flex flex-wrap gap-1" data-type-idx="${idx}"></div>
                    <div class="option-hiddens" data-type-idx="${idx}"></div>
                </div>
            </div>`;
                typesContainer.appendChild(div);

                // Opsi dari input teks -> tag + hidden input
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

                // Hapus tipe
                div.querySelector('.remove-type-btn').addEventListener('click', () => div.remove());
            }

            function addOptionTag(typeIdx, value) {
                if (!value) return;
                const tagsEl = typesContainer.querySelector(`.option-tags[data-type-idx="${typeIdx}"]`);
                const hiddenEl = typesContainer.querySelector(`.option-hiddens[data-type-idx="${typeIdx}"]`);
                const optIdx = hiddenEl.children.length;

                // Tag visual
                const tag = document.createElement('span');
                tag.className = 'badge bg-label-primary d-inline-flex align-items-center gap-1 px-2 py-1';
                tag.innerHTML =
                    `${value} <button type="button" class="btn-close btn-close-sm" aria-label="Hapus"></button>`;
                tag.querySelector('.btn-close').addEventListener('click', () => {
                    tag.remove();
                    // Hapus hidden input berdasarkan value
                    Array.from(hiddenEl.children).forEach(inp => {
                        if (inp.value === value) inp.remove();
                    });
                });
                tagsEl.appendChild(tag);

                // Hidden input
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `variant_types[${typeIdx}][options][]`;
                hidden.value = value;
                hiddenEl.appendChild(hidden);
            }

            document.getElementById('add-variant-type').addEventListener('click', createTypeRow);

            // ============================================================
            // VARIASI – GENERATE KOMBINASI
            // ============================================================
            document.getElementById('generate-combinations').addEventListener('click', function() {
                const types = [];
                typesContainer.querySelectorAll('[data-type-index]').forEach(typeDiv => {
                    const idx = typeDiv.dataset.typeIndex;
                    const name = typeDiv.querySelector('.type-name-input').value.trim();
                    const options = Array.from(typeDiv.querySelectorAll(
                            `.option-hiddens[data-type-idx="${idx}"] input`))
                        .map(i => i.value);
                    if (name && options.length) types.push({
                        name,
                        options
                    });
                });

                if (!types.length) {
                    alert('Tambahkan minimal 1 tipe variasi dengan opsi terlebih dahulu.');
                    return;
                }

                // Cartesian product
                const combinations = types.reduce((acc, type) => {
                    if (!acc.length) return type.options.map(o => [{
                        type: type.name,
                        value: o
                    }]);
                    return acc.flatMap(combo =>
                        type.options.map(o => [...combo, {
                            type: type.name,
                            value: o
                        }])
                    );
                }, []);

                renderCombinations(combinations);
            });

            function renderCombinations(combinations) {
                const tbody = document.getElementById('combinations-tbody');
                tbody.innerHTML = '';

                combinations.forEach((combo, i) => {
                    const label = combo.map(c => c.value).join(' / ');
                    // Simpan mapping option type->value sebagai data attributes
                    const optionJson = JSON.stringify(combo);

                    const row = document.createElement('tr');
                    row.innerHTML =
                        `
                <td>
                    <strong>${label}</strong>
                    ${combo.map((c, ci) => `<input type="hidden" name="variants[${i}][option_ids][]" data-type="${c.type}" data-value="${c.value}" value="">`).join('')}
                </td>
                <td>
                    <input type="file" class="variant-img-input" id="variant-img-${i}" accept="image/*" style="display:none;">
                    <label for="variant-img-${i}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-image-add"></i>
                    </label>
                    <img id="variant-img-preview-${i}" src="" alt="" style="max-height:48px;display:none;border-radius:4px;" class="ms-1">
                    <input type="hidden" name="variants[${i}][img_variant]" id="variant-img-path-${i}" value="">
                </td>
                <td><input type="text" class="form-control form-control-sm" name="variants[${i}][sku]" placeholder="SKU-${label.replace(/ \/ /g, '-')}" required></td>
                <td><input type="text" class="form-control form-control-sm" name="variants[${i}][barcode]" placeholder="Opsional"></td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" name="variants[${i}][harga_jual]" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" name="variants[${i}][harga_beli]" required>
                    </div>
                </td>
                <td><input type="number" class="form-control form-control-sm" name="variants[${i}][qty]" value="0" required></td>`;
                    tbody.appendChild(row);

                    // Upload foto variant
                    const imgInput = row.querySelector(`#variant-img-${i}`);
                    imgInput.addEventListener('change', function() {
                        if (!this.files[0]) return;
                        const formData = new FormData();
                        formData.append('file', this.files[0]);
                        fetch('/produk/upload', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        }).then(r => r.text()).then(path => {
                            document.getElementById(`variant-img-path-${i}`).value = path;
                            const preview = document.getElementById(
                                `variant-img-preview-${i}`);
                            preview.src = `/storage/${path}`;
                            preview.style.display = 'inline-block';
                        });
                    });
                });

                document.getElementById('combinations-container').style.display = 'block';
            }

            // ============================================================
            // SUBMIT – SYNC QUILL SEBELUM FORM DIKIRIM
            // ============================================================
            document.getElementById('addform').addEventListener('submit', function() {
                hiddenDesc.value = quillDesc.root.innerHTML;
                hiddenSpec.value = quillSpec.root.innerHTML;
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
