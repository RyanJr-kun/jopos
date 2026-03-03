<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = Unit::select('status')->distinct()->pluck('status');
        $query = Unit::withCount('produks')->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama', 'LIKE', "%{$search}%");
        }
        if ($request->filled('status')) {
            $statusValue = $request->input('status') === 'Aktif' ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $units = $query->paginate(15)->withQueryString();
        if ($request->ajax()) {
            return view('dashboard.produk._unit_table', compact('units'))->render();
        }
        return view('dashboard.produk.unit', [
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
            'nama' => 'required|max:255|unique:units',
            'slug' => 'required|max:255|unique:units',
            'singkat' => 'required|max:255|unique:units',
            'status' => 'nullable|boolean',
        ]);

        $validatedData['status'] = $request->has('status');

        Unit::create($validatedData);
        Alert::success('Berhasil', 'Unit Baru Berhasil Ditambahkan.');
        return redirect()->route('unit.index');
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

            'nama' => ['required', 'max:255', Rule::unique('units')->ignore($unit->id)],
            'slug' => ['required', 'max:255', Rule::unique('units')->ignore($unit->id)],
            'singkat' => ['required', 'max:255', Rule::unique('units')->ignore($unit->id)],
            'status' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['status'] = $request->has('status');

        $unit->update($validatedData);
        Alert::success('Berhasil', 'Data Unit Berhasil Diperbarui.');
        return redirect()->route('unit.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        if ($unit->produks()->count() > 0) {
            Alert::error('Gagal', 'Unit tidak dapat dihapus karena masih memiliki produk terkait!');
            return back();
        }
        $unit->delete();
        Alert::success('Berhasil', 'Data Unit Berhasil Dihapus.');
        return redirect()->route('unit.index');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(Unit::class, 'slug', $request->nama);
        return response()->json(['slug' => $slug]);
    }
}
