<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\TotalItem;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportStock implements ToCollection, WithHeadingRow
{
    use Importable;
    private $returned;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $insert = [];
        $inserted = 0;
        $skip = '';
        $skipped = 0;
        foreach ($collection as $key => $col) {
            $idItem = Item::where('code_item', $col['code_item'])->first();
            $idWarehouse = Warehouse::where('id', $col['id_warehouse'])->first();
            if($idItem) {
                $dataExists = TotalItem::where('id_items', $idItem->id)
                    ->where('id_warehouses', $idWarehouse->id)
                    ->first();
                
                if ($dataExists) {
                    $data = TotalItem::find($dataExists->id);
                    $data->stock = ($data->stock + $col['stock']);
                    $data->updated_at = now();
                    $data->save();
                } else {
                    array_push($insert, [
                        'id_items' => $idItem->id,
                        'id_warehouses' => $idWarehouse->id,
                        'stock' => $col['stock'],
                        'created_at'  => now()
                    ]);
                }
                post_stock_movement($idItem->id, $col['id_user'], $idWarehouse->id, 'in', $col['stock']);
                $inserted++;
            } else {
                $skipped++;
                $skip .= $key;
            }
        }

        if($inserted > 0) {
            TotalItem::insert($insert);
        }

        $this->returned = 'Berhasil melakukan import sebanyak ' . $inserted . ' baris.' .( $skipped > 0 ? ' Baris yang tidak valid sebanyak ' . $skipped . ' baris, pada baris (' . ($skipped) . ')' : '');
    }

    public function getRowCount() {
        return $this->returned;
    }
}
