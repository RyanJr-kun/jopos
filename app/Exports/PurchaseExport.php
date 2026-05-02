<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Carbon;

class PurchaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->purchases;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Referensi',
            'Supplier',
            'Status Bayar',
            'Status Barang',
            'Total',
            'Dibayar',
            'Sisa',
        ];
    }

    /**
     * @param Purchase $pembelian
     * @return array
     */
    public function map($pembelian): array
    {
        return [
            Carbon::parse($pembelian->tanggal_pembelian)->format('d-m-Y'),
            $pembelian->referensi,
            $pembelian->supplier->name ?? 'N/A',
            $pembelian->status_pembayaran,
            $pembelian->status_barang,
            $pembelian->total_akhir,
            $pembelian->jumlah_dibayar,
            $pembelian->total_akhir - $pembelian->jumlah_dibayar, // Menghitung sisa secara dinamis
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
