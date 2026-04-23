<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Income::with(['transaction_category', 'user'])->latest();

        $kategoriFilters = TransactionCategory::where('type', 'income')
            ->whereHas('incomes')
            ->orderBy('name')
            ->get();
            
        $allKategoris = TransactionCategory::where('type', 'income')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('referensi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('transaction_category_id', $request->input('kategori_id'));
        }

        $incomes = $query->paginate(15)->withQueryString();

        // --- PRO AJAX RETURN ---
        if ($request->ajax()) {
            $html = view('content.keuangan.pemasukan', [
                'title' => 'Income',
                'incomes' => $incomes,
                'kategoriFilters' => $kategoriFilters,
                'allKategoris' => $allKategoris,
                'referensi_otomatis' => $this->generateIncomeReferenceNumber()
            ])->fragment('income-table-area');

            return response()->json([
                'html' => $html,
                'total' => $incomes->total()
            ]);
        }

        return view('content.keuangan.pemasukan', [
            'title' => 'Income',
            'incomes' => $incomes,
            'kategoriFilters' => $kategoriFilters,
            'allKategoris' => $allKategoris,
            'referensi_otomatis' => $this->generateIncomeReferenceNumber()
        ]);
    }

    /**
     * Menghasilkan nomor referensi income yang unik.
     */
    private function generateIncomeReferenceNumber()
    {
        // Format: IN-YYYYMMDD-XXXX (e.g., IN-20230831-0001)
        $date = now()->format('Ymd');
        $prefix = 'IN-' . $date . '-';

        // Cari referensi terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastIncome = Income::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastIncome) {
            $lastSequence = (int) substr($lastIncome->referensi, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
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
        $validateData = $request->validate([
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'referensi' => 'required|string|max:100|unique:incomes',
            'keterangan' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validateData['user_id'] = Auth::id();

        Income::create($validateData);
        return redirect()->route('income.index')->with('success', 'Income baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function getjson(Income $income)
    {
        return response()->json($income);
    }

    public function update(Request $request, Income $income)
    {
        $rules = [
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'referensi' => ['required', 'string', 'max:100', Rule::unique('incomes')->ignore($income->id)],
            'keterangan' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        $validateData = $request->validate($rules);
        $validateData['user_id'] = Auth::id();

        $income->update($validateData);
        return redirect()->route('income.index')->with('success', 'Income Berhasil Diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('income.index')->with('success', 'Income Berhasil Dihapus!');
    }
}
