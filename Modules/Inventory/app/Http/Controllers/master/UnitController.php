<?php

namespace Modules\Inventory\Http\Controllers\master;

use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Modules\Inventory\Models\Unit;

class UnitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-unit', only: ['index', 'getUnitJson', 'checkSlug']), 
            new Middleware('permission:create-unit', only: ['create', 'store']),
            new Middleware('permission:edit-unit', only: ['edit', 'update']),
            new Middleware('permission:delete-unit', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = Unit::select('status')->distinct()->pluck('status');
        $query = Unit::withCount('products')->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }
        if ($request->filled('status')) {
            $statusValue = $request->input('status') === 'Aktif' ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $units = $query->paginate(15)->withQueryString();
        if ($request->ajax()) {
            return view('inventory::master.unit._unit_table', compact('units'))->render();
        }
        return view('inventory::master.unit.unit', [
            'title' => 'Units',
            'units' => $units,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:units',
            'slug' => 'required|max:255|unique:units',
            'singkat' => 'required|max:255|unique:units',
            'status' => 'nullable|boolean',
        ]);

        $validatedData['status'] = $request->has('status');

        Unit::create($validatedData);
        return redirect()->route('unit.index')->with('success', 'Unit Baru Berhasil Ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
    }

    public function getUnitJson(Unit $unit)
    {
        return response()->json($unit);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return redirect()->route('unit.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $rules = [

            'name' => ['required', 'max:255', Rule::unique('units')->ignore($unit->id)],
            'slug' => ['required', 'max:255', Rule::unique('units')->ignore($unit->id)],
            'singkat' => ['required', 'max:255', Rule::unique('units')->ignore($unit->id)],
            'status' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['status'] = $request->has('status');

        $unit->update($validatedData);
        return redirect()->route('unit.index')->with('success', 'Data Unit Berhasil Diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        if ($unit->products()->count() > 0) {
            return back()->with('error', 'Unit tidak dapat dihapus karena masih memiliki produk terkait!');
        }
        $unit->delete();
        return redirect()->route('unit.index')->with('success', 'Data Unit Berhasil Dihapus.');
    }

    public function chekSlug(Request $request) 
    {
        $slug = SlugService::createSlug(Unit::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
