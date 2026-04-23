@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Role — ' . ucfirst($role->name))

@section('content')

{{-- Page Header --}}
<div class="row align-items-center mb-4">
    <div class="col">
        <h4 class="fw-bold mb-1">
            <i class="bx bx-shield-alt-2 text-warning me-2"></i>Edit Role
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role & Permission</a></li>
                <li class="breadcrumb-item active">Edit — <strong class="text-capitalize">{{ $role->name }}</strong></li>
            </ol>
        </nav>
    </div>
    <div class="col-auto">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
    </div>
</div>

<form action="{{ route('roles.update', $role->id) }}" method="POST" id="roleForm">
    @csrf
    @method('PUT')

    <div class="row g-4">

        {{-- LEFT: Informasi Role --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bx bx-info-circle text-warning me-2"></i>Informasi Role
                    </h5>
                </div>
                <div class="card-body">

                    {{-- Badge Role Saat Ini --}}
                    <div class="d-flex align-items-center gap-2 my-4 p-3 rounded-2 bg-light-subtle border">
                        @php
                            $roleColors = [
                                'admin'   => 'danger',  'manager'  => 'warning',
                                'kasir'   => 'info',    'teknisi'  => 'primary',
                                'staff'   => 'secondary', 'sales'  => 'success',
                            ];
                            $colorClass = $roleColors[strtolower($role->name)] ?? 'primary';
                        @endphp
                        <span class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-label-{{ $colorClass }}">
                                <i class="bx bx-shield"></i>
                            </span>
                        </span>
                        <div>
                            <p class="mb-0 small text-muted">Role Saat Ini</p>
                            <strong class="text-capitalize">{{ $role->name }}</strong>
                        </div>
                        <span class="badge bg-label-{{ $colorClass }} ms-auto">
                            {{ $role->users()->count() }} User
                        </span>
                    </div>

                    {{-- Nama Role --}}
                    <div class="mb-4">
                        <label for="roleName" class="form-label fw-semibold">
                            Nama Role <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="roleName"
                            name="name"
                            class="form-control form-control-lg @error('name') is-invalid @enderror
                                   {{ strtolower($role->name) === 'admin' ? 'bg-light' : '' }}"
                            placeholder="Contoh: kasir, manager, teknisi"
                            value="{{ old('name', $role->name) }}"
                            required
                            autocomplete="off"
                            {{ strtolower($role->name) === 'admin' ? 'readonly' : '' }}
                        >
                        @if(strtolower($role->name) === 'admin')
                            <div class="form-text text-warning">
                                <i class="bx bx-lock me-1"></i>Nama role <strong>admin</strong> tidak dapat diubah.
                            </div>
                        @else
                            <div class="form-text">Gunakan huruf kecil tanpa spasi untuk konsistensi.</div>
                        @endif
                        @error('name')
                            <div class="invalid-feedback d-block">
                                <i class="bx bx-error-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Info --}}
                    <div class="alert alert-warning d-flex gap-2 py-2 px-3 small mb-3" role="alert">
                        <i class="bx bx-bulb flex-shrink-0 fs-5"></i>
                        <span>
                            Perubahan permission akan langsung berlaku untuk semua pengguna yang memiliki role ini.
                        </span>
                    </div>

                    {{-- Ringkasan Permission Terpilih --}}
                    <div class="p-3 rounded-2 bg-light-subtle border">
                        <p class="small text-muted mb-1 fw-semibold">Permission Dipilih:</p>
                        <h4 class="mb-0 fw-bold text-warning" id="selectedCount">
                            {{ count($rolePermissionIds) }}
                        </h4>
                        <p class="small text-muted mb-0">
                            dari {{ collect($groupedPermissions)->flatten()->count() }} total permission
                        </p>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="card-footer border-top d-flex flex-column gap-2 py-3">
                    <button type="submit" class="btn btn-warning w-100 text-white">
                        <i class="bx bx-save me-1"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bx bx-x me-1"></i>Batal
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT: Permission Matrix --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="bx bx-lock-open-alt text-success me-2"></i>Assign Permissions
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnSelectAll">
                            <i class="bx bx-check-double me-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearAll">
                            <i class="bx bx-x me-1"></i>Hapus Semua
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(empty($groupedPermissions))
                        <div class="text-center py-5 text-muted">
                            <i class="bx bx-lock fs-1 mb-2 d-block"></i>
                            <p class="mb-0">Belum ada permission yang terdaftar.</p>
                        </div>
                    @else
                        <div class="accordion accordion-flush" id="permissionAccordion">
                            @foreach($groupedPermissions as $groupName => $permissions)
                            @php
                                $groupKey      = Str::slug($groupName);
                                $groupChecked  = collect($permissions)->filter(fn($p) => in_array($p->id, old('permissions', $rolePermissionIds)))->count();
                            @endphp

                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header" id="heading-{{ $groupKey }}">
                                    <button
                                        class="accordion-button {{ $groupChecked > 0 ? '' : 'collapsed' }} fw-semibold py-3"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $groupKey }}"
                                        aria-expanded="{{ $groupChecked > 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse-{{ $groupKey }}"
                                    >
                                        <span class="me-auto d-flex align-items-center gap-2">
                                            <i class="bx bx-folder {{ $groupChecked > 0 ? 'text-warning' : 'text-muted' }}"></i>
                                            {{ $groupName }}
                                            @if($groupChecked > 0)
                                                <span class="badge bg-warning text-dark small">aktif</span>
                                            @endif
                                        </span>
                                        <span class="badge {{ $groupChecked > 0 ? 'bg-warning text-dark' : 'bg-primary' }} rounded-pill ms-2 me-2 group-badge" data-group="{{ $groupKey }}">
                                            {{ $groupChecked }}/{{ count($permissions) }}
                                        </span>
                                    </button>
                                </h2>

                                <div id="collapse-{{ $groupKey }}"
                                     class="accordion-collapse collapse {{ $groupChecked > 0 ? 'show' : '' }}"
                                     aria-labelledby="heading-{{ $groupKey }}"
                                     data-bs-parent="">
                                    <div class="accordion-body pb-3 pt-2">

                                        {{-- Group Select All --}}
                                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                            <div class="form-check mb-0">
                                                <input
                                                    class="form-check-input select-all-group"
                                                    type="checkbox"
                                                    id="selectAll-{{ $groupKey }}"
                                                    data-group="{{ $groupKey }}"
                                                    {{ $groupChecked === count($permissions) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label fw-semibold text-warning" for="selectAll-{{ $groupKey }}">
                                                    Pilih Semua — {{ $groupName }}
                                                </label>
                                            </div>
                                            <span class="text-muted small">{{ count($permissions) }} permission</span>
                                        </div>

                                        {{-- Permission Checkboxes --}}
                                        <div class="row g-2">
                                            @foreach($permissions as $permission)
                                            @php
                                                $action = explode(' ', $permission->name)[0];
                                                $actionIcons = [
                                                    'view'   => ['icon' => 'bx-show',        'color' => 'info'],
                                                    'create' => ['icon' => 'bx-plus-circle',  'color' => 'success'],
                                                    'edit'   => ['icon' => 'bx-edit',         'color' => 'warning'],
                                                    'delete' => ['icon' => 'bx-trash',        'color' => 'danger'],
                                                    'update' => ['icon' => 'bx-refresh',      'color' => 'warning'],
                                                    'export' => ['icon' => 'bx-export',       'color' => 'primary'],
                                                    'import' => ['icon' => 'bx-import',       'color' => 'secondary'],
                                                ];
                                                $actionInfo = $actionIcons[$action] ?? ['icon' => 'bx-key', 'color' => 'secondary'];
                                                $isChecked  = in_array($permission->id, old('permissions', $rolePermissionIds));
                                            @endphp
                                            <div class="col-sm-6 col-md-4">
                                                <label class="permission-card d-flex align-items-center gap-2 p-2 rounded-2 border {{ $isChecked ? 'active' : '' }}"
                                                       for="perm-{{ $permission->id }}"
                                                       style="cursor:pointer; transition: all .2s;">
                                                    <input
                                                        class="form-check-input permission-checkbox flex-shrink-0 mt-0"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        id="perm-{{ $permission->id }}"
                                                        data-group="{{ $groupKey }}"
                                                        {{ $isChecked ? 'checked' : '' }}
                                                    >
                                                    <span class="badge bg-label-{{ $actionInfo['color'] }} flex-shrink-0">
                                                        <i class="bx {{ $actionInfo['icon'] }}"></i>
                                                    </span>
                                                    <span class="small text-truncate" title="{{ $permission->name }}">
                                                        {{ $permission->name }}
                                                    </span>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if(!empty($groupedPermissions))
                <div class="card-footer border-top py-3 d-flex align-items-center justify-content-between">
                    <span class="text-muted small">
                        <i class="bx bx-info-circle me-1"></i>
                        Accordion yang terbuka menandakan ada permission aktif di grup tersebut.
                    </span>
                    <button type="submit" class="btn btn-warning btn-sm text-white">
                        <i class="bx bx-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- /row --}}
</form>

@endsection

@section('page-script')
<style>
    .permission-card:hover {
        background-color: var(--bs-warning-bg-subtle);
        border-color: var(--bs-warning) !important;
    }
    .permission-card.active {
        background-color: var(--bs-warning-bg-subtle);
        border-color: var(--bs-warning) !important;
    }
    .accordion-button:not(.collapsed) {
        background-color: var(--bs-warning-bg-subtle);
        color: var(--bs-warning-text-emphasis);
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const allCheckboxes   = document.querySelectorAll('.permission-checkbox');
    const selectedCountEl = document.getElementById('selectedCount');
    const btnSelectAll    = document.getElementById('btnSelectAll');
    const btnClearAll     = document.getElementById('btnClearAll');

    // ─── Helpers ──────────────────────────────────────────────────────────────

    function updateBadge(groupKey) {
        const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${groupKey}"]`);
        const checkedCount    = document.querySelectorAll(`.permission-checkbox[data-group="${groupKey}"]:checked`).length;
        const total           = groupCheckboxes.length;
        const badge           = document.querySelector(`.group-badge[data-group="${groupKey}"]`);
        const groupAllCb      = document.getElementById(`selectAll-${groupKey}`);

        if (badge) {
            badge.textContent = `${checkedCount}/${total}`;
            badge.className   = badge.className.replace(/bg-\S+/, checkedCount > 0 ? 'bg-warning text-dark' : 'bg-primary');
        }
        if (groupAllCb) {
            groupAllCb.checked       = checkedCount === total && total > 0;
            groupAllCb.indeterminate = checkedCount > 0 && checkedCount < total;
        }
    }

    function updateGlobalCount() {
        const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        if (selectedCountEl) selectedCountEl.textContent = checkedCount;
    }

    function syncCardState(checkbox) {
        const card = checkbox.closest('.permission-card');
        if (card) card.classList.toggle('active', checkbox.checked);
    }

    function allGroups() {
        return new Set([...allCheckboxes].map(cb => cb.dataset.group));
    }

    // ─── Individual checkbox change ────────────────────────────────────────────
    allCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            syncCardState(this);
            updateBadge(this.dataset.group);
            updateGlobalCount();
        });
    });

    // ─── Group "Select All" checkboxes ────────────────────────────────────────
    document.querySelectorAll('.select-all-group').forEach(function (groupAllCb) {
        groupAllCb.addEventListener('change', function () {
            const groupKey        = this.dataset.group;
            const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${groupKey}"]`);
            groupCheckboxes.forEach(cb => {
                cb.checked = this.checked;
                syncCardState(cb);
            });
            updateBadge(groupKey);
            updateGlobalCount();
        });
    });

    // ─── Global "Select All" button ───────────────────────────────────────────
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            allCheckboxes.forEach(cb => { cb.checked = true; syncCardState(cb); });
            allGroups().forEach(g => updateBadge(g));
            updateGlobalCount();
        });
    }

    // ─── Global "Clear All" button ────────────────────────────────────────────
    if (btnClearAll) {
        btnClearAll.addEventListener('click', function () {
            allCheckboxes.forEach(cb => { cb.checked = false; syncCardState(cb); });
            allGroups().forEach(g => updateBadge(g));
            updateGlobalCount();
        });
    }
});
</script>
@endsection
