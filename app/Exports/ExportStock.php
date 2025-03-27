<?php

namespace App\Exports;

use App\Models\TotalItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportStock implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return TotalItem::with(['warehouse', 'item'])->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Barang',
            'Total',
            'Nama Gudang'
        ];
    }

    public function map($total_item): array
    {
        $staticNum = 1;
        return [
            $staticNum++,
            $total_item->item->name_item,
            $total_item->stock,
            $total_item->warehouse->name_warehouse,
        ];
    }
}
