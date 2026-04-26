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
                    <li class="breadcrumb-item active">Edit — <strong class="text-capitalize">{{ $role->name }}</strong>
                    </li>
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
                                    'admin' => 'danger',
                                    'manager' => 'warning',
                                    'kasir' => 'info',
                                    'teknisi' => 'primary',
                                    'staff' => 'secondary',
                                    'sales' => 'success',
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
                            <input type="text" id="roleName" name="name"
                                class="form-control form-control-lg @error('name') is-invalid @enderror
                                   {{ strtolower($role->name) === 'admin' ? 'bg-light' : '' }}"
                                placeholder="Contoh: kasir, manager, teknisi" value="{{ old('name', $role->name) }}"
                                required autocomplete="off" {{ strtolower($role->name) === 'admin' ? 'readonly' : '' }}>
                            @if (strtolower($role->name) === 'admin')
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

            {{-- RIGHT: Permission Matrix Table --}}
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
                        @if (empty($groupedPermissions))
                            <div class="text-center py-5 text-muted">
                                <i class="bx bx-lock fs-1 mb-2 d-block"></i>
                                <p class="mb-0">Belum ada permission yang terdaftar.</p>
                            </div>
                        @else
                            @php
                                $allActions = [
                                    'view' => ['label' => 'View', 'icon' => 'bx-show', 'color' => 'info'],
                                    'create' => ['label' => 'Create', 'icon' => 'bx-plus-circle', 'color' => 'success'],
                                    'edit' => ['label' => 'Edit', 'icon' => 'bx-edit', 'color' => 'warning'],
                                    'delete' => ['label' => 'Delete', 'icon' => 'bx-trash', 'color' => 'danger'],
                                    'print' => ['label' => 'Print', 'icon' => 'bx-printer', 'color' => 'secondary'],
                                    'export' => ['label' => 'Export', 'icon' => 'bx-export', 'color' => 'primary'],
                                ];
                                $usedActions = [];
                                foreach ($groupedPermissions as $perms) {
                                    foreach ($perms as $p) {
                                        $act = explode('-', $p->name)[0];
                                        if (array_key_exists($act, $allActions)) {
                                            $usedActions[$act] = true;
                                        }
                                    }
                                }
                                $activeActions = array_intersect_key($allActions, $usedActions);
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover permission-matrix mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 py-3" style="min-width:180px">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="checkbox" class="form-check-input mt-0"
                                                        id="cbSelectAllGlobal" title="Pilih / Hapus Semua">
                                                    <span class="fw-bold text-muted small text-uppercase">Menu /
                                                        Modul</span>
                                                </div>
                                            </th>
                                            @foreach ($activeActions as $actKey => $actMeta)
                                                <th class="text-center py-3" style="min-width:80px">
                                                    <span
                                                        class="badge bg-label-{{ $actMeta['color'] }} d-inline-flex align-items-center gap-1 px-2 py-1">
                                                        <i class="bx {{ $actMeta['icon'] }}"></i>
                                                        <span>{{ $actMeta['label'] }}</span>
                                                    </span>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedPermissions as $groupName => $permissions)
                                            @php
                                                $groupKey = Str::slug($groupName);
                                                $permMap = [];
                                                foreach ($permissions as $p) {
                                                    $act = explode('-', $p->name)[0];
                                                    $permMap[$act] = $p;
                                                }
                                                $groupChecked = collect($permissions)
                                                    ->filter(
                                                        fn($p) => in_array(
                                                            $p->id,
                                                            old('permissions', $rolePermissionIds),
                                                        ),
                                                    )
                                                    ->count();
                                                $allChecked = $groupChecked === count($permissions);
                                            @endphp
                                            <tr class="permission-row {{ $groupChecked > 0 ? 'row-has-active' : '' }}"
                                                data-group="{{ $groupKey }}">

                                                {{-- Nama Grup + Group Select All --}}
                                                <td class="ps-3 py-2 align-middle">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input mt-0 select-all-group flex-shrink-0"
                                                            type="checkbox" id="selectAll-{{ $groupKey }}"
                                                            data-group="{{ $groupKey }}"
                                                            {{ $allChecked ? 'checked' : '' }}
                                                            title="Pilih semua {{ $groupName }}">
                                                        <label for="selectAll-{{ $groupKey }}"
                                                            class="mb-0 fw-semibold small" style="cursor:pointer">
                                                            {{ $groupName }}
                                                            @if ($groupChecked > 0)
                                                                <span
                                                                    class="badge bg-label-warning text-dark ms-1">aktif</span>
                                                            @endif
                                                        </label>
                                                        <span
                                                            class="badge {{ $groupChecked > 0 ? 'bg-warning text-dark' : 'bg-primary' }} rounded-pill ms-auto group-badge"
                                                            data-group="{{ $groupKey }}">
                                                            {{ $groupChecked }}/{{ count($permissions) }}
                                                        </span>
                                                    </div>
                                                </td>

                                                {{-- Kolom per action --}}
                                                @foreach ($activeActions as $actKey => $actMeta)
                                                    <td class="text-center align-middle py-2">
                                                        @if (isset($permMap[$actKey]))
                                                            @php
                                                                $permission = $permMap[$actKey];
                                                                $isChecked = in_array(
                                                                    $permission->id,
                                                                    old('permissions', $rolePermissionIds),
                                                                );
                                                            @endphp
                                                            <div class="perm-cell {{ $isChecked ? 'cell-active' : '' }}"
                                                                data-group="{{ $groupKey }}">
                                                                <input class="form-check-input permission-checkbox"
                                                                    type="checkbox" name="permissions[]"
                                                                    value="{{ $permission->id }}"
                                                                    id="perm-{{ $permission->id }}"
                                                                    data-group="{{ $groupKey }}"
                                                                    {{ $isChecked ? 'checked' : '' }}
                                                                    title="{{ $permission->name }}">
                                                            </div>
                                                        @else
                                                            <span class="text-muted" style="font-size:.75rem">—</span>
                                                        @endif
                                                    </td>
                                                @endforeach

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    @if (!empty($groupedPermissions))
                        <div class="card-footer border-top py-3 d-flex align-items-center justify-content-between">
                            <span class="text-muted small">
                                <i class="bx bx-info-circle me-1"></i>
                                Centang kolom aksi pada setiap baris modul untuk menetapkan permission.
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
        /* ── Permission Matrix Table ── */
        .permission-matrix th,
        .permission-matrix td {
            vertical-align: middle;
        }

        .permission-matrix tbody tr:hover {
            background-color: var(--bs-warning-bg-subtle);
        }

        .permission-matrix tbody tr.row-has-active>td:first-child {
            border-left: 3px solid var(--bs-warning);
        }

        .perm-cell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            transition: background .15s;
        }

        .perm-cell.cell-active {
            background-color: var(--bs-warning-bg-subtle);
        }

        .perm-cell:has(input:checked) {
            background-color: var(--bs-warning-bg-subtle);
        }

        .permission-matrix .form-check-input {
            cursor: pointer;
            width: 1.1rem;
            height: 1.1rem;
        }

        .permission-matrix thead th {
            font-size: .78rem;
            white-space: nowrap;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const allCheckboxes = document.querySelectorAll('.permission-checkbox');
            const selectedCountEl = document.getElementById('selectedCount');
            const btnSelectAll = document.getElementById('btnSelectAll');
            const btnClearAll = document.getElementById('btnClearAll');
            const cbGlobal = document.getElementById('cbSelectAllGlobal');

            // ─── Helpers ──────────────────────────────────────────────────────────────

            function updateBadge(groupKey) {
                const groupCbs = document.querySelectorAll(`.permission-checkbox[data-group="${groupKey}"]`);
                const checkedCount = [...groupCbs].filter(cb => cb.checked).length;
                const total = groupCbs.length;
                const badge = document.querySelector(`.group-badge[data-group="${groupKey}"]`);
                const groupAllCb = document.getElementById(`selectAll-${groupKey}`);
                const row = document.querySelector(`tr.permission-row[data-group="${groupKey}"]`);

                if (badge) {
                    badge.textContent = `${checkedCount}/${total}`;
                    badge.className = badge.className
                        .replace(/bg-\S+/, checkedCount > 0 ? 'bg-warning text-dark' : 'bg-primary')
                        .replace('text-dark', '');
                    if (checkedCount > 0) badge.classList.add('text-dark');
                }
                if (groupAllCb) {
                    groupAllCb.checked = checkedCount === total && total > 0;
                    groupAllCb.indeterminate = checkedCount > 0 && checkedCount < total;
                }
                if (row) row.classList.toggle('row-has-active', checkedCount > 0);
            }

            function updateGlobalCount() {
                const checkedCount = [...allCheckboxes].filter(cb => cb.checked).length;
                if (selectedCountEl) selectedCountEl.textContent = checkedCount;
            }

            function syncCellState(checkbox) {
                const cell = checkbox.closest('.perm-cell');
                if (cell) cell.classList.toggle('cell-active', checkbox.checked);
            }

            function allGroups() {
                return new Set([...allCheckboxes].map(cb => cb.dataset.group));
            }

            // ─── Init ─────────────────────────────────────────────────────────────────
            function initState() {
                allGroups().forEach(g => updateBadge(g));
                updateGlobalCount();
                allCheckboxes.forEach(cb => syncCellState(cb));
            }

            // ─── Individual checkbox change ────────────────────────────────────────────
            allCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    syncCellState(this);
                    updateBadge(this.dataset.group);
                    updateGlobalCount();
                });
            });

            // ─── Group "Select All" checkboxes (per baris) ────────────────────────────
            document.querySelectorAll('.select-all-group').forEach(function(groupAllCb) {
                groupAllCb.addEventListener('change', function() {
                    const groupKey = this.dataset.group;
                    const groupCheckboxes = document.querySelectorAll(
                        `.permission-checkbox[data-group="${groupKey}"]`);
                    groupCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                        syncCellState(cb);
                    });
                    updateBadge(groupKey);
                    updateGlobalCount();
                });
            });

            // ─── Global header checkbox ───────────────────────────────────────────────
            if (cbGlobal) {
                cbGlobal.addEventListener('change', function() {
                    allCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                        syncCellState(cb);
                    });
                    allGroups().forEach(g => updateBadge(g));
                    updateGlobalCount();
                });
            }

            // ─── "Pilih Semua" button ─────────────────────────────────────────────────
            if (btnSelectAll) {
                btnSelectAll.addEventListener('click', function() {
                    allCheckboxes.forEach(cb => {
                        cb.checked = true;
                        syncCellState(cb);
                    });
                    allGroups().forEach(g => updateBadge(g));
                    updateGlobalCount();
                    if (cbGlobal) cbGlobal.checked = true;
                });
            }

            // ─── "Hapus Semua" button ─────────────────────────────────────────────────
            if (btnClearAll) {
                btnClearAll.addEventListener('click', function() {
                    allCheckboxes.forEach(cb => {
                        cb.checked = false;
                        syncCardState(cb);
                    });
                    allGroups().forEach(g => updateBadge(g));
                    updateGlobalCount();
                });
            }
        });
    </script>
@endsection
