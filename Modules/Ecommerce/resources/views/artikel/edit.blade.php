@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Artikel: ' . $artikel->judul_artikel)

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
                    <div class="card-header pb-0 px-3 pt-3 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bolder">
                            <i class="bx bx-edit me-2 text-primary"></i>Edit Artikel
                        </h5>
                        <a href="{{ route('artikel.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('artikel.update', $artikel) }}" method="POST" enctype="multipart/form-data"
                            id="artikelForm">
                            @csrf
                            @method('PUT')

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

                            {{-- Slug Info --}}
                            <div class="alert alert-light border mb-3 py-2 px-3 d-flex align-items-center gap-2">
                                <i class="bx bx-link text-muted"></i>
                                <small class="text-muted">Slug saat ini:
                                    <strong>{{ $artikel->slug }}</strong>
                                    <span class="ms-2 text-info">(akan diperbarui otomatis jika judul diubah)</span>
                                </small>
                            </div>

                            {{-- Judul Artikel --}}
                            <div class="mb-3">
                                <label for="judul_artikel" class="form-label fw-semibold">
                                    Judul Artikel <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg @error('judul_artikel') is-invalid @enderror"
                                    id="judul_artikel" name="judul_artikel" placeholder="Masukkan judul artikel..."
                                    value="{{ old('judul_artikel', $artikel->judul_artikel) }}" required>
                                @error('judul_artikel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Isi Artikel <span class="text-danger">*</span>
                                </label>
                                <div class="@error('isi_artikel') editor-invalid @enderror">
                                    <div id="quill-editor">{!! old('isi_artikel', $artikel->isi_artikel) !!}</div>
                                </div>
                                <input type="hidden" name="isi_artikel" id="isi_artikel"
                                    value="{{ old('isi_artikel', $artikel->isi_artikel) }}">
                                @error('isi_artikel')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="submit" name="status_submit" value="draft" class="btn btn-outline-warning">
                                    <i class="bx bx-save me-1"></i>Simpan sebagai Draft
                                </button>
                                <button type="submit" name="status_submit" value="published" class="btn btn-primary">
                                    <i class="bx bx-send me-1"></i>Update & Publish
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
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
                            <option value="draft" @selected(old('status', $artikel->status) == 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $artikel->status) == 'published')>Published</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            @if ($artikel->status === 'published')
                                <span class="badge bg-label-success">
                                    <i class="bx bx-globe me-1"></i>Sedang Dipublikasikan
                                </span>
                            @else
                                <span class="badge bg-label-warning">
                                    <i class="bx bx-edit me-1"></i>Draft
                                </span>
                            @endif
                        </div>
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
                            @php
                                $selectedKategori = old('kategori', $artikel->kategori ?? []);
                            @endphp
                            @foreach ($selectedKategori as $kat)
                                <option value="{{ $kat }}" selected>{{ $kat }}</option>
                            @endforeach
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
                        {{-- Thumbnail Existing --}}
                        @if ($artikel->thumbnail)
                            <div id="existingThumbnailWrapper" class="mb-2 position-relative">
                                <img src="{{ $artikel->thumbnail_url }}" alt="Thumbnail saat ini"
                                    class="img-fluid rounded-2 w-100 object-fit-cover" style="max-height:180px;">
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <span class="badge bg-label-success">
                                        <i class="bx bx-check me-1"></i>Thumbnail terpasang
                                    </span>
                                    <label class="form-check-label text-danger small" for="remove_thumbnail">
                                        <input type="checkbox" class="form-check-input me-1" id="remove_thumbnail"
                                            name="remove_thumbnail" value="1" form="artikelForm">
                                        Hapus thumbnail
                                    </label>
                                </div>
                            </div>
                        @endif

                        {{-- Preview Upload Baru --}}
                        <div id="thumbnailPreviewWrapper" class="mb-2 d-none">
                            <img id="thumbnailPreview" src="#" alt="Preview Thumbnail"
                                class="img-fluid rounded-2 w-100 object-fit-cover" style="max-height:180px;">
                            <small class="text-success">
                                <i class="bx bx-check-circle me-1"></i>Gambar baru dipilih
                            </small>
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

                {{-- Meta Info --}}
                <div class="card rounded-2 shadow-sm">
                    <div class="card-body py-2 px-3">
                        <small class="text-muted d-block">
                            <i class="bx bx-user me-1"></i>Penulis:
                            <strong>{{ $artikel->user?->name ?? '-' }}</strong>
                        </small>
                        <small class="text-muted d-block mt-1">
                            <i class="bx bx-calendar me-1"></i>Dibuat:
                            <strong>{{ $artikel->created_at->format('d M Y, H:i') }}</strong>
                        </small>
                        <small class="text-muted d-block mt-1">
                            <i class="bx bx-time me-1"></i>Diperbarui:
                            <strong>{{ $artikel->updated_at->format('d M Y, H:i') }}</strong>
                        </small>
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
                // quill.root.innerHTML menangkap format HTML mentah dari editor
                // Jika editor kosong, kirim string kosong agar terbaca oleh validasi Laravel
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

        // 4. Preview Thumbnail Baru
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
