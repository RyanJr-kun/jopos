<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\Warrantie;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Validation\Rule;

class WarrantieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Warrantie::latest();

        // Filter berdasarkan pencarian name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $warranties = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.master.garansi._garansi_table', compact('warranties'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('content.master.garansi.garansi', compact('warranties'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:warranties',
            'slug' => 'required|string|max:100|unique:warranties',
            'duration' => 'required|integer|min:1',
            'period' => 'required|string|in:Day,Week,Month,Year',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $totalDays = 0;
        switch ($validated['period']) {
            case 'Year':
                $totalDays = $validated['duration'] * 360;
                break;
            case 'Month':
                $totalDays = $validated['duration'] * 30;
                break;
            case 'Week':
                $totalDays = $validated['duration'] * 7;
                break;
            case 'Day':
                $totalDays = $validated['duration'];
                break;
        }

        $dataToStore = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
            'duration' => $totalDays,
        ];

        Warrantie::create($dataToStore);

        // Langsung redirect (metode biasa)
        return redirect()->route('garansi.index')->with('success', 'Warrantie Baru Berhasil Ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warrantie $garansi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getWarrantieJson(Warrantie $garansi)
    {
        return response()->json($garansi);
    }

    public function edit(Warrantie $garansi)
    {
        return redirect()->route('garansi.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warrantie $garansi)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('warranties')->ignore($garansi->id)],
            'slug' => ['required', 'max:255', Rule::unique('warranties')->ignore($garansi->id)],
            'duration' => 'required|integer|min:1',
            'period' => 'required|string|in:Day,Week,Month,Year',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $totalDays = 0;
        switch ($validated['period']) {
            case 'Year':
                $totalDays = $validated['duration'] * 360; 
                break;
            case 'Month':
                $totalDays = $validated['duration'] * 30;
                break;
            case 'Week':
                $totalDays = $validated['duration'] * 7;
                break;
            case 'Day':
                $totalDays = $validated['duration'];
                break;
        }

        $dataToUpdate = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
            'duration' => $totalDays, 
        ];

        $garansi->update($dataToUpdate);

        // Langsung redirect (metode biasa)
        return redirect()->route('garansi.index')->with('success', 'Warrantie Berhasil Diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warrantie $garansi)
    {
        if ($garansi->products()->count() > 0) {
            return back()->with('error', 'Warrantie tidak dapat dihapus karena masih memiliki produk terkait!');
        }
        $garansi->delete();
        return redirect()->route('garansi.index')->with('success', 'Warrantie Berhasil Dihapus.');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(Warrantie::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
