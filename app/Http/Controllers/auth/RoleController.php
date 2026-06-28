<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-roles',   only: ['index']),
            new Middleware('permission:create-roles', only: ['create', 'store']),
            new Middleware('permission:edit-roles',   only: ['edit', 'update']),
            new Middleware('permission:delete-roles', only: ['destroy']),
        ];
    }

    /**
     * Kelompokkan permissions berdasarkan modules.name (kolom baru).
     * Memanfaatkan relasi ke tabel modules agar tidak perlu string-parsing.
     *
     * Return: ['banner' => [Permission, ...], 'brand' => [...], ...]
     */
    private function groupedPermissions(): array
    {
        // Eager-load relasi module sekaligus, satu query + join
        $permissions = Permission::select('permissions.*')
            ->join('modules', 'modules.id', '=', 'permissions.modules_id')
            ->orderBy('modules.name')   // urut alfabet per modul
            ->orderByRaw("FIELD(permissions.action, 'view','create','edit','delete','print','export')")
            ->get();

        $groups = [];
        foreach ($permissions as $permission) {
            // Gunakan nama modul dari DB — tidak perlu parse string lagi
            $moduleName = $permission->module_name ?? $permission->getRelationValue('module')?->name;

            // Fallback: ambil via join alias jika relasi belum didefinisikan di model
            if (!$moduleName) {
                // kolom modules.name ikut di-select via join
                $moduleName = \DB::table('modules')
                    ->where('id', $permission->modules_id)
                    ->value('name');
            }

            $groups[$moduleName][] = $permission;
        }

        return $groups;
    }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('id')->get();
        $modules = DB::table('modules')->pluck('name', 'id');

        return view('content.auth.role.index', compact('roles', 'modules'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $groupedPermissions = $this->groupedPermissions();
        return view('content.auth.role.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions'     => ['nullable', 'array'],
            'permissions.*'   => ['integer', 'exists:permissions,id'],
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah digunakan, silakan pilih nama lain.',
            'name.max'      => 'Nama role maksimal 100 karakter.',
        ]);

        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role \"<strong>{$role->name}</strong>\" berhasil dibuat dan permission telah dikonfigurasi.");
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $groupedPermissions = $this->groupedPermissions();
        $rolePermissionIds  = $role->permissions->pluck('id')->toArray();

        return view('content.auth.role.edit', compact('role', 'groupedPermissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        if (strtolower($role->name) === 'admin') {
            $request->merge(['name' => 'admin']);
        }

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah digunakan, silakan pilih nama lain.',
        ]);

        $role->update(['name' => $validated['name']]);

        $permissions = !empty($validated['permissions'])
            ? Permission::whereIn('id', $validated['permissions'])->get()
            : [];

        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')
            ->with('success', "Role \"<strong>{$role->name}</strong>\" berhasil diperbarui.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if (strtolower($role->name) === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Role <strong>Admin</strong> tidak dapat dihapus karena merupakan role sistem.');
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Role \"<strong>{$role->name}</strong>\" tidak dapat dihapus karena masih digunakan oleh {$userCount} pengguna.");
        }

        $roleName = $role->name;
        $role->syncPermissions([]);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role \"<strong>{$roleName}</strong>\" berhasil dihapus.");
    }
}
