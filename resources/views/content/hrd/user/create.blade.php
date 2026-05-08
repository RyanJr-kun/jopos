@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah User Baru')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        /* ===== SNEAT CUSTOM OVERRIDES ===== */

        /* Section Header */
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.625rem;
            border-bottom: 1px solid #e7e7e8;
        }

        .section-header .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: rgba(105, 108, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #696cff;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .section-header h6 {
            margin: 0;
            font-size: 0.9375rem;
            font-weight: 600;
            color: #566a7f;
        }

        /* Avatar Upload Area */
        .avatar-upload-wrapper {
            background: #f8f8ff;
            border: 1.5px dashed #c4c4ff;
            border-radius: 10px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 200px;
        }

        .avatar-upload-wrapper .filepond--root {
            width: 100%;
            margin-bottom: 0;
        }

        /* FilePond custom */
        .filepond--panel-root {
            background-color: transparent !important;
            border: none !important;
        }

        .filepond--drop-label {
            color: #8592a3 !important;
        }

        .filepond--label-action {
            color: #696cff !important;
            text-decoration-color: #696cff !important;
        }

        /* Form Label */
        .form-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #566a7f;
            margin-bottom: 0.375rem;
        }

        .form-label .req {
            color: #ff3e1d;
            margin-left: 2px;
        }

        /* Input hint text */
        .form-text {
            font-size: 0.75rem;
            color: #a1acb8;
        }

        /* Status switch */
        .status-switch-card {
            background: #f8f9fa;
            border: 1px solid #e7e7e8;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-switch-card .status-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #566a7f;
        }

        .status-switch-card .status-desc {
            font-size: 0.75rem;
            color: #a1acb8;
            margin-top: 1px;
        }

        /* Action Bar */
        .form-action-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            padding-top: 1.25rem;
            margin-top: 1rem;
            border-top: 1px solid #e7e7e8;
        }

        /* Responsive: stack action bar on mobile */
        @media (max-width: 575.98px) {
            .form-action-bar {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .form-action-bar .btn {
                width: 100%;
                justify-content: center;
            }

            .avatar-upload-wrapper {
                min-height: 160px;
                padding: 1rem;
            }
        }

        /* Divider with label */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.75rem 0;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e7e7e8;
        }

        .section-divider span {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #a1acb8;
            white-space: nowrap;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@section('content')
    <div class="card shadow-none border-0">
        <div class="card-body p-3 p-sm-4 p-xl-5">
            <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                {{-- ============================================================
                     BAGIAN 1: FOTO & DATA AKUN
                ============================================================ --}}
                <div class="row g-4 align-items-start">

                    {{-- Foto Pengguna --}}
                    <div class="col-12 col-md-4 col-xl-3">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="bx bx-image-alt"></i>
                            </div>
                            <h6>Foto Pengguna</h6>
                        </div>
                        <div class="avatar-upload-wrapper">
                            <input type="file" class="filepond" name="avatar" id="image">
                        </div>
                        <p class="form-text text-center mt-2">PNG / JPG, maks. 2 MB. <br>Rasio 1:1 disarankan.</p>
                    </div>

                    {{-- Data Akun --}}
                    <div class="col-12 col-md-8 col-xl-9">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="bx bx-user-circle"></i>
                            </div>
                            <h6>Data Akun</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="name" class="form-label">Nama Lengkap <span class="req">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="username" class="form-label">Username <span class="req">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-at"></i></span>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" placeholder="username" value="{{ old('username') }}"
                                        required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="email" class="form-label">Email <span class="req">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="contoh@email.com"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="password" class="form-label">Password <span class="req">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror" type="password"
                                        placeholder="Min. 5 karakter" required>
                                    <span class="input-group-text cursor-pointer" id="toggle-password">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="role_name" class="form-label">Role <span class="req">*</span></label>
                                <select name="role_name"
                                    class="form-select select2 @error('role_name') is-invalid @enderror" id="role_name"
                                    required>
                                    <option value="" disabled selected>Pilih Role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}" @selected(old('role_name') == $role->name)>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6 d-flex align-items-end">
                                <div class="status-switch-card w-100">
                                    <div>
                                        <div class="status-label">Status Akun</div>
                                        <div class="status-desc">Aktifkan agar user bisa login</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="status" value="0">
                                        <input id="status_toggle" class="form-check-input" type="checkbox" name="status"
                                            value="1" @checked(old('status', true))>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="section-divider">
                    <span>Data Karyawan</span>
                </div>

                {{-- ============================================================
                     BAGIAN 2: DATA KARYAWAN
                ============================================================ --}}
                <div class="section-header">
                    <div class="section-icon">
                        <i class="bx bx-id-card"></i>
                    </div>
                    <h6>Data Karyawan</h6>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-xl-4">
                        <label for="nik" class="form-label">NIK / ID Karyawan</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                            name="nik" placeholder="Nomor Induk Karyawan" value="{{ old('nik') }}">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-select select2 @error('jabatan') is-invalid @enderror"
                            id="jabatan">
                            <option value="">— Pilih Jabatan —</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->value }}" @selected(old('jabatan') === $jabatan->value)>
                                    {{ $jabatan->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                        @error('jabatan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <label for="kontak" class="form-label">Nomor Kontak</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-phone"></i></span>
                            <input type="tel" class="form-control @error('kontak') is-invalid @enderror"
                                id="kontak" name="kontak" placeholder="08xxxxxxxxxx" value="{{ old('kontak') }}">
                            @error('kontak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                        <input type="date" class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                            id="tanggal_bergabung" name="tanggal_bergabung" value="{{ old('tanggal_bergabung') }}">
                        @error('tanggal_bergabung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <label for="store_id" class="form-label">Toko / Gudang</label>
                        <select name="store_id" class="form-select select2 @error('store_id') is-invalid @enderror"
                            id="store_id">
                            <option value="" selected>— Tidak ditugaskan —</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>
                                    {{ $store->name_toko }} ({{ ucfirst($store->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('store_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <label for="alamat" class="form-label">Alamat Rumah</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2"
                            placeholder="Alamat lengkap karyawan">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ============================================================
                     TOMBOL AKSI
                ============================================================ --}}
                <div class="form-action-bar">
                    <a href="{{ route('users.index') }}" id="cancel-button" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-x me-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bx bx-user-plus me-1"></i> Buat User
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').each(function() {
                    const $this = $(this);
                    $this.select2({
                        placeholder: $this.data('placeholder') || 'Pilih...',
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10,
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

            // --- FilePond Init ---
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType,
                FilePondPluginImageTransform
            );

            const pond = FilePond.create(document.querySelector('input[id="image"]'), {
                labelIdle: `<i class='bx bx-cloud-upload' style='font-size:1.5rem;vertical-align:middle;'></i><br>Seret & Lepas atau <span class="filepond--label-action">Pilih Gambar</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                imageCropAspectRatio: '1:1',
                acceptedFileTypes: ['image/png', 'image/jpeg'],
                labelFileTypeNotAllowed: 'Hanya PNG dan JPG yang diizinkan.',
                server: {
                    process: {
                        url: '{{ route('users.upload') }}',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    },
                    revert: {
                        url: '{{ route('users.revert') }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                }
            });

            // --- Toggle Password Visibility ---
            const toggleBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    this.querySelector('i').className = isHidden ? 'bx bx-show' : 'bx bx-hide';
                });
            }

            // --- Bersihkan file temp saat batal ---
            document.getElementById('cancel-button')?.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.href;
                const file = pond.getFiles()[0];
                const serverId = file?.serverId;
                if (!serverId) {
                    window.location.href = href;
                    return;
                }
                fetch('{{ route('users.revert') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: serverId
                }).finally(() => {
                    window.location.href = href;
                });
            });
        });
    </script>
@endsection
