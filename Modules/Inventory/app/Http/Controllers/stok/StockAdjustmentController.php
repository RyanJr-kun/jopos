<?php

namespace Modules\Inventory\Http\Controllers\stok;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    /**
     * Menampilkan riwayat penyesuaian stok.
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_penyesuaian', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($qUser) => $qUser->where('username', 'like', "%{$search}%"));
            });
        }

        // Tambahkan filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_penyesuaian', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        $stocks = $query->paginate(15)->withQueryString();

        return view('inventory::inventaris.adjustment.index', compact('stocks'));
    }

    /**
     * Menampilkan form untuk membuat penyesuaian stok baru.
     */
    public function create()
    {
        return view('inventory::inventaris.adjustment.create');
    }

    /**
     * Menyimpan penyesuaian stok baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'catatan' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.tipe' => 'required|in:IN,OUT',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.alasan' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                // 1. Buat record master
                $penyesuaian = StockAdjustment::create([
                    'kode_penyesuaian' => $this->generateAdjustmentCode(),
                    'tanggal_penyesuaian' => now(),
                    'user_id' => Auth::id(),
                    'catatan' => $validatedData['catatan'],
                ]);

                // 2. Loop dan proses setiap item
                foreach ($validatedData['items'] as $itemData) {
                    $produk = Product::find($itemData['product_id']);
                    $stokSebelum = $produk->qty;
                    $jumlah = (int)$itemData['jumlah'];

                    if ($itemData['tipe'] === 'IN') {
                        $stokSetelah = $stokSebelum + $jumlah;
                        $produk->increment('qty', $jumlah);
                    } else { // OUT
                        $stokSetelah = $stokSebelum - $jumlah;
                        $produk->decrement('qty', $jumlah);
                    }

                    // 3. Buat record detail
                    $penyesuaian->details()->create([
                        'product_id' => $itemData['product_id'],
                        'tipe' => $itemData['tipe'],
                        'jumlah' => $jumlah,
                        'stok_sebelum' => $stokSebelum,
                        'stok_setelah' => $stokSetelah,
                        'alasan' => $itemData['alasan'],
                    ]);
                }
            });

            return redirect()->route('stok-penyesuaian.index')->with('success', 'Penyesuaian stok berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghasilkan kode unik untuk penyesuaian stok.
     */
    private function generateAdjustmentCode()
    {
        // Format: ADJ-YYYYMMDD-XXXX (e.g., ADJ-20231027-0001)
        $date = now()->format('Ymd');
        $prefix = 'ADJ-' . $date . '-';

        // Cari kode terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastAdjustment = StockAdjustment::where('kode_penyesuaian', 'like', $prefix . '%')
            ->latest('kode_penyesuaian')
            ->first();

        $sequence = 1;
        if ($lastAdjustment) {
            $lastSequence = (int) substr($lastAdjustment->kode_penyesuaian, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menampilkan detail dari riwayat penyesuaian stok.
     */
    public function show(String $kode_penyesuaian)
    {
        // Cari penyesuaian berdasarkan kode unik, bukan ID.
        $stok_penyesuaian = StockAdjustment::where('kode_penyesuaian', $kode_penyesuaian)->firstOrFail();

        // Eager load relasi yang dibutuhkan
        $stok_penyesuaian->load(['user', 'details.produk.unit']);

        return view('inventory::inventaris.adjustment.show', [
            'title' => 'Detail Penyesuaian ' . $stok_penyesuaian->kode_penyesuaian,
            'penyesuaian' => $stok_penyesuaian,
        ]);
    }

    /**
     * Membatalkan & menghapus penyesuaian stok, serta mengembalikan stok produk.
     *
     * @param StockAdjustment $stok_penyesuaian
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(StockAdjustment $stok_penyesuaian)
    {
        try {
            DB::transaction(function () use ($stok_penyesuaian) {
                // 1. Loop melalui setiap detail untuk mengembalikan stok
                foreach ($stok_penyesuaian->details as $detail) {
                    $produk = $detail->produk;

                    // Jika produk masih ada, kembalikan stoknya
                    if ($produk) {
                        if ($detail->tipe === 'IN') {
                            // Jika tipe IN (penambahan), maka kurangi stoknya untuk membatalkan
                            $produk->decrement('qty', $detail->jumlah);
                        } else { // OUT
                            // Jika tipe OUT (pengurangan), maka tambah stoknya untuk membatalkan
                            $produk->increment('qty', $detail->jumlah);
                        }
                    }
                }

                // 2. Hapus record penyesuaian (dan detailnya via cascade)
                $stok_penyesuaian->delete();
            });

            return redirect()->route('stok-penyesuaian.index')->with('success', 'Penyesuaian stok berhasil dibatalkan dan stok produk telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membatalkan penyesuaian: ' . $e->getMessage());
        }
    }
}
