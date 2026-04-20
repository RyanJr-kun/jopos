<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use Illuminate\Validation\Rule; // Import Rule untuk validasi unique saat update
use App\Models\Supplier;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = Supplier::select('status')->distinct()->pluck('status');
        $query = Supplier::latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Terapkan filter status jika ada input 'status'
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $suppliers = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.pembelian._pemasok_table', compact('suppliers'))->render();
        }

        return view('content.pembelian.pemasok', compact('suppliers', 'statuses'));
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
            'name' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kontak' => 'required|string|max:20|unique:suppliers,kontak',
            'email' => 'nullable|email|unique:suppliers,email',
            'alamat' => 'nullable|string',
            'note' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validatedData['status'] = $request->has('status');
        $pemasok = Supplier::create($validatedData);

        // Cek jika request adalah AJAX (misalnya dari modal)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier baru berhasil ditambahkan!',
                'data'    => $pemasok,
            ]);
        }

        // Redirect standar jika bukan AJAX
        return redirect('/pemasok')->with('success', 'Supplier baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $pemasok)
    {
        //
    }

    /**
     * Get JSON data for a specific resource.
     * Digunakan oleh modal edit untuk mengisi form.
     */
    public function getjson(Supplier $pemasok)
    {
        return response()->json($pemasok);
    }

    /**
     * Show the form for editing the specified resource.
     * Karena edit ditangani oleh modal di halaman index, metode ini hanya redirect.
     */
    public function edit(Supplier $pemasok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $pemasok)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kontak' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'kontak')->ignore($pemasok->id)],
            'email' => ['nullable', 'email', Rule::unique('suppliers', 'email')->ignore($pemasok->id)],
            'alamat' => 'nullable|string',
            'status' => 'nullable|boolean',
            'note' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['status'] = $request->has('status');

        $pemasok->update($validatedData);
        return redirect('/pemasok')->with('success', 'Data pemasok berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $pemasok)
    {
        // if ($pemasok->purchases()->exists()) {
        //     return back()->with('error', 'Supplier tidak dapat dihapus karena masih memiliki transaksi pembelian terkait!');
        // }

        $pemasok->delete();
        return redirect('/pemasok')->with('success', 'Supplier berhasil dihapus!');
    }
}
