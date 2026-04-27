@extends('layouts/contentNavbarLayout')

@section('title', 'Toko & Gudang')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('vendor-script')
    {{-- FilePond: load plugins SEBELUM core --}}
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@section('content')

    {{-- ── Page Header ── --}}
    <div class="loc-page-header">
        <h4 class="loc-page-title">
            <span class="loc-breadcrumb">Master /</span> Toko &amp; Gudang
        </h4>
        <button type="button" class="btn btn-loc-add btn-add" data-bs-toggle="modal" data-bs-target="#modalStore">
            <i class="bx bx-plus"></i> Tambah Lokasi
        </button>
    </div>

    {{-- ── Tabs + Content ── --}}
    <div class="loc-tab-wrapper">

        <ul class="nav loc-nav-tabs" id="locTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active" id="tab-toko" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-toko" aria-controls="navs-toko" aria-selected="true">
                    <i class="bx bx-store"></i>
                    Daftar Toko
                    <span class="loc-tab-badge">{{ $tokos->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link" id="tab-gudang" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-gudang" aria-controls="navs-gudang" aria-selected="false">
                    <i class="bx bx-building-house"></i>
                    Daftar Gudang
                    <span class="loc-tab-badge">{{ $gudangs->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ══ TAB TOKO ══ --}}
            <div class="tab-pane fade show active" id="navs-toko" role="tabpanel">
                <div class="row g-3 loc-cards-grid">
                    @forelse ($tokos as $toko)
                        <div class="col-md-6 col-lg-4">
                            <div class="loc-card loc-card-toko">
                                <div class="loc-card-body">

                                    <div class="loc-card-header">
                                        <div class="d-flex align-items-center" style="min-width:0">
                                            <div class="loc-logo-wrap me-3">
                                                <img src="{{ $toko->logo_path }}" alt="Logo" class="loc-logo">
                                                <span class="loc-type-dot"></span>
                                            </div>
                                            <div class="loc-name-block">
                                                <h6>{{ $toko->name_toko }}</h6>
                                                @if ($toko->is_active)
                                                    <span class="loc-status-badge status-active">Aktif</span>
                                                @else
                                                    <span class="loc-status-badge status-inactive">Non-Aktif</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="dropdown ms-2">
                                            <button class="loc-dropdown-btn" type="button" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end loc-dropdown-menu">
                                                <a class="dropdown-item btn-edit" href="javascript:void(0);"
                                                    data-store='@json($toko)'>
                                                    <i class="bx bx-edit-alt text-info"></i> Edit
                                                </a>
                                                <form action="{{ route('toko.destroy', $toko->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus toko ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bx bx-trash text-danger"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="loc-info-row">
                                        <i class="bx bx-map loc-info-icon"></i>
                                        <span>{{ $toko->full_region }}</span>
                                    </div>
                                    <div class="loc-info-row">
                                        <i class="bx bx-user loc-info-icon"></i>
                                        <span>PIC: <strong>{{ $toko->pic->name ?? 'Belum Ditentukan' }}</strong></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="loc-empty-state">
                                <img class="loc-empty-img"
                                    src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" width="140"
                                    alt="Kosong">
                                <h6 class="mt-2">Belum ada data Toko</h6>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ══ TAB GUDANG ══ --}}
            <div class="tab-pane fade" id="navs-gudang" role="tabpanel">
                <div class="row g-3 loc-cards-grid">
                    @forelse ($gudangs as $gudang)
                        <div class="col-md-6 col-lg-4">
                            <div class="loc-card loc-card-gudang">
                                <div class="loc-card-body">

                                    <div class="loc-card-header">
                                        <div class="d-flex align-items-center" style="min-width:0">
                                            <div class="loc-logo-wrap me-3">
                                                <img src="{{ $gudang->logo_path }}" alt="Logo" class="loc-logo">
                                                <span class="loc-type-dot"></span>
                                            </div>
                                            <div class="loc-name-block">
                                                <h6>{{ $gudang->name_toko }}</h6>
                                                @if ($gudang->is_active)
                                                    <span class="loc-status-badge status-active">Aktif</span>
                                                @else
                                                    <span class="loc-status-badge status-inactive">Non-Aktif</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="dropdown ms-2">
                                            <button class="loc-dropdown-btn" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end loc-dropdown-menu">
                                                <a class="dropdown-item btn-edit" href="javascript:void(0);"
                                                    data-store='@json($gudang)'>
                                                    <i class="bx bx-edit-alt text-info"></i> Edit
                                                </a>
                                                <form action="{{ route('toko.destroy', $gudang->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus gudang ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bx bx-trash text-danger"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="loc-info-row">
                                        <i class="bx bx-map loc-info-icon"></i>
                                        <span>{{ $gudang->full_region }}</span>
                                    </div>
                                    <div class="loc-info-row">
                                        <i class="bx bx-user loc-info-icon"></i>
                                        <span>PIC: <strong>{{ $gudang->pic->name ?? 'Belum Ditentukan' }}</strong></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="loc-empty-state">
                                <img class="loc-empty-img"
                                    src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
                                    width="140" alt="Kosong">
                                <h6 class="mt-2">Belum ada data Gudang</h6>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Modal Tambah / Edit Lokasi ── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade loc-modal" id="modalStore" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalStoreTitle">
                        <span class="modal-title-icon"><i class="bx bx-map-pin"></i></span>
                        Tambah Lokasi Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formStore" action="{{ route('toko.store') }}" method="POST">
                    @csrf
                    <div id="methodContainer"></div>

                    <div class="modal-body">

                        {{-- ── SECTION: Informasi Dasar ── --}}
                        <div class="loc-form-section">
                            <div class="loc-form-section-title">
                                <i class="bx bx-info-circle"></i> Informasi Dasar
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="type">Tipe Lokasi</label>
                                    <select name="type" id="type" class="form-select select2" required>
                                        <option value="toko">🏪 Toko</option>
                                        <option value="gudang">🏭 Gudang</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="name_toko">Nama Lokasi</label>
                                    <input type="text" name="name_toko" id="name_toko"
                                        class="form-control loc-form-control" placeholder="Contoh: Cabang Kartasura"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="pic_id">PIC (Kepala Toko/Gudang)</label>
                                    {{-- FIX: option value="" kosong (bukan teks) agar allowClear bekerja --}}
                                    <select name="pic_id" id="pic_id" class="form-select select2"
                                        data-placeholder="— Pilih Karyawan —">
                                        <option value=""></option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="telepon">Nomor Telepon</label>
                                    <input type="text" name="telepon" id="telepon"
                                        class="form-control loc-form-control" placeholder="0812xxxxxx">
                                </div>
                            </div>
                        </div>

                        {{-- ── SECTION: Wilayah ── --}}
                        <div class="loc-form-section">
                            <div class="loc-form-section-title">
                                <i class="bx bx-map"></i> Wilayah
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="provinsi">Provinsi</label>
                                    {{-- FIX: option value="" kosong agar placeholder Select2 muncul --}}
                                    <select name="provinsi" id="provinsi" class="form-select select2"
                                        data-placeholder="— Pilih Provinsi —" required>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="kabupaten_kota">Kabupaten / Kota</label>
                                    <select name="kabupaten_kota" id="kabupaten_kota" class="form-select select2"
                                        data-placeholder="— Pilih Kabupaten/Kota —" disabled required>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="kecamatan">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="form-select select2"
                                        data-placeholder="— Pilih Kecamatan —" disabled required>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="loc-form-label" for="desa">Desa / Kelurahan</label>
                                    <select name="desa" id="desa" class="form-select select2"
                                        data-placeholder="— Pilih Desa —" disabled required>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="loc-form-label" for="alamat">Alamat Lengkap</label>
                                    <textarea name="alamat" id="alamat" class="form-control loc-form-control" rows="2"
                                        placeholder="Jalan Raya No. 123, RT/RW..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── SECTION: Koordinat GPS ── --}}
                        <div class="loc-form-section">
                            <div class="loc-form-section-title">
                                <i class="bx bx-current-location"></i> Koordinat GPS
                                <span class="fw-normal text-muted">(opsional)</span>
                            </div>
                            <div class="loc-gps-row">
                                <div>
                                    <div class="loc-gps-label"><i class="bx bx-radio-circle-marked"></i> Latitude</div>
                                    <input type="text" name="latitude" id="latitude" placeholder="-7.55xxxxx">
                                </div>
                                <div>
                                    <div class="loc-gps-label"><i class="bx bx-radio-circle-marked"></i> Longitude</div>
                                    <input type="text" name="longitude" id="longitude" placeholder="110.82xxxxx">
                                </div>
                            </div>
                        </div>

                        {{-- ── SECTION: Logo & Status ── --}}
                        <div class="loc-form-section mb-0">
                            <div class="loc-form-section-title">
                                <i class="bx bx-image"></i> Logo &amp; Status
                            </div>
                            <div class="loc-upload-area mb-3">
                                <input type="file" name="logo" class="filepond">
                            </div>
                            <div class="loc-status-toggle">
                                <div class="loc-status-label">
                                    {{-- FIX: diubah dari "Status Lokasi" → "Status Toko" --}}
                                    <span>Status Toko</span>
                                    <span>Aktifkan toko agar dapat digunakan</span>
                                </div>
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked role="switch">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-loc-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-loc-save">
                            <i class="bx bx-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

            // ══════════════════════════════════════════════════════════════════
            // 0. Setup Modal Instance & Perfect Scrollbar (FIX SCROLL BUG)
            // ══════════════════════════════════════════════════════════════════
            const modalEl    = document.getElementById('modalStore');
            const modalBodyEl = modalEl.querySelector('.modal-body');
            let ps = null; // PerfectScrollbar instance

            // getOrCreateInstance mencegah penumpukan Modal instance!
            const modalStoreInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

            // ══════════════════════════════════════════════════════════════════
            // 1. FilePond Init (hanya sekali, setelah DOM siap)
            // ══════════════════════════════════════════════════════════════════
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginImagePreview
            );

            let pond = null;

            function initFilePond() {
                // Cegah double-init
                const inputEl = modalEl.querySelector('input.filepond');
                if (!inputEl) return;
                if (pond) return;

                pond = FilePond.create(inputEl, {
                    acceptedFileTypes : ['image/png', 'image/jpeg', 'image/jpg'],
                    maxFileSize       : '2MB',
                    labelIdle         : '<i class="bx bx-cloud-upload" style="font-size:1.5rem;vertical-align:middle;margin-right:6px"></i> Seret foto ke sini, atau <span class="filepond--label-action">pilih file</span>',
                    server: {
                        process: {
                            url    : '/dashboard/store/upload',
                            method : 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            onload : (response) => response,
                            onerror: (response) => response
                        },
                        revert: {
                            url    : '/dashboard/store/revert',
                            method : 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }
                    }
                });

                // Refresh PerfectScrollbar saat file ditambahkan (tinggi berubah)
                pond.on('addfile',    () => { if (ps) setTimeout(() => ps.update(), 400); });
                pond.on('removefile', () => { if (ps) setTimeout(() => ps.update(), 400); });
            }

            // ══════════════════════════════════════════════════════════════════
            // 2. Select2 Init
            // ══════════════════════════════════════════════════════════════════
            const $modal = $('#modalStore');

            function initSelect2() {
                $modal.find('.select2').each(function() {
                    const $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({
                        dropdownParent          : $modal,
                        placeholder             : $el.data('placeholder') || 'Pilih...',
                        allowClear              : true,
                        width                   : '100%',
                        minimumResultsForSearch : 8
                    });
                });

                // Setelah Select2 render, perbarui ukuran scroll
                if (ps) setTimeout(() => ps.update(), 200);
            }

            // ══════════════════════════════════════════════════════════════════
            // 3. shown.bs.modal — satu handler terpusat
            // ══════════════════════════════════════════════════════════════════
            $(modalEl).on('shown.bs.modal', function() {

                // ── Perfect Scrollbar ──
                if (typeof PerfectScrollbar !== 'undefined' && modalBodyEl) {
                    if (!ps) {
                        ps = new PerfectScrollbar(modalBodyEl, {
                            wheelPropagation: false,
                            suppressScrollX : true
                        });
                    } else {
                        ps.update();
                    }
                }

                // ── FilePond (inisialisasi pertama kali) ──
                initFilePond();

                // ── Select2 ──
                initSelect2();

                // ── Load Provinsi (hanya sekali, event .one sudah di bawah) ──
            });

            // ══════════════════════════════════════════════════════════════════
            // 4. Tab accent
            // ══════════════════════════════════════════════════════════════════
            const $navTabs = $('.loc-nav-tabs');
            $('#tab-gudang').on('shown.bs.tab', () => $navTabs.addClass('active-gudang'));
            $('#tab-toko').on('shown.bs.tab',   () => $navTabs.removeClass('active-gudang'));

            // ══════════════════════════════════════════════════════════════════
            // 5. Helper Wilayah
            // ══════════════════════════════════════════════════════════════════
            function resetWilayah() {
                ['#kabupaten_kota', '#kecamatan', '#desa'].forEach(function(id) {
                    $(id).html('<option value=""></option>')
                         .prop('disabled', true)
                         .trigger('change.select2');
                });
            }

            // ══════════════════════════════════════════════════════════════════
            // 6. Load Provinsi (satu kali saja, pakai .one di modal)
            // ══════════════════════════════════════════════════════════════════
            $modal.one('shown.bs.modal', function() {
                fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                    .then(r => r.json())
                    .then(provinces => {
                        let opts = '<option value=""></option>';
                        provinces.forEach(p => {
                            opts += `<option value="${p.name}" data-id="${p.id}">${p.name}</option>`;
                        });
                        $('#provinsi').html(opts).trigger('change.select2');
                        if (ps) ps.update();
                    });
            });

            // ══════════════════════════════════════════════════════════════════
            // 7. Cascading Wilayah
            // ══════════════════════════════════════════════════════════════════
            $('#provinsi').on('change', function() {
                const idProv = $(this).find(':selected').data('id');
                resetWilayah();
                if (!idProv) return;
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`)
                    .then(r => r.json())
                    .then(list => {
                        let opts = '<option value=""></option>';
                        list.forEach(k => {
                            opts += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
                        });
                        $('#kabupaten_kota').html(opts).prop('disabled', false).trigger('change.select2');
                    });
            });

            $('#kabupaten_kota').on('change', function() {
                const idKab = $(this).find(':selected').data('id');
                $('#kecamatan, #desa').html('<option value=""></option>')
                                     .prop('disabled', true)
                                     .trigger('change.select2');
                if (!idKab) return;
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKab}.json`)
                    .then(r => r.json())
                    .then(list => {
                        let opts = '<option value=""></option>';
                        list.forEach(k => {
                            opts += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
                        });
                        $('#kecamatan').html(opts).prop('disabled', false).trigger('change.select2');
                    });
            });

            $('#kecamatan').on('change', function() {
                const idKec = $(this).find(':selected').data('id');
                $('#desa').html('<option value=""></option>').prop('disabled', true).trigger('change.select2');
                if (!idKec) return;
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${idKec}.json`)
                    .then(r => r.json())
                    .then(list => {
                        let opts = '<option value=""></option>';
                        list.forEach(d => {
                            opts += `<option value="${d.name}" data-id="${d.id}">${d.name}</option>`;
                        });
                        $('#desa').html(opts).prop('disabled', false).trigger('change.select2');
                    });
            });

            // ══════════════════════════════════════════════════════════════════
            // 8. Modal Tambah
            // ══════════════════════════════════════════════════════════════════
            $('.btn-add').on('click', function() {
                $('#modalStoreTitle').html(
                    '<span class="modal-title-icon"><i class="bx bx-map-pin"></i></span> Tambah Lokasi Baru'
                );
                $('#formStore')[0].reset();
                $('#formStore').attr('action', "{{ route('toko.store') }}");
                $('#methodContainer').html('');

                // Reset field — trigger change.select2 untuk UI dropdown saja
                $('#type').val('toko').trigger('change');
                $('#pic_id').val('').trigger('change.select2');
                $('#provinsi').val('').trigger('change.select2');
                resetWilayah();
                if (pond) pond.removeFiles();

                // data-bs-toggle sudah handle .show(), tidak perlu panggil lagi
            });

            // ══════════════════════════════════════════════════════════════════
            // 9. Modal Edit
            // ══════════════════════════════════════════════════════════════════
            $(document).on('click', '.btn-edit', function() {
                const data = JSON.parse($(this).attr('data-store'));

                $('#modalStoreTitle').html(
                    '<span class="modal-title-icon"><i class="bx bx-edit"></i></span> Edit Data Lokasi'
                );
                $('#formStore').attr('action', `/dashboard/store/${data.id}`);
                $('#methodContainer').html('<input type="hidden" name="_method" value="PUT">');

                $('#name_toko').val(data.name_toko);
                $('#telepon').val(data.telepon  || '');
                $('#alamat').val(data.alamat    || '');
                $('#latitude').val(data.latitude  || '');
                $('#longitude').val(data.longitude || '');
                $('#is_active').prop('checked', data.is_active == 1);
                $('#type').val(data.type || 'toko').trigger('change');
                $('#pic_id').val(data.pic_id || '').trigger('change.select2');

                const isiWilayah = () => {
                    if (!data.provinsi) return;

                    $('#provinsi').val(data.provinsi).trigger('change.select2');

                    const provOpt = $('#provinsi option').filter(function() {
                        return $(this).val() === data.provinsi;
                    });
                    if (!provOpt.length) return;

                    const idProv = provOpt.data('id');

                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`)
                        .then(r => r.json())
                        .then(regencies => {
                            let opts = '<option value=""></option>';
                            regencies.forEach(k => {
                                opts += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
                            });
                            $('#kabupaten_kota').html(opts).prop('disabled', false).trigger('change.select2');

                            if (!data.kabupaten_kota) return;
                            $('#kabupaten_kota').val(data.kabupaten_kota).trigger('change.select2');

                            const kabOpt = $('#kabupaten_kota option').filter(function() {
                                return $(this).val() === data.kabupaten_kota;
                            });
                            if (!kabOpt.length) return;

                            const idKab = kabOpt.data('id');

                            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKab}.json`)
                                .then(r => r.json())
                                .then(districts => {
                                    let opts = '<option value=""></option>';
                                    districts.forEach(k => {
                                        opts += `<option value="${k.name}" data-id="${k.id}">${k.name}</option>`;
                                    });
                                    $('#kecamatan').html(opts).prop('disabled', false).trigger('change.select2');

                                    if (!data.kecamatan) return;
                                    $('#kecamatan').val(data.kecamatan).trigger('change.select2');

                                    const kecOpt = $('#kecamatan option').filter(function() {
                                        return $(this).val() === data.kecamatan;
                                    });
                                    if (!kecOpt.length) return;

                                    const idKec = kecOpt.data('id');

                                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${idKec}.json`)
                                        .then(r => r.json())
                                        .then(villages => {
                                            let opts = '<option value=""></option>';
                                            villages.forEach(d => {
                                                opts += `<option value="${d.name}" data-id="${d.id}">${d.name}</option>`;
                                            });
                                            $('#desa').html(opts).prop('disabled', false).trigger('change.select2');
                                            if (data.desa) {
                                                $('#desa').val(data.desa).trigger('change.select2');
                                            }
                                            if (ps) ps.update();
                                        });
                                });
                        });
                };

                const tungguDanIsi = () => {
                    if ($('#provinsi option').length > 1) {
                        isiWilayah();
                    } else {
                        setTimeout(tungguDanIsi, 150);
                    }
                };
                tungguDanIsi();

                if (pond) pond.removeFiles();

                // Tampilkan modal via Instance (FIX: cegah double-modal)
                modalStoreInstance.show();
            });

        });
    </script>
@endsection
