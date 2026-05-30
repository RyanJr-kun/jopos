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

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-store.scss'])
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
    <div class="card">
        <div class="card-header">
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
        </div>
        <div class="card-body">
            <div class="tab-content p-0">

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
                                                    <img src="{{ $toko->logo ? Storage::url($toko->logo) : asset('assets/img/default-store.png') }}"
                                                        alt="Logo" class="loc-logo">
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
                                                <div class="dropdown">
                                                    <button class="btn p-0" type="button" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item btn-edit" href="javascript:void(0);"
                                                            data-store='@json($toko)'
                                                            data-logo-url="{{ $toko->logo ? Storage::url($toko->logo) : '' }}">
                                                            <i class="bx bx-edit-alt me-1 text-info"></i> Edit
                                                        </a>
                                                        {{-- FIX: Ganti inline confirm() dengan delete modal component --}}
                                                        <button type="button" class="dropdown-item btn-delete-trigger"
                                                            data-bs-toggle="modal" data-bs-target="#modalDeleteStore"
                                                            data-title="{{ $toko->name_toko }}"
                                                            data-action="{{ route('toko.destroy', $toko->id) }}">
                                                            <i class="bx bx-trash me-1 text-danger"></i> Hapus
                                                        </button>
                                                    </div>
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
                                        <div class="loc-info-row mt-2">
                                            <i class="bx bx-group loc-info-icon"></i>
                                            <span>Anggota: <strong>{{ $toko->employees->count() }} Orang</strong></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-3 btn-members"
                                            data-id="{{ $toko->id }}">
                                            <i class="bx bx-user-plus me-1"></i> Atur Anggota
                                        </button>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="loc-empty-state">
                                    <img class="loc-empty-img"
                                        src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
                                        width="140" alt="Kosong">
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
                                                    <img src="{{ $gudang->logo ? Storage::url($gudang->logo) : asset('assets/img/default-store.png') }}"
                                                        alt="Logo" class="loc-logo">
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
                                                <button class="loc-dropdown-btn" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end loc-dropdown-menu">
                                                    <a class="dropdown-item btn-edit" href="javascript:void(0);"
                                                        data-store='@json($gudang)'
                                                        data-logo-url="{{ $gudang->logo ? Storage::url($gudang->logo) : '' }}">
                                                        <i class="bx bx-edit-alt me-1 text-info"></i> Edit
                                                    </a>
                                                    {{-- FIX: Ganti inline confirm() dengan delete modal component --}}
                                                    <button type="button" class="dropdown-item btn-delete-trigger"
                                                        data-bs-toggle="modal" data-bs-target="#modalDeleteStore"
                                                        data-title="{{ $gudang->name_toko }}"
                                                        data-action="{{ route('toko.destroy', $gudang->id) }}">
                                                        <i class="bx bx-trash text-danger"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="loc-info-row">
                                            <i class="bx bx-map loc-info-icon"></i>
                                            <span>{{ $gudang->full_region }}</span>
                                        </div>
                                        <div class="loc-info-row">
                                            <i class="bx bx-user loc-info-icon"></i>
                                            <span>PIC:
                                                <strong>{{ $gudang->pic->name ?? 'Belum Ditentukan' }}</strong></span>
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

    </div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Modal Tambah / Edit Lokasi ── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalStore" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStoreTitle">Tambah Lokasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formStore" action="{{ route('toko.store') }}" method="POST">
                    @csrf
                    <div id="methodContainer"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Nama Lokasi</label>
                                <input type="text" name="name_toko" id="name_toko" class="form-control"
                                    placeholder="Contoh: JOPOS Cabang Kartasura" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipe Lokasi</label>
                                <select name="type" id="type" class="form-select select2" required>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">PIC (Kepala Toko/Gudang)</label>
                                <select name="pic_id" id="pic_id" class="form-select select2"
                                    data-placeholder="Pilih Penanggung Jawab">
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="name@example.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" id="telepon" class="form-control"
                                    placeholder="0812xxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi</label>
                                <select name="provinsi" id="provinsi" class="form-select select2"
                                    data-placeholder="Pilih Provinsi" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kabupaten / Kota</label>
                                <select name="kabupaten_kota" id="kabupaten_kota" class="form-select select2"
                                    data-placeholder="Pilih Kabupaten" disabled required>
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <select name="kecamatan" id="kecamatan" class="form-select select2"
                                    data-placeholder="Pilih Kecamatan" disabled required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Desa / Kelurahan</label>
                                <select name="desa" id="desa" class="form-select select2"
                                    data-placeholder="Pilih Desa / Kelurahan" disabled required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Jalan Raya No. 123..."></textarea>
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="map_url"><i class="bx bx-link"></i> Link Google Maps</label>
                            <input type="text" name="map_url" id="map_url" class="form-control"
                                placeholder="https://maps.app.goo.gl/xxxx">
                            <small class="text-muted">Gunakan link dari tombol 'Share' di Google Maps.</small>
                        </div>


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

                        <div class="mb-3">
                            <label class="form-label">Logo / Foto Lokasi</label>

                            {{-- Preview Logo Lama (Ditampilkan jika ada) --}}
                            <div id="existing-logo-preview" class="mb-2" style="display: none;">
                                <div class="position-relative d-inline-block">
                                    <img id="preview-img" src="" alt="Current Logo"
                                        style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                    {{-- Tombol Silang (Delete) --}}
                                    <button type="button" id="btn-remove-logo"
                                        class="btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle rounded-circle"
                                        style="width: 24px; height: 24px; padding: 0; line-height: 1;" title="Hapus Logo">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                                <p class="text-muted small mt-1">Logo saat ini. Klik tanda silang (X) untuk menghapus dan
                                    mengunggah baru.</p>
                            </div>

                            {{-- Wrapper FilePond (Akan disembunyikan jika logo lama tampil) --}}
                            <div id="filepond-wrapper">
                                <input type="file" name="logo" class="filepond">
                            </div>

                            {{-- Input hidden sebagai penanda ke Controller apakah user menghapus logo lama --}}
                            <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="is_active">Status Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMembers" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atur Anggota: <span id="memberStoreName" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMembers">
                    @csrf
                    <input type="hidden" id="memberStoreId" name="store_id">
                    <div class="modal-body p-0">
                        <div class="list-group list-group-flush" id="employeeListContainer"
                            style="max-height: 400px; overflow-y: auto;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Form Delete (hidden, di-submit oleh confirm button modal) ── --}}
    <form id="formDelete" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ── Modal Konfirmasi Hapus (menggunakan component delete-modal) ── --}}
    @include('components.delete-modal', [
        'modalId' => 'modalDeleteStore',
        'message' => 'Yakin ingin menghapus lokasi ini?',
        'itemTitleId' => 'deleteStoreName',
        'confirmBtnId' => 'confirmDeleteStore',
    ])

@endsection

@section('page-script')

    {{-- Select2 Init --}}
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10,
                        dropdownParent: $this.closest('.modal').length ?
                            $this.closest('.modal') : $(document.body),
                    });
                });
            } else {
                setTimeout(initSelect2, 100);
            }
        };
        initSelect2();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Referensi elemen wilayah ──────────────────────────────────────
            const provSelect = document.getElementById('provinsi');
            const kabSelect = document.getElementById('kabupaten_kota');
            const kecSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            /**
             * Helper: reset sebuah <select> ke default dan (opsional) disable-nya.
             * Setelah reset, beritahu Select2 agar UI-nya ikut diperbarui.
             */
            function resetSelect(el, placeholder, shouldDisable = true) {
                el.innerHTML = `<option value="">${placeholder}</option>`;
                el.disabled = shouldDisable;
                // FIX: Sinkronkan state Select2 setelah mengubah opsi/disabled
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(el).trigger('change.select2');
                }
            }

            /**
             * Helper: enable sebuah <select> dan beritahu Select2.
             */
            function enableSelect(el) {
                el.disabled = false;
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(el).trigger('change.select2');
                }
            }

            /**
             * Helper: set value sebuah <select> lalu beritahu Select2.
             */
            function setSelectValue(el, value) {
                el.value = value;
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(el).trigger('change.select2');
                }
            }

            // ── 1. Load Provinsi saat halaman dibuka ────────────────────────
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(r => r.json())
                .then(provinces => {
                    provinces.forEach(prov => {
                        const opt = new Option(prov.name, prov.name);
                        opt.dataset.id = prov.id;
                        provSelect.add(opt);
                    });
                    // Refresh Select2 setelah opsi dimuat
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        $('#provinsi').trigger('change.select2');
                    }
                })
                .catch(() => console.warn('Gagal memuat data provinsi.'));

            // ── 2. Provinsi → Kabupaten ──────────────────────────────────────
            // FIX: Gunakan jQuery .on('change') agar kompatibel dengan Select2
            $(provSelect).on('change', function() {
                const selectedOpt = provSelect.options[provSelect.selectedIndex];
                const idProv = selectedOpt ? selectedOpt.dataset.id : null;

                resetSelect(kabSelect, '-- Pilih Kabupaten/Kota --', true);
                resetSelect(kecSelect, '-- Pilih Kecamatan --', true);
                resetSelect(desaSelect, '-- Pilih Desa --', true);

                if (!idProv) return;

                enableSelect(kabSelect);
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`)
                    .then(r => r.json())
                    .then(regencies => {
                        regencies.forEach(kab => {
                            const opt = new Option(kab.name, kab.name);
                            opt.dataset.id = kab.id;
                            kabSelect.add(opt);
                        });
                        // Refresh Select2 setelah opsi dimuat
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#kabupaten_kota').trigger('change.select2');
                        }
                    })
                    .catch(() => console.warn('Gagal memuat data kabupaten.'));
            });

            // ── 3. Kabupaten → Kecamatan ─────────────────────────────────────
            $(kabSelect).on('change', function() {
                const selectedOpt = kabSelect.options[kabSelect.selectedIndex];
                const idKab = selectedOpt ? selectedOpt.dataset.id : null;

                resetSelect(kecSelect, '-- Pilih Kecamatan --', true);
                resetSelect(desaSelect, '-- Pilih Desa --', true);

                if (!idKab) return;

                enableSelect(kecSelect);
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKab}.json`)
                    .then(r => r.json())
                    .then(districts => {
                        districts.forEach(kec => {
                            const opt = new Option(kec.name, kec.name);
                            opt.dataset.id = kec.id;
                            kecSelect.add(opt);
                        });
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#kecamatan').trigger('change.select2');
                        }
                    })
                    .catch(() => console.warn('Gagal memuat data kecamatan.'));
            });

            // ── 4. Kecamatan → Desa ──────────────────────────────────────────
            $(kecSelect).on('change', function() {
                const selectedOpt = kecSelect.options[kecSelect.selectedIndex];
                const idKec = selectedOpt ? selectedOpt.dataset.id : null;

                resetSelect(desaSelect, '-- Pilih Desa --', true);

                if (!idKec) return;

                enableSelect(desaSelect);
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${idKec}.json`)
                    .then(r => r.json())
                    .then(villages => {
                        villages.forEach(desa => {
                            desaSelect.add(new Option(desa.name, desa.name));
                        });
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#desa').trigger('change.select2');
                        }
                    })
                    .catch(() => console.warn('Gagal memuat data desa.'));
            });

            /**
             * FIX: Isi dropdown wilayah secara bertahap (async chain) untuk mode Edit.
             * Tidak bisa langsung set .value karena opsi kab/kec/desa belum ada di DOM —
             * harus tunggu fetch selesai di setiap level sebelum lanjut ke level berikutnya.
             */
            function fillRegionForEdit(data) {
                // Cari opsi provinsi yang namanya cocok (opsi sudah dimuat saat page load)
                const provOpt = Array.from(provSelect.options).find(o => o.value === data.provinsi);
                if (!provOpt) {
                    // Provinsi tidak ditemukan, reset saja
                    resetSelect(kabSelect, '-- Pilih Kabupaten/Kota --');
                    resetSelect(kecSelect, '-- Pilih Kecamatan --');
                    resetSelect(desaSelect, '-- Pilih Desa --');
                    return;
                }

                // Set provinsi
                setSelectValue(provSelect, data.provinsi);
                const idProv = provOpt.dataset.id;

                // Load kabupaten
                resetSelect(kabSelect, '-- Pilih Kabupaten/Kota --', true);
                resetSelect(kecSelect, '-- Pilih Kecamatan --', true);
                resetSelect(desaSelect, '-- Pilih Desa --', true);

                if (!idProv) return;
                enableSelect(kabSelect);

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`)
                    .then(r => r.json())
                    .then(regencies => {
                        regencies.forEach(kab => {
                            const opt = new Option(kab.name, kab.name);
                            opt.dataset.id = kab.id;
                            kabSelect.add(opt);
                        });
                        setSelectValue(kabSelect, data.kabupaten_kota);

                        const kabOpt = Array.from(kabSelect.options).find(o => o.value === data.kabupaten_kota);
                        const idKab = kabOpt ? kabOpt.dataset.id : null;
                        if (!idKab) return;

                        enableSelect(kecSelect);
                        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKab}.json`)
                            .then(r => r.json())
                            .then(districts => {
                                districts.forEach(kec => {
                                    const opt = new Option(kec.name, kec.name);
                                    opt.dataset.id = kec.id;
                                    kecSelect.add(opt);
                                });
                                setSelectValue(kecSelect, data.kecamatan);

                                const kecOpt = Array.from(kecSelect.options).find(o => o.value === data
                                    .kecamatan);
                                const idKec = kecOpt ? kecOpt.dataset.id : null;
                                if (!idKec) return;

                                enableSelect(desaSelect);
                                fetch(
                                        `https://www.emsifa.com/api-wilayah-indonesia/api/villages/${idKec}.json`
                                    )
                                    .then(r => r.json())
                                    .then(villages => {
                                        villages.forEach(desa => {
                                            desaSelect.add(new Option(desa.name, desa.name));
                                        });
                                        setSelectValue(desaSelect, data.desa);
                                    })
                                    .catch(() => console.warn('Gagal memuat data desa.'));
                            })
                            .catch(() => console.warn('Gagal memuat data kecamatan.'));
                    })
                    .catch(() => console.warn('Gagal memuat data kabupaten.'));
            }

            // ── FilePond Init ────────────────────────────────────────────────
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginImagePreview
            );



            const pond = FilePond.create(document.querySelector('.filepond'), {
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                server: {
                    process: {
                        url: 'toko/upload',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        onload: (response) => response,
                        onerror: (response) => response,
                    },
                    revert: {
                        url: 'toko/revert',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    },
                },
            });


            document.getElementById('btn-remove-logo').addEventListener('click', function() {
                // Sembunyikan preview gambar
                document.getElementById('existing-logo-preview').style.display = 'none';
                // Tampilkan area upload FilePond
                document.getElementById('filepond-wrapper').style.display = 'block';
                // Beri sinyal ke backend bahwa gambar lama dihapus
                document.getElementById('remove_logo').value = '1';
            });

            // ── Referensi form & elemen modal ────────────────────────────────
            // FIX: Sebelumnya menggunakan `formtoko` yang tidak pernah didefinisikan.
            //      Gunakan getElementById sesuai id form yang benar: 'formStore'.
            const formStore = document.getElementById('formStore');
            const methodContainer = document.getElementById('methodContainer');
            const modalTitle = document.getElementById('modalStoreTitle');

            // ── Tombol Tambah: reset form ke mode Create ─────────────────────
            document.querySelector('.btn-add').addEventListener('click', function() {
                modalTitle.textContent = 'Tambah Lokasi Baru';
                formStore.reset();
                formStore.action = "{{ route('toko.store') }}";
                methodContainer.innerHTML = '';
                pond.removeFiles();

                // Reset tampilan gambar & filepond
                document.getElementById('existing-logo-preview').style.display = 'none';
                document.getElementById('filepond-wrapper').style.display = 'block';
                document.getElementById('remove_logo').value = '0';

                // Reset wilayah ke kondisi awal
                resetSelect(kabSelect, '-- Pilih Kabupaten/Kota --', true);
                resetSelect(kecSelect, '-- Pilih Kecamatan --', true);
                resetSelect(desaSelect, '-- Pilih Desa --', true);
                setSelectValue(provSelect, '');

                // Reset Select2 untuk field lain
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#type').val('toko').trigger('change.select2');
                    $('#pic_id').val('').trigger('change.select2');
                }
            });

            // ── Tombol Edit: isi form dengan data existing ───────────────────
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const data = JSON.parse(this.getAttribute('data-store'));
                    const logoUrl = this.getAttribute(
                        'data-logo-url'); // <-- Mengambil URL lengkap gambar dari Blade

                    modalTitle.textContent = 'Edit Data Lokasi';
                    formStore.action = `toko/${data.id}`;
                    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                    // Isi field biasa
                    document.getElementById('name_toko').value = data.name_toko || '';
                    document.getElementById('telepon').value = data.telepon || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('alamat').value = data.alamat || '';
                    document.getElementById('map_url').value = data.map_url || '';
                    document.getElementById('latitude').value = data.latitude || '';
                    document.getElementById('longitude').value = data.longitude || '';
                    document.getElementById('is_active').checked = data.is_active == 1;

                    // Select2 fields
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        $('#type').val(data.type || 'toko').trigger('change.select2');
                        $('#pic_id').val(data.pic_id || '').trigger('change.select2');
                    } else {
                        document.getElementById('type').value = data.type || 'toko';
                        document.getElementById('pic_id').value = data.pic_id || '';
                    }

                    // ── Perbaikan Logika Gambar Modal Edit ──
                    const previewContainer = document.getElementById('existing-logo-preview');
                    const previewImg = document.getElementById('preview-img');
                    const filepondWrapper = document.getElementById(
                        'filepond-wrapper'); // Ambil elemen filepond

                    // Reset penanda hapus logo
                    document.getElementById('remove_logo').value = '0';

                    // Cek jika logoUrl berisi string (berarti gambar ada)
                    if (logoUrl) {
                        previewImg.src = logoUrl; // Masukkan URL asli R2
                        previewContainer.style.display = 'block'; // Tampilkan gambar lama
                        filepondWrapper.style.display = 'none'; // Sembunyikan FilePond
                    } else {
                        previewContainer.style.display = 'none'; // Sembunyikan frame gambar
                        filepondWrapper.style.display = 'block'; // Munculkan form unggah FilePond
                    }

                    fillRegionForEdit(data);
                    pond.removeFiles();

                    // Tampilkan modal
                    var myModal = new bootstrap.Modal(document.getElementById('modalStore'));
                    myModal.show();
                });
            });

            // ── Delete Modal: wiring tombol konfirmasi ───────────────────────
            const formDelete = document.getElementById('formDelete');
            const confirmDeleteBtn = document.getElementById('confirmDeleteStore');
            const modalDeleteEl = document.getElementById('modalDeleteStore');

            // Saat modal hapus akan tampil, simpan action URL ke form hidden
            modalDeleteEl.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                if (trigger) {
                    const action = trigger.getAttribute('data-action');
                    formDelete.action = action;
                }
            });

            // Saat tombol "Ya, Hapus" diklik, submit form hidden
            confirmDeleteBtn.addEventListener('click', function() {
                formDelete.submit();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalMembersEl = document.getElementById('modalMembers');
            const modalMembers = new bootstrap.Modal(modalMembersEl);

            document.querySelectorAll('.btn-members').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const storeId = this.dataset.id;
                    const container = document.getElementById('employeeListContainer');

                    // Tampilkan loading
                    container.innerHTML =
                        '<div class="p-4 text-center"><i class="bx bx-loader-alt bx-spin fs-2"></i><br>Memuat data...</div>';
                    modalMembers.show();

                    // ✅ Fix Bug 1: Gunakan URL absolut dengan leading slash
                    fetch(`/toko/${storeId}/members`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        // ✅ Fix Bug 3: Cek response.ok sebelum parse JSON
                        .then(r => {
                            if (!r.ok) throw new Error(`Server error: ${r.status}`);
                            return r.json();
                        })
                        .then(data => {
                            document.getElementById('memberStoreName').textContent = data
                                .store_name;
                            document.getElementById('memberStoreId').value = storeId;

                            // ✅ Fix Bug 3: Pastikan employees adalah array
                            const employees = Array.isArray(data.employees) ? data.employees :
                            [];
                            let html = '';

                            employees.forEach(emp => {
                                html += `
                    <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                        <div class="d-flex align-items-center">
                            <input class="form-check-input me-3" type="checkbox"
                                   name="user_ids[]" value="${emp.id}"
                                   ${emp.is_member ? 'checked' : ''}>
                            <div>
                                <div class="fw-bold text-dark">${emp.name}</div>
                                <small class="text-muted">${emp.jabatan} •
                                    <span class="badge bg-label-secondary">${emp.current_store}</span>
                                </small>
                            </div>
                        </div>
                    </label>`;
                            });

                            container.innerHTML = html ||
                                '<div class="p-4 text-center text-muted">Belum ada data karyawan.</div>';
                        })
                        // ✅ Fix Bug 5: Tambah .catch() agar error terlihat di modal
                        .catch(err => {
                            container.innerHTML = `
                    <div class="p-4 text-center text-danger">
                        <i class="bx bx-error-circle fs-2 d-block mb-2"></i>
                        Gagal memuat data anggota.<br>
                        <small class="text-muted">${err.message}</small>
                    </div>`;
                            console.error('Fetch members error:', err);
                        });
                });
            });

            document.getElementById('formMembers').addEventListener('submit', function(e) {
                e.preventDefault();
                const storeId = document.getElementById('memberStoreId').value;
                const formData = new FormData(this);

                // ✅ Fix Bug 1 & 2: URL absolut + method POST sesuai route baru
                fetch(`/toko/${storeId}/members/update`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(r => {
                        if (!r.ok) throw new Error(`Server error: ${r.status}`);
                        return r.json();
                    })
                    .then(response => {
                        if (response.success) {
                            modalMembers.hide();
                            location.reload();
                        }
                    })
                    .catch(err => {
                        alert('Gagal menyimpan perubahan: ' + err.message);
                        console.error('Update members error:', err);
                    });
            });
        });
    </script>
@endsection
