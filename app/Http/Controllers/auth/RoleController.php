<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ 
            new Middleware('permission:view-roles', only: ['index','groupedPermissions']), // Tambahkan permission untuk akses index dan groupedPermissions
            new Middleware('permission:create-roles', only: ['create', 'store']),
            new Middleware('permission:edit-roles', only: ['edit', 'update']),
            new Middleware('permission:delete-roles', only: ['destroy']),
        ];
    }
    /**
     * Kelompokkan permissions berdasarkan prefix kata pertama.
     * Contoh: "view users" => Group "Users"
     */
    private function groupedPermissions(): array
    {
        $allPermissions = Permission::orderBy('name')->get();

        $groups = [];
        foreach ($allPermissions as $permission) {
            // Ambil kata terakhir sebagai nama "modul" (lebih natural untuk format: "view users")
            $parts = explode(' ', $permission->name);
            // Gunakan kata terakhir sebagai grup jika formatnya "action module"
            $groupName = count($parts) > 1
                ? ucwords(implode(' ', array_slice($parts, 1)))
                : ucfirst($parts[0]);

            $groups[$groupName][] = $permission;
        }

        ksort($groups);
        return $groups;
    }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('id')->get();
        return view('content.auth.role.index', compact('roles'));
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
            'name'        => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
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
        $groupedPermissions  = $this->groupedPermissions();
        $rolePermissionIds   = $role->permissions->pluck('id')->toArray();

        return view('content.auth.role.edit', compact('role', 'groupedPermissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Cegah perubahan nama pada role "admin" yang kritis
        if (strtolower($role->name) === 'admin') {
            $request->merge(['name' => 'admin']); // paksa tetap admin
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
        // Tolak penghapusan role admin
        if (strtolower($role->name) === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Role <strong>Admin</strong> tidak dapat dihapus karena merupakan role sistem.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Role \"<strong>{$role->name}</strong>\" tidak dapat dihapus karena masih digunakan oleh {$role->users()->count()} pengguna.");
        }

        $roleName = $role->name;
        $role->syncPermissions([]); // Hapus semua relasi permission
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role \"<strong>{$roleName}</strong>\" berhasil dihapus.");
    }
}
