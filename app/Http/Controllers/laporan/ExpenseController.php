<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mulai query dengan eager loading untuk efisiensi
        $query = Expense::with(['user', 'transaction_category'])->latest();

        $kategoriFilters = TransactionCategory::where('type', 'expense')
            ->whereHas('expenses')
            ->orderBy('name')
            ->get();
        $allKategoris = TransactionCategory::where('type', 'expense')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('keterangan', 'like', "%{$search}%")
                ->orWhere('referensi', 'like', "%{$search}%");
        }

        // Terapkan filter kategori jika ada
        if ($request->filled('kategori_id')) {
            $query->where('transaction_category_id', $request->input('kategori_id'));
        }

        $expenses = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.keuangan._expense_table', compact('expenses'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('content.keuangan.expense', [
            'title' => 'Data Expense',
            'expenses' => $expenses,
            'kategoriFilters' => $kategoriFilters,
            'allKategoris' => $allKategoris,
            'referensi_otomatis' => $this->generateExpenseReferenceNumber()
        ]);
    }

    /**
     * Menghasilkan nomor referensi expense yang unik.
     */
    private function generateExpenseReferenceNumber()
    {
        // Format: EX-YYYYMMDD-XXXX (e.g., EX-20231027-0001)
        $date = now()->format('Ymd');
        $prefix = 'EX-' . $date . '-';

        // Cari referensi terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastExpense = \App\Models\Expense::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastExpense) {
            $lastSequence = (int) substr($lastExpense->referensi, -4);
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
            'referensi' => 'required|string|max:100|unique:expenses',
            'keterangan' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validateData['user_id'] = Auth::id();

        Expense::create($validateData);
        Alert::success('Berhasil', 'Expense baru berhasil ditambahkan!');
        return redirect()->route('expense.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    public function getjson(Expense $expense)
    {
        return response()->json($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $rules = [
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'referensi' => ['required', 'string', 'max:100', Rule::unique('expenses')->ignore($expense->id)],
            'keterangan' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        $validateData = $request->validate($rules);
        $validateData['user_id'] = Auth::id();

        $expense->update($validateData);
        Alert::success('Berhasil', 'Expense Berhasil Diperbarui!');
        return redirect()->route('expense.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        Alert::success('Berhasil', 'Expense Berhasil Dihapus!');
        return redirect()->route('expense.index');
    }
}
