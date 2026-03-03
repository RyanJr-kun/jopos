<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\Garansi;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class GaransiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Garansi::latest();

        // Filter berdasarkan pencarian nama
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $garansis = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.produk._garansi_table', compact('garansis'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('dashboard.produk.garansi', [
            'title' => 'Manajemen Garansi',
            'garansis' => $garansis
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
            'nama' => 'required|string|max:100|unique:garansis',
            'slug' => 'required|string|max:100|unique:garansis',
            'durasi' => 'required|integer|min:1',
            'period' => 'required|string|in:Day,Week,Month,Year',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        // Kalkulasi total bulan. Unit dasar penyimpanan adalah bulan.
        $totalDays = 0;
        switch ($validated['period']) {
            case 'Year':
                $totalDays = $validated['durasi'] * 360;
                break;
            case 'Month':
                $totalDays = $validated['durasi'] * 30;
                break;
            case 'Week':
                $totalDays = $validated['durasi'] * 7;
                break;
            case 'Day':
                $totalDays = $validated['durasi'];
                break;
        }

        // Siapkan data untuk disimpan, termasuk slug
        $dataToStore = [
            'nama' => $validated['nama'],
            'slug' => $validated['slug'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $request->has('status'),
            'durasi' => $totalDays,
        ];

        $garansi = Garansi::create($dataToStore);
        if ($request->wantsJson()) {
            // Muat ulang model untuk mendapatkan atribut tambahan seperti 'formatted_duration'
            $garansi->refresh();
            // Secara eksplisit tambahkan accessor 'formatted_duration' ke output JSON
            $garansi->append('formatted_duration');

            return response()->json([
                'success' => true,
                'message' => 'Garansi Baru Berhasil Ditambahkan.',
                'data'    => $garansi
            ]);
        }

        // Fallback untuk non-AJAX request
        Alert::success('Berhasil', 'Garansi Baru Berhasil Ditambahkan.');
        return redirect()->route('garansi.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Garansi $garansi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getGaransiJson(Garansi $garansi)
    {
        return response()->json($garansi);
    }

    public function edit(Garansi $garansi)
    {
        return redirect()->route('garansi.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Garansi $garansi)
    {
        // Validasi untuk update
        $validated = $request->validate([
            'nama' => ['required', 'max:255', Rule::unique('garansis')->ignore($garansi->id)],
            'slug' => ['required', 'max:255', Rule::unique('garansis')->ignore($garansi->id)],
            'durasi' => 'required|integer|min:1',
            'period' => 'required|string|in:Day,Week,Month,Year',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        // Kalkulasi ulang total bulan. Unit dasar penyimpanan adalah bulan.
        $totalDays = 0;
        switch ($validated['period']) {
            case 'Year':
                $totalDays = $validated['durasi'] * 360; // 12 bulan * 30 hari
                break;
            case 'Month':
                $totalDays = $validated['durasi'] * 30;
                break;
            case 'Week':
                $totalDays = $validated['durasi'] * 7;
                break;
            case 'Day':
                $totalDays = $validated['durasi'];
                break;
        }

        // Siapkan data untuk diupdate
        $dataToUpdate = [
            'nama' => $validated['nama'],
            'slug' => $validated['slug'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $request->has('status'),
            'durasi' => $totalDays, // Simpan total HARI
        ];

        $garansi->update($dataToUpdate);

        if ($request->wantsJson()) {
            $garansi->refresh();
            $garansi->append('formatted_duration');
            return response()->json([
                'success' => true,
                'message' => 'Garansi Berhasil Diperbarui.',
                'data'    => $garansi
            ]);
        }

        Alert::success('Berhasil', 'Garansi Berhasil Diperbarui.');
        return redirect()->route('garansi.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garansi $garansi)
    {
        if ($garansi->produks()->count() > 0) {
            Alert::error('Gagal', 'Garansi tidak dapat dihapus karena masih memiliki produk terkait!');
            return back();
        }
        $garansi->delete();
        Alert::success('Berhasil', 'Garansi Berhasil Dihapus.');
        return redirect()->route('garansi.index');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(Garansi::class, 'slug', $request->nama);
        return response()->json(['slug' => $slug]);
    }
}
