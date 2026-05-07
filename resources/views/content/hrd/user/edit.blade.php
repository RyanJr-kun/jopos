@extends('layouts/contentNavbarLayout')

@section('title', 'Edit User')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
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

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="{{ route('users.update', $user->username) }}" enctype="multipart/form-data">
                @method('put')
                @csrf

                {{-- ==============================
                 BAGIAN 1: FOTO & DATA AKUN
            ============================== --}}
                <div class="row mb-4">
                    <div class="col-12 col-md-4 mb-4 mb-md-0">
                        <h6 class="ms-2">Foto Pengguna :</h6>
                        <input type="file" class="filepond" name="avatar" id="image">
                    </div>

                    <div class="col-12 col-md-8">
                        <h6 class="mb-3">Data Akun :</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Nama Lengkap"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" placeholder="Username"
                                    value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="example@gmail.com"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" type="password"
                                    placeholder="Kosongkan jika tidak ingin diubah">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role_name" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_name"
                                    class="form-select select2 @error('role_name') is-invalid @enderror" id="role_name"
                                    required @if (auth()->id() === $user->id) disabled @endif>
                                    <option value="" disabled selected>Pilih Role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}" @selected(old('role_name', $user->getRoleNames()->first()) == $role->name)>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Jika disabled, kirim via hidden input --}}
                                @if (auth()->id() === $user->id)
                                    <input type="hidden" name="role_name" value="{{ $user->getRoleNames()->first() }}">
                                @endif
                                @error('role_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Aktif</label>
                                <div class="justify-content-end form-check form-switch form-check-reverse mt-2">
                                    <label class="me-auto form-check-label" for="status_toggle">Aktif</label>
                                    <input type="hidden" name="status" value="0">
                                    <input id="status_toggle" class="form-check-input" type="checkbox" name="status"
                                        value="1" @checked(old('status', $user->status))>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                {{-- ==============================
                 BAGIAN 2: DATA KARYAWAN
                 (dari tabel employee_profiles)
            ============================== --}}
                <h6 class="mb-3">Data Karyawan :</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nik" class="form-label">NIK / ID Karyawan</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                            name="nik" placeholder="Nomor Induk Karyawan"
                            value="{{ old('nik', $user->employee?->nik) }}"">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-select select2 @error('jabatan') is-invalid @enderror"
                            id="jabatan">
                            <option value="">— Pilih Jabatan —</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->value }}" @selected(old('jabatan', $user->employee?->jabatan?->value) === $jabatan->value)>
                                    {{ $jabatan->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                        @error('jabatan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="kontak" class="form-label">Nomor Kontak</label>
                        <input type="tel" class="form-control @error('kontak') is-invalid @enderror" id="kontak"
                            name="kontak" placeholder="08..." value="{{ old('kontak', $user->employee?->kontak) }}">
                        @error('kontak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                        <input type="date" class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                            id="tanggal_bergabung" name="tanggal_bergabung"
                            value="{{ old('tanggal_bergabung', $user->employee?->tanggal_bergabung?->format('Y-m-d')) }}">
                        @error('tanggal_bergabung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="store_id" class="form-label">Toko / Gudang</label>
                        <select name="store_id" class="form-select select2 @error('store_id') is-invalid @enderror"
                            id="store_id">
                            <option value="">— Tidak ditugaskan —</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" @selected(old('store_id', $user->employee?->store_id) == $store->id)>
                                    {{ $store->name_toko }} ({{ ucfirst($store->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('store_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="alamat" class="form-label">Alamat Rumah</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2"
                            placeholder="Alamat lengkap karyawan">{{ old('alamat', $user->employee?->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                    <button type="submit" class="btn btn-outline-success btn-sm">Perbarui User</button>
                    <a href="{{ route('users.index') }}" id="cancel-button"
                        class="btn btn-outline-danger btn-sm ms-3">Batal</a>
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
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType,
                FilePondPluginImageTransform
            );

            const pond = FilePond.create(document.querySelector('input[id="image"]'), {
                labelIdle: `Seret & Lepas atau <span class="filepond--label-action">Cari Gambar</span>`,
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
                },
                {{-- Load avatar yang sudah ada dari employee_profiles --}}
                files: [
                    @if ($user->employee?->avatar && Storage::disk('r2')->exists($user->employee->avatar))
                        '{{ asset('storage/' . $user->employee->avatar) }}'
                    @endif
                ]
            });

            // Bersihkan file temp saat batal
            document.getElementById('cancel-button')?.addEventListener('click', function(e) {
                e.preventDefault();
                const newFile = pond.getFiles().find(file =>
                    file.origin === FilePond.FileOrigin.INPUT &&
                    file.status === FilePond.FileStatus.PROCESSING_COMPLETE
                );
                if (newFile?.serverId) {
                    fetch('{{ route('users.revert') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: newFile.serverId
                    }).finally(() => {
                        window.location.href = this.href;
                    });
                } else {
                    window.location.href = this.href;
                }
            });
        });
    </script>
@endsection
