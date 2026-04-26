@extends('layouts/contentNavbarLayout')

@section('title', 'Role & Permission Management')

@section('content')

{{-- Page Header --}}
<div class="row align-items-center mb-4">
    <div class="col">
        <h4 class="fw-bold mb-1">
            <i class="bx bx-shield-quarter text-primary me-2"></i>Role & Permission
        </h4>
        <p class="text-muted mb-0 small">Kelola hak akses dan role pengguna aplikasi Anda.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Role
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bx bx-check-circle me-2 fs-5"></i>
        <div>{!! session('success') !!}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bx bx-error-circle me-2 fs-5"></i>
        <div>{!! session('error') !!}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #696cff 0%, #9b59b6 100%);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded bg-white bg-opacity-25 text-white">
                        <i class="bx bx-shield fs-4"></i>
                    </span>
                </div>
                <div class="text-white">
                    <p class="mb-0 text-white-50 small">Total Role</p>
                    <h3 class="fw-bold mb-0">{{ $roles->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #03c3ec 0%, #0061a3 100%);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded bg-white bg-opacity-25 text-white">
                        <i class="bx bx-lock-open-alt fs-4"></i>
                    </span>
                </div>
                <div class="text-white">
                    <p class="mb-0 text-white-50 small">Total Permission</p>
                    <h3 class="fw-bold mb-0">{{ \Spatie\Permission\Models\Permission::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #71dd37 0%, #1b9e3e 100%);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded bg-white bg-opacity-25 text-white">
                        <i class="bx bx-group fs-4"></i>
                    </span>
                </div>
                <div class="text-white">
                    <p class="mb-0 text-white-50 small">Total Pengguna</p>
                    <h3 class="fw-bold mb-0">{{ $roles->sum('users_count') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ff9f43 0%, #e84118 100%);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded bg-white bg-opacity-25 text-white">
                        <i class="bx bx-shield-x fs-4"></i>
                    </span>
                </div>
                <div class="text-white">
                    <p class="mb-0 text-white-50 small">Role Tanpa Permission</p>
                    <h3 class="fw-bold mb-0">{{ $roles->filter(fn($r) => $r->permissions->isEmpty())->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Roles Table Card --}}
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div>
            <h5 class="mb-0 fw-bold">Daftar Role</h5>
            <small class="text-muted">Klik <strong>Edit</strong> untuk mengelola permission tiap role.</small>
        </div>
        <span class="badge bg-label-primary rounded-pill">{{ $roles->count() }} Role</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Role</th>
                    <th>Guard</th>
                    <th>Permissions</th>
                    <th width="10%">Users</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($roles as $index => $role)
                <tr>
                    <td>
                        <span class="text-muted small fw-bold">{{ $index + 1 }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $roleColors = [
                                    'admin'    => 'danger',
                                    'manager'  => 'warning',
                                    'kasir'    => 'info',
                                    'teknisi'  => 'primary',
                                    'staff'    => 'secondary',
                                    'sales'    => 'success',
                                ];
                                $colorClass = $roleColors[strtolower($role->name)] ?? 'primary';
                            @endphp
                            <span class="avatar avatar-xs">
                                <span class="avatar-initial rounded-circle bg-label-{{ $colorClass }}">
                                    <i class="bx bx-shield"></i>
                                </span>
                            </span>
                            <strong class="text-capitalize">{{ $role->name }}</strong>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-label-secondary">{{ $role->guard_name }}</span>
                    </td>
                    <td>
                        @php
                            $perms   = $role->permissions;
                            $maxShow = 5;
                            $shown   = $perms->take($maxShow);
                            $more    = $perms->count() - $maxShow;
                        @endphp

                        @if($perms->isEmpty())
                            <span class="text-muted fst-italic small">Tidak ada permission</span>
                        @else
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($shown as $perm)
                                    <span class="badge bg-label-info" title="{{ $perm->name }}">
                                        {{ Str::limit($perm->name, 20) }}
                                    </span>
                                @endforeach
                                @if($more > 0)
                                    <span class="badge bg-secondary cursor-pointer"
                                          data-bs-toggle="tooltip"
                                          title="{{ $perms->skip($maxShow)->pluck('name')->join(', ') }}">
                                        +{{ $more }} more
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-label-{{ $colorClass }}">
                            <i class="bx bx-user me-1"></i>{{ $role->users_count }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('roles.edit', $role->id) }}"
                           class="btn btn-sm btn-outline-primary me-1"
                           title="Edit Role">
                            <i class="bx bx-edit-alt me-1"></i>Edit
                        </a>

                        @if(strtolower($role->name) !== 'admin')
                        <button type="button"
                                class="btn btn-sm btn-outline-danger btn-delete-role"
                                data-role-id="{{ $role->id }}"
                                data-role-name="{{ $role->name }}"
                                data-users-count="{{ $role->users_count }}"
                                data-delete-url="{{ route('roles.destroy', $role->id) }}"
                                title="Hapus Role">
                            <i class="bx bx-trash"></i>
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-secondary" disabled title="Role sistem tidak dapat dihapus">
                            <i class="bx bx-lock-alt"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center text-muted">
                            <i class="bx bx-shield-x fs-1 mb-2"></i>
                            <p class="mb-1 fw-semibold">Belum ada role yang dibuat.</p>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="bx bx-plus me-1"></i>Tambah Role Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="deleteRoleForm" method="POST" action="#" class="d-none">
    @csrf
    @method('DELETE')
</form>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-3">
                    <span class="avatar avatar-xl bg-label-danger">
                        <i class="bx bx-trash fs-2 text-danger"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1">Hapus Role?</h5>
                <p class="text-muted mb-0">Anda akan menghapus role <strong id="modalRoleName"></strong>.</p>
                <p class="text-danger small mt-1 mb-4" id="modalWarningText"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bx bx-trash me-1"></i>Ya, Hapus!
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // --- Initialize Bootstrap Tooltips ---
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el));

    // --- Delete Role Logic ---
    const deleteModal    = new bootstrap.Modal(document.getElementById('deleteRoleModal'));
    const modalRoleName  = document.getElementById('modalRoleName');
    const modalWarning   = document.getElementById('modalWarningText');
    const confirmBtn     = document.getElementById('confirmDeleteBtn');
    const deleteForm     = document.getElementById('deleteRoleForm');

    document.querySelectorAll('.btn-delete-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const roleName   = this.dataset.roleName;
            const usersCount = parseInt(this.dataset.usersCount, 10);
            const deleteUrl  = this.dataset.deleteUrl;

            modalRoleName.textContent = `"${roleName}"`;
            deleteForm.action = deleteUrl;

            if (usersCount > 0) {
                modalWarning.textContent =
                    `⚠ Perhatian: Role ini masih dipakai oleh ${usersCount} pengguna!`;
                confirmBtn.disabled = true;
            } else {
                modalWarning.textContent = 'Tindakan ini tidak dapat dibatalkan.';
                confirmBtn.disabled = false;
            }

            deleteModal.show();
        });
    });

    confirmBtn.addEventListener('click', function () {
        deleteForm.submit();
    });

    // --- Auto-dismiss Flash Alerts ---
    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection
