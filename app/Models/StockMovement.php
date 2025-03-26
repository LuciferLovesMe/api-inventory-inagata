<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'id_items',
        'id_warehouses',
        'id_users',
        'stock',
        'type',
        'created_at',
        'updated_at'
    ];

    public function item() {
        return $this->belongsTo(Item::class, 'id_items');
    }

    public function user() {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'id_warehouses');
    }
}
