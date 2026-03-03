<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use App\Models\Pemasok;
use App\Models\Pemasukan;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\ProfilToko;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Exports\LabaRugiExport;
use App\Exports\PembelianExport;
use App\Exports\PenjualanExport;
use App\Exports\InventarisExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormats;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan pergerakan inventaris.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function inventaris(Request $request)
    {
        // Query 1: Pembelian (Stok Masuk)
        $pembelian = DB::table('pembelian_details')
            ->join('pembelians', 'pembelian_details.pembelian_id', '=', 'pembelians.id')
            ->join('produks', 'pembelian_details.produk_id', '=', 'produks.id')
            ->where('pembelians.status_barang', 'Diterima')
            ->select(
                'pembelians.tanggal_pembelian as tanggal',
                'produks.id as produk_id',
                'produks.nama_produk',
                'produks.sku',
                DB::raw("'Pembelian' as tipe_gerakan"),
                'pembelians.referensi',
                'pembelian_details.qty as jumlah_masuk', // FIX: Menggunakan kolom 'qty'
                DB::raw("0 as jumlah_keluar"),
                'pembelians.catatan as keterangan',
                DB::raw("'pembelian.show' as route_name"),
                'pembelians.referensi as referensi_id'
            );

        // Query 2: Penjualan (Stok Keluar)
        $penjualan = DB::table('item_penjualans')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->select(
                'penjualans.tanggal_penjualan as tanggal',
                'produks.id as produk_id',
                'produks.nama_produk',
                'produks.sku',
                DB::raw("'Penjualan' as tipe_gerakan"),
                'penjualans.referensi',
                DB::raw("0 as jumlah_masuk"),
                'item_penjualans.jumlah as jumlah_keluar',
                'penjualans.catatan as keterangan',
                DB::raw("'penjualan.show' as route_name"),
                'penjualans.referensi as referensi_id'
            );

        // Query 3: Stok Opname (Masuk/Keluar)
        $opname = DB::table('stok_opname_details')
            ->join('stok_opnames', 'stok_opname_details.stok_opname_id', '=', 'stok_opnames.id')
            ->join('produks', 'stok_opname_details.produk_id', '=', 'produks.id')
            ->where('stok_opname_details.selisih', '!=', 0)
            ->select(
                'stok_opnames.tanggal_opname as tanggal',
                'produks.id as produk_id',
                'produks.nama_produk',
                'produks.sku',
                DB::raw("'Stok Opname' as tipe_gerakan"),
                'stok_opnames.kode_opname as referensi',
                DB::raw("CASE WHEN stok_opname_details.selisih > 0 THEN stok_opname_details.selisih ELSE 0 END as jumlah_masuk"),
                DB::raw("CASE WHEN stok_opname_details.selisih < 0 THEN ABS(stok_opname_details.selisih) ELSE 0 END as jumlah_keluar"),
                'stok_opname_details.keterangan',
                DB::raw("'stok-opname.show' as route_name"),
                'stok_opnames.kode_opname as referensi_id'
            );

        // Query 4: Penyesuaian Stok (Masuk/Keluar)
        $penyesuaian = DB::table('stok_penyesuaian_details')
            ->join('stok_penyesuaians', 'stok_penyesuaian_details.stok_penyesuaian_id', '=', 'stok_penyesuaians.id')
            ->join('produks', 'stok_penyesuaian_details.produk_id', '=', 'produks.id')
            ->select(
                'stok_penyesuaians.tanggal_penyesuaian as tanggal',
                'produks.id as produk_id',
                'produks.nama_produk',
                'produks.sku',
                DB::raw("'Penyesuaian' as tipe_gerakan"),
                'stok_penyesuaians.kode_penyesuaian as referensi',
                DB::raw("CASE WHEN stok_penyesuaian_details.tipe = 'IN' THEN stok_penyesuaian_details.jumlah ELSE 0 END as jumlah_masuk"),
                DB::raw("CASE WHEN stok_penyesuaian_details.tipe = 'OUT' THEN stok_penyesuaian_details.jumlah ELSE 0 END as jumlah_keluar"),
                'stok_penyesuaian_details.alasan as keterangan',
                DB::raw("'stok-penyesuaian.show' as route_name"),
                'stok_penyesuaians.kode_penyesuaian as referensi_id' // FIX: Gunakan kode_penyesuaian agar URL konsisten
            );

        // Gabungkan semua query
        $unionQuery = $penyesuaian->unionAll($opname)->unionAll($penjualan)->unionAll($pembelian);

        // Buat query baru dari hasil union untuk bisa diurutkan dan difilter
        $query = DB::query()->fromSub($unionQuery, 'pergerakan_inventaris');

        // Terapkan filter
        if ($request->filled('produk_id')) {
            $query->where('pergerakan_inventaris.produk_id', $request->produk_id);
        }
        if ($request->filled('tipe_gerakan')) {
            $query->where('pergerakan_inventaris.tipe_gerakan', $request->tipe_gerakan);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('pergerakan_inventaris.tanggal', [$request->start_date, $request->end_date]);
        }

        // Clone query untuk menghitung total sebelum paginasi
        $totalQuery = clone $query;
        $summary = $totalQuery->selectRaw('SUM(jumlah_masuk) as total_masuk, SUM(jumlah_keluar) as total_keluar')->first();

        // Urutkan dan lakukan paginasi
        $pergerakan = $query->orderBy('tanggal', 'desc')->paginate(50)->withQueryString();

        return view('dashboard.laporan.laporan-inventaris', [
            'title' => 'Laporan Pergerakan Inventaris',
            'pergerakan' => $pergerakan,
            'summary' => (object) [
                'total_produk' => Produk::count(),
                'total_stok' => Produk::sum('qty'),
                'total_masuk' => $summary->total_masuk ?? 0,
                'total_keluar' => $summary->total_keluar ?? 0,
            ],
            'produks' => Produk::orderBy('nama_produk')->get(['id', 'nama_produk']),
            'tipe_gerakan_options' => ['Pembelian', 'Penjualan', 'Stok Opname', 'Penyesuaian'],
        ]);
    }

    /**
     * Menangani ekspor laporan pergerakan inventaris ke Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportInventaris(Request $request) // PERUBAHAN
    {
        $profilToko = ProfilToko::first();
        $type = $request->query('type', 'xlsx');

        // --- REUSEABLE QUERY LOGIC ---
        $baseQuery = function (Request $request) {
            // Query 1: Pembelian (Stok Masuk)
            $pembelian = DB::table('pembelian_details')
                ->join('pembelians', 'pembelian_details.pembelian_id', '=', 'pembelians.id')
                ->join('produks', 'pembelian_details.produk_id', '=', 'produks.id')
                ->where('pembelians.status_barang', 'Diterima')
                ->select(
                    'pembelians.tanggal_pembelian as tanggal',
                    'produks.id as produk_id',
                    'produks.nama_produk',
                    'produks.sku',
                    DB::raw("'Pembelian' as tipe_gerakan"),
                    'pembelians.referensi',
                    'pembelian_details.qty as jumlah_masuk',
                    DB::raw("0 as jumlah_keluar"),
                    'pembelians.catatan as keterangan'
                );

            // Query 2: Penjualan (Stok Keluar)
            $penjualan = DB::table('item_penjualans')
                ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
                ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
                ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
                ->select(
                    'penjualans.tanggal_penjualan as tanggal',
                    'produks.id as produk_id',
                    'produks.nama_produk',
                    'produks.sku',
                    DB::raw("'Penjualan' as tipe_gerakan"),
                    'penjualans.referensi',
                    DB::raw("0 as jumlah_masuk"),
                    'item_penjualans.jumlah as jumlah_keluar',
                    'penjualans.catatan as keterangan'
                );

            // Query 3: Stok Opname (Masuk/Keluar)
            $opname = DB::table('stok_opname_details')
                ->join('stok_opnames', 'stok_opname_details.stok_opname_id', '=', 'stok_opnames.id')
                ->join('produks', 'stok_opname_details.produk_id', '=', 'produks.id')
                ->where('stok_opname_details.selisih', '!=', 0)
                ->select(
                    'stok_opnames.tanggal_opname as tanggal',
                    'produks.id as produk_id',
                    'produks.nama_produk',
                    'produks.sku',
                    DB::raw("'Stok Opname' as tipe_gerakan"),
                    'stok_opnames.kode_opname as referensi',
                    DB::raw("CASE WHEN stok_opname_details.selisih > 0 THEN stok_opname_details.selisih ELSE 0 END as jumlah_masuk"),
                    DB::raw("CASE WHEN stok_opname_details.selisih < 0 THEN ABS(stok_opname_details.selisih) ELSE 0 END as jumlah_keluar"),
                    'stok_opname_details.keterangan'
                );

            // Query 4: Penyesuaian Stok (Masuk/Keluar)
            $penyesuaian = DB::table('stok_penyesuaian_details')
                ->join('stok_penyesuaians', 'stok_penyesuaian_details.stok_penyesuaian_id', '=', 'stok_penyesuaians.id')
                ->join('produks', 'stok_penyesuaian_details.produk_id', '=', 'produks.id')
                ->select(
                    'stok_penyesuaians.tanggal_penyesuaian as tanggal',
                    'produks.id as produk_id',
                    'produks.nama_produk',
                    'produks.sku',
                    DB::raw("'Penyesuaian' as tipe_gerakan"),
                    'stok_penyesuaians.kode_penyesuaian as referensi',
                    DB::raw("CASE WHEN stok_penyesuaian_details.tipe = 'IN' THEN stok_penyesuaian_details.jumlah ELSE 0 END as jumlah_masuk"),
                    DB::raw("CASE WHEN stok_penyesuaian_details.tipe = 'OUT' THEN stok_penyesuaian_details.jumlah ELSE 0 END as jumlah_keluar"),
                    'stok_penyesuaian_details.alasan as keterangan'
                );

            // Gabungkan semua query
            $unionQuery = $penyesuaian->unionAll($opname)->unionAll($penjualan)->unionAll($pembelian);

            // Buat query baru dari hasil union untuk bisa diurutkan dan difilter
            $query = DB::query()->fromSub($unionQuery, 'pergerakan_inventaris');

            // Terapkan filter
            if ($request->filled('produk_id')) {
                $query->where('pergerakan_inventaris.produk_id', $request->produk_id);
            }
            if ($request->filled('tipe_gerakan')) {
                $query->where('pergerakan_inventaris.tipe_gerakan', $request->tipe_gerakan);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('pergerakan_inventaris.tanggal', [$request->start_date, $request->end_date]);
            }
            return $query;
        };
        // --- END REUSEABLE QUERY LOGIC ---

        $query = $baseQuery($request);
        $pergerakan = $query->orderBy('tanggal', 'desc')->get();

        // Tentukan tanggal default jika tidak ada filter
        $startDate = $request->input('start_date', $pergerakan->min('tanggal') ?? now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', $pergerakan->max('tanggal') ?? now()->endOfMonth()->toDateString());

        $data = [
            'title' => 'Laporan Pergerakan Inventaris',
            'profilToko' => $profilToko,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pergerakan' => $pergerakan,
        ];

        $fileName = 'laporan-inventaris-' . now()->format('Y-m-d_H-i-s') . '.' . $type;

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('dashboard.laporan.pdf.export-inventaris', $data);
            return $pdf->download($fileName);
        }

        return Excel::download(new InventarisExport($pergerakan), $fileName, ExcelFormats::XLSX);
    }

    /**
     * Menampilkan laporan pembelian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function pembelian(Request $request)
    {
        $query = Pembelian::with(['pemasok', 'user'])
            ->latest('tanggal_pembelian'); // ->select() tidak diperlukan, Eloquent akan memilih semua kolom secara default.

        // Terapkan filter
        if ($request->filled('pemasok_id')) {
            $query->where('pemasok_id', $request->pemasok_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('status_barang')) {
            $query->where('status_barang', $request->status_barang);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pembelian', [$request->start_date, $request->end_date]);
        }

        // Clone query untuk menghitung total sebelum paginasi
        $totalQuery = clone $query;
        $totalItemsQuery = clone $query;

        $totals = $totalQuery->reorder()->selectRaw('
            SUM(total_akhir) as grand_total,
            COUNT(id) as total_transactions
        ')->first();

        // Hitung total produk yang diterima dari transaksi yang sudah difilter
        $totals->total_products_received = $totalItemsQuery
            ->where('status_barang', 'Diterima') // Hanya hitung yang statusnya diterima
            ->join('pembelian_details', 'pembelians.id', '=', 'pembelian_details.pembelian_id')
            ->sum('pembelian_details.qty');

        // Hitung total hutang (opsional, jika masih ingin digunakan di tempat lain)
        $totals->total_due = $totalQuery->sum(DB::raw('total_akhir - jumlah_dibayar'));

        // Lakukan paginasi
        $pembelians = $query->paginate(50)->withQueryString();

        return view('dashboard.laporan.laporan-pembelian', [
            'title' => 'Laporan Pembelian',
            'pembelians' => $pembelians,
            'pemasoks' => Pemasok::orderBy('nama')->get(['id', 'nama']),
            'statusPembayaranOptions' => ['Lunas', 'Belum Lunas', 'Dibatalkan'],
            'statusBarangOptions' => ['Diterima', 'Belum Diterima', 'Dibatalkan'],
            'totals' => $totals,
        ]);
    }

    /**
     * Menangani ekspor laporan pembelian ke Excel atau PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPembelian(Request $request)
    {
        $profilToko = ProfilToko::first();
        $type = $request->query('type', 'xlsx');

        // Gunakan query yang sama dengan method pembelian() untuk konsistensi filter
        $query = Pembelian::with(['pemasok', 'user'])
            ->latest('tanggal_pembelian');

        // Terapkan filter
        if ($request->filled('pemasok_id')) {
            $query->where('pemasok_id', $request->pemasok_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('status_barang')) {
            $query->where('status_barang', $request->status_barang);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pembelian', [$request->start_date, $request->end_date]);
        }

        // Ambil semua data yang cocok tanpa paginasi
        $pembelians = $query->get();

        // Siapkan data untuk diteruskan ke view/export class
        $data = [
            'title' => 'Laporan Pembelian',
            'profilToko' => $profilToko,
            'startDate' => $request->input('start_date', $pembelians->min('tanggal_pembelian')),
            'endDate' => $request->input('end_date', $pembelians->max('tanggal_pembelian')),
            'pembelians' => $pembelians,
        ];

        $fileName = 'laporan-pembelian-' . now()->format('Y-m-d_H-i-s') . '.' . $type;

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('dashboard.laporan.pdf.export-pembelian', $data);
            return $pdf->download($fileName);
        }
        return Excel::download(new PembelianExport($pembelians), $fileName, ExcelFormats::XLSX);
    }
    /**
     * Menampilkan laporan penjualan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function penjualan(Request $request)
    {
        $query = Penjualan::with(['pelanggan', 'user'])
            ->latest('tanggal_penjualan'); // ->select() tidak diperlukan, Eloquent akan memilih semua kolom secara default.

        // Terapkan filter
        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_penjualan', [$request->start_date, $request->end_date]);
        }

        // Clone query untuk menghitung total sebelum paginasi
        $totalQuery = clone $query;
        $totalItemsQuery = clone $query;

        $totals = $totalQuery->reorder()->selectRaw('
            SUM(total_akhir) as grand_total,
            COUNT(id) as total_transactions
        ')->first();

        // Hitung total item terjual dari transaksi yang sudah difilter
        $totals->total_products_sold = $totalItemsQuery->join('item_penjualans', 'penjualans.id', '=', 'item_penjualans.penjualan_id')->sum('item_penjualans.jumlah');

        // Lakukan paginasi
        $penjualans = $query->paginate(50)->withQueryString();

        return view('dashboard.laporan.laporan-penjualan', [
            'title' => 'Laporan Penjualan',
            'penjualans' => $penjualans,
            'pelanggans' => Pelanggan::orderBy('nama')->get(['id', 'nama']),
            'statusPembayaranOptions' => ['Lunas', 'Belum Lunas', 'Dibatalkan'],
            'totals' => $totals,
        ]);
    }

    /**
     * Menangani ekspor laporan penjualan ke Excel atau PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPenjualan(Request $request)
    {
        $profilToko = ProfilToko::first();
        $type = $request->query('type', 'xlsx');

        // Gunakan query yang sama dengan method penjualan() untuk konsistensi filter
        $query = Penjualan::with(['pelanggan', 'user'])
            ->latest('tanggal_penjualan');

        // Terapkan filter
        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_penjualan', [$request->start_date, $request->end_date]);
        }

        // Ambil semua data yang cocok tanpa paginasi
        $penjualans = $query->get();

        // Hitung total berdasarkan data yang sudah difilter
        $grand_total = $penjualans->sum('total_akhir');
        $total_paid = $penjualans->sum('jumlah_dibayar');
        $total_due = $penjualans->sum('sisa_pembayaran');

        // Tentukan tanggal default jika tidak ada filter
        $startDate = $request->input('start_date', $penjualans->min('tanggal_penjualan') ?? now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', $penjualans->max('tanggal_penjualan') ?? now()->endOfMonth()->toDateString());

        $data = [
            'title' => 'Laporan Penjualan',
            'profilToko' => $profilToko,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'penjualans' => $penjualans,
            'totals' => (object) [
                'grand_total' => $grand_total,
                'total_paid' => $total_paid,
                'total_due' => $total_due,
            ],
        ];

        $fileName = 'laporan-penjualan-' . now()->format('Y-m-d_H-i-s') . '.' . $type;

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('dashboard.laporan.pdf.export-penjualan', $data);
            return $pdf->download($fileName);
        }

        return Excel::download(new PenjualanExport($penjualans), $fileName, ExcelFormats::XLSX);
    }

    /**
     * Menampilkan laporan laba rugi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function labaRugi(Request $request)
    {
        // 1. Atur rentang tanggal, defaultnya bulan ini
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // 2. Hitung Total Pendapatan dari Penjualan (yang tidak dibatalkan)
        $totalRevenue = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // Tambahan: Hitung Total Pendapatan Lain-lain dari tabel Pemasukan
        $totalOtherIncome = Pemasukan::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        // 3. Hitung Harga Pokok Penjualan (HPP / COGS)
        // HPP = Jumlah barang terjual * harga beli produk
        $cogs = DB::table('item_penjualans')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
            ->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('item_penjualans.jumlah * produks.harga_beli'));

        // 4. Hitung Laba Kotor (Pendapatan - HPP)
        $grossProfit = $totalRevenue - $cogs;

        // 5. Hitung Total Beban Operasional dari tabel pengeluaran
        $totalExpenses = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');

        // 6. Hitung Laba Bersih (Laba Kotor - Beban Operasional)
        $netProfit = $grossProfit + $totalOtherIncome - $totalExpenses;

        // 7. Siapkan data untuk grafik 6 bulan terakhir
        $chartLabels = [];
        $chartNetProfits = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth()->toDateString();
            $monthEnd = $date->endOfMonth()->toDateString();

            // Pendapatan bulan ini
            $monthlyRevenue = Penjualan::whereBetween('tanggal_penjualan', [$monthStart, $monthEnd])
                ->where('status_pembayaran', '!=', 'Dibatalkan')
                ->sum('total_akhir');

            // HPP bulan ini
            $monthlyCogs = DB::table('item_penjualans')
                ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
                ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
                ->whereBetween('penjualans.tanggal_penjualan', [$monthStart, $monthEnd])
                ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
                ->sum(DB::raw('item_penjualans.jumlah * produks.harga_beli'));

            // Pemasukan lain-lain bulan ini
            $monthlyOtherIncome = Pemasukan::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');

            // Beban bulan ini
            $monthlyExpenses = Pengeluaran::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');

            // Laba bersih bulan ini
            $monthlyGrossProfit = $monthlyRevenue - $monthlyCogs;
            $monthlyNetProfit = $monthlyGrossProfit + $monthlyOtherIncome - $monthlyExpenses;

            // Tambahkan ke array untuk dikirim ke view
            $chartLabels[] = $date->isoFormat('MMMM Y');
            $chartNetProfits[] = $monthlyNetProfit;
        }

        return view('dashboard.laporan.laporan-laba-rugi', [
            'title' => 'Laporan Laba Rugi',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalOtherIncome' => $totalOtherIncome,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'chartLabels' => $chartLabels,
            'chartNetProfits' => $chartNetProfits,
        ]);
    }

    /**
     * Menangani ekspor laporan laba rugi ke PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportLabaRugi(Request $request)
    {
        $profilToko = ProfilToko::first();
        // 1. Atur rentang tanggal dari request
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // 2. Hitung Total Pendapatan
        $totalRevenue = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // Tambahan: Hitung Total Pendapatan Lain-lain
        $totalOtherIncome = Pemasukan::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        // 3. Hitung HPP
        $cogs = DB::table('item_penjualans')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
            ->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('item_penjualans.jumlah * produks.harga_beli'));

        // 4. Hitung Laba Kotor
        $grossProfit = $totalRevenue - $cogs;

        // 5. Hitung Total Beban
        $totalExpenses = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');

        // 6. Hitung Laba Bersih
        $netProfit = $grossProfit + $totalOtherIncome - $totalExpenses;

        // 7. Siapkan data untuk view PDF
        $pdf = Pdf::loadView('dashboard.laporan.pdf.export-laba', [
            'title' => 'Laporan Laba Rugi',
            'profilToko' => $profilToko,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalOtherIncome' => $totalOtherIncome,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
        ]);

        $fileName = 'laporan-laba-rugi-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }
}
