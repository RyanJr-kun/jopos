@extends('layouts/contentNavbarLayout')

@section('title', 'Buat Artikel Baru')

@section('vendor-style')
    {{-- Menggunakan Quill CSS Tema Snow --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-3">

            {{-- Formulir Utama --}}
            <div class="col-12 col-xl-8">
                <div class="card rounded-2 shadow-sm">
                    <div class="card-header pb-0 px-3 pt-3">
                        <h5 class="mb-0 fw-bolder">
                            <i class="bx bx-edit-alt me-2 text-primary"></i>Buat Artikel Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('artikel.store') }}" method="POST" enctype="multipart/form-data"
                            id="artikelForm">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                                    <strong>Oops! Terjadi kesalahan:</strong>
                                    <ul class="mb-0 mt-1 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Judul Artikel --}}
                            <div class="mb-3">
                                <label for="judul_artikel" class="form-label fw-semibold">
                                    Judul Artikel <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg @error('judul_artikel') is-invalid @enderror"
                                    id="judul_artikel" name="judul_artikel" placeholder="Masukkan judul artikel..."
                                    value="{{ old('judul_artikel') }}" required>
                                @error('judul_artikel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Slug akan di-generate otomatis dari judul.</small>
                            </div>

                            {{-- Isi Artikel (Quill Editor) --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Isi Artikel <span class="text-danger">*</span>
                                </label>
                                {{-- Wrapper untuk mendeteksi error --}}
                                <div class="@error('isi_artikel') editor-invalid @enderror">
                                    {{-- Div yang akan dirender menjadi editor oleh Quill --}}
                                    <div id="quill-editor">{!! old('isi_artikel') !!}</div>
                                </div>
                                {{-- Hidden input tempat data asli akan dikirim ke server --}}
                                <input type="hidden" name="isi_artikel" id="isi_artikel" value="{{ old('isi_artikel') }}">

                                @error('isi_artikel')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Submit Buttons (bottom of form) --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('artikel.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i>Batal
                                </a>
                                <button type="submit" name="status_submit" value="draft" class="btn btn-outline-warning">
                                    <i class="bx bx-save me-1"></i>Simpan sebagai Draft
                                </button>
                                <button type="submit" name="status_submit" value="published" class="btn btn-primary">
                                    <i class="bx bx-send me-1"></i>Publish
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Pengaturan Artikel --}}
            <div class="col-12 col-xl-4">

                {{-- Status --}}
                <div class="card rounded-2 shadow-sm mb-3">
                    <div class="card-header pb-0 pt-3 px-3">
                        <h6 class="mb-0 fw-bolder">
                            <i class="bx bx-toggle-left me-2 text-warning"></i>Status Publikasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                            form="artikelForm">
                            <option value="draft" @selected(old('status', 'draft') == 'draft')>Draft</option>
                            <option value="published" @selected(old('status') == 'published')>Published</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-1 d-block">
                            Klik tombol "Publish" di bawah untuk langsung mempublikasi.
                        </small>
                    </div>
                </div>

                {{-- Kategori (Select2 Tags) --}}
                <div class="card rounded-2 shadow-sm mb-3">
                    <div class="card-header pb-0 pt-3 px-3">
                        <h6 class="mb-0 fw-bolder">
                            <i class="bx bx-tag me-2 text-info"></i>Kategori
                        </h6>
                    </div>
                    <div class="card-body">
                        <select name="kategori[]" id="kategori" multiple
                            class="form-select select2-kategori @error('kategori') is-invalid @enderror" form="artikelForm">
                            @if (old('kategori'))
                                @foreach (old('kategori') as $kat)
                                    <option value="{{ $kat }}" selected>{{ $kat }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-1 d-block">
                            Ketik lalu Enter untuk menambahkan kategori baru.
                        </small>
                    </div>
                </div>

                {{-- Thumbnail --}}
                <div class="card rounded-2 shadow-sm mb-3">
                    <div class="card-header pb-0 pt-3 px-3">
                        <h6 class="mb-0 fw-bolder">
                            <i class="bx bx-image me-2 text-success"></i>Thumbnail
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- Preview Area --}}
                        <div id="thumbnailPreviewWrapper" class="mb-2 d-none">
                            <img id="thumbnailPreview" src="#" alt="Preview Thumbnail"
                                class="img-fluid rounded-2 w-100 object-fit-cover" style="max-height:180px;">
                        </div>
                        <input type="file" name="thumbnail" id="thumbnail"
                            class="form-control @error('thumbnail') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/jpg,image/webp" form="artikelForm">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-1 d-block">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-script')
    {{-- Import Quill JS --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Quill Editor
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Ketik isi artikel kamu di sini...',
                modules: {
                    toolbar: [
                        [{
                            'header': [2, 3, 4, false]
                        }], // Pilihan ukuran heading
                        ['bold', 'italic', 'underline', 'strike'], // Format teks
                        [{
                            'color': []
                        }, {
                            'background': []
                        }], // Warna teks & background
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }], // Subscript / superscript
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }], // List numbering/bullet
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }], // Indentasi
                        [{
                            'align': []
                        }], // Perataan teks (kiri, tengah, kanan, justify)
                        ['link', 'image', 'video'], // Sisipkan media
                        ['blockquote', 'code-block'], // Blockquote & Code
                        ['clean'] // Tombol hapus format
                    ]
                }
            });

            // 2. Sinkronisasi Quill ke Hidden Input saat Form Submit
            const form = document.getElementById('artikelForm');
            const isiArtikelInput = document.getElementById('isi_artikel');

            form.addEventListener('submit', function() {
                // quill.getSemanticHTML() untuk v2, atau root.innerHTML menangkap format HTML mentah dari editor
                // Jika editor kosong (hanya berisi tag p kosong), kirim string kosong agar terbaca oleh validasi Laravel
                let content = quill.root.innerHTML;
                if (content === '<p><br></p>') {
                    content = '';
                }
                isiArtikelInput.value = content;
            });
        });

        // 3. Select2 Tags - Kategori
        (function initSelect2Kategori() {
            if (typeof $ === 'undefined' || !$.fn.select2) {
                setTimeout(initSelect2Kategori, 150);
                return;
            }
            $('.select2-kategori').select2({
                tags: true,
                placeholder: "Ketik lalu Enter untuk tambah kategori",
                allowClear: true,
                width: '100%',
                tokenSeparators: [',']
            });
        })();

        // 4. Preview Thumbnail
        document.getElementById('thumbnail').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('thumbnailPreview').src = e.target.result;
                    document.getElementById('thumbnailPreviewWrapper').classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        // 5. Tombol submit langsung set status
        document.querySelectorAll('[name="status_submit"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('status').value = this.value;
            });
        });
    </script>
@endsection
