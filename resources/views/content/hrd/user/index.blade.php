@extends('layouts/contentNavbarLayout')

@section('title', 'Data Employee')
@section('content')

    <div class="row g-4 align-items-stretch">

        {{-- Kartu Total Pengguna --}}
        <div class="col-12 col-md-4 col-xl-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4d50eb 0%, #8592ff 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded bg-white text-primary shadow-sm">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-white text-opacity-75 mb-0 text-sm fw-medium">Total Karyawan</p>
                        <h2 class="text-white mb-0 fw-bold" id="resumeTotaluser">
                            {{ $data->count() }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Tombol Tambah --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-5 col-md-4">
                            <label class="form-label mb-1 text-muted">Pencarian</label>
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder=" Nama, email, atau NIK..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-3">
                            <label class="form-label mb-1 text-muted"
                                style="font-size: 0.75rem; text-transform: uppercase;">Filter Role</label>
                            <select name="role" class="form-select form-select-sm select2" data-placeholder="Semua Role">
                                <option value="">Semua Role</option>
                                @foreach ($roles as $roleName)
                                    <option value="{{ $roleName }}" @selected(request('role') == $roleName)>
                                        {{ ucfirst($roleName) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-md-3">
                            <label class="form-label mb-1 text-muted"
                                style="font-size: 0.75rem; text-transform: uppercase;">Status</label>
                            <select name="status" id="statusFilter" class="form-select form-select-sm select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua</option>
                                <option value="Aktif" @selected(request('status') == 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected(request('status') == 'Tidak Aktif')>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-auto ms-sm-auto">
                            <a href="{{ route('users.create') }}" class="btn btn-primary w-100 shadow-sm">
                                <i class="bx bx-plus me-1"></i>user
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="col-12">
            <div class="card rounded-3 shadow-sm">
                <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">Direktori Pengguna</h5>
                        <p class="text-sm mb-0 text-muted mt-1">Kelola akses sistem dan profil karyawan perusahaan.</p>
                    </div>
                </div>

                <div class="card-body p-0" id="isiTable">

                    @fragment('user-table-body')

                        {{-- ===================================================
                             TAMPILAN MOBILE: Card Style (Lebih bersih dari list border)
                        =================================================== --}}
                        <div class="d-sm-none p-3">
                            @forelse ($data as $user)
                                @php $employee = $user->employee; @endphp
                                <div class="card border border-light shadow-none mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                {{-- Avatar --}}
                                                @if ($employee?->avatar)
                                                    <img src="{{ asset('storage/' . $employee->avatar) }}"
                                                        class="rounded-circle shadow-sm"
                                                        style="width:45px;height:45px;object-fit:cover;"
                                                        alt="{{ $user->name }}">
                                                @else
                                                    <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center shadow-sm"
                                                        style="width:45px;height:45px;font-size:16px;font-weight:700;flex-shrink:0;">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                @endif

                                                <div>
                                                    <h6 class="mb-0 fw-bold text-truncate" style="max-width: 180px;">
                                                        {{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                            {{-- Status --}}
                                            <div>
                                                @if ($user->status)
                                                    <span class="badge bg-label-success rounded-pill"><i
                                                            class='bx bx-check me-1'></i>Aktif</span>
                                                @else
                                                    <span class="badge bg-label-secondary rounded-pill">Nonaktif</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="bg-label-light rounded p-2 mb-3 text-sm">
                                            @if ($employee?->jabatan)
                                                <div class="mb-1"><i class="bx bx-briefcase text-muted me-2"></i>
                                                    <span
                                                        class="fw-medium">{{ \App\Enums\Jabatan::from($employee->jabatan instanceof \App\Enums\Jabatan ? $employee->jabatan->value : $employee->jabatan)->getLabel() }}</span>
                                                </div>

                                                <div class="mb-1">
                                                    <i class="bx bx-store-alt text-muted me-2"></i>
                                                    <span
                                                        class="text-dark">{{ $employee?->store?->name_toko ?? 'Pusat / Belum di-assign' }}</span>
                                                </div>
                                            @endif
                                            @if ($employee?->kontak)
                                                <div><i class="bx bx-phone text-muted me-2"></i>{{ $employee->kontak }}</div>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex flex-wrap gap-1">
                                                @forelse ($user->getRoleNames() as $role)
                                                    @php
                                                        $c = match (strtolower($role)) {
                                                            'admin', 'superadmin' => 'danger',
                                                            'kepala_toko', 'manager' => 'primary',
                                                            'kasir' => 'info',
                                                            default => 'secondary',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $c }} bg-opacity-10 text-{{ $c }} text-xs">{{ $role }}</span>
                                                @empty
                                                    <span class="text-muted text-xs">No Role</span>
                                                @endforelse
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('users.edit', $user->username) }}"
                                                    class="btn btn-icon btn-sm btn-outline-secondary">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-icon btn-sm btn-outline-danger delete-user-btn"
                                                    data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                    data-user-username="{{ $user->username }}"
                                                    data-user-name="{{ $user->name }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <div class="bg-label-secondary rounded-circle d-inline-flex p-3 mb-3">
                                        <i class="bx bx-user-x fs-2"></i>
                                    </div>
                                    <h6 class="fw-medium mb-1">Tidak ada data</h6>
                                    <p class="text-sm">Data pengguna belum tersedia atau tidak ditemukan.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ===================================================
                             TAMPILAN TABLET & DESKTOP: Tabel Rapih
                        =================================================== --}}
                        <div class="d-none d-sm-block">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="d-none d-md-table-cell text-center" style="width:50px;">#</th>
                                            <th>Profil Pengguna</th>
                                            <th class="d-none d-lg-table-cell">Info Kontak</th>
                                            <th>Posisi & Hak Akses</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width:100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @forelse ($data as $key => $user)
                                            @php $employee = $user->employee; @endphp
                                            <tr>
                                                <td class="d-none d-md-table-cell text-center text-muted small">
                                                    {{ ++$key }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        {{-- Avatar --}}
                                                        @if ($employee?->avatar)
                                                            <img src="{{ asset('storage/' . $employee->avatar) }}"
                                                                class="rounded-circle shadow-sm"
                                                                style="width:40px;height:40px;object-fit:cover;flex-shrink:0;"
                                                                alt="{{ $user->name }}">
                                                        @else
                                                            <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center shadow-sm"
                                                                style="width:40px;height:40px;font-size:14px;font-weight:700;flex-shrink:0;">
                                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                                            <span class="text-muted small">{{ $user->username }}</span>
                                                            <div class="d-lg-none mt-1"><small class="text-muted"><i
                                                                        class="bx bx-envelope me-1"></i>{{ $user->email }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Kolom Kontak: lg ke atas --}}
                                                <td class="d-none d-lg-table-cell">
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="text-sm"><i
                                                                class="bx bx-envelope text-muted me-2"></i>{{ $user->email }}</span>
                                                        @if ($employee?->kontak)
                                                            <span class="text-sm"><i
                                                                    class="bx bxl-whatsapp text-success me-2"></i>{{ $employee->kontak }}</span>
                                                        @else
                                                            <span class="text-sm text-muted fst-italic"><i
                                                                    class="bx bx-phone text-muted me-2"></i>Belum ada no.
                                                                HP</span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex flex-column gap-2 align-items-start">
                                                        @if ($employee?->jabatan)
                                                            @php
                                                                $jVal =
                                                                    $employee->jabatan instanceof \App\Enums\Jabatan
                                                                        ? $employee->jabatan->value
                                                                        : $employee->jabatan;
                                                            @endphp
                                                            <span class="fw-medium text-dark text-sm"><i
                                                                    class="bx bx-briefcase-alt-2 text-muted me-1"></i>{{ \App\Enums\Jabatan::from($jVal)->getLabel() }}</span>
                                                        @else
                                                            <span class="text-muted text-sm fst-italic">Belum di-assign</span>
                                                        @endif

                                                        <span class="text-muted text-xs">
                                                            <i
                                                                class="bx bx-store-alt me-1"></i>{{ $employee?->store?->name_toko ?? 'Pusat / Belum di-assign' }}
                                                        </span>

                                                        <div class="d-flex flex-wrap gap-1">
                                                            @forelse ($user->getRoleNames() as $role)
                                                                @php
                                                                    $c = match (strtolower($role)) {
                                                                        'admin', 'superadmin' => 'danger',
                                                                        'kepala_toko', 'manager' => 'primary',
                                                                        'kasir' => 'info',
                                                                        default => 'secondary',
                                                                    };
                                                                @endphp
                                                                <span
                                                                    class="badge bg-{{ $c }} bg-opacity-10 text-{{ $c }} text-xs">{{ $role }}</span>
                                                            @empty
                                                                <span class="badge bg-label-secondary text-xs">Akses
                                                                    Kosong</span>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($user->status)
                                                        <span class="badge bg-label-success rounded-pill px-3">Aktif</span>
                                                    @else
                                                        <span
                                                            class="badge bg-label-secondary rounded-pill px-3">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-2">
                                                        <a href="{{ route('users.edit', $user->username) }}"
                                                            class="btn btn-sm btn-icon btn-label-secondary" title="Edit Data">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-icon btn-label-danger delete-user-btn"
                                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                            data-user-username="{{ $user->username }}"
                                                            data-user-name="{{ $user->name }}" title="Hapus Akun">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="bg-label-secondary rounded-circle d-inline-flex p-3 mb-3">
                                                        <i class="bx bx-user-x fs-2"></i>
                                                    </div>
                                                    <h6 class="fw-medium mb-1">Tidak ada data</h6>
                                                    <p class="text-sm text-muted">Data pengguna belum tersedia atau tidak
                                                        ditemukan dalam pencarian.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    @endfragment

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-5 px-4">
                    <div class="bg-label-danger rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bx bx-trash fs-1"></i>
                    </div>
                    <h5 class="mb-1 fw-bold">Hapus Akun?</h5>
                    <p class="text-sm mb-3">Tindakan ini tidak dapat dibatalkan. Menghapus: <br><strong
                            id="userNameToDelete" class="text-dark"></strong></p>
                    <form id="deleteUserForm" method="POST" action="#">
                        @method('delete')
                        @csrf
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
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
                        minimumResultsForSearch: 10
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

            // --- Modal Hapus ---
            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    deleteModal.querySelector('#userNameToDelete').textContent = btn.dataset.userName;
                    deleteModal.querySelector('#deleteUserForm').action =
                        `/dashboard/users/${btn.dataset.userUsername}`;
                });
            }

            // --- AJAX Live Filter ---
            const searchInput = $('#searchInput');
            const roleFilter = $('select[name="role"]');
            const statusFilter = $('#statusFilter');
            const tableBody = $('#isiTable');
            const totalUser = $('#resumeTotaluser');

            let typingTimer;

            function fetchUsers() {
                $.ajax({
                    url: "{{ route('users.index') }}",
                    type: 'GET',
                    data: {
                        search: searchInput.val(),
                        role: roleFilter.val(),
                        status: statusFilter.val()
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    beforeSend: () => tableBody.css('opacity', '0.4'),
                    success: (res) => {
                        tableBody.html(res.html);
                        totalUser.text(res.total);
                        tableBody.css('opacity', '1');
                    },
                    error: (xhr) => {
                        console.error('Gagal fetch data:', xhr);
                        tableBody.css('opacity', '1');
                    }
                });
            }

            searchInput.on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(fetchUsers, 500);
            });

            roleFilter.on('change', fetchUsers);
            statusFilter.on('change', fetchUsers);
        });
    </script>
@endsection
