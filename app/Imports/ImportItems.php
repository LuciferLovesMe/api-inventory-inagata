<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportItems implements ToCollection, WithHeadingRow
{
    use Importable;
    private $returned;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $count = 1;
        foreach($collection as $key => $item) {
            Item::insert([
                'code_item' => $item['code'],
                'name_item' => $item['nama'],
                'created_at' => now()
            ]);
            $count++;
        }
        

        $this->returned = 'Berhasil melakukan import sebanyak ' . $count . ' baris.';
    }

    public function getRowCount() {
        return $this->returned;
    }
}
