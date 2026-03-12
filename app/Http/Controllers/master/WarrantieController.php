<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\Warrantie;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use RealRashid\SweetAlert\Facades\Alert;
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
            return view('content.produk._garansi_table', compact('warranties'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('content.produk.garansi', [
            'title' => 'Manajemen Warrantie',
            'warranties' => $warranties
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
        // Validasi input. Jika request adalah AJAX dan validasi gagal, Laravel akan otomatis mengirim response JSON 422.
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:warranties',
            'slug' => 'required|string|max:100|unique:warranties',
            'duration' => 'required|integer|min:1',
            'period' => 'required|string|in:Day,Week,Month,Year',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        // Kalkulasi total bulan. Unit dasar penyimpanan adalah bulan.
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

        // Siapkan data untuk disimpan, termasuk slug
        $dataToStore = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
            'duration' => $totalDays,
        ];

        $garansi = Warrantie::create($dataToStore);
        if ($request->wantsJson()) {
            // Muat ulang model untuk mendapatkan atribut tambahan seperti 'formatted_duration'
            $garansi->refresh();
            // Secara eksplisit tambahkan accessor 'formatted_duration' ke output JSON
            $garansi->append('formatted_duration');

            return response()->json([
                'success' => true,
                'message' => 'Warrantie Baru Berhasil Ditambahkan.',
                'data'    => $garansi
            ]);
        }

        // Fallback untuk non-AJAX request
        Alert::success('Berhasil', 'Warrantie Baru Berhasil Ditambahkan.');
        return redirect()->route('garansi.index');
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
        // Validasi untuk update
        $validated = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('warranties')->ignore($garansi->id)],
            'slug' => ['required', 'max:255', Rule::unique('warranties')->ignore($garansi->id)],
            'duration' => 'required|integer|min:1',
            'period' => 'required|string|in:Day,Week,Month,Year',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        // Kalkulasi ulang total bulan. Unit dasar penyimpanan adalah bulan.
        $totalDays = 0;
        switch ($validated['period']) {
            case 'Year':
                $totalDays = $validated['duration'] * 360; // 12 bulan * 30 hari
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

        // Siapkan data untuk diupdate
        $dataToUpdate = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
            'duration' => $totalDays, // Simpan total HARI
        ];

        $garansi->update($dataToUpdate);

        if ($request->wantsJson()) {
            $garansi->refresh();
            $garansi->append('formatted_duration');
            return response()->json([
                'success' => true,
                'message' => 'Warrantie Berhasil Diperbarui.',
                'data'    => $garansi
            ]);
        }

        Alert::success('Berhasil', 'Warrantie Berhasil Diperbarui.');
        return redirect()->route('garansi.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warrantie $garansi)
    {
        if ($garansi->products()->count() > 0) {
            Alert::error('Gagal', 'Warrantie tidak dapat dihapus karena masih memiliki produk terkait!');
            return back();
        }
        $garansi->delete();
        Alert::success('Berhasil', 'Warrantie Berhasil Dihapus.');
        return redirect()->route('garansi.index');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(Warrantie::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
