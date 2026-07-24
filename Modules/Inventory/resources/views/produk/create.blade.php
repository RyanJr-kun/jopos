@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Product')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading fw-bold mb-1">Gagal Menyimpan!</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="addform" method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data">
        @csrf

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
                    <div class="col-12">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name_product') is-invalid @enderror"
                            id="name_product" name="name_product" value="{{ old('name_product') }}" required autofocus>
                        @error('name_product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="col-md-6">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('kategori') is-invalid @enderror" name="kategori"
                            id="kategori" required>
                            <option value="" disabled selected>Pilih Kategori...</option>
                            @foreach ($kategoris as $parent)
                                @if ($parent->children->isEmpty())
                                    <option value="{{ $parent->id }}" @selected(old('kategori') == $parent->id) data-level="parent">
                                        {{ $parent->name }}
                                    </option>
                                @else
                                    <optgroup label="{{ $parent->name }}">
                                        @foreach ($parent->children as $child)
                                            <option value="{{ $child->id }}" @selected(old('kategori') == $child->id)
                                                data-level="child">
                                                {{ $child->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand --}}
                    <div class="col-md-6">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="brand" required>
                            <option value="" disabled selected>Pilih Brand...</option>
                            @foreach ($brand as $item)
                                <option value="{{ $item->id }}" @selected(old('brand') == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Unit & Garansi --}}
                    <div class="col-md-6">
                        <label class="form-label">Unit / Satuan <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="unit" required>
                            <option value="" disabled selected>Pilih Unit...</option>
                            @foreach ($unit as $item)
                                <option value="{{ $item->id }}" @selected(old('unit') == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Garansi <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="garansi" required>
                            <option value="" disabled selected>Pilih Garansi...</option>
                            @foreach ($garansi as $item)
                                <option value="{{ $item->id }}" @selected(old('garansi') == $item->id)>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Spesifikasi Teknis</label>
                        <div id="specification-container"></div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btn-add-spec">
                            <i class="bx bx-plus"></i> Tambah Baris Spesifikasi
                        </button>
                    </div>

                    {{-- Deskripsi Pakai Quill --}}
                    <div class="col-md-12 mt-4">
                        <label class="form-label">Deskripsi Lengkap</label>
                        <div class="quill-wrapper">
                            <div id="quill-description">{!! old('description') !!}</div>
                        </div>
                        <input type="hidden" name="description" id="description" value="{{ old('description') }}">
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
                        <input class="form-check-input" type="checkbox" id="toggle-variants">
                        <label class="form-check-label fw-bold" for="toggle-variants">Aktifkan Varian Produk (Warna, Ukuran,
                            dll)</label>
                    </div>
                    <small class="ms-auto">Centang ini jika produk memiliki variasi harga/stok.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control base-price-input" name="harga_beli"
                                value="{{ old('harga_beli') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control base-price-input" name="harga_jual"
                                value="{{ old('harga_jual') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" class="form-control" name="stok_minimum"
                            value="{{ old('stok_minimum', 0) }}" required>
                    </div>
                    <hr class="my-3">

                    <div class="col-md-4">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" id="sku" name="sku"
                            value="{{ old('sku') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barcode</label>
                        <input type="text" class="form-control" id="barcode" name="barcode"
                            value="{{ old('barcode') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" id="slug" name="slug"
                            value="{{ old('slug') }}" readonly required>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="wajib_seri" name="wajib_seri"
                                value="1" @checked(old('wajib_seri'))>
                            <label class="form-check-label fw-bold text-warning" for="wajib_seri">
                                Produk ini melacak Nomor Seri (Serial Number)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 3: VARIASI PRODUK (Hidden by Default) --}}
        {{-- ============================================================ --}}
        <div class="card mb-4 rounded-2" id="variant-section" style="display:none; border: 2px solid #696cff;">
            <div class="card-header pt-3 pb-0 bg-label-primary">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Variasi</h6>
            </div>
            <div class="card-body pt-3">
                <div class="mb-3">
                    <div id="variant-types-container"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-variant-type">
                        <i class="bx bx-plus"></i> Tambah Tipe (Warna, RAM, dll)
                    </button>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-primary btn-sm w-100" id="generate-combinations">
                        <i class="bx bx-refresh"></i> Buat Baris Kombinasi
                    </button>
                </div>

                <div id="combinations-container" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="combinations-table">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Varian</th>
                                    <th width="10%">Foto</th>
                                    <th width="20%">SKU</th>
                                    <th width="20%">Hrg Beli</th>
                                    <th width="20%">Hrg Jual</th>
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
                <input type="file" id="gallery-pond" name="gallery_files" multiple accept="image/*">
                <div id="gallery-hidden-inputs"></div>
            </div>
        </div>

        {{-- TOMBOL SUBMIT --}}
        <div class="d-flex justify-content-end mb-5 gap-2">
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
            <button id="saveBtn" type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Produk
                Baru</button>
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
            const storageBaseUrl = "{{ rtrim(Storage::disk('r2')->url(''), '/') }}";

            // 1. GENERATE SLUG & SKU AUTO
            document.getElementById('name_product').addEventListener('change', function() {
                const name = this.value;
                // Auto slug
                fetch(`/produk/checkSlug?name_product=${name}`)
                    .then(r => r.json())
                    .then(d => document.getElementById('slug').value = d.slug);
                // Auto isi SKU dasar jika kosong
                const skuInput = document.getElementById('sku');
                if (!skuInput.value) {
                    skuInput.value = 'SKU-' + name.toUpperCase().replace(/[^a-zA-Z0-9]/g, '').substring(0,
                        8);
                }
            });

            // 2. INIT QUILL EDITOR
            const quillDesc = new Quill('#quill-description', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi produk yang menarik...',
            });
            const hiddenDesc = document.getElementById('description');
            quillDesc.on('text-change', () => hiddenDesc.value = quillDesc.root.innerHTML);
            if (hiddenDesc.value) quillDesc.root.innerHTML = hiddenDesc.value;

            // 3. INIT FILEPOND
            FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType);
            const galleryInput = document.getElementById('gallery-pond');
            const hiddenGallery = document.getElementById('gallery-hidden-inputs');
            let uploadingCount = 0;

            const galleryPond = FilePond.create(galleryInput, {
                allowMultiple: true,
                labelIdle: `Seret & Lepas gambar ke sini atau <span class="filepond--label-action">Browse</span>`,
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

            galleryPond.on('processfile', (error, fileItem) => {
                if (error) return;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'gallery[]';
                input.value = fileItem.serverId;
                input.dataset.fileId = fileItem.id;

                if (hiddenGallery.children.length === 0) {
                    const primary = document.createElement('input');
                    primary.type = 'hidden';
                    primary.name = 'primary_image';
                    primary.value = fileItem.serverId;
                    primary.id = 'primary_image_input';
                    hiddenGallery.appendChild(primary);
                }
                hiddenGallery.appendChild(input);
            });

            galleryPond.on('removefile', (error, fileItem) => {
                const input = hiddenGallery.querySelector(`[data-file-id="${fileItem.id}"]`);
                if (input) input.remove();
            });

            // 4. LOGIKA VARIAN vs HARGA DEFAULT
            const toggleVariants = document.getElementById('toggle-variants');
            const variantSection = document.getElementById('variant-section');
            const baseInputs = document.querySelectorAll('.base-price-input');

            toggleVariants.addEventListener('change', function() {
                if (this.checked) {
                    variantSection.style.display = 'block';
                    // Jika pakai varian, input harga default jadi opsional/tidak required
                    baseInputs.forEach(inp => {
                        inp.removeAttribute('required');
                        inp.parentElement.parentElement.style.opacity = '0.5';
                    });
                } else {
                    variantSection.style.display = 'none';
                    // Kembalikan ke required
                    baseInputs.forEach(inp => {
                        inp.setAttribute('required', 'required');
                        inp.parentElement.parentElement.style.opacity = '1';
                    });
                }
            });

            // specification dynamic rows
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

            // 5. GENERATE VARIAN ROW (Versi Rapi)
            const typesContainer = document.getElementById('variant-types-container');
            let typeCount = 0;

            document.getElementById('add-variant-type').addEventListener('click', function() {
                const idx = typeCount++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-2 position-relative bg-white';
                div.dataset.typeIndex = idx;
                div.innerHTML = `
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-type-btn"></button>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control type-name-input" name="variant_types[${idx}][name]" placeholder="Nama Tipe (Misal: Warna)" required>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control option-input" placeholder="Ketik opsi lalu Enter (Merah, Biru)" data-type-idx="${idx}">
                            <div class="option-tags mt-2 d-flex flex-wrap gap-1" data-type-idx="${idx}"></div>
                            <div class="option-hiddens" data-type-idx="${idx}"></div>
                        </div>
                    </div>`;
                typesContainer.appendChild(div);

                const optionInput = div.querySelector('.option-input');
                optionInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        addOptionTag(idx, this.value.trim().replace(/,$/, ''), div);
                        this.value = '';
                    }
                });
                div.querySelector('.remove-type-btn').addEventListener('click', () => div.remove());
            });

            function addOptionTag(typeIdx, value, containerDiv) {
                if (!value) return;
                const tagsEl = containerDiv.querySelector('.option-tags');
                const hiddenEl = containerDiv.querySelector('.option-hiddens');

                const tag = document.createElement('span');
                tag.className = 'badge bg-secondary d-inline-flex align-items-center gap-1';
                tag.innerHTML =
                    `${value} <i class="bx bx-x text-white" style="cursor:pointer" onclick="this.parentElement.remove()"></i>`;
                tagsEl.appendChild(tag);

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `variant_types[${typeIdx}][options][]`;
                hidden.value = value;
                tag.appendChild(hidden); // Tempel hidden di dalam tag agar terhapus otomatis
            }

            // MENGGABUNGKAN KOMBINASI
            document.getElementById('generate-combinations').addEventListener('click', function() {
                const types = [];
                typesContainer.querySelectorAll('[data-type-index]').forEach(typeDiv => {
                    const name = typeDiv.querySelector('.type-name-input').value.trim();
                    const options = Array.from(typeDiv.querySelectorAll('.option-tags input')).map(
                        i => i.value);
                    if (name && options.length) types.push({
                        name,
                        options
                    });
                });

                if (!types.length) return alert('Isi tipe variasi dulu!');

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

                const tbody = document.getElementById('combinations-tbody');
                tbody.innerHTML = '';
                const baseSku = document.getElementById('sku').value || 'SKU';

                combinations.forEach((combo, i) => {
                    const label = combo.map(c => c.value).join('-');
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <span class="badge bg-label-info">${label}</span>
                            ${combo.map(c => `<input type="hidden" name="variants[${i}][option_ids][]" value="${c.value}">`).join('')}
                        </td>
                        <td class="text-center">
                            <input type="file" id="v-img-${i}" accept="image/*" style="display:none;">
                            <label for="v-img-${i}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-camera"></i></label>
                            <img id="v-preview-${i}" src="" style="display:none; width:30px; height:30px; object-fit:cover; border-radius:4px;">
                            <input type="hidden" name="variants[${i}][img_variant]" id="v-path-${i}">
                            </td>
                            <td><input type="text" class="form-control form-control-sm" name="variants[${i}][sku]" value="${baseSku}-${label}" required></td>
                        <td><input type="number" class="form-control form-control-sm" name="variants[${i}][harga_beli]" required></td>
                        <td><input type="number" class="form-control form-control-sm" name="variants[${i}][harga_jual]" required></td>
                        
                    `;
                    tbody.appendChild(row);

                    row.querySelector(`#v-img-${i}`).addEventListener('change', function() {
                        if (!this.files[0]) return;
                        const formData = new FormData();
                        formData.append('file', this.files[0]);
                        fetch('/produk/upload', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: formData
                            })
                            .then(r => r.text()).then(path => {
                                document.getElementById(`v-path-${i}`).value = path;
                                const prev = document.getElementById(`v-preview-${i}`);
                                prev.src = `${storageBaseUrl}/${path.trim()}`;
                                prev.style.display = 'inline-block';
                            });
                    });
                });
                document.getElementById('combinations-container').style.display = 'block';
            });

            // SUBMIT FORM
            document.getElementById('addform').addEventListener('submit', function() {
                hiddenDesc.value = quillDesc.root.innerHTML;
            });
        });
    </script>

    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    $(this).select2({
                        placeholder: "Pilih...",
                        width: '100%',
                        minimumResultsForSearch: 10
                    });
                });
            } else setTimeout(initSelect2, 100);
        };
        initSelect2();
    </script>
@endsection
