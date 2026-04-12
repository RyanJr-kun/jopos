<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarisExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $pergerakan;

    public function __construct($pergerakan)
    {
        $this->pergerakan = $pergerakan;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->pergerakan;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'SKU',
            'Nama Product',
            'Tipe Gerakan',
            'Referensi',
            'Masuk',
            'Keluar',
            'Keterangan',
        ];
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function map($item): array
    {
        return [
            Carbon::parse($item->tanggal)->format('d-m-Y H:i'),
            $item->sku ?? '-',
            $item->name_product ?? 'Product Dihapus',
            $item->tipe_gerakan,
            $item->referensi,
            $item->jumlah_masuk > 0 ? $item->jumlah_masuk : '0',
            $item->jumlah_keluar > 0 ? $item->jumlah_keluar : '0',
            $item->keterangan ?? '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Membuat baris header menjadi bold
        $sheet->getStyle('1')->getFont()->setBold(true);

        // Mengatur lebar kolom secara otomatis
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
