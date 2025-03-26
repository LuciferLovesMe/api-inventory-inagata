<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TotalItem extends Model
{
    protected $table = 'total_items';
    protected $fillable = [
        'id_items',
        'id_warehouses',
        'stock',
        'created_at',
        'updated_at'
    ];

    public function item() {
        return $this->belongsTo(Item::class, 'id_items');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'id_warehouses');
    }
}
