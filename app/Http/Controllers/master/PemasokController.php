<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use Illuminate\Validation\Rule; // Import Rule untuk validasi unique saat update
use App\Models\Pemasok;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pemasok::latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        // Terapkan filter status jika ada input 'status'
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pemasoks = $query->paginate(10)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.pembelian._pemasok_table', compact('pemasoks'))->render();
        }

        return view('dashboard.pembelian.pemasok', [
            'title' => 'Pemasok',
            'pemasoks' => $pemasoks,
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
            'nama' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kontak' => 'required|string|max:20|unique:pemasoks,kontak',
            'email' => 'nullable|email|unique:pemasoks,email',
            'alamat' => 'nullable|string',
            'note' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validatedData['status'] = $request->has('status');
        $pemasok = Pemasok::create($validatedData);

        // Cek jika request adalah AJAX (misalnya dari modal)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pemasok baru berhasil ditambahkan!',
                'data'    => $pemasok,
            ]);
        }

        // Redirect standar jika bukan AJAX
        Alert::success('Berhasil', 'Pemasok baru berhasil ditambahkan!');
        return redirect('/pemasok');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pemasok $pemasok)
    {
        //
    }

    /**
     * Get JSON data for a specific resource.
     * Digunakan oleh modal edit untuk mengisi form.
     */
    public function getjson(Pemasok $pemasok)
    {
        return response()->json($pemasok);
    }

    /**
     * Show the form for editing the specified resource.
     * Karena edit ditangani oleh modal di halaman index, metode ini hanya redirect.
     */
    public function edit(Pemasok $pemasok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pemasok $pemasok)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kontak' => ['required', 'string', 'max:20', Rule::unique('pemasoks', 'kontak')->ignore($pemasok->id)],
            'email' => ['nullable', 'email', Rule::unique('pemasoks', 'email')->ignore($pemasok->id)],
            'alamat' => 'nullable|string',
            'status' => 'nullable|boolean',
            'note' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['status'] = $request->has('status');

        $pemasok->update($validatedData);
        Alert::success('Berhasil', 'Data pemasok berhasil diperbarui!');
        return redirect('/pemasok');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pemasok $pemasok)
    {
        // if ($pemasok->pembelians()->exists()) {
        //     Alert::error('Gagal', 'Pemasok tidak dapat dihapus karena masih memiliki transaksi pembelian terkait!');
        //     return back();
        // }

        $pemasok->delete();
        Alert::success('Berhasil', 'Pemasok berhasil dihapus!');
        return redirect('/pemasok');
    }
}
