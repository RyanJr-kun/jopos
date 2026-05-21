<?php

namespace App\Http\Controllers\hrd;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ 
            new Middleware('permission:view-pelanggan', only: ['index','getJson']),
            new Middleware('permission:create-pelanggan', only: ['create', 'store']),
            new Middleware('permission:edit-pelanggan', only: ['edit', 'update']),
            new Middleware('permission:delete-pelanggan', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mulai query builder
        $statuses = Customer::select('status')->distinct()->pluck('status');
        $query = Customer::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $customers = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.hrd.customer._pelanggan_table', compact('customers'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('content.hrd.customer.index', compact('customers', 'statuses'));
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
            'name' => 'required|string|max:255|unique:customers,name',
            'kontak' => 'required|string|max:20|unique:customers,kontak',
            'email' => 'nullable|email|unique:customers,email',
            'alamat' => 'nullable|string',
        ]);

        // Handle 'status' for both form submission and JSON request
        if ($request->isJson()) {
            $validatedData['status'] = $request->input('status', true); // Default to true if not present
        } else {
            $validatedData['status'] = $request->has('status');
        }

        $pelanggan = Customer::create($validatedData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'pelanggan baru berhasil ditambahkan!',
                'data'    => $pelanggan
            ], 201);
        }

        return redirect()->route('pelanggan.index')->with('success', 'pelanggan baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $pelanggan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $pelanggan)
    {
        //
    }

    public function getjson(Customer $pelanggan)
    {
        return response()->json($pelanggan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $pelanggan)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('customers')->ignore($pelanggan->id)],
            'kontak' => ['required', 'string', 'max:20', Rule::unique('customers', 'kontak')->ignore($pelanggan->id)],
            'email' => ['nullable', 'email', Rule::unique('customers', 'email')->ignore($pelanggan->id)],
            'alamat' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['status'] = $request->boolean('status');

        $pelanggan->update($validatedData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui!'
            ]);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Customer $pelanggan)
    {
        if ($pelanggan->sales()->count() > 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak dapat dihapus karena masih memiliki transaksi penjualan terkait!'
                ], 422); // 422 Unprocessable Entity
            }
            return back()->with('error', 'Customer tidak dapat dihapus karena masih memiliki transaksi penjualan terkait!');
        }

        $pelanggan->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus!'
            ]);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil dihapus!');
    }
}
