@extends('layouts/contentNavbarLayout')

@section('title', 'Manajemen Banner')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-banner.scss'])
@endsection

@section('content')

    {{-- ================================================================
         STAT CARDS
    ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(105,108,255,0.1);">
                    <i class="bx bx-images" style="color:#696cff;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $banners->count() }}</div>
                    <div class="stat-label">Total Banner</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(40,199,111,0.1);">
                    <i class="bx bx-check-circle" style="color:#28c76f;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $banners->where('is_active', true)->count() }}</div>
                    <div class="stat-label">Banner Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(168,170,174,0.1);">
                    <i class="bx bx-hide" style="color:#8592a3;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $banners->where('is_active', false)->count() }}</div>
                    <div class="stat-label">Tidak Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(255,171,0,0.1);">
                    <i class="bx bx-layout" style="color:#e6980a;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ collect(\App\Enums\BannerPosition::cases())->count() }}</div>
                    <div class="stat-label">Tipe Posisi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         POSITION GUIDE (COLLAPSIBLE)
    ================================================================ --}}
    <div class="position-guide mb-4">
        <div class="position-guide-header" data-bs-toggle="collapse" data-bs-target="#guideBody" aria-expanded="false">
            <i class="bx bx-info-circle text-primary"></i>
            <h6 class="guide-title">Panduan Ukuran Banner per Posisi</h6>
            <i class="bx bx-chevron-down ms-auto text-muted" id="guide-chevron"></i>
        </div>
        <div class="collapse" id="guideBody">
            <div class="position-guide-body">
                <div class="position-chip-row">
                    <div class="position-chip">
                        <div class="chip-preview ratio-9-16" style="background:rgba(105,108,255,0.18);"></div>
                        <div class="chip-info">
                            <div class="chip-name">Main Banner</div>
                            <div class="chip-dim">Rasio 9:16 · Portrait</div>
                        </div>
                    </div>
                    <div class="position-chip">
                        <div class="chip-preview ratio-1-1" style="background:rgba(40,199,111,0.18);"></div>
                        <div class="chip-info">
                            <div class="chip-name">Main2 & Main3</div>
                            <div class="chip-dim">Rasio 1:1 · Square</div>
                        </div>
                    </div>
                    <div class="position-chip">
                        <div class="chip-preview ratio-wide" style="background:rgba(3,195,236,0.18);"></div>
                        <div class="chip-info">
                            <div class="chip-name">Bestseller Desktop</div>
                            <div class="chip-dim">3840 × 720 px · Ultrawide</div>
                        </div>
                    </div>
                    <div class="position-chip">
                        <div class="chip-preview ratio-2-1" style="background:rgba(255,171,0,0.18);"></div>
                        <div class="chip-info">
                            <div class="chip-name">Bestseller Mobile</div>
                            <div class="chip-dim">640 × 320 px · 2:1</div>
                        </div>
                    </div>
                    <div class="position-chip">
                        <div class="chip-preview ratio-4-5" style="background:rgba(234,84,85,0.18);"></div>
                        <div class="chip-info">
                            <div class="chip-name">Promo Banner</div>
                            <div class="chip-dim">Rasio 4:5 · Portrait</div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0" style="font-size:0.75rem;color:#a1acb8;">
                    <i class="bx bx-bulb me-1"></i>
                    Gunakan ukuran yang tepat agar banner tidak terpotong atau terlihat buram di perangkat pengguna.
                    Format yang didukung: <strong>PNG, JPG, WEBP, SVG</strong> · Maks. <strong>2 MB</strong>.
                </p>
            </div>
        </div>
    </div>

    {{-- ================================================================
         BANNER TABLE CARD
    ================================================================ --}}

    <div class="banner-card">
        <div class="banner-card-header">
            <div class="">
                <h6><i class="bx bx-list-ul me-1"></i> Daftar Banner</h6>
            </div>
            @can('create-banner')
                <div class="">
                    <button class="btn btn-primary px-2" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bx bx-plus-circle"></i>
                    </button>
                </div>
            @endcan
        </div>

        {{-- Banner Mobile List --}}
        <div class="banner-mobile-list d-md-none">
            @forelse ($banners as $banner)
                @php
                    $posVal = $banner->posisi;
                    $posBadgeClass = match (true) {
                        str_contains($posVal, 'desktop') => 'best-d',
                        str_contains($posVal, 'mobile') => 'best-m',
                        str_contains($posVal, 'promo') => 'promo',
                        str_contains($posVal, 'main') => 'main',
                        default => 'other',
                    };
                @endphp
                <div class="bmc" id="banner-row-{{ $banner->id }}">
                    <div class="bmc-thumb">
                        <img src="{{ $banner->img_banner ? Storage::url($banner->img_banner) : asset('assets/img/default-banner.png') }}"
                            alt="{{ $banner->judul ?? 'Banner' }}">
                    </div>
                    <div class="bmc-body">
                        <div class="bmc-top">
                            <div>
                                <div class="bmc-title">{{ $banner->judul ?? 'Tanpa Judul' }}</div>
                                <div class="bmc-id">ID : {{ $banner->id }}</div>
                            </div>
                            <div class="bmc-actions">
                                <a href="javascript:;" class="bmc-btn" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-id="{{ $banner->id }}" title="Edit banner">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="javascript:;" class="bmc-btn del btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-id="{{ $banner->id }}"
                                    data-title="{{ $banner->judul ?? 'Tanpa Judul' }}"
                                    data-url="{{ route('banner.destroy', $banner->id) }}" title="Hapus banner">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </div>
                        </div>

                        <div class="bmc-meta">
                            <span class="bmc-badge badge-{{ $posBadgeClass }}">
                                <i class="bx bx-map-pin"></i>
                                {{ \App\Enums\BannerPosition::from($banner->posisi)->getLabel() }}
                            </span>

                            @if ($banner->is_active)
                                <span class="bmc-badge badge-active"><i class="bx bx-check"></i> Aktif</span>
                            @else
                                <span class="bmc-badge badge-inactive">Nonaktif</span>
                            @endif

                            <span class="bmc-badge badge-urutan">Urutan: {{ $banner->urutan ?? 0 }}</span>
                        </div>

                        @if ($banner->url_tujuan)
                            <a href="{{ $banner->url_tujuan }}" target="_blank" class="bmc-url"
                                title="{{ $banner->url_tujuan }}">
                                <i class="bx bx-link-external"></i>
                                {{ Str::limit($banner->url_tujuan, 35) }}
                            </a>
                        @else
                            <span class="bmc-url" style="color: var(--muted);">— Tanpa link</span>
                        @endif

                        <div class="bmc-date">
                            <i class="bx bx-calendar"></i>
                            {{ $banner->created_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon"><i class="bx bx-image-add"></i></div>
                    <p class="fw-semibold mb-1">Belum ada banner</p>
                    <p>Tambahkan banner pertama Anda dengan klik tombol di atas.</p>
                </div>
            @endforelse
        </div>

        {{-- Banner Table --}}
        <div id="banner-table-container" class="table-responsive d-none d-md-block">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="min-width:220px;">Banner</th>
                        <th class="col-url" style="min-width:150px;">Link Tujuan</th>
                        <th style="min-width:130px;">Posisi</th>
                        <th class="text-center" style="min-width:90px;">Urutan</th>
                        <th class="text-center" style="min-width:100px;">Status</th>
                        <th class="col-created text-center" style="min-width:100px;">Dibuat</th>
                        <th class="text-center" style="min-width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="banner-table-body">
                    @forelse ($banners as $banner)
                        @php
                            $posVal = $banner->posisi;

                            // Exact match untuk menentukan class rasio gambar
                            $thumbClass = match ($posVal) {
                                'main2', 'main3' => 'ratio-1-1',
                                'main' => 'ratio-9-16',
                                'bestseller_desktop' => 'ratio-wide',
                                'bestseller_mobile' => 'ratio-2-1',
                                'promo' => 'ratio-4-5',
                                default => 'ratio-default',
                            };

                            // Menentukan class warna badge posisi
                            $posBadgeClass = match (true) {
                                str_contains($posVal, 'desktop') => 'best-d',
                                str_contains($posVal, 'mobile') => 'best-m',
                                str_contains($posVal, 'promo') => 'promo',
                                str_contains($posVal, 'main') => 'main',
                                default => 'other',
                            };
                        @endphp
                        <tr id="banner-row-{{ $banner->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="banner-thumb-wrap {{ $thumbClass }}">
                                        <img src="{{ $banner->img_banner ? Storage::url($banner->img_banner) : asset('assets/img/default-banner.png') }}"
                                            alt="{{ $banner->judul ?? 'Banner' }}">
                                    </div>
                                    <div class="banner-title-cell">
                                        <div class="title-text">{{ $banner->judul ?? 'Tanpa Judul' }}</div>
                                        <div class="title-sub">ID : {{ $banner->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-url">
                                @if ($banner->url_tujuan)
                                    <a href="{{ $banner->url_tujuan }}" target="_blank" class="url-link text-primary"
                                        title="{{ $banner->url_tujuan }}">
                                        {{ Str::limit($banner->url_tujuan, 30) }}
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="pos-badge {{ $posBadgeClass }}">
                                    <i class="bx bx-map-pin"></i>
                                    {{ \App\Enums\BannerPosition::from($banner->posisi)->getLabel() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-secondary">{{ $banner->urutan ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @if ($banner->is_active)
                                    <span class="badge bg-label-success"><i class="bx bx-check me-1"></i>Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="col-created text-center">
                                <small class="text-muted">{{ $banner->created_at->translatedFormat('d M Y') }}</small>
                            </td>
                            <td class="text-center">
                                @can('edit-banner')
                                    <a href="javascript:;" class="action-btn text-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-id="{{ $banner->id }}" title="Edit banner">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                @endcan
                                @can('delete-banner')
                                    <a href="javascript:;" class="action-btn del text-danger btn-delete"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $banner->id }}"
                                        data-title="{{ $banner->judul ?? 'Tanpa Judul' }}"
                                        data-url="{{ route('banner.destroy', $banner->id) }}" title="Hapus banner">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr id="banner-row-empty">
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="bx bx-image-add"></i></div>
                                    <p class="fw-semibold mb-1">Belum ada banner</p>
                                    <p>Tambahkan banner pertama Anda dengan klik tombol <strong>Tambah Banner</strong> di
                                        atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ================================================================
         MODAL: CREATE
    ================================================================ --}}
    @can('create-banner')
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

                <form id="createBannerForm" class="modal-content" enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">
                            <i class="bx bx-image-add me-2 text-primary"></i>Tambah Banner Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body" id="create-modal-body" style="position: relative;">

                        {{-- Posisi (di atas, agar hint muncul sebelum upload) --}}
                        <div class="row g-3 mb-3">
                            <div class="col-8">
                                <label for="create_posisi" class="form-label">Posisi <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="create_posisi" name="posisi" required>
                                    <option value="" disabled selected>Pilih posisi banner...</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->value }}">{{ $position->getLabel() }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="posisi-error"></div>
                            </div>
                            <div class="col-4">
                                <label for="create_urutan" class="form-label">Urutan</label>
                                <input type="number" class="form-control" id="create_urutan" name="urutan" value="0"
                                    min="0" required>
                                <div class="invalid-feedback" id="urutan-error"></div>
                            </div>
                        </div>

                        {{-- Hint box (muncul setelah posisi dipilih) --}}
                        <div class="position-hint-box" id="create-hint-box">
                            <i class="bx bx-ruler hint-icon"></i>
                            <div class="hint-text" id="create-hint-text"></div>
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="mb-3 mt-3">
                            <label class="form-label">Gambar Banner <span class="text-danger">*</span></label>
                            <input type="file" class="filepond" name="img_banner" id="create_img_banner" required>
                            <div class="invalid-feedback" id="img_banner-error"></div>
                        </div>

                        {{-- Judul --}}
                        <div class="mb-3">
                            <label for="create_title" class="form-label">
                                Judul
                                <span class="badge bg-label-secondary ms-1" style="font-size:0.65rem;">Opsional</span>
                            </label>
                            <input type="text" class="form-control" id="create_title" name="judul"
                                placeholder="cth: Promo Kemerdekaan 17 Agustus">
                            <div class="invalid-feedback" id="judul-error"></div>
                        </div>

                        {{-- Link Tujuan --}}
                        <div class="mb-3">
                            <label for="create_url_tujuan" class="form-label">
                                Link Tujuan
                                <span class="badge bg-label-secondary ms-1" style="font-size:0.65rem;">Opsional</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-link"></i></span>
                                <input type="url" class="form-control" id="create_url_tujuan" name="url_tujuan"
                                    placeholder="https://tokoanda.com/promo">
                            </div>
                            <div class="invalid-feedback" id="create-url_tujuan-error"></div>
                        </div>

                        {{-- Status --}}
                        <div class="d-flex align-items-center gap-3 p-3 rounded-2"
                            style="background:#f8f9fa;border:1px solid #e7e7e8;">
                            <div class="flex-grow-1">
                                <div style="font-size:.8125rem;font-weight:600;color:#566a7f;">Aktifkan Banner</div>
                                <div style="font-size:.75rem;color:#a1acb8;">Banner akan langsung tampil di toko</div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="create_is_active" name="is_active"
                                    value="1" checked>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="cancel-create-button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="button" id="submitCreateBtn" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- ================================================================
         MODAL: EDIT
    ================================================================ --}}
    @can('edit-banner')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form id="editBannerForm" class="modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">
                            <i class="bx bx-edit me-2 text-warning"></i>Edit Banner
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body" id="edit-modal-body" style="position: relative;">

                        {{-- Loading skeleton --}}
                        <div id="edit-loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Memuat data banner...</p>
                        </div>

                        {{-- Form fields (hidden while loading) --}}
                        <div id="edit-form-content" style="display:none;">

                            <div class="row g-3 mb-3">
                                <div class="col-8">
                                    <label for="edit_posisi" class="form-label">Posisi <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_posisi" name="posisi" required>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->value }}">{{ $position->getLabel() }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="edit-posisi-error"></div>
                                </div>
                                <div class="col-4">
                                    <label for="edit_urutan" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="edit_urutan" name="urutan"
                                        min="0" required>
                                    <div class="invalid-feedback" id="edit-urutan-error"></div>
                                </div>
                            </div>

                            {{-- Hint box edit --}}
                            <div class="position-hint-box" id="edit-hint-box">
                                <i class="bx bx-ruler hint-icon"></i>
                                <div class="hint-text" id="edit-hint-text"></div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="form-label">Gambar Banner</label>
                                {{-- Input hidden penanda hapus gambar --}}
                                <input type="hidden" name="remove_banner" id="edit_remove_banner" value="0">

                                {{-- Container untuk gambar lama --}}
                                <div id="edit-existing-banner-container" class="position-relative mb-3 d-none">
                                    <img id="edit-existing-banner-img" src="" alt="Banner"
                                        class="img-thumbnail rounded" style="max-height: 150px; object-fit: cover;">
                                    <button type="button" id="btn-remove-edit-banner"
                                        class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-0 d-flex align-items-center justify-content-center"
                                        style="width: 25px; height: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                        <span aria-hidden="true" style="font-weight: bold; line-height: 1;">&times;</span>
                                    </button>
                                </div>

                                {{-- Container FilePond --}}
                                <div id="edit-filepond-container">
                                    <input type="file" class="filepond" name="img_banner" id="edit_img_banner">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_title" class="form-label">
                                    Judul
                                    <span class="badge bg-label-secondary ms-1" style="font-size:.65rem;">Opsional</span>
                                </label>
                                <input type="text" class="form-control" id="edit_title" name="judul">
                                <div class="invalid-feedback" id="edit-judul-error"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_url_tujuan" class="form-label">
                                    Link Tujuan
                                    <span class="badge bg-label-secondary ms-1" style="font-size:.65rem;">Opsional</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                                    <input type="url" class="form-control" id="edit_url_tujuan" name="url_tujuan">
                                </div>
                                <div class="invalid-feedback" id="edit-url_tujuan-error"></div>
                            </div>

                            <div class="d-flex align-items-center gap-3 p-3 rounded-2"
                                style="background:#f8f9fa;border:1px solid #e7e7e8;">
                                <div class="flex-grow-1">
                                    <div style="font-size:.8125rem;font-weight:600;color:#566a7f;">Aktifkan Banner</div>
                                    <div style="font-size:.75rem;color:#a1acb8;">Nonaktifkan untuk menyembunyikan sementara
                                    </div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                        value="1">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="submit" id="submitEditBtn" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endcan

    {{-- ================================================================
         MODAL: DELETE
    ================================================================ --}}
    @can('delete-banner')
        <x-delete-modal message="Apakah Anda yakin ingin menghapus banner ini?" item-title-id="delete-banner-title"
            modal-id="deleteModal" />
    @endcan

@endsection

@section('page-script')
    <script>
        // ================================================================
        // POSITION META — ukuran & hint per posisi
        // ================================================================
        const POSITION_META = {
            // nilai key harus sesuai dengan $position->value dari BannerPosition enum
            'main': {
                label: 'Main Banner',
                dim: 'Rasio <strong>16:9</strong> (Landscape) — cth: 1920 × 1080 px',
                thumbClass: 'ratio-9-16',
                badgeClass: 'main'
            },
            'main2': {
                label: 'Main Banner 2',
                dim: 'Rasio <strong>1:1</strong> (Square) — cth: 800 × 800 px',
                thumbClass: 'ratio-1-1',
                badgeClass: 'main'
            },
            'main3': {
                label: 'Main Banner 3',
                dim: 'Rasio <strong>1:1</strong> (Square) — cth: 800 × 800 px',
                thumbClass: 'ratio-1-1',
                badgeClass: 'main'
            },
            'bestseller_desktop': {
                label: 'Bestseller Desktop',
                dim: 'Ukuran tetap <strong>3840 × 720 px</strong> (Ultrawide)',
                thumbClass: 'ratio-wide',
                badgeClass: 'best-d'
            },
            'bestseller_mobile': {
                label: 'Bestseller Mobile',
                dim: 'Ukuran tetap <strong>640 × 320 px</strong> (Rasio 2:1)',
                thumbClass: 'ratio-2-1',
                badgeClass: 'best-m'
            },
            'promo': {
                label: 'Promo Banner',
                dim: 'Rasio <strong>4:5</strong> (Portrait) — cth: 800 × 1000 px',
                thumbClass: 'ratio-4-5',
                badgeClass: 'promo'
            },
        };

        function getPositionMeta(val) {
            if (!val) return null;
            // exact match
            if (POSITION_META[val]) return POSITION_META[val];
            // fallback by substring
            if (val.includes('main2') || val.includes('main3')) return POSITION_META['main2'];
            if (val.includes('main')) return POSITION_META['main'];
            if (val.includes('bestseller_desktop')) return POSITION_META['bestseller_desktop'];
            if (val.includes('bestseller_mobile')) return POSITION_META['bestseller_mobile'];
            if (val.includes('promo')) return POSITION_META['promo'];
            return null;
        }

        // ================================================================
        // HELPER: Build table row HTML (Sneat classes)
        // ================================================================
        function createTableRow(banner) {
            let r2Base = '{{ Storage::url('') }}';
            if (!r2Base.endsWith('/')) r2Base += '/';
            const imageUrl = banner.img_banner ?
                r2Base + banner.img_banner :
                '{{ asset('assets/img/default-banner.png') }}';

            const meta = getPositionMeta(banner.posisi);
            const thumbClass = meta?.thumbClass ?? 'ratio-default';
            const badgeClass = meta?.badgeClass ?? 'other';

            const statusBadge = banner.is_active ?
                `<span class="badge bg-label-success"><i class="bx bx-check me-1"></i>Aktif</span>` :
                `<span class="badge bg-label-secondary">Nonaktif</span>`;

            const urlDisplay = banner.url_tujuan ?
                `<a href="${banner.url_tujuan}" target="_blank" class="url-link text-primary" title="${banner.url_tujuan}">${banner.url_tujuan.length > 30 ? banner.url_tujuan.substring(0, 30) + '...' : banner.url_tujuan}</a>` :
                `<span class="text-muted small">—</span>`;

            const createdAt = new Date(banner.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            const posLabel = banner.posisi_label || (meta?.label ?? banner.posisi);

            return `
        <tr id="banner-row-${banner.id}">
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="banner-thumb-wrap ${thumbClass}">
                        <img src="${imageUrl}" alt="${banner.judul || 'Banner'}">
                    </div>
                    <div class="banner-title-cell">
                        <div class="title-text">${banner.judul || 'Tanpa Judul'}</div>
                        <div class="title-sub">ID : ${banner.id}</div>
                    </div>
                </div>
            </td>
            <td class="col-url">${urlDisplay}</td>
            <td><span class="pos-badge ${badgeClass}"><i class="bx bx-map-pin"></i>${posLabel}</span></td>
            <td class="text-center"><span class="badge bg-label-secondary">${banner.urutan ?? 0}</span></td>
            <td class="text-center">${statusBadge}</td>
            <td class="col-created text-center"><small class="text-muted">${createdAt}</small></td>
            <td class="text-center">
                <a href="javascript:;" class="action-btn text-secondary"
                   data-bs-toggle="modal" data-bs-target="#editModal" data-id="${banner.id}" title="Edit banner">
                   <i class="bx bx-edit"></i>
                </a>
                <a href="javascript:;" class="action-btn del text-danger btn-delete"
                   data-bs-toggle="modal" data-bs-target="#deleteModal"
                   data-id="${banner.id}" data-title="${banner.judul || 'Tanpa Judul'}" title="Hapus banner">
                   <i class="bx bx-trash"></i>
                </a>
            </td>
        </tr>`;
        }

        // ================================================================
        // HELPER: Toast, clearErrors, showErrors
        // ================================================================
        function showToast(type, message) {
            const toastId = type === 'success' ? 'successToast' : 'errorToast';
            const toastEl = document.getElementById(toastId);
            if (!toastEl) return;
            toastEl.querySelector('.toast-body').textContent = message;
            new bootstrap.Toast(toastEl, {
                delay: 4000
            }).show();
        }

        function clearFormErrors(form) {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showValidationErrors(form, errors, prefix = '') {
            Object.keys(errors).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                const errDiv = form.querySelector(`#${prefix}${key}-error`);
                if (input) input.classList.add('is-invalid');
                if (errDiv) errDiv.textContent = errors[key][0];
            });
        }

        // ================================================================
        // HELPER: Position hint box
        // ================================================================
        function updateHintBox(selectEl, hintBoxId, hintTextId) {
            const hintBox = document.getElementById(hintBoxId);
            const hintText = document.getElementById(hintTextId);
            const meta = getPositionMeta(selectEl.value);
            if (meta && selectEl.value) {
                hintText.innerHTML = `<strong>${meta.label}:</strong> ${meta.dim}`;
                hintBox.classList.add('show');
            } else {
                hintBox.classList.remove('show');
            }
        }

        // ================================================================
        // MAIN
        // ================================================================
        document.addEventListener('DOMContentLoaded', function() {
            let createScrollbar, editScrollbar;

            const createModalBody = document.getElementById('create-modal-body');
            const editModalBody = document.getElementById('edit-modal-body');
            const createModalEl = document.getElementById('createModal');

            // Inisialisasi saat modal Selesai Muncul
            createModalEl.addEventListener('shown.bs.modal', function() {
                if (createModalBody) {
                    createScrollbar = new PerfectScrollbar(createModalBody, {
                        wheelPropagation: false,
                        suppressScrollX: true // Matikan scroll horizontal jika tidak perlu
                    });
                }
            });

            // Hapus instance saat modal ditutup untuk menghemat memori
            createModalEl.addEventListener('hidden.bs.modal', function() {
                if (createScrollbar) {
                    createScrollbar.destroy();
                    createScrollbar = null;
                }
            });

            const editModalEl = document.getElementById('editModal');

            editModalEl.addEventListener('shown.bs.modal', function() {
                if (editModalBody) {
                    editScrollbar = new PerfectScrollbar(editModalBody, {
                        wheelPropagation: false,
                        suppressScrollX: true
                    });
                }
            });

            editModalEl.addEventListener('hidden.bs.modal', function() {
                if (editScrollbar) {
                    editScrollbar.destroy();
                    editScrollbar = null;
                }
            });

            // Logika tombol hapus gambar lama di Modal Edit
            const btnRemoveEditBanner = document.getElementById('btn-remove-edit-banner');
            if (btnRemoveEditBanner) {
                btnRemoveEditBanner.addEventListener('click', function() {
                    document.getElementById('edit-existing-banner-container').classList.add('d-none');
                    document.getElementById('edit-existing-banner-container').classList.remove(
                        'd-inline-block');
                    document.getElementById('edit-filepond-container').classList.remove('d-none');
                    document.getElementById('edit_remove_banner').value = '1';
                });
            }
            // --- FilePond ---
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType
            );

            const csrfToken = "{{ csrf_token() }}";
            const pondOptions = {
                labelIdle: `<i class='bx bx-cloud-upload' style='font-size:1.5rem;vertical-align:middle;'></i><br>Seret & Lepas atau <span class="filepond--label-action">Pilih Gambar</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
                labelMaxFileSize: 'Maks. 2 MB',
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                labelFileTypeNotAllowed: 'Hanya PNG, JPG, WEBP, dan SVG yang diizinkan.',
                server: {
                    process: {
                        url: '{{ route('banner.upload') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                    revert: {
                        url: '{{ route('banner.revert') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                }
            };

            // --- Guide chevron toggle ---
            document.getElementById('guideBody')?.addEventListener('show.bs.collapse', () => {
                document.getElementById('guide-chevron')?.classList.replace('bx-chevron-down',
                    'bx-chevron-up');
            });
            document.getElementById('guideBody')?.addEventListener('hide.bs.collapse', () => {
                document.getElementById('guide-chevron')?.classList.replace('bx-chevron-up',
                    'bx-chevron-down');
            });

            // ========================
            // CREATE MODAL
            // ========================
            const createForm = document.getElementById('createBannerForm');
            const createModal = document.getElementById('createModal');
            const submitCreateBtn = document.getElementById('submitCreateBtn');
            const createPosisi = document.getElementById('create_posisi');

            const createPond = FilePond.create(document.querySelector('#create_img_banner'), pondOptions);
            const createInput = document.querySelector('#create_img_banner');

            createInput.addEventListener('FilePond:addfile', () => {
                // Beri waktu 200ms agar animasi preview FilePond selesai, lalu update scrollbar
                setTimeout(() => {
                    if (typeof createScrollbar !== 'undefined' && createScrollbar) {
                        createScrollbar.update();
                    }
                }, 200);
            });

            createInput.addEventListener('FilePond:removefile', () => {
                // Saat gambar dihapus, tinggi berkurang, update lagi scrollbarnya
                setTimeout(() => {
                    if (typeof createScrollbar !== 'undefined' && createScrollbar) {
                        createScrollbar.update();
                    }
                }, 200);
            });

            // Hint on posisi change
            createPosisi.addEventListener('change', () => {
                updateHintBox(createPosisi, 'create-hint-box', 'create-hint-text');
            });

            submitCreateBtn.addEventListener('click', function() {
                clearFormErrors(createForm);
                const formData = new FormData(createForm);
                const pondFile = createPond.getFile();
                if (pondFile) formData.set('img_banner', pondFile.serverId);

                submitCreateBtn.disabled = true;
                submitCreateBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...`;

                fetch('{{ route('banner.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const emptyRow = document.getElementById('banner-row-empty');
                            if (emptyRow) emptyRow.remove();
                            document.getElementById('banner-table-body').insertAdjacentHTML('beforeend',
                                createTableRow(data.data));
                            bootstrap.Modal.getInstance(createModal).hide();
                            showToast('success', data.message);
                        } else {
                            if (data.errors) showValidationErrors(createForm, data.errors);
                            else showToast('error', data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(() => showToast('error', 'Tidak dapat terhubung ke server.'))
                    .finally(() => {
                        submitCreateBtn.disabled = false;
                        submitCreateBtn.innerHTML = `<i class="bx bx-save me-1"></i> Simpan Banner`;
                    });
            });

            document.getElementById('cancel-create-button').addEventListener('click', () => {
                clearFormErrors(createForm);
                createForm.reset();
                createPond.removeFiles();
                document.getElementById('create-hint-box').classList.remove('show');
                bootstrap.Modal.getInstance(createModal).hide();
            });

            createModal.addEventListener('hidden.bs.modal', () => {
                clearFormErrors(createForm);
                createForm.reset();
                createPond.removeFiles();
                document.getElementById('create-hint-box').classList.remove('show');
            });

            // ========================
            // EDIT MODAL
            // ========================
            let editPond = null;
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editBannerForm');
            const submitEditBtn = document.getElementById('submitEditBtn');
            const editPosisi = document.getElementById('edit_posisi');

            editPosisi.addEventListener('change', () => {
                updateHintBox(editPosisi, 'edit-hint-box', 'edit-hint-text');
            });

            editModal.addEventListener('hidden.bs.modal', () => {
                if (editPond) {
                    editPond.destroy();
                    editPond = null;
                }
                clearFormErrors(editForm);
                editForm.reset();
                document.getElementById('edit-form-content').style.display = 'none';
                document.getElementById('edit-loading').style.display = 'block';
                document.getElementById('edit-hint-box').classList.remove('show');
            });

            editModal.addEventListener('show.bs.modal', function(event) {
                const bannerId = event.relatedTarget.getAttribute('data-id');
                editForm.dataset.updateUrl = `/banner/${bannerId}`;

                document.getElementById('edit-loading').style.display = 'block';
                document.getElementById('edit-form-content').style.display = 'none';

                fetch(`/banner/${bannerId}/json`)
                    .then(r => {
                        if (!r.ok) throw new Error(`HTTP ${r.status}`);
                        return r.json();
                    })
                    .then(data => {
                        document.getElementById('edit_title').value = data.judul ?? '';
                        document.getElementById('edit_url_tujuan').value = data.url_tujuan ?? '';
                        document.getElementById('edit_posisi').value = data.posisi ?? '';
                        document.getElementById('edit_urutan').value = data.urutan ?? 0;
                        document.getElementById('edit_is_active').checked = !!data.is_active;

                        // Update hint after data load
                        updateHintBox(editPosisi, 'edit-hint-box', 'edit-hint-text');

                        // === METODE BARU: Load Gambar ke <img> ===
                        const existingContainer = document.getElementById(
                            'edit-existing-banner-container');
                        const filepondContainer = document.getElementById('edit-filepond-container');
                        const existingImg = document.getElementById('edit-existing-banner-img');
                        const removeBannerInput = document.getElementById('edit_remove_banner');

                        removeBannerInput.value = '0'; // Reset status hapus

                        if (data.img_banner) {
                            // Bangun URL Cloudflare R2
                            let r2Base = '{{ Storage::url('') }}';
                            if (!r2Base.endsWith('/')) r2Base += '/'; // Pastikan ada garis miring

                            existingImg.src = r2Base + data.img_banner;
                            existingContainer.classList.remove('d-none');
                            existingContainer.classList.add('d-inline-block');
                            filepondContainer.classList.add('d-none'); // Sembunyikan FilePond
                        } else {
                            // Jika tidak ada gambar
                            existingContainer.classList.add('d-none');
                            existingContainer.classList.remove('d-inline-block');
                            filepondContainer.classList.remove('d-none'); // Munculkan FilePond
                        }

                        // Inisialisasi FilePond bersih (tanpa load file lama)
                        if (editPond) {
                            editPond.destroy();
                            editPond = null;
                        }
                        editPond = FilePond.create(document.querySelector('#edit_img_banner'),
                            pondOptions);

                        const editInput = document.querySelector('#edit_img_banner');
                        editInput.addEventListener('FilePond:addfile', () => {
                            submitEditBtn.disabled = true;
                            submitEditBtn.innerHTML =
                                `<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah...`;

                            setTimeout(() => {
                                if (typeof editScrollbar !== 'undefined' &&
                                    editScrollbar) {
                                    editScrollbar.update();
                                }
                            }, 200);
                        });
                        editInput.addEventListener('FilePond:processfile', () => {
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML =
                                `<i class="bx bx-save me-1"></i> Simpan Perubahan`;
                        });
                        editInput.addEventListener('FilePond:removefile', () => {
                            submitEditBtn.disabled = false;
                            submitEditBtn.innerHTML =
                                `<i class="bx bx-save me-1"></i> Simpan Perubahan`;
                            setTimeout(() => {
                                if (typeof editScrollbar !== 'undefined' &&
                                    editScrollbar) {
                                    editScrollbar.update();
                                }
                            }, 200);
                        });

                        document.getElementById('edit-loading').style.display = 'none';
                        document.getElementById('edit-form-content').style.display = 'block';
                    })
                    .catch(err => {
                        console.error(err);
                        bootstrap.Modal.getInstance(editModal).hide();
                        showToast('error', 'Tidak dapat mengambil data banner. Silakan coba lagi.');
                    });
            });

            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearFormErrors(editForm);
                const formData = new FormData(editForm);
                formData.append('_method', 'PUT');

                if (editPond) {
                    const pondFile = editPond.getFile();
                    if (pondFile && pondFile.status === FilePond.FileStatus.PROCESSING_COMPLETE) {
                        formData.set('img_banner', pondFile.serverId);
                    } else if (!pondFile) {
                        formData.set('img_banner', '');
                    } else {
                        formData.delete('img_banner');
                    }
                }

                submitEditBtn.disabled = true;
                submitEditBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...`;

                fetch(editForm.dataset.updateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const oldRow = document.getElementById(`banner-row-${data.data.id}`);
                            if (oldRow) oldRow.outerHTML = createTableRow(data.data);
                            bootstrap.Modal.getInstance(editModal).hide();
                            showToast('success', data.message);
                        } else {
                            if (data.errors) showValidationErrors(editForm, data.errors, 'edit-');
                            else showToast('error', data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(() => showToast('error', 'Tidak dapat terhubung ke server.'))
                    .finally(() => {
                        submitEditBtn.disabled = false;
                        submitEditBtn.innerHTML = `<i class="bx bx-save me-1"></i> Simpan Perubahan`;
                    });
            });

            // ========================
            // DELETE MODAL
            // ========================
            const deleteModal = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let bannerIdToDelete = null;

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                bannerIdToDelete = btn.getAttribute('data-id');
                document.getElementById('delete-banner-title').textContent =
                    btn.getAttribute('data-title') || 'banner ini';
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (!bannerIdToDelete) return;
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...`;

                fetch(`/banner/${bannerIdToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        bootstrap.Modal.getInstance(deleteModal).hide();
                        if (data.success) {
                            const row = document.getElementById(`banner-row-${bannerIdToDelete}`);
                            if (row) row.remove();
                            const tbody = document.getElementById('banner-table-body');
                            if (tbody && tbody.children.length === 0) {
                                tbody.innerHTML = `
                        <tr id="banner-row-empty">
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="bx bx-image-add"></i></div>
                                    <p class="fw-semibold mb-1">Belum ada banner</p>
                                    <p>Tambahkan banner pertama Anda dengan klik tombol <strong>Tambah Banner</strong>.</p>
                                </div>
                            </td>
                        </tr>`;
                            }
                            showToast('success', data.message);
                        } else {
                            showToast('error', data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(() => {
                        bootstrap.Modal.getInstance(deleteModal).hide();
                        showToast('error', 'Tidak dapat terhubung ke server.');
                    })
                    .finally(() => {
                        confirmDeleteBtn.disabled = false;
                        confirmDeleteBtn.innerHTML = 'Ya, Hapus';
                        bannerIdToDelete = null;
                    });
            });

        }); // END DOMContentLoaded
    </script>
@endsection
