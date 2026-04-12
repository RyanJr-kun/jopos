@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection

{{-- Form Isian --}}
<form id="editProductForm" method="post" action="{{ route('produk.update', $produk->slug) }}"
    enctype="multipart/form-data">
    @method('put')
    @csrf
    {{-- informasi produk --}}
    <div class="card m-4">
        <div class="card-header pt-3 pb-0 mb-0 ">
            <h6 class="">Informasi Product</h6>
        </div>
        <div class="card-body px-4 pt-0">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name_product" class="form-label">Nama Product <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_product') is-invalid @enderror"
                        id="name_product" name="name_product" value="{{ old('name_product', $produk->name_product) }}"
                        required autofocus>
                    @error('name_product')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="barcode" class="form-label">Item Barcode <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                        name="barcode" value="{{ old('barcode', $produk->barcode) }}" required>
                    @error('barcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        name="slug" value="{{ old('slug', $produk->slug) }}" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                        name="sku" value="{{ old('sku', $produk->sku) }}" required>
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select @error('kategori') is-invalid @enderror" name="kategori" id="kategori"
                        placeholder="Departure" required>
                        <option value="" disabled selected>Pilih</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('kategori', $produk->category?->id) == $kategori->id)>{{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
                    <select class="form-select @error('brand') is-invalid @enderror" id="brand" name="brand"
                        required>
                        <option value="" disabled selected>Pilih</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand', $produk->brand?->id) == $brand->id)>{{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                    <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit"
                        required>
                        <option value="" disabled selected>Pilih</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit', $produk->unit?->id) == $unit->id)>{{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="description
                            " class="form-label">Description <span
                                class="text-danger">*</span></label>
                        <div id="quill-editor" style="min-height: 100px;">{!! old('description', $produk->description) !!}</div>
                        <input type="hidden" name="description" id="description"
                            value="{{ old('description', $produk->description) }}">
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- stock & harga --}}
    <div class="card m-4">
        <div class="card-header pt-3 pb-0 mb-0 ">
            <h6 class="">Stock & Harga</h6>
        </div>
        <div class="card-body px-4 pt-0">
            <div class="row g-3">

                <div class="col-md-3">
                    <label for="qty" class="form-label">Stock <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty"
                        name="qty" value="{{ old('qty', $produk->qty) }}">
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label for="harga_jual" class="form-control-label">Harga Jual <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="number" class="form-control @error('harga_jual') is-invalid @enderror"
                            id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}">
                        @error('harga_jual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label for="harga_beli" class="form-control-label">Harga Beli <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="number" class="form-control @error('harga_beli') is-invalid @enderror"
                            id="harga_beli" name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}">
                        @error('harga_beli')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="garansi" class="form-label">Warrantie</label>
                    <select class="form-select @error('garansi') is-invalid @enderror" id="garansi" name="garansi"
                        required>
                        <option value="" disabled selected>Pilih</option>
                        @foreach ($warranties as $garansi)
                            <option value="{{ $garansi->id }}" @selected(old('garansi', $produk->warrantie_id) == $garansi->id)>{{ $garansi->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('garansi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="stok_minimum" class="form-label">Batas Stock Minimum <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror"
                        id="stok_minimum" name="stok_minimum"
                        value="{{ old('stok_minimum', $produk->stok_minimum) }}">
                    @error('stok_minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="pajak" class="form-label">Taxe <span class="text-danger">*</span></label>
                    <select class="form-select @error('pajak') is-invalid @enderror" id="pajak" name="pajak"
                        required>
                        <option value="" disabled selected> pilih </option>
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
                        <label class="form-check-label fw-bolder" for="wajib_seri">Product ini memiliki <u
                                class="text-warning">Nomor Seri</u></label>
                    </div>
                    <small id="serial-number-info" class="text-muted text-sm"
                        style="display: none; opacity: 0; transition: opacity 0.3s ease-in-out;">Jika dicentang, Anda
                        harus memasukkan nomor seri saat pembelian dan memilihnya saat penjualan.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- gambar --}}
    <div class="card m-4 ">
        <div class="card-header mb-n5">
            <h6 class="">Gambar Product</h6>
        </div>
        <div class="card-body">
            <input type="file" class="filepond" name="img_produk" id="image" style="max-height: 100px;">
        </div>
    </div>

    {{-- button submit --}}
    <div class="d-flex justify-content-end mt-3 me-4">
        <button type="submit" class="btn btn-outline-info" id="submit-edit-produk">Simpan Perubahan</button>
        <a href="{{ route('produk.index') }}" id="cancel-button" class="btn btn-danger ms-3">Batalkan</a>
    </div>
</form>

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- SLUG GENERATION ---
            const nameInput = document.querySelector('#name_product');
            const slugInput = document.querySelector('#slug');

            if (nameInput && slugInput) {
                nameInput.addEventListener('change', function() {
                    fetch(`/produk/checkSlug?name_product=${nameInput.value}`)
                        .then(response => response.json())
                        .then(data => slugInput.value = data.slug);
                });
            }

            // --- QUILL INITIALIZATION ---
            if (document.getElementById('quill-editor')) {
                const hiddenInputDescription = document.getElementById('description');
                const quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    placeholder: 'Tulis description produk di sini...',
                });
                quill.on('text-change', function() {
                    hiddenInputDescription.value = quill.root.innerHTML;
                });
                if (hiddenInputDescription.value) {
                    quill.root.innerHTML = hiddenInputDescription.value;
                }
            }

            // --- FILEPOND INITIALIZATION ---
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType,
                FilePondPluginImageTransform
            );

            const inputElement = document.querySelector('#image');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const pond = FilePond.create(inputElement, {
                files: [
                    @if ($produk->img_produk && Storage::disk('public')->exists($produk->img_produk))
                        '{{ asset('storage/' . $produk->img_produk) }}'
                    @endif
                ],
                labelIdle: `Seret & Lepas gambar atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                imagePreviewHeight: 300,
                maxFileSize: "2MB",
                allowImageCrop: true,
                imageCropAspectRatio: "1:1",
                labelMaxFileSizeExceeded: "Ukuran file terlalu besar",
                labelMaxFileSize: "Ukuran file maksimum adalah 2MB",
                acceptedFileTypes: ["image/png", "image/jpeg", "image/webp", "image/svg+xml"],
                labelFileTypeNotAllowed: "Jenis file tidak valid.",
                server: {
                    process: {
                        url: "/produk/upload",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken
                        }
                    },
                    revert: {
                        url: "/produk/revert",
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken
                        }
                    },
                },
            });

            const saveBtn = document.getElementById('submit-edit-produk');
            const originalSubmitText = saveBtn.innerHTML;

            inputElement.addEventListener('FilePond:addfile', (e) => {
                // Hanya nonaktifkan tombol jika file berasal dari input pengguna, bukan dari inisialisasi
                if (e.detail.file.origin === FilePond.FileOrigin.INPUT) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML =
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...`;
                }
            });

            inputElement.addEventListener('FilePond:processfile', (e) => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalSubmitText;
            });

            inputElement.addEventListener('FilePond:removefile', (e) => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalSubmitText;
            });

            const cancelBtn = document.getElementById('cancel-button');
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const newFile = pond.getFiles().find(file =>
                    file.origin === FilePond.FileOrigin.INPUT &&
                    file.status === FilePond.FileStatus.PROCESSING_COMPLETE
                );

                if (newFile && newFile.serverId) {
                    fetch('/produk/revert', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: newFile.serverId
                    }).finally(() => {
                        window.location.href = this.href;
                    });
                } else {
                    window.location.href = this.href;
                }
            });

            // --- HAS SERIAL CHECKBOX ANIMATION ---
            const serialCheckbox = document.getElementById('wajib_seri');
            const serialInfo = document.getElementById('serial-number-info');
            let hideTimeout; // Variabel untuk menyimpan ID timeout

            function toggleSerialInfoVisibility() {
                // Hapus timeout yang mungkin sedang berjalan untuk mencegah konflik
                clearTimeout(hideTimeout);

                if (serialCheckbox.checked) {
                    serialInfo.style.display = 'block';
                    // Gunakan timeout agar properti display diterapkan sebelum transisi dimulai
                    setTimeout(() => {
                        serialInfo.style.opacity = 1;
                    }, 10);
                } else {
                    serialInfo.style.opacity = 0;
                    // Atur timeout untuk menyembunyikan elemen setelah transisi selesai
                    hideTimeout = setTimeout(() => {
                        serialInfo.style.display = 'none';
                    }, 300); // Durasi ini harus cocok dengan duration transisi CSS
                }
            }

            // Panggil fungsi saat halaman dimuat untuk mengatur state awal
            toggleSerialInfoVisibility();
            // Tambahkan event listener untuk perubahan pada checkbox
            serialCheckbox.addEventListener('change', toggleSerialInfoVisibility);
        });
    </script>
@endsection
@endsection
