@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
    <x-slot:title>Edit User: {{ $user->name }}</x-slot:title>

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-body p-4">
            <form method="post" action="{{ route('users.update', $user->username) }}" enctype="multipart/form-data">
                @method('put')
                @csrf
                <div class="row">
                    <div class="col-12 col-md-4 mb-4 mb-md-0">
                        <h6 class="ms-2">Gambar Pengguna :</h6>
                        <input type="file" class="filepond" name="img_user" id="image">
                    </div>

                    <div class="col-12 col-md-8">
                        <div class="row">
                            <h6>Data Pribadi :</h6>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Nama Lengkap"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" placeholder="Username"
                                    value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="example@gmail.com"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kontak" class="form-label">Kontak</label>
                                <input type="tel" class="form-control @error('kontak') is-invalid @enderror"
                                    value="{{ old('kontak', $user->kontak) }}" id="kontak" name="kontak"
                                    placeholder="08...">
                                @error('kontak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">Posisi <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id"
                                    name="role_id" required @if (auth()->id() === $user->id) disabled @endif>
                                    <option value="" disabled selected>Pilih Role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                            {{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @if (auth()->id() === $user->id)
                                    {{-- Jika disabled, nilainya tidak akan dikirim. Kirim via input hidden agar validasi tidak gagal. --}}
                                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                    <small class="form-text text-muted">Anda tidak dapat mengubah role Anda
                                        sendiri.</small>
                                @endif
                                @error('role_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mulai_kerja" class="form-label">Mulai Bekerja</label>
                                <input id="mulai_kerja" name="mulai_kerja"
                                    class="form-control @error('mulai_kerja') is-invalid @enderror"
                                    value="{{ old('mulai_kerja', $user->mulai_kerja ? $user->mulai_kerja->format('Y-m-d') : '') }}"
                                    placeholder="Pilih tanggal" type="date">
                                @error('mulai_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="password">Password</label>
                                <input name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" type="password"
                                    placeholder="Kosongkan jika tidak ingin diubah">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="justify-content-end form-check form-switch form-check-reverse">
                                    <label class="me-auto form-check-label" for="status_toggle">Status Aktif</label>
                                    <input type="hidden" name="status" value="0">
                                    <input id="status_toggle" class="form-check-input" type="checkbox"
                                        name="status" value="1" @checked(old('status', $user->status))>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-3">
                    <button type="submit" class="btn btn-outline-success btn-sm">Perbarui User</button>
                    <a href="{{ route('users.index') }}" id="cancel-button"
                        class="btn btn-outline-danger btn-sm ms-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('page-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FilePond
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType,
                FilePondPluginImageTransform
            );

            const inputElement = document.querySelector('input[id="image"]');
            const pond = FilePond.create(inputElement, {
                labelIdle: `Seret & Lepas gambar atau <span class="filepond--label-action">Cari</span>`,
                allowImagePreview: true,
                allowFileSizeValidation: true,
                maxFileSize: '2MB',
                allowImageCrop: true,
                imageCropAspectRatio: '1:1',
                acceptedFileTypes: ['image/png', 'image/jpeg'],
                labelFileTypeNotAllowed: 'Jenis file tidak valid. Hanya PNG dan JPG yang diizinkan.',
                server: {
                    process: {
                        url: '{{ route('users.upload') }}',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    },
                    revert: {
                        url: '/dashboard/users/revert',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                },
                files: [
                    @if ($user->img_user && Storage::disk('public')->exists($user->img_user))
                        '{{ asset('storage/' . $user->img_user) }}'
                    @endif
                ]
            });
            const cancelButton = document.getElementById('cancel-button');
            if (cancelButton) {
                cancelButton.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah navigasi langsung
                    // Cari file yang BARU diunggah oleh pengguna dan sudah selesai diproses
                    const newFile = pond.getFiles().find(file =>
                        file.origin === FilePond.FileOrigin.INPUT &&
                        file.status === FilePond.FileStatus.PROCESSING_COMPLETE
                    );
                    if (newFile && newFile.serverId) {
                        // Jika ada file baru yang sudah diunggah, hapus dulu dari server
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
            }
        });
    </script>
@endsection
@endsection
