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


    <div class="row g-4 align-items-stretch ">
        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white text-opacity-75 mb-0 text-sm fw-medium">Toko &amp; Gudang</p>
                        <h2 class="text-white mb-0 fw-bold" id="resumeTotaluser">
                            {{ $tokos->count() + $gudangs->count() }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Tombol Tambah --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body">

                    {{-- Form filter: sekarang AJAX (live-filter), fallback GET tetap jalan
                         jika JS mati / user menekan Enter sebelum JS siap --}}
                    <form id="filterForm" action="{{ route('toko.index') }}" method="GET" class="row g-3 align-items-end">

                        {{-- Filter Pencarian --}}
                        <div class="col-12 col-sm-3 col-md-4">
                            <label class="form-label mb-1 text-muted">Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama Toko, alamat..."
                                value="{{ request('search') }}" autocomplete="off">
                        </div>

                        {{-- Filter Daerah --}}
                        <div class="col-12 col-sm-3 col-md-3">
                            <label class="form-label mb-1 text-muted">Daerah / Wilayah</label>
                            <input type="text" name="daerah" class="form-control" placeholder="Prov, Kota, Kec..."
                                value="{{ request('daerah') }}" autocomplete="off">
                        </div>

                        {{-- Filter Status --}}
                        <div class="col-8 col-sm-3 col-md-2">
                            <label class="form-label mb-1 text-muted"
                                style="font-size: 0.75rem; text-transform: uppercase;">Status</label>
                            <select name="status" class="form-select form-select-sm select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua</option>
                                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Nonaktif</option>
                            </select>
                        </div>

                        {{-- Tombol Terapkan Filter (fallback) & Reset --}}
                        <div class="col-4 col-sm-auto">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-2" title="Terapkan Filter">
                                    <i class="bx bx-search fs-5" id="filterIcon"></i>
                                </button>
                                <a href="{{ route('toko.index') }}" id="filterResetBtn"
                                    class="btn btn-outline-secondary px-2 {{ request()->anyFilled(['search', 'daerah', 'status']) ? '' : 'd-none' }}"
                                    title="Reset Filter">
                                    <i class="bx bx-reset fs-5"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Tombol Tambah --}}
                        <div class="col-12 col-sm-auto ms-sm-auto mt-3 mt-sm-0">
                            <button type="button" class="btn btn-outline-blue w-100 px-2 btn-add" data-bs-toggle="modal"
                                data-bs-target="#modalStore" title="Tambah Lokasi Baru">
                                <i class="bx bx-plus-circle fs-5"></i>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav loc-nav-tabs" id="locTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active" id="tab-toko" role="tab"
                                data-bs-toggle="tab" data-bs-target="#navs-toko" aria-controls="navs-toko"
                                aria-selected="true">
                                <i class="bx bx-store"></i>
                                Daftar Toko
                                <span class="loc-tab-badge" id="tokoBadgeCount">{{ $tokos->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" id="tab-gudang" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-gudang" aria-controls="navs-gudang" aria-selected="false">
                                <i class="bx bx-building-house"></i>
                                Daftar Gudang
                                <span class="loc-tab-badge" id="gudangBadgeCount">{{ $gudangs->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0">

                        {{-- ══ TAB TOKO ══ --}}
                        <div class="tab-pane fade show active" id="navs-toko" role="tabpanel">
                            <div class="row g-3 loc-cards-grid" id="tokoCardsGrid">
                                @include('content.hrd.store._toko-cards', ['tokos' => $tokos])
                            </div>
                        </div>

                        {{-- ══ TAB GUDANG ══ --}}
                        <div class="tab-pane fade" id="navs-gudang" role="tabpanel">
                            <div class="row g-3 loc-cards-grid" id="gudangCardsGrid">
                                @include('content.hrd.store._gudang-cards', ['gudangs' => $gudangs])
                            </div>
                        </div>

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
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
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
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content loc-emp-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0">Atur Anggota</h5>
                        <small class="text-muted" id="memberStoreName">-</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMembers" class="d-flex flex-column" style="min-height:0;">
                    @csrf
                    <input type="hidden" id="memberStoreId" name="store_id">

                    <div class="loc-emp-toolbar">
                        <div class="loc-emp-search">
                            <i class="bx bx-search"></i>
                            <input type="text" id="memberSearchInput" placeholder="Cari nama atau jabatan...">
                        </div>
                        <button type="button" class="loc-emp-selectall" id="memberSelectAllBtn">Pilih Semua</button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="loc-emp-list" id="employeeListContainer"></div>
                    </div>
                    <div class="modal-footer d-flex align-items-center">
                        <span class="loc-emp-counter me-auto" id="memberSelectedCount">0 dipilih</span>
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

    {{-- ── Panel Popover Detail Anggota (satu instance, dipakai ulang) ── --}}
    <div class="loc-anggota-popover" id="locAnggotaPopover">
        <div class="loc-anggota-popover-header">
            <span id="locAnggotaPopoverTitle">Anggota</span>
            <button type="button" class="btn-close" style="transform:scale(.75)" aria-label="Close"
                id="locAnggotaPopoverClose"></button>
        </div>
        <div class="loc-anggota-popover-body" id="locAnggotaPopoverBody"></div>
    </div>

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
                        // FIX: URL relatif diganti absolut (lihat alasan yang sama di formStore.action)
                        url: '{{ url('toko/upload') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        onload: (response) => response,
                        onerror: (response) => response,
                    },
                    revert: {
                        url: '{{ url('toko/revert') }}',
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
            // FIX: dibungkus jadi fungsi (bukan langsung querySelectorAll+forEach)
            // supaya bisa dipanggil ulang setelah kartu di-refresh via AJAX filter,
            // karena listener lama tidak menempel ke elemen HTML yang baru disisipkan.
            window.wireEditButtons = function() {
                document.querySelectorAll('.btn-edit').forEach(button => {
                    button.addEventListener('click', function() {
                        const data = JSON.parse(this.getAttribute('data-store'));
                        const logoUrl = this.getAttribute(
                            'data-logo-url'); // <-- Mengambil URL lengkap gambar dari Blade

                        modalTitle.textContent = 'Edit Data Lokasi';
                        // FIX: sebelumnya URL relatif `toko/${id}` yang resolusinya
                        // tergantung ada/tidaknya trailing slash pada URL halaman saat ini.
                        // Pakai URL absolut dari route() supaya selalu benar.
                        formStore.action = `{{ url('toko') }}/${data.id}`;
                        methodContainer.innerHTML =
                            '<input type="hidden" name="_method" value="PUT">';

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
                        const previewContainer = document.getElementById(
                            'existing-logo-preview');
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
                            filepondWrapper.style.display =
                                'block'; // Munculkan form unggah FilePond
                        }

                        fillRegionForEdit(data);
                        pond.removeFiles();

                        // Tampilkan modal
                        var myModal = new bootstrap.Modal(document.getElementById(
                            'modalStore'));
                        myModal.show();
                    });
                });
            }; // end wireEditButtons

            wireEditButtons(); // pasang listener untuk kartu yang di-render server-side

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
            const container = document.getElementById('employeeListContainer');
            const searchInput = document.getElementById('memberSearchInput');
            const selectAllBtn = document.getElementById('memberSelectAllBtn');
            const counterEl = document.getElementById('memberSelectedCount');

            function escapeHtml(str) {
                return String(str ?? '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [m]));
            }

            function initials(name) {
                if (!name) return '?';
                return name.trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
            }

            function updateCounter() {
                const count = container.querySelectorAll('.loc-emp-input:checked').length;
                counterEl.textContent = `${count} dipilih`;
            }

            function visibleItems() {
                return [...container.querySelectorAll('.loc-emp-item')].filter(i => i.style.display !== 'none');
            }

            function refreshSelectAllLabel() {
                const items = visibleItems();
                const allChecked = items.length > 0 && items.every(i => i.querySelector('.loc-emp-input').checked);
                selectAllBtn.textContent = allChecked ? 'Batal Semua' : 'Pilih Semua';
            }

            // Toggle visual "is-selected" + counter setiap checkbox berubah
            // (delegasi di container yang statis, jadi tetap jalan walau isinya diganti-ganti)
            container.addEventListener('change', function(e) {
                if (!e.target.matches('.loc-emp-input')) return;
                e.target.closest('.loc-emp-item').classList.toggle('is-selected', e.target.checked);
                updateCounter();
                refreshSelectAllLabel();
            });

            // Cari nama / jabatan secara live
            searchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                container.querySelectorAll('.loc-emp-item').forEach(item => {
                    item.style.display = item.dataset.search.includes(q) ? '' : 'none';
                });
                refreshSelectAllLabel();
            });

            // Pilih/batalkan semua yang sedang terlihat (menghormati hasil pencarian)
            selectAllBtn.addEventListener('click', function() {
                const items = visibleItems();
                const allChecked = items.length > 0 && items.every(i => i.querySelector('.loc-emp-input')
                    .checked);
                items.forEach(item => {
                    const input = item.querySelector('.loc-emp-input');
                    input.checked = !allChecked;
                    item.classList.toggle('is-selected', input.checked);
                });
                updateCounter();
                refreshSelectAllLabel();
            });

            // FIX: dibungkus jadi fungsi supaya bisa dipanggil ulang setelah
            // kartu di-refresh via AJAX filter (lihat alasan yang sama di wireEditButtons)
            window.wireMemberButtons = function() {
                document.querySelectorAll('.btn-members').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const storeId = this.dataset.id;

                        // Reset toolbar setiap kali modal dibuka
                        searchInput.value = '';
                        selectAllBtn.textContent = 'Pilih Semua';
                        counterEl.textContent = '0 dipilih';

                        container.innerHTML =
                            '<div class="loc-emp-loading"><i class="bx bx-loader-alt bx-spin fs-3"></i><br>Memuat data...</div>';
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
                                document.getElementById('memberStoreName').textContent =
                                    data.store_name;
                                document.getElementById('memberStoreId').value = storeId;

                                // ✅ Fix Bug 3: Pastikan employees adalah array
                                const employees = Array.isArray(data.employees) ? data
                                    .employees : [];
                                let html = '';

                                employees.forEach(emp => {
                                    const checked = emp.is_member ? 'checked' : '';
                                    const selectedClass = emp.is_member ?
                                        'is-selected' : '';
                                    const avatarHtml = emp.avatar ?
                                        `<img class="loc-emp-avatar" src="${escapeHtml(emp.avatar)}" alt="${escapeHtml(emp.name)}">` :
                                        `<div class="loc-emp-avatar loc-emp-avatar-initial">${escapeHtml(initials(emp.name))}</div>`;

                                    let tagHtml = '';
                                    if (emp.current_store === 'Belum Ditempatkan') {
                                        tagHtml =
                                            `<span class="loc-emp-tag loc-emp-tag-muted">Belum ditempatkan</span>`;
                                    } else if (emp.current_store !== data
                                        .store_name) {
                                        tagHtml =
                                            `<span class="loc-emp-tag loc-emp-tag-warning" title="Saat ini di ${escapeHtml(emp.current_store)}">${escapeHtml(emp.current_store)}</span>`;
                                    }

                                    html += `
                    <label class="loc-emp-item ${selectedClass}" data-search="${escapeHtml((emp.name + ' ' + emp.jabatan).toLowerCase())}">
                        <input class="loc-emp-input" type="checkbox" name="user_ids[]" value="${emp.id}" ${checked}>
                        <span class="loc-emp-check"><i class="bx bx-check"></i></span>
                        ${avatarHtml}
                        <span class="loc-emp-info">
                            <span class="loc-emp-name">${escapeHtml(emp.name)}</span>
                            <span class="loc-emp-jabatan">${escapeHtml(emp.jabatan)}</span>
                        </span>
                        ${tagHtml}
                    </label>`;
                                });

                                container.innerHTML = html ||
                                    '<div class="loc-emp-empty"><i class="bx bx-user-x fs-3"></i><br>Belum ada data karyawan.</div>';

                                updateCounter();
                                refreshSelectAllLabel();
                            })
                            // ✅ Fix Bug 5: Tambah .catch() agar error terlihat di modal
                            .catch(err => {
                                container.innerHTML = `
                    <div class="loc-emp-error">
                        <i class="bx bx-error-circle fs-2 d-block mb-2"></i>
                        Gagal memuat data anggota.<br>
                        <small class="text-muted">${escapeHtml(err.message)}</small>
                    </div>`;
                                console.error('Fetch members error:', err);
                            });
                    });
                });
            }; // end wireMemberButtons

            wireMemberButtons(); // pasang listener untuk kartu yang di-render server-side

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

    {{-- ── AJAX Live Filter (tanpa perlu klik tombol) ────────────────────── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = filterForm.querySelector('[name="search"]');
            const daerahInput = filterForm.querySelector('[name="daerah"]');
            const statusSelect = filterForm.querySelector('[name="status"]');
            const filterIcon = document.getElementById('filterIcon');
            const filterResetBtn = document.getElementById('filterResetBtn');

            const tokoGrid = document.getElementById('tokoCardsGrid');
            const gudangGrid = document.getElementById('gudangCardsGrid');
            const tokoBadge = document.getElementById('tokoBadgeCount');
            const gudangBadge = document.getElementById('gudangBadgeCount');
            const totalBadge = document.getElementById('resumeTotaluser');

            let debounceTimer = null;
            let activeRequest = null;

            function setLoading(isLoading) {
                tokoGrid.style.opacity = isLoading ? 0.5 : 1;
                gudangGrid.style.opacity = isLoading ? 0.5 : 1;
                filterIcon.classList.toggle('bx-search', !isLoading);
                filterIcon.classList.toggle('bx-loader-alt', isLoading);
                filterIcon.classList.toggle('bx-spin', isLoading);
            }

            function runFilter() {
                const params = new URLSearchParams(new FormData(filterForm)).toString();

                // Batalkan request sebelumnya jika masih berjalan (mencegah race
                // condition: hasil filter lama tiba lebih lambat lalu menimpa yang baru)
                if (activeRequest) activeRequest.abort();
                activeRequest = new AbortController();

                setLoading(true);

                fetch(`${filterForm.action}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        signal: activeRequest.signal,
                    })
                    .then(r => {
                        if (!r.ok) throw new Error(`Server error: ${r.status}`);
                        return r.json();
                    })
                    .then(data => {
                        tokoGrid.innerHTML = data.tokos_html;
                        gudangGrid.innerHTML = data.gudangs_html;
                        tokoBadge.textContent = data.tokos_count;
                        gudangBadge.textContent = data.gudangs_count;
                        totalBadge.textContent = data.total;

                        // Pasang ulang listener tombol Edit & Atur Anggota
                        // untuk kartu-kartu yang baru saja disisipkan
                        if (typeof window.wireEditButtons === 'function') window.wireEditButtons();
                        if (typeof window.wireMemberButtons === 'function') window.wireMemberButtons();

                        // Toggle tombol reset
                        const hasFilter = filterForm.search.value || filterForm.daerah.value || filterForm
                            .status.value;
                        filterResetBtn.classList.toggle('d-none', !hasFilter);

                        // Update URL browser tanpa reload, biar bisa di-refresh/share
                        window.history.replaceState(null, '', `${filterForm.action}?${params}`);
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            console.error('Gagal memuat filter:', err);
                        }
                    })
                    .finally(() => setLoading(false));
            }

            function debounceFilter(delay = 400) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runFilter, delay);
            }

            // Input teks: debounce supaya tidak fetch di setiap ketikan huruf
            searchInput.addEventListener('input', () => debounceFilter());
            daerahInput.addEventListener('input', () => debounceFilter());

            // Select status: pakai jQuery karena elemen dibungkus Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(statusSelect).on('change', () => debounceFilter(100));
            } else {
                statusSelect.addEventListener('change', () => debounceFilter(100));
            }

            // Fallback: tombol submit / tekan Enter tetap berfungsi tanpa reload
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                runFilter();
            });
        });
    </script>

    {{-- ── Popover Detail Anggota ──────────────────────────────────────────
         Dipasang via event delegation di document, jadi otomatis jalan juga
         untuk kartu-kartu baru hasil AJAX filter tanpa perlu di-wire ulang. --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popover = document.getElementById('locAnggotaPopover');
            const popoverTitle = document.getElementById('locAnggotaPopoverTitle');
            const popoverBody = document.getElementById('locAnggotaPopoverBody');
            const popoverClose = document.getElementById('locAnggotaPopoverClose');

            let currentTrigger = null;
            let currentRequest = null;

            function initials(name) {
                if (!name) return '?';
                return name.trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
            }

            function escapeHtml(str) {
                return String(str ?? '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [m]));
            }

            function positionPopover(trigger) {
                const rect = trigger.getBoundingClientRect();
                const popW = 280;
                let left = rect.right - popW; // rata kanan ke tombol
                if (left < 8) left = 8;
                if (left + popW > window.innerWidth - 8) left = window.innerWidth - popW - 8;

                let top = rect.bottom + 8;
                // Kalau kepotong bawah layar, tampilkan di atas tombol
                if (top + 200 > window.innerHeight) {
                    top = rect.top - 8;
                    popover.style.transform = 'translateY(-100%)';
                    popover.dataset.flip = '1';
                } else {
                    popover.style.transform = '';
                    popover.dataset.flip = '0';
                }

                popover.style.left = `${left}px`;
                popover.style.top = `${top}px`;
            }

            function closePopover() {
                popover.classList.remove('is-visible');
                if (currentTrigger) currentTrigger.classList.remove('is-open');
                currentTrigger = null;
                if (currentRequest) currentRequest.abort();
            }

            function openPopover(trigger) {
                const storeId = trigger.dataset.id;
                const storeName = trigger.dataset.storeName || 'Lokasi';

                currentTrigger = trigger;
                trigger.classList.add('is-open');
                popoverTitle.textContent = `Anggota ${storeName}`;
                popoverBody.innerHTML =
                    '<div class="loc-anggota-popover-loading"><i class="bx bx-loader-alt bx-spin fs-4"></i><br>Memuat...</div>';

                positionPopover(trigger);
                popover.classList.add('is-visible');

                if (currentRequest) currentRequest.abort();
                currentRequest = new AbortController();

                fetch(`/toko/${storeId}/members?members_only=1`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: currentRequest.signal,
                    })
                    .then(r => {
                        if (!r.ok) throw new Error(`Server error: ${r.status}`);
                        return r.json();
                    })
                    .then(data => {
                        const members = Array.isArray(data.employees) ? data.employees : [];

                        if (!members.length) {
                            popoverBody.innerHTML =
                                '<div class="loc-anggota-popover-empty"><i class="bx bx-user-x fs-3"></i><br>Belum ada anggota</div>';
                            return;
                        }

                        popoverBody.innerHTML = members.map(m => `
                            <div class="loc-anggota-popover-item">
                                ${m.avatar
                                    ? `<img class="loc-anggota-popover-avatar" src="${escapeHtml(m.avatar)}" alt="${escapeHtml(m.name)}">`
                                    : `<div class="loc-anggota-popover-avatar">${escapeHtml(initials(m.name))}</div>`}
                                <div>
                                    <div class="loc-anggota-popover-name">${escapeHtml(m.name)}</div>
                                    <div class="loc-anggota-popover-jabatan">${escapeHtml(m.jabatan)}</div>
                                </div>
                            </div>
                        `).join('');
                    })
                    .catch(err => {
                        if (err.name === 'AbortError') return;
                        popoverBody.innerHTML =
                            '<div class="loc-anggota-popover-error"><i class="bx bx-error-circle fs-3"></i><br>Gagal memuat data anggota</div>';
                        console.error('Gagal memuat anggota:', err);
                    });
            }

            // Delegasi klik: bekerja untuk trigger yang ada sekarang MAUPUN
            // yang baru muncul lewat AJAX filter (tidak perlu wiring ulang)
            document.addEventListener('click', function(e) {
                const trigger = e.target.closest('.loc-anggota-trigger');
                if (trigger) {
                    e.stopPropagation();
                    if (currentTrigger === trigger) {
                        closePopover();
                    } else {
                        openPopover(trigger);
                    }
                    return;
                }

                // Klik di luar popover -> tutup
                if (!e.target.closest('.loc-anggota-popover')) {
                    closePopover();
                }
            });

            popoverClose.addEventListener('click', closePopover);

            // Tutup saat scroll / resize / ganti tab / Escape supaya posisi tidak "ngambang"
            window.addEventListener('scroll', closePopover, true);
            window.addEventListener('resize', closePopover);
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closePopover();
            });
            document.querySelectorAll('#locTabs button').forEach(tab => {
                tab.addEventListener('shown.bs.tab', closePopover);
            });
        });
    </script>
@endsection
