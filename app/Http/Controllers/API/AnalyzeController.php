<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\TotalItem;
use App\Models\Warehouse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AnalyzeController extends Controller
{
    private $totalItem, $item, $warehouse, $stockMovement;

    public function __construct(TotalItem $totalItem, Item $item, Warehouse $warehouse, StockMovement $stockMovement)
    {
        $this->totalItem = $totalItem;
        $this->item = $item;
        $this->warehouse = $warehouse;
        $this->stockMovement = $stockMovement;
    }

    public function getAnalyze (Request $request) 
    {
        $message = '';
        $data = null;
        $responseCode = Response::HTTP_OK;

        try {
            $mostUsed = DB::table('stock_movements')
                ->selectRaw('stock_movements.*, warehouses.name_warehouse, items.name_item, count(stock_movements.id_items) as counted')
                ->join('items', 'items.id', 'stock_movements.id_items')
                ->join('warehouses', 'warehouses.id', 'stock_movements.id_warehouses')
                ->where('type', 'out')
                ->orderBy('counted', 'desc')
                ->groupBy('stock_movements.id_items')
                ->first();
            $unused = DB::table('items as i')
                ->whereRaw("i.id NOT IN (SELECT stock_movements.id_items FROM stock_movements where stock_movements.type='out')")
                ->get();
            $leastStock = $this->totalItem
                ->with(['warehouse', 'item'])
                ->where('stock', '<', 10)
                ->get();

            $message = 'Berhasil';
            $data = [
                'sering_digunakan' => $mostUsed,
                'tidak_digunakan' => $unused,
                'dibawah_batas_minimum' => $leastStock
            ];
        } catch (Exception $e) {
            $message = 'Terjadi kesalahan.';
            $data = $e->getMessage();
            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan.';
            $data = $e->getMessage();
            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        } finally {
            return response()->json([
                'message' => $message,
                'data' => $data
            ], $responseCode);
        }
    }
}
