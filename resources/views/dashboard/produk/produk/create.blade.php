@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')

@section('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@section('breadcrumb')
    @php
        // Definisikan item breadcrumb dalam bentuk array
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Manajemen Produk', 'url' => route('produk.index')],
            ['name' => 'Buat Produk Baru', 'url' => '#'],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

{{-- Form Isian --}}
<form id="addform" method="post" action="{{ route('produk.store') }} " enctype="multipart/form-data">
    @csrf
    <div class="card rounded-2">
        <div class="card-header pt-3 pb-0 mb-0 ">
            <h6 class="">Informasi Produk</h6>
        </div>
        <div class="card-body px-4 pt-0">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                        id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" required autofocus>
                    @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="barcode" class="form-label">Item Barcode <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                        name="barcode" value="{{ old('barcode') }}" required>
                    @error('barcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        name="slug" value="{{ old('slug') }}" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                        name="sku" value="{{ old('sku') }}" required>
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select @error('kategori') is-invalid @enderror" name="kategori" id="kategori"
                        placeholder="Departure" required>
                        <option value="" disabled selected>Pilih</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}" @selected(old('kategori') == $item->id)>{{ $item->nama }}
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
                        @foreach ($brand as $item)
                            <option value="{{ $item->id }}" @selected(old('brand') == $item->id)>{{ $item->nama }}
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
                        @foreach ($unit as $item)
                            <option value="{{ $item->id }}" @selected(old('unit') == $item->id)>{{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <div id="quill-editor" style="min-height: 100px;">{!! old('deskripsi') !!}</div>
                        <input type="hidden" name="deskripsi" id="deskripsi" value="{{ old('deskripsi') }}">
                        @error('deskripsi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 rounded-2">
        <div class="card-header pt-3 pb-0 mb-0 ">
            <h6 class="">Stok & Harga</h6>
        </div>
        <div class="card-body px-4 pt-0">
            <div class="row g-3">

                <div class="col-md-3">
                    <label for="qty" class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty"
                        name="qty" value="{{ old('qty') }}">
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label for="harga" class="form-control-label">Harga Jual <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="number" class="form-control @error('harga_jual') is-invalid @enderror"
                            id="harga" name="harga_jual" value="{{ old('harga_jual') }}">
                        @error('harga_jual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label for="hargaBeli" class="form-control-label">Harga Beli <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="number" class="form-control @error('harga_beli') is-invalid @enderror"
                            id="hargaBeli" name="harga_beli" value="{{ old('harga_beli') }}">
                        @error('harga_beli')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="garansi" class="form-label">Garansi</label>
                    <select class="form-select @error('garansi') is-invalid @enderror" id="garansi" name="garansi"
                        required>
                        <option value="" disabled selected>Pilih</option>
                        @foreach ($garansi as $item)
                            <option value="{{ $item->id }}" @selected(old('garansi') == $item->id)>{{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('garansi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="stok_minimum" class="form-label">Batas Stok Minimum <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror"
                        id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum') }}">
                    @error('stok_minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="pajak" class="form-label">Pajak <span class="text-danger">*</span></label>
                    <select class="form-select @error('pajak') is-invalid @enderror" id="pajak" name="pajak"
                        required>
                        <option value="" disabled selected>pilih</option>
                        @foreach ($pajak as $item)
                            <option value="{{ $item->id }}" @selected(old('pajak') == $item->id)>
                                {{ $item->nama_pajak }}</option>
                        @endforeach
                    </select>
                    @error('pajak')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_serial" name="has_serial"
                            value="1" @checked(old('has_serial'))>
                        <label class="form-check-label fw-bolder" for="has_serial">Produk ini memiliki <u
                                class="text-warning">Nomor Seri</u></label>
                    </div>
                    <small id="serial-number-info" class="text-muted text-sm"
                        style="display: none; opacity: 0; transition: opacity 0.3s ease-in-out;">Jika dicentang,
                        Anda harus memasukkan nomor seri saat pembelian dan memilihnya saat penjualan.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 rounded-2">
        <div class="card-header pt-3 pb-0 mb-n3">
            <h6 class="">Gambar Produk</h6>
        </div>
        <div class="card-body">
            <input type="file" class="filepond" name="img_produk" id="image">
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3 me-4">
        <button id="saveBtn" type="submit" class="btn btn-outline-info">Buat Produk</button>
        <a href="{{ route('produk.index') }}" id="cancel-button" class="btn btn-danger ms-3">Batalkan</a>
    </div>
</form>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.querySelector('#nama_produk');
        const slugInput = document.querySelector('#slug');

        if (namaInput && slugInput) {
            namaInput.addEventListener('change', function() {
                fetch(`/produk/checkSlug?nama_produk=${namaInput.value}`)
                    .then(response => response.json())
                    .then(data => slugInput.value = data.slug);
            });
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
            labelIdle: `Seret & Lepas gambar atau <span class="filepond--label-action">Cari</span>`,
            allowImagePreview: true,
            imagePreviewHeight: 300,
            allowFileSizeValidation: true,
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
                    },
                },
                revert: {
                    url: "/produk/revert",
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                },
            },
        });

        const saveBtn = document.getElementById('saveBtn');
        const originalSubmitText = saveBtn.innerHTML;
        pond.on("addfile", () => {
            saveBtn.disabled = true;
            saveBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...`;
        });
        pond.on("processfile", () => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalSubmitText;
        });
        pond.on("removefile", () => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalSubmitText;
        });

        // --- QUILL INITIALIZATION ---
        if (document.getElementById('quill-editor')) {
            const hiddenInputDeskripsi = document.getElementById('deskripsi');
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi produk di sini...',
            });

            quill.on('text-change', function() {
                hiddenInputDeskripsi.value = quill.root.innerHTML;
            });

            // Jika ada old value, set ke editor
            if (hiddenInputDeskripsi.value) {
                quill.root.innerHTML = hiddenInputDeskripsi.value;
            }
        }

        // --- HAS SERIAL CHECKBOX ANIMATION ---
        const serialCheckbox = document.getElementById('has_serial');
        const serialInfo = document.getElementById('serial-number-info');
        let hideTimeout; // Variabel untuk menyimpan ID timeout

        function toggleSerialInfoVisibility() {
            // Hapus timeout yang mungkin sedang berjalan untuk mencegah konflik
            clearTimeout(hideTimeout);

            if (serialCheckbox.checked) {
                serialInfo.style.display = 'block';
                // Use a timeout to allow the display property to apply before starting the transition
                setTimeout(() => {
                    serialInfo.style.opacity = 1;
                }, 10);
            } else {
                serialInfo.style.opacity = 0;
                // Atur timeout untuk menyembunyikan elemen setelah transisi selesai
                hideTimeout = setTimeout(() => {
                    serialInfo.style.display = 'none';
                }, 300); // This duration should match the CSS transition duration
            }
        }
        serialCheckbox.addEventListener('change', toggleSerialInfoVisibility);
        document.addEventListener('DOMContentLoaded', toggleSerialInfoVisibility); // Jalankan saat DOM siap
    });
</script>
@endsection
