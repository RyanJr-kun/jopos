<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // app/Http/Controllers/auth/RoleController.php

    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('content.role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $request->name, 'guard_name' => 'web']);
        return back()->with('success', 'Role berhasil dibuat.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Role berhasil dihapus.');
    }
}
