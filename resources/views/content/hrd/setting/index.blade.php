@extends('layouts/contentNavbarLayout')
@section('title', 'Pengaturan Akun')

@section('vendor-style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')
    @php
        $activeTab = session('active_tab', 'profile');
    @endphp
    <div class="row">
        <div class="col-12 mb-4">
            <ul class="nav nav-pills d-flex" id="settingsTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link px-2 px-md-4 {{ $activeTab == 'profile' ? 'active' : '' }}" id="profile-tab"
                        data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
                        <i class="bx bx-user me-1"></i>
                        <span class="d-none d-md-block">Profil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-md-4 {{ $activeTab == 'security' ? 'active' : '' }}" id="security-tab"
                        data-bs-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">
                        <i class="bx bx-lock-alt me-1"></i>
                        <span class="d-none d-md-block">Keamanan</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-md-4 {{ $activeTab == 'notifications' ? 'active' : '' }}"
                        id="notifications-tab" data-bs-toggle="tab" href="#notifications" role="tab"
                        aria-controls="notifications" aria-selected="false">
                        <i class="bx bx-bell me-1"></i>
                        <span class="d-none d-md-block">Notifikasi</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-md-4 {{ $activeTab == 'activity' ? 'active' : '' }}" id="activity-tab"
                        data-bs-toggle="tab" href="#activity" role="tab" aria-controls="activity" aria-selected="false">
                        <i class="bx bx-history me-1"></i>
                        <span class="d-none d-md-block">Aktivitas Akun</span></a>
                </li>
            </ul>
        </div>

        <div class="col-12">
            <div class="tab-content p-0" id="settingsTabContent">

                <div class="tab-pane fade {{ $activeTab == 'profile' ? 'show active' : '' }}" id="profile" role="tabpanel"
                    aria-labelledby="profile-tab">
                    <div class="card mb-4">
                        <h5 class="card-header fw-bold py-3">Detail Profil</h5>
                        <div class="card-body">
                            <form action="{{ route('setting.profile.update', $user->username) }}" method="POST">
                                @csrf
                                @method('PUT')

                                @php
                                    // Cek apakah user benar-benar punya avatar di database
                                    $hasAvatar = !empty($employee?->avatar);
                                    $avatarUrl = $hasAvatar
                                        ? Storage::url($employee->avatar)
                                        : asset('assets/img/avatars/1.png');
                                @endphp

                                <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">

                                    <div id="avatar-preview-container" class="d-flex align-items-center gap-4 w-100"
                                        style="display: {{ $hasAvatar ? 'flex' : 'none' }} !important;">
                                        <img src="{{ $avatarUrl }}" alt="user-avatar" class="d-block rounded"
                                            height="100" width="100" id="uploadedAvatar" style="object-fit: cover;" />
                                        <div>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                id="btn-delete-avatar">
                                                <i class="bx bx-trash me-1"></i> Hapus Foto Saat Ini
                                            </button>
                                            <p class="text-muted mb-0 mt-2" style="font-size: 0.8rem;">Hapus foto untuk
                                                mengunggah yang baru.</p>
                                        </div>
                                    </div>

                                    <div id="avatar-upload-container" class="w-100"
                                        style="display: {{ $hasAvatar ? 'none' : 'block' }};">
                                        <label for="avatar" class="form-label">Unggah Foto Profil</label>
                                        <input type="file" id="avatar" class="filepond" name="avatar"
                                            accept="image/png, image/jpeg, image/jpg" />
                                    </div>

                                    <input type="hidden" name="avatar" id="avatar_hidden_input">
                                    <input type="hidden" name="remove_avatar" id="remove_avatar_input" value="0">

                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text"
                                            id="name" name="name" value="{{ old('name', $user->name) }}"
                                            autofocus />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email"
                                            id="email" name="email" value="{{ old('email', $user->email) }}" />
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="kontak" class="form-label">Nomor Kontak</label>
                                        <input type="text" class="form-control @error('kontak') is-invalid @enderror"
                                            id="kontak" name="kontak"
                                            value="{{ old('kontak', $employee?->kontak) }}" />
                                        @error('kontak')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="jabatan" class="form-label">Jabatan</label>
                                        <select id="jabatan" name="jabatan"
                                            class="form-select @error('jabatan') is-invalid @enderror"
                                            {{ Auth::user()->hasRole('admin') ? '' : 'disabled' }}>
                                            <option value="">Pilih Jabatan</option>
                                            @foreach (\App\Enums\Jabatan::cases() as $jabatan)
                                                <option value="{{ $jabatan->value }}"
                                                    {{ old('jabatan', $employee?->jabatan?->value) == $jabatan->value ? 'selected' : '' }}>
                                                    {{ $jabatan->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if (!Auth::user()->hasRole('admin'))
                                            <small class="text-muted">Hanya Admin yang dapat mengubah jabatan.</small>
                                        @endif
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2">{{ old('alamat', $employee?->alamat) }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab == 'security' ? 'show active' : '' }}" id="security"
                    role="tabpanel" aria-labelledby="security-tab">
                    <div class="card mb-4">
                        <h5 class="card-header">Ubah Password</h5>
                        <div class="card-body">
                            <form action="{{ route('setting.password.update', $user->username) }}" method="POST">
                                @csrf
                                @method('PUT')

                                @if (Auth::id() === $user->id)
                                    <div class="row">
                                        <div class="mb-3 col-md-6 form-password-toggle">
                                            <label class="form-label" for="current_password">Password Saat Ini</label>
                                            <div class="input-group input-group-merge">
                                                <input class="form-control @error('current_password') is-invalid @enderror"
                                                    type="password" name="current_password" id="current_password"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                                <span class="input-group-text cursor-pointer"><i
                                                        class="bx bx-hide"></i></span>
                                            </div>
                                            @error('current_password')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-3">Anda sedang mengubah password milik
                                        <b>{{ $user->name }}</b> sebagai Admin.
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="password">Password Baru</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" id="password" name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                            <span class="input-group-text cursor-pointer"><i
                                                    class="bx bx-hide"></i></span>
                                        </div>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="password_confirmation">Konfirmasi Password
                                            Baru</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                            <span class="input-group-text cursor-pointer"><i
                                                    class="bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <h6 class="fw-semibold">Persyaratan Password:</h6>
                                        <ul class="ps-3 mb-0 text-muted">
                                            <li class="mb-1">Minimal 8 karakter.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">Simpan Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab == 'notifications' ? 'show active' : '' }}" id="notifications"
                    role="tabpanel" aria-labelledby="notifications-tab">
                    <div class="card mb-4">
                        <h5 class="card-header">Preferensi Notifikasi</h5>
                        <div class="card-body">
                            <span>Pilih notifikasi apa saja yang ingin Anda terima di halaman Dasbor.</span>
                            <form action="{{ route('setting.notifications.update', $user->username) }}" method="POST"
                                class="mt-4">
                                @csrf
                                @method('PUT')
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless border-bottom">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Tipe Notifikasi</th>
                                                <th class="text-nowrap text-center">Aktivasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            {{-- SEMBUNYIKAN UNTUK SELAIN ADMIN/GUDANG --}}
                                            @if ($canViewInventoryNotifs)
                                                <tr>
                                                    <td class="text-nowrap"><i class="bx bx-package me-2 text-danger"></i>
                                                        Peringatan Stok Rendah</td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="low_stock" value="1"
                                                                {{ $notifSettings['low_stock'] ?? true ? 'checked' : '' }} />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap"><i
                                                            class="bx bx-barcode me-2 text-warning"></i> Peringatan
                                                        Pendaftaran Nomor Seri (SN)</td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="unregistered_serial" value="1"
                                                                {{ $notifSettings['unregistered_serial'] ?? true ? 'checked' : '' }} />
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- INI MUNCUL UNTUK SEMUA USER (TERMASUK ANAK MAGANG) --}}
                                            <tr>
                                                <td class="text-nowrap"><i class="bx bx-bell me-2 text-primary"></i>
                                                    Pemberitahuan Sistem JOPOS</td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" name="system"
                                                            value="1"
                                                            {{ $notifSettings['system'] ?? true ? 'checked' : '' }} />
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">Simpan Preferensi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab == 'activity' ? 'show active' : '' }}" id="activity"
                    role="tabpanel" aria-labelledby="activity-tab">
                    <div class="card mb-4">
                        <h5 class="card-header">Info Akun & Hak Akses</h5>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted text-uppercase">Username</small>
                                    <p class="mb-0 fw-semibold">{{ $user->username }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted text-uppercase">Bergabung Sejak</small>
                                    <p class="mb-0 fw-semibold">{{ $user->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted text-uppercase mb-2 d-block">Role Sistem</small>
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-label-primary me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge bg-label-secondary">Tidak ada role</span>
                                    @endforelse
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3">
                                <a href="{{ route('notifications.all') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-history me-1"></i> Lihat Semua Notifikasi Saya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnDelete = document.getElementById('btn-delete-avatar');
            const previewContainer = document.getElementById('avatar-preview-container');
            const uploadContainer = document.getElementById('avatar-upload-container');
            const removeAvatarInput = document.getElementById('remove_avatar_input');

            if (btnDelete) {
                btnDelete.addEventListener('click', function() {
                    // 1. Sembunyikan container gambar lama
                    previewContainer.style.setProperty('display', 'none', 'important');

                    // 2. Tampilkan area FilePond
                    uploadContainer.style.display = 'block';

                    // 3. Ubah value hidden input jadi 1. 
                    // Ini akan memicu logika "$request->boolean('remove_avatar')" di Controller untuk menghapus file fisik.
                    removeAvatarInput.value = '1';
                });
            }
        });
    </script>
    <script>
        // Register plugin
        FilePond.registerPlugin(FilePondPluginImagePreview);

        // Setup FilePond
        const inputElement = document.querySelector('input[type="file"].filepond');
        const pond = FilePond.create(inputElement, {
            server: {
                process: {
                    url: '{{ route('users.upload') }}', // Gunakan route yg sudah ada di jopos
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    onload: (response) => {
                        document.getElementById('avatar_hidden_input').value = response;
                        return response;
                    }
                },
                revert: {
                    url: '{{ route('users.revert') }}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    onload: () => {
                        document.getElementById('avatar_hidden_input').value = '';
                    }
                }
            }
        });
    </script>
@endsection
