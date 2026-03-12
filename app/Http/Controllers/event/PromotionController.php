<?php

namespace App\Http\Controllers\event;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Promotion::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $promotions = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('content.promo._promo_table', compact('promotions'))->render();
        }

        return view('content.promo.index', [
            'title' => 'Manajemen Promotion & Diskon',
            'promotions' => $promotions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.promo.create', [
            'title' => 'Buat Promotion Baru',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promotions,code',
            'type' => 'required|in:percentage,fixed',
            'nilai_diskon' => 'required|numeric|min:0',
            'min_pembelian' => 'nullable|numeric|min:0',
            'max_diskon' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',

        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['status'] = $request->has('status');

        $promo = Promotion::create($validatedData);

        if ($request->has('products')) {
            $promo->products()->sync($request->products);
        }

        Alert::success('Berhasil', 'Promotion baru berhasil ditambahkan!');
        return redirect()->route('promo.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promo)
    {
        return view('content.promo.show', [
            'title' => 'Detail Promotion: ' . $promo->name,
            'promo' => $promo->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promo)
    {
        return view('content.promo.edit', [
            'title' => 'Edit Promotion: ' . $promo->name,
            'promo' => $promo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promo)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:50', Rule::unique('promotions', 'code')->ignore($promo->id)],
            'type' => 'required|in:percentage,fixed',
            'nilai_diskon' => 'required|numeric|min:0',
            'min_pembelian' => 'nullable|numeric|min:0',
            'max_diskon' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['status'] = $request->has('status');

        $promo->update($validatedData);

        $promo->products()->sync($request->products ?? []);

        Alert::success('Berhasil', 'Promotion berhasil diperbarui!');
        return redirect()->route('promo.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promo)
    {
        try {
            $promo->delete();
            Alert::success('Berhasil', 'Promotion berhasil dihapus!');
            return redirect()->route('promo.index');
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat menghapus promo: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Get JSON data for a specific resource.
     */
    public function getJson(Promotion $promo)
    {
        return response()->json($promo);
    }

    /**
     * Validate a promo code via AJAX.
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $kodePromotion = $request->input('code');
        $subtotal = $request->input('subtotal');

        $promo = Promotion::where('code', $kodePromotion)->first();

        // Cek 1: Kode promo tidak ditemukan
        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak ditemukan.'], 404);
        }

        // Cek 2: Promotion tidak aktif
        if (!$promo->status) {
            return response()->json(['success' => false, 'message' => 'Promotion sudah tidak aktif.'], 422);
        }

        // Cek 3: Tanggal promo belum/sudah lewat
        $now = now();
        if ($now->isBefore($promo->tanggal_mulai) || $now->isAfter($promo->tanggal_berakhir)) {
            return response()->json(['success' => false, 'message' => 'Promotion tidak berlaku pada tanggal ini.'], 422);
        }

        // Cek 4: Minimum pembelian tidak tercapai
        if ($promo->min_pembelian && $subtotal < $promo->min_pembelian) {
            $minPurchaseFormatted = 'Rp ' . number_format($promo->min_pembelian, 0, ',', '.');
            return response()->json(['success' => false, 'message' => "Minimum pembelian untuk promo ini adalah {$minPurchaseFormatted}."], 422);
        }

        // Jika semua validasi lolos, kembalikan data promo
        return response()->json(['success' => true, 'promo' => $promo]);
    }

    /**
     * Update the status of a promo via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Promotion  $promo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Promotion $promo)
    {
        // Hanya update jika statusnya saat ini aktif
        if ($promo->status) {
            $promo->status = false;
            $promo->save();

            return response()->json(['success' => true, 'message' => 'Status promo berhasil diperbarui.']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status promo sudah tidak aktif.'
        ], 409); // 409 Conflict
    }
}
