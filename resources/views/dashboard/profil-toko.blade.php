@extends('layouts/contentNavbarLayout')

@section('title', 'Cards basic - UI elements')
@section('content')
@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection
@section('breadcrumb')
    @php
        // Definisikan item breadcrumb dalam bentuk array
        $breadcrumbItems = [
            ['name' => 'Page', 'url' => '/dashboard'],
            ['name' => 'Setting', 'url' => '#'],
            ['name' => 'Profile Toko', 'url' => route('pengaturan.profil-toko.edit')],
        ];
    @endphp
    <x-breadcrumb :items="$breadcrumbItems" />
@endsection

<div class="container-fluid p-3">
    <div class="card rounded-2">
        <div class="card-body">
            <form action="{{ route('pengaturan.profil-toko.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Informasi Toko --}}
                    <div class="col-md-8">
                        <h5>Informasi Dasar</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama_toko" class="form-label">Nama Toko <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_toko') is-invalid @enderror"
                                    id="nama_toko" name="nama_toko" value="{{ old('nama_toko', $profil->nama_toko) }}"
                                    required>
                                @error('nama_toko')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror"
                                    id="telepon" name="telepon" value="{{ old('telepon', $profil->telepon) }}">
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $profil->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $profil->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Logo Toko --}}
                    <div class="col-md-4">
                        <h5 class="mb-3">Logo Toko</h5>
                        <input type="file" class="filepond" name="logo" id="logo">
                        @error('logo')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('page-script')
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageCrop,
                FilePondPluginFileValidateType
            );

            const inputElement = document.querySelector('#logo');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Siapkan array file untuk FilePond
            const pondFiles = [];
            @if ($profil->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($profil->logo))
                pondFiles.push({
                    source: '{{ $profil->logo }}',
                    options: {
                        type: 'local',
                        file: {
                            name: '{{ basename($profil->logo) }}',
                            size: {{ \Illuminate\Support\Facades\Storage::disk('public')->size($profil->logo) }},
                        },
                        metadata: {
                            poster: '{{ asset('storage/' . $profil->logo) }}'
                        }
                    }
                });
            @endif

            FilePond.create(inputElement, {
                files: pondFiles,
                labelIdle: `Seret & Lepas gambar atau <span class="filepond--label-action">Cari</span>`,
                // ... opsi lainnya
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/svg+xml'],
                maxFileSize: '2MB',
                server: {
                    process: {
                        url: '{{ route('pengaturan.profil-toko.upload') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    },
                    revert: {
                        url: '{{ route('pengaturan.profil-toko.revert') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }
                    // Properti 'load' sudah dihapus dari sini
                },
            });
        });
    </script>
@endsection
@endsection
