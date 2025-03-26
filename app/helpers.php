<?php

use App\Models\StockMovement;

if(!function_exists('post_stock_movement')){
    function post_stock_movement($itemId, $userId, $warehouseId, $type, $total) {
        StockMovement::insert([
            'id_items' => $itemId,
            'id_users' => $userId,
            'id_warehouses' => $warehouseId,
            'type' => $type,
            'total' => $total,
            'movement_date' => now(),
            'created_at' => now()
        ]);
    }
}