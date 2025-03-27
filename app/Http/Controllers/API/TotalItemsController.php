<?php

namespace App\Http\Controllers\API;

use App\Exports\ExportStock;
use App\Http\Controllers\Controller;
use App\Imports\ImportStock;
use App\Models\Item;
use App\Models\TotalItem;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TotalItemsController extends Controller
{
    private $total_items, $items;

    public function __construct(TotalItem $total_item, Item $item)
    {
        $this->total_items = $total_item;
        $this->items = $item;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        try {
            $response = ($request->user()->id_warehouses != null) ? 
                $this->total_items->orderBy('id', 'desc')->with(['item', 'warehouse'])->where('id_warehouse', $request->user()->id_warehouse)->get() : 
                $this->total_items->orderBy('id', 'desc')->with(['item', 'warehouse'])->get();
            $message = 'Berhasil menampilkan list stok barang.';
        } catch (Exception $e) {
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } catch (QueryException $e) {
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } finally {
            return response()->json([
                'message' => $message,
                'data' => $response,
            ], $responseCode);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';
        
        DB::beginTransaction();
        try {
            $idWarehouses = ($request->user()->id_warehouses != null) ? $request->user()->id_warehouse : $request->get('id_warehouse');
            $idItems = $this->items->where('code_item', $request->get('code_item'))->first();
            if ($idItems) {
                $dataExists = $this->total_items
                    ->where('id_warehouses', $idWarehouses)
                    ->where('id_items', $idItems->id)
                    ->first();
                
                if($dataExists) {
                    $this->total_items
                        ->where('id', $dataExists->id)
                        ->update([
                            'stock' => ($dataExists->stock + $request->get('stock')),
                            'updated_at' => now()
                        ]);
                } else {
                    $this->total_items
                        ->insert([
                           'id_items' => $idItems->id,
                           'id_warehouses' => $idWarehouses,
                           'stock' => $request->get('stock'),
                           'created_at' => now()
                        ]);
                }

                post_stock_movement($idItems->id, $request->user()->id, $idWarehouses, 'in', $request->get('stock'));

                DB::commit();
                $response = 'Berhasil menambahkan stok barang ('. $idItems->name_item .') sebanyak ' . $request->get('stock') . ' buah.';
                $message = 'Berhasil';
            }
        } catch (Exception $e) {
            DB::rollBack();
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } catch (QueryException $e) {
            DB::rollBack();
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } finally {
            return response()->json([
                'message' => $message,
                'data' => $response,
            ], $responseCode);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function outStock(Request $request) {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';
        $threshold = 10;
        
        DB::beginTransaction();
        try {
            $idWarehouses = ($request->user()->id_warehouses != null) ? $request->user()->id_warehouse : $request->get('id_warehouse');
            $idItems = $this->items->where('code_item', $request->get('code_item'))->first();
            if ($idItems) {
                $dataExists = $this->total_items
                    ->where('id_warehouses', $idWarehouses)
                    ->where('id_items', $idItems->id)
                    ->first();
                
                if($dataExists) {
                    if($dataExists->stock > $request->get('stock')){
                        $this->total_items
                            ->where('id', $dataExists->id)
                            ->update([
                                'stock' => ($dataExists->stock - $request->get('stock')),
                                'updated_at' => now()
                            ]);

                        post_stock_movement($idItems->id, $request->user()->id, $idWarehouses, 'out', $request->get('stock'));
                        
                        if(($dataExists->stock - $request->get('stock')) < $threshold) {

                        }
                        
                        DB::commit();
                        $response = 'Berhasil mengurangi stok barang ('. $idItems->name_item .') sebanyak ' . $request->get('stock') . ' buah.' . (($dataExists->stock - $request->get('stock')) < $threshold ? 'Peringatan! Stok barang tersebut telah mencapai batas minimun.' : '');
                        $message = 'Berhasil';
                    } else {
                        $response = 'Stok yang tersedia tidak mencukupi.';
                        $message = 'Terjadi kesalahan.';
                        $responseCode = Response::HTTP_BAD_REQUEST;
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } catch (QueryException $e) {
            DB::rollBack();
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } finally {
            return response()->json([
                'message' => $message,
                'data' => $response,
            ], $responseCode);
        }        
    }

    public function import(Request $request) {

        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';
        
        DB::beginTransaction();
        try {
            $file = $request->file('import');
            $import = new ImportStock;
            Excel::import($import, $file);

            DB::commit();
            $response = 'Berhasil';
            $message = $import->getRowCount();
        } catch (Exception $e) {
            DB::rollBack();
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } catch (QueryException $e) {
            DB::rollBack();
            $response = 'Terjadi kesalahan.';
            $message = $e->getMessage();
            $responseCode = Response::HTTP_BAD_REQUEST;
        } finally {
            return response()->json([
                'message' => $message,
                'data' => $response,
            ], $responseCode);
        }        
    }

    public function export (Request $request) {
        // $file = Excel::download(new ExportStock, 'stock.xlsx');
        // $response = Response::make($file);
        // $response->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // return $response;
        return Excel::download(new ExportStock, 'stock.xlsx');
    }
}
