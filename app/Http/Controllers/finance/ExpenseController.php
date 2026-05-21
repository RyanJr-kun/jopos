<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-expense', only: ['index']),
            
            // 2. Akses Tambah Data
            new Middleware('permission:create-expense', only: ['create', 'store']),
            
            // 3. Akses Edit Data
            new Middleware('permission:edit-expense', only: ['edit', 'update']),
            
            // 4. Akses Hapus Data
            new Middleware('permission:delete-expense', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['transaction_category', 'user'])->latest();

        $kategoriFilters = TransactionCategory::query()->where('type', 'expense')
            ->whereHas('expenses')
            ->orderBy('name', 'asc')
            ->get();

        $allKategoris = TransactionCategory::query()->where('type', 'expense')
            ->where('status', 1)
            ->orderBy('name', 'asc')
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

        $expenses = $query->paginate(15)->withQueryString();

        // --- PRO AJAX RETURN ---
        if ($request->ajax()) {
            $html = view('content.finance.pengeluaran', [
                'title' => 'expense',
                'expenses' => $expenses,
                'kategoriFilters' => $kategoriFilters,
                'allKategoris' => $allKategoris,
                'referensi_otomatis' => $this->generateExpenseReferenceNumber()
            ])->fragment('expense-table-area');

            return response()->json([
                'html' => $html,
                'total' => $expenses->total()
            ]);
        }

        return view('content.finance.pengeluaran', [
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
        $lastExpense = Expense::query()->where('referensi', 'like', $prefix . '%')
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
        return redirect()->route('expense.index')->with('success', 'Expense baru berhasil ditambahkan!');
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
        return redirect()->route('expense.index')->with('success', 'Expense Berhasil Diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expense.index')->with('success', 'Expense Berhasil Dihapus!');
    }
}
