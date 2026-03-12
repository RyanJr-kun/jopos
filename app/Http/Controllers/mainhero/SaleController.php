<?php

namespace App\Http\Controllers\mainhero;

use App\Http\Controllers\Controller;

use App\Models\Taxe;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StoreSetting;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\Category;
// BARU: Import model SerialNumber
use App\Models\SerialNumber;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil semua status pembayaran yang unik untuk dropdown filter
        $statuses = Sale::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        // Mulai query builder
        $query = Sale::with(['pelanggan', 'user'])->latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('referensi', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($q_pelanggan) use ($search) {
                        $q_pelanggan->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Terapkan filter status jika ada input 'status'
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->input('status'));
        }

        $penjualan = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.penjualan._penjualan_table', compact('penjualan'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('content.penjualan.index', [
            'title' => 'Daftar Invoice Sale',
            'penjualan' => $penjualan,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // PERBAIKAN: Eager load relasi untuk efisiensi dan ketersediaan data di view
        $products = Product::with(['category', 'unit', 'pajak', 'promotions'])
            ->where('qty', '>', 0)
            ->orderBy('name_produk')
            ->get();
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        $kategoris = Category::where('status', 1)->orderBy('name')->get();
        $taxes = Taxe::all(); // Ambil semua data pajak

        return view('content.penjualan.create', [
            'title' => 'Kasir',
            'products' => $products,
            'customers' => $customers,
            'kategoris' => $kategoris,
            'taxes' => $taxes, // Teruskan data pajak ke view
            'referensi' => $this->generateInvoiceNumber() // Variabel ini diteruskan ke view
        ]);
    }

    private function generateInvoiceNumber()
    {
        // Contoh format: INV-20250831-0001
        $date = now()->format('Ymd');
        $prefix = 'INV-' . $date . '-';

        // Cari invoice terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastSale = Sale::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastSale) {
            // Ambil nomor urut dari invoice terakhir dan tambahkan 1
            $lastSequence = (int) substr($lastSale->referensi, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        // Disesuaikan untuk menerima semua input dari form kasir
        $validatedData = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'referensi' => 'required|string|unique:sales,referensi',
            'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS', // Status pembayaran akan ditentukan otomatis
            'catatan' => 'nullable|string',
            'jumlah_dibayar' => 'required|numeric|min:0',
            'service' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.diskon' => 'required|numeric|min:0',
            'items.*.taxe_id' => 'nullable|exists:taxes,id',
            'items.*.serial_numbers' => 'nullable|array', // BARU: Validasi bahwa serial_numbers adalah array (jika ada)
            'items.*.serial_numbers.*' => 'string', // BARU: Validasi setiap elemen di dalamnya adalah string
        ]);

        try {
            // Ambil data pajak yang relevan dalam satu query untuk efisiensi
            $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxes = Taxe::whereIn('id', $pajakIds)->get()->keyBy('id');

            // Memulai Database Transaction
            $penjualan = DB::transaction(function () use ($validatedData, $taxes) {
                // 2. Ambil semua produk yang dibutuhkan dalam satu query untuk menghindari N+1
                $produkIds = collect($validatedData['items'])->pluck('product_id');
                // DIUBAH: Menggunakan with() untuk eager load relasi wajib_seri jika ada
                $products = Product::whereIn('id', $produkIds)->get()->keyBy('id');

                // Hitung total dari server-side berdasarkan data yang divalidasi
                $subtotal_dpp_keseluruhan = 0; // Subtotal dari Dasar Pengenaan Taxe
                $total_pajak_item = 0;

                foreach ($validatedData['items'] as $itemData) {
                    $produk = $products->get($itemData['product_id']);
                    // Pastikan produk ada dan stok mencukupi (validasi tambahan)
                    if (!$produk || $produk->qty < $itemData['jumlah']) {
                        // Rollback transaksi dan kirim pesan error
                        throw new \Exception("Stock untuk produk '{$produk->name_produk}' tidak mencukupi.");
                    }

                    // Cek apakah produk ini wajib menggunakan nomor seri
                    if ($produk->wajib_seri) {
                        // Jika wajib, pastikan array 'serial_numbers' ada dan jumlahnya cocok
                        if (!isset($itemData['serial_numbers']) || count($itemData['serial_numbers']) !== (int)$itemData['jumlah']) {
                            throw new \Exception("Jumlah nomor seri untuk produk '{$produk->name_produk}' tidak sesuai dengan kuantitas pembelian.");
                        }

                        // Verifikasi bahwa semua nomor seri yang dikirim valid, tersedia, dan milik produk yang benar
                        $snCount = SerialNumber::where('product_id', $produk->id)
                            ->whereIn('nomor_seri', $itemData['serial_numbers'])
                            ->where('status', 'Tersedia')
                            ->count();

                        if ($snCount !== (int)$itemData['jumlah']) {
                            throw new \Exception("Satu atau lebih nomor seri untuk '{$produk->name_produk}' tidak valid atau tidak tersedia.");
                        }
                    }

                    // LOGIKA BARU: Harga jual dianggap sudah termasuk pajak
                    $harga_jual_total_item = $itemData['harga_jual'] * $itemData['jumlah'];
                    $taxe_id = $itemData['taxe_id'] ?? null;
                    $pajak_rate = $taxe_id ? ($taxes->get($taxe_id)->rate ?? 0) : 0;

                    // Hitung DPP (Dasar Pengenaan Taxe) dan Taxe dari harga jual inklusif
                    $dpp_item = $harga_jual_total_item / (1 + ($pajak_rate / 100));
                    $pajak_amount_item = $harga_jual_total_item - $dpp_item;

                    // PERBAIKAN: Diskon item mengurangi DPP, sama seperti di method update()
                    $dpp_item_setelah_diskon = $dpp_item - $itemData['diskon'];

                    $subtotal_dpp_keseluruhan += $dpp_item_setelah_diskon;
                    $total_pajak_item += $pajak_amount_item;
                }

                // Ambil biaya tambahan dari data yang sudah divalidasi
                $service = (float) ($validatedData['service'] ?? 0);
                $ongkir = (float) ($validatedData['ongkir'] ?? 0);
                $diskon_global = (float) ($validatedData['diskon'] ?? 0);
                $total_akhir = ($subtotal_dpp_keseluruhan + $total_pajak_item + $service + $ongkir) - $diskon_global;
                $jumlah_dibayar = (float) $validatedData['jumlah_dibayar'];
                $kembalian = $jumlah_dibayar - $total_akhir;

                // Tentukan status pembayaran secara otomatis
                $status_pembayaran = ($jumlah_dibayar >= $total_akhir) ? 'Lunas' : 'Belum Lunas';

                // 3. Simpan data ke tabel 'sales'
                $penjualan = Sale::create([
                    'referensi' => $validatedData['referensi'],
                    'tanggal_penjualan' => now(),
                    'user_id' => Auth::id(),
                    'customer_id' => $validatedData['customer_id'],
                    'subtotal' => $subtotal_dpp_keseluruhan, // Simpan subtotal DPP setelah diskon item
                    'diskon' => $diskon_global,
                    'service' => $service,
                    'ongkir' => $ongkir,
                    'pajak' => $total_pajak_item,
                    'total_akhir' => $total_akhir,
                    'jumlah_dibayar' => $jumlah_dibayar,
                    'kembalian' => $kembalian > 0 ? $kembalian : 0, // Jangan simpan kembalian negatif
                    'status_pembayaran' => $status_pembayaran,
                    'metode_pembayaran' => $validatedData['metode_pembayaran'],
                    'catatan' => $validatedData['catatan'],
                ]);

                // 4. Simpan setiap item ke 'item_penjualan' dan kurangi stok
                foreach ($validatedData['items'] as $itemData) {
                    $produk = $products->get($itemData['product_id']);

                    // LOGIKA BARU: Hitung ulang DPP dan Taxe per item untuk disimpan
                    $harga_jual_total_item = $itemData['harga_jual'] * $itemData['jumlah'];
                    $taxe_id = $itemData['taxe_id'] ?? null;
                    $pajak_rate = $taxe_id ? ($taxes->get($taxe_id)->rate ?? 0) : 0;
                    $dpp_item = $harga_jual_total_item / (1 + ($pajak_rate / 100));
                    $pajak_amount_item = $harga_jual_total_item - $dpp_item;

                    // Subtotal item sekarang adalah DPP dikurangi diskon item
                    $subtotal_item_final = $dpp_item - $itemData['diskon'];
                    // DIUBAH: Simpan item yang dibuat ke variabel $penjualanItem
                    $penjualanItem = $penjualan->items()->create([
                        'product_id' => $produk->id,
                        'jumlah' => $itemData['jumlah'],
                        'harga_jual' => $itemData['harga_jual'],
                        'diskon_item' => $itemData['diskon'],
                        'taxe_id' => $taxe_id,
                        'pajak_item' => $pajak_amount_item,
                        'subtotal' => $subtotal_item_final, // Simpan subtotal (DPP - diskon)
                    ]);

                    // Kurangi stok produk hanya jika transaksi tidak dibatalkan
                    // Status 'Dibatalkan' tidak bisa dibuat dari sini, jadi stok selalu dikurangi.
                    $produk->decrement('qty', $itemData['jumlah']);

                    // PENYESUAIAN LOGIKA NOMOR SERI
                    if ($produk->wajib_seri && isset($itemData['serial_numbers'])) {
                        SerialNumber::whereIn('nomor_seri', $itemData['serial_numbers'])
                            ->where('product_id', $produk->id)
                            ->update([
                                'status' => 'Terjual',
                                // DIUBAH: Tautkan ke item penjualan spesifik yang baru dibuat
                                'item_sale_id' => $penjualanItem->id
                            ]);
                    }
                }

                return $penjualan;
            });

            // 5. Redirect ke halaman faktur jika berhasil
            Alert::success('Berhasil', 'Transaksi berhasil disimpan!');
            return redirect()->route('penjualan.show', $penjualan->referensi);
        } catch (\Exception $e) {
            // Redirect kembali dengan pesan error jika transaksi gagal
            Alert::error('Gagal', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $penjualan)
    {
        // Eager load relasi untuk menghindari N+1 problem
        $penjualan->load('items.produk', 'items.serialNumbers', 'pelanggan', 'user');
        $profilToko = StoreSetting::first();

        return view('content.penjualan.show', [
            'title' => 'Faktur Sale: ' . $penjualan->referensi,
            'penjualan' => $penjualan,
            'profilToko' => $profilToko,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $penjualan)
    {
        // Eager load relasi untuk efisiensi
        $penjualan->load('items.produk', 'pelanggan', 'user');

        // Ambil data yang dibutuhkan untuk form, mirip seperti method create()
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        $taxes = Taxe::all();

        return view('content.penjualan.edit', [
            'title' => 'Edit Invoice: ' . $penjualan->referensi,
            'penjualan' => $penjualan,
            'customers' => $customers,
            'taxes' => $taxes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $penjualan)
    {

        if ($request->input('status_pembayaran') === 'Dibatalkan' && !$request->has('items')) {
            if ($penjualan->status_pembayaran !== 'Dibatalkan') {
                try {
                    DB::transaction(function () use ($penjualan) {
                        // 1. Kembalikan stok dan status nomor seri untuk setiap item.
                        foreach ($penjualan->items as $item) {
                            // a. Kembalikan jumlah stok produk
                            Product::where('id', $item->product_id)->increment('qty', $item->jumlah);

                            // b. Kembalikan status nomor seri menjadi 'Tersedia'
                            // dan hapus relasinya dengan item penjualan ini.
                            SerialNumber::where('item_sale_id', $item->id)->update([
                                'status' => 'Tersedia',
                                'item_sale_id' => null
                            ]);
                        }
                        $penjualan->update(['status_pembayaran' => 'Dibatalkan']);
                    });
                    Alert::success('Berhasil', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
                } catch (\Exception $e) {
                    Alert::error('Gagal', 'Gagal membatalkan transaksi: ' . $e->getMessage());
                }
            } else {
                Alert::info('Info', 'Transaksi ini sudah dalam status Dibatalkan.');
            }
            return redirect()->route('penjualan.index');
        }

        $request->merge([
            'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar')),
            'service' => preg_replace('/[^0-9]/', '', $request->input('service', 0)),
            'ongkir' => preg_replace('/[^0-9]/', '', $request->input('ongkir', 0)),
            'diskon' => preg_replace('/[^0-9]/', '', $request->input('diskon', 0)),
        ]);

        $validatedData = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'tanggal_penjualan' => 'required|date',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas,Dibatalkan',
            'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS',
            'catatan' => 'nullable|string',
            'jumlah_dibayar' => 'required|numeric|min:0',
            'service' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1', // Di frontend, ini disebut 'jumlah'
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.diskon' => 'required|numeric|min:0',
            'items.*.taxe_id' => 'nullable|exists:taxes,id',
            'items.*.serial_numbers' => 'nullable|array',
            'items.*.serial_numbers.*' => 'string',
        ]);

        try {
            $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::whereIn('id', $pajakIds)->get()->keyBy('id');

            $penjualan = DB::transaction(function () use ($request, $penjualan, $validatedData, $taxesData) {
                // --- MANAJEMEN NOMOR SERI ---
                // 1. Ambil semua item LAMA beserta nomor serinya SEBELUM ada perubahan.
                $oldItemsWithSerials = $penjualan->items()->with('serialNumbers')->get();

                // 2. Kumpulkan semua nomor seri LAMA ke dalam satu array.
                $oldSerialNumbers = $oldItemsWithSerials->pluck('serialNumbers')->flatten()->pluck('nomor_seri')->all();

                // 3. Kumpulkan semua nomor seri BARU dari request.
                $newSerialNumbers = collect($validatedData['items'])
                    ->where('serial_numbers')
                    ->pluck('serial_numbers')
                    ->flatten()
                    ->all();

                // 4. Cari nomor seri yang ada di daftar LAMA tapi TIDAK ADA di daftar BARU.
                // Ini adalah nomor seri yang itemnya dihapus dari transaksi.
                $serialsToMakeAvailable = array_diff($oldSerialNumbers, $newSerialNumbers);

                // 5. Jika ada, update statusnya kembali menjadi 'Tersedia'.
                if (!empty($serialsToMakeAvailable)) {
                    SerialNumber::whereIn('nomor_seri', $serialsToMakeAvailable)
                        ->update(['status' => 'Tersedia', 'item_sale_id' => null]);
                }

                // --- MANAJEMEN STOK ---
                $statusLama = $penjualan->status_pembayaran;
                $statusBaru = $validatedData['status_pembayaran'];

                // a. Ambil semua produk yang relevan untuk data baru dalam satu query (eager load wajib_seri)
                $newProductIds = collect($validatedData['items'])->pluck('product_id');
                $products = Product::whereIn('id', $newProductIds)->get()->keyBy('id');

                // b. Logika penyesuaian stok berdasarkan perubahan status dan item
                if ($statusBaru === 'Dibatalkan') {
                    // Status diubah menjadi Dibatalkan -> Kembalikan stok item lama
                    if ($statusLama !== 'Dibatalkan') {
                        foreach ($penjualan->items as $oldItem) {
                            // Jika status diubah jadi Dibatalkan, semua SN juga harus dikembalikan
                            // (Logika ini sudah tercakup di `array_diff` di atas karena $newSerialNumbers akan kosong)
                            // Namun, kita tambahkan di sini untuk penanganan pembatalan cepat.
                            SerialNumber::where('item_sale_id', $oldItem->id)->update([
                                'status' => 'Tersedia',
                                'item_sale_id' => null
                            ]);
                            Product::where('id', $oldItem->product_id)->increment('qty', $oldItem->jumlah);
                        }
                    }
                } else { // $statusBaru adalah 'Lunas' atau 'Belum Lunas'
                    // Jika status lama BUKAN 'Dibatalkan', kembalikan stok item lama terlebih dahulu
                    // untuk menghitung ulang stok berdasarkan item baru.
                    if ($statusLama !== 'Dibatalkan') {
                        foreach ($penjualan->items as $oldItem) {
                            Product::where('id', $oldItem->product_id)->increment('qty', $oldItem->jumlah);
                        }
                    }

                    // --- PERBAIKAN LOGIKA & N+1 ---
                    // 1. Pre-fetch current quantities of all products involved in the new transaction
                    $newProductIds = collect($validatedData['items'])->pluck('product_id');
                    $produkQtysSaatIni = Product::whereIn('id', $newProductIds)->pluck('qty', 'id');

                    // 2. Validasi semua stok sebelum melakukan perubahan
                    foreach ($validatedData['items'] as $itemData) {
                        $produk = $products->get($itemData['product_id']);
                        $stokTersedia = $produkQtysSaatIni->get($itemData['product_id'], 0);
                        if (!$produk || $stokTersedia < $itemData['jumlah']) {
                            throw new \Exception("Stock untuk produk '{$produk->name_produk}' tidak mencukupi (tersedia: {$stokTersedia}, dibutuhkan: {$itemData['jumlah']}).");
                        }

                        // Validasi ulang nomor seri yang dikirim
                        if ($produk->wajib_seri) {
                            if (!isset($itemData['serial_numbers']) || count($itemData['serial_numbers']) !== (int)$itemData['jumlah']) {
                                throw new \Exception("Jumlah nomor seri untuk produk '{$produk->name_produk}' tidak sesuai dengan kuantitas.");
                            }
                            // Verifikasi bahwa SN yang dikirim valid (Tersedia ATAU sudah terikat dengan transaksi ini sebelumnya)
                            $validSnCount = SerialNumber::where('product_id', $produk->id)
                                ->whereIn('nomor_seri', $itemData['serial_numbers'])
                                ->where(fn($q) => $q->where('status', 'Tersedia')->orWhereIn('nomor_seri', $oldSerialNumbers))
                                ->count();
                            if ($validSnCount !== (int)$itemData['jumlah']) {
                                throw new \Exception("Satu atau lebih nomor seri untuk '{$produk->name_produk}' tidak valid atau sudah terjual di transaksi lain.");
                            }
                        }
                    }

                    // 3. Jika semua validasi lolos, baru kurangi stok
                    foreach ($validatedData['items'] as $itemData) {
                        Product::where('id', $itemData['product_id'])->decrement('qty', $itemData['jumlah']);
                    }
                    // --- AKHIR PERBAIKAN ---
                }

                // --- PENGHITUNGAN ULANG TOTAL (SERVER-SIDE) ---
                $subtotal_dpp = 0;
                $total_pajak_keseluruhan = 0;

                foreach ($validatedData['items'] as $itemData) {
                    // Logika perhitungan di sini disesuaikan dengan method store() (pajak inklusif)
                    // untuk konsistensi.
                    $harga_jual_total_item = $itemData['harga_jual'] * $itemData['jumlah'];
                    $taxe_id = $itemData['taxe_id'] ?? null;
                    $pajak_rate = $taxe_id ? ($taxesData->get($taxe_id)->rate ?? 0) : 0;

                    // Hitung DPP (Dasar Pengenaan Taxe) dan Taxe dari harga jual inklusif
                    $dpp_item = $harga_jual_total_item / (1 + ($pajak_rate / 100));
                    $pajak_amount_item = $harga_jual_total_item - $dpp_item;

                    // Diskon item mengurangi DPP
                    $dpp_item_setelah_diskon = $dpp_item - $itemData['diskon'];

                    $subtotal_dpp += $dpp_item_setelah_diskon;
                    $total_pajak_keseluruhan += $pajak_amount_item;
                }

                $service = (float)($validatedData['service'] ?? 0);
                $ongkir = (float)($validatedData['ongkir'] ?? 0);
                $diskon_global = (float)($validatedData['diskon'] ?? 0);
                $total_akhir = ($subtotal_dpp + $total_pajak_keseluruhan + $service + $ongkir) - $diskon_global;
                $jumlah_dibayar = (float) $validatedData['jumlah_dibayar'];
                $kembalian = $jumlah_dibayar - $total_akhir;

                // --- UPDATE DATA PENJUALAN ---
                // a. Update record utama di tabel 'sales'
                $penjualan->update([
                    'customer_id' => $validatedData['customer_id'],
                    'tanggal_penjualan' => $validatedData['tanggal_penjualan'],
                    'metode_pembayaran' => $validatedData['metode_pembayaran'], // Langsung gunakan status dari form
                    'status_pembayaran' => $validatedData['status_pembayaran'],
                    'subtotal' => $subtotal_dpp,
                    'diskon' => $diskon_global,
                    'service' => $service,
                    'ongkir' => $ongkir,
                    'pajak' => $total_pajak_keseluruhan,
                    'total_akhir' => $total_akhir,
                    'jumlah_dibayar' => $validatedData['jumlah_dibayar'],
                    'kembalian' => $kembalian > 0 ? $kembalian : 0,
                    'catatan' => $validatedData['catatan'],
                ]);

                // b. Hapus item penjualan yang lama
                $penjualan->items()->delete();

                // c. Buat kembali item penjualan berdasarkan data baru
                foreach ($validatedData['items'] as $index => $itemData) {
                    // Hitung ulang DPP dan Taxe per item untuk disimpan
                    $harga_jual_total_item = (float)$itemData['harga_jual'] * (int)$itemData['jumlah'];
                    $taxe_id = $itemData['taxe_id'] ?? null;
                    $pajak_rate = $taxe_id ? ($taxesData->get($taxe_id)->rate ?? 0) : 0;
                    $dpp_item = $harga_jual_total_item / (1 + ($pajak_rate / 100));
                    $pajak_amount_item = $harga_jual_total_item - $dpp_item;

                    // Subtotal item adalah DPP dikurangi diskon item
                    $subtotal_item_final = $dpp_item - $itemData['diskon'];

                    $newItem = $penjualan->items()->create([
                        'product_id' => $itemData['product_id'],
                        'jumlah' => $itemData['jumlah'],
                        'harga_jual' => $itemData['harga_jual'],
                        'diskon_item' => $itemData['diskon'],
                        'taxe_id' => $taxe_id,
                        'pajak_item' => $pajak_amount_item,
                        'subtotal' => $subtotal_item_final,
                    ]);

                    // Update status nomor seri yang BARU menjadi 'Terjual' dan kaitkan dengan item baru
                    if (isset($itemData['serial_numbers']) && !empty($itemData['serial_numbers'])) {
                        SerialNumber::whereIn('nomor_seri', $itemData['serial_numbers'])
                            ->where('product_id', $itemData['product_id'])
                            ->update([
                                'status' => 'Terjual',
                                'item_sale_id' => $newItem->id
                            ]);
                    }
                }

                return $penjualan->load('items.produk', 'pelanggan', 'user');
            });
            Alert::success('Berhasil', 'Transaksi berhasil diperbarui.');
            return redirect()->route('penjualan.show', $penjualan->referensi);
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Gagal memperbarui transaksi: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $penjualan)
    {
        // try {
        //     DB::transaction(function () use ($penjualan) {
        //         // 1. Kembalikan stok untuk setiap item, HANYA JIKA statusnya BUKAN 'Dibatalkan'.
        //         // Jika sudah 'Dibatalkan', stok sudah dikembalikan sebelumnya.
        //         if ($penjualan->status_pembayaran !== 'Dibatalkan') {
        //             foreach ($penjualan->items as $item) {
        //                 Product::where('id', $item->product_id)->increment('qty', $item->jumlah);
        //             }
        //         }

        //         // 2. Hapus item terkait secara eksplisit untuk memastikan tidak ada data yatim jika cascade delete tidak diset
        //         $penjualan->items()->delete();
        //         // 3. Hapus data penjualan utama setelah item dihapus
        //         $penjualan->delete();
        //     });

        //     Alert::success('Berhasil', 'Transaksi penjualan berhasil dihapus dan stok telah dikembalikan.');
        //     return redirect()->route('penjualan.index');

        // } catch (\Exception $e) {
        //     Alert::error('Gagal', 'Terjadi kesalahan saat menghapus transaksi: ' . $e->getMessage());
        //     return back();
        // }
    }

    /**
     * Generate PDF for the specified resource.
     */
    public function generatePdf(Sale $penjualan)
    {
        // Eager load relasi untuk efisiensi
        $penjualan->load('pelanggan', 'user', 'items.produk', 'items.serialNumbers');
        $profilToko = StoreSetting::first();

        // Data yang akan dikirim ke view
        $data = [
            'penjualan' => $penjualan,
            'profilToko' => $profilToko,
        ];

        // Membuat PDF
        $pdf = Pdf::loadView('content.penjualan.faktur-penjualan-pdf', $data);
        return $pdf->stream('faktur-penjualan-' . $penjualan->referensi . '.pdf');
    }




    public function getTodayHistory(Request $request)
    {
        if ($request->ajax()) {
            $todaySales = Sale::with('pelanggan')
                ->whereDate('created_at', Carbon::today())
                ->latest() // Urutkan dari yang terbaru
                ->get()
                ->map(function ($sale) {
                    return [
                        'referensi' => $sale->referensi,
                        'total_akhir' => $sale->total_akhir,
                        'status' => $sale->status_pembayaran, // Disesuaikan
                        'name' => $sale->pelanggan->name ?? 'Customer Umum',
                        'waktu' => $sale->created_at->format('H:i'),
                    ];
                });

            return response()->json($todaySales);
        }
        // Jika bukan request AJAX, kembalikan ke halaman sebelumnya atau 404
        return redirect()->back();
    }

    /**
     * Menampilkan struk thermal untuk penjualan.
     *
     * @param  \App\Models\Sale  $penjualan
     * @return \Illuminate\View\View
     */
    public function printThermal(Sale $penjualan)
    {
        // Eager load relasi yang dibutuhkan untuk efisiensi
        $penjualan->load('pelanggan', 'user', 'items.produk', 'items.serialNumbers');
        $profilToko = StoreSetting::first();

        return view('content.penjualan.thermal', compact('penjualan', 'profilToko'));
    }
}
