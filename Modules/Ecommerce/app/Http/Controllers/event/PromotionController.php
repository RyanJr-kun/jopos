<?php

namespace Modules\Ecommerce\Http\Controllers\event;

use App\Http\Controllers\Controller;
use Modules\Ecommerce\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return view('ecommerce::promo._promo_table', compact('promotions'))->render();
        }

        return view('ecommerce::promo.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ecommerce::promo.create', [
            'title' => 'Buat Promotion Baru',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50|unique:promotions,code',
            'type'             => 'required|in:percentage,fixed',

            // Jika tipe persentase, nilai_diskon max 100
            'nilai_diskon'     => [
                'required',
                'numeric',
                'min:0',
                $request->input('type') === 'percentage' ? 'max:100' : 'max:999999999',
            ],

            'min_pembelian'    => 'nullable|numeric|min:0',

            // max_diskon hanya relevan & divalidasi jika tipe persentase
            'max_diskon'       => $request->input('type') === 'percentage'
                ? 'nullable|numeric|min:0'
                : 'nullable',

            'tanggal_mulai'    => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',

            'is_all_products'  => 'nullable|boolean',

            // products hanya divalidasi jika is_all_products TIDAK dicentang
            'products'         => $request->boolean('is_all_products')
                ? 'nullable'
                : 'nullable|array',
            'products.*'       => 'exists:products,id',

            'status'           => 'nullable|boolean',
            'description'      => 'nullable|string',
        ]);

        $validatedData['user_id']         = Auth::id();
        $validatedData['status']          = $request->has('status');
        $validatedData['is_all_products'] = $request->has('is_all_products');

        // Bersihkan max_diskon jika tipe bukan persentase
        if ($request->input('type') !== 'percentage') {
            $validatedData['max_diskon'] = null;
        }

        $promo = Promotion::create($validatedData);

        // Sync produk hanya jika bukan "semua produk"
        if (!$validatedData['is_all_products']) {
            $promo->products()->sync($request->products ?? []);
        } else {
            // Kosongkan relasi produk jika berlaku untuk semua
            $promo->products()->detach();
        }

        return redirect()->route('promo.index')
            ->with('success', 'Promotion baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promo)
    {
        return view('ecommerce::promo.show', [
            'title' => 'Detail Promotion: ' . $promo->name,
            'promo' => $promo->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promo)
    {
        return view('ecommerce::promo.edit', [
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
            'name'             => 'required|string|max:255',
            'code'             => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('promotions', 'code')->ignore($promo->id),
            ],
            'type'             => 'required|in:percentage,fixed',

            'nilai_diskon'     => [
                'required',
                'numeric',
                'min:0',
                $request->input('type') === 'percentage' ? 'max:100' : 'max:999999999',
            ],

            'min_pembelian'    => 'nullable|numeric|min:0',

            'max_diskon'       => $request->input('type') === 'percentage'
                ? 'nullable|numeric|min:0'
                : 'nullable',

            'tanggal_mulai'    => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',

            'is_all_products'  => 'nullable|boolean',

            'products'         => $request->boolean('is_all_products')
                ? 'nullable'
                : 'nullable|array',
            'products.*'       => 'exists:products,id',

            'status'           => 'nullable|boolean',
            'description'      => 'nullable|string',
        ]);

        $validatedData['user_id']         = Auth::id();
        $validatedData['status']          = $request->has('status');
        $validatedData['is_all_products'] = $request->has('is_all_products');

        if ($request->input('type') !== 'percentage') {
            $validatedData['max_diskon'] = null;
        }

        $promo->update($validatedData);

        if (!$validatedData['is_all_products']) {
            $promo->products()->sync($request->products ?? []);
        } else {
            $promo->products()->detach();
        }

        return redirect()->route('promo.index')
            ->with('success', 'Promotion berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promo)
    {
        try {
            $promo->delete();
            return redirect()->route('promo.index')
                ->with('success', 'Promotion berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus promo: ' . $e->getMessage());
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
            'code'     => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $kodePromotion = $request->input('code');
        $subtotal      = $request->input('subtotal');

        $promo = Promotion::where('code', $kodePromotion)->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak ditemukan.'], 404);
        }

        if (!$promo->status) {
            return response()->json(['success' => false, 'message' => 'Promotion sudah tidak aktif.'], 422);
        }

        $now = now();
        if ($now->isBefore($promo->tanggal_mulai) || $now->isAfter($promo->tanggal_berakhir)) {
            return response()->json(['success' => false, 'message' => 'Promotion tidak berlaku pada tanggal ini.'], 422);
        }

        if ($promo->min_pembelian && $subtotal < $promo->min_pembelian) {
            $minFormatted = 'Rp ' . number_format($promo->min_pembelian, 0, ',', '.');
            return response()->json([
                'success' => false,
                'message' => "Minimum pembelian untuk promo ini adalah {$minFormatted}.",
            ], 422);
        }

        return response()->json(['success' => true, 'promo' => $promo]);
    }

    /**
     * Update the status of a promo via AJAX.
     */
    public function updateStatus(Request $request, Promotion $promo)
    {
        if ($promo->status) {
            $promo->status = false;
            $promo->save();

            return response()->json(['success' => true, 'message' => 'Status promo berhasil diperbarui.']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status promo sudah tidak aktif.',
        ], 409);
    }
}
