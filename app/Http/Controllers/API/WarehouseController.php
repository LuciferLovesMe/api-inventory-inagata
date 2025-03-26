<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    private $warehouses;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouses = $warehouse;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        try {
            $response = $this->warehouses->orderBy('id', 'desc')->get();
            $message = 'Berhasil menampilkan list gudang.';
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
            $request->validate([
                'name' => 'required|string',
                'address' => 'required|string'
            ]);
            
            $warehouse = $this->warehouses
                ->insert([
                    'name_warehouse' => $request->get('name'),
                    'address_warehouse' => $request->get('address'),
                    'created_at' => now()
                ]);
            
            DB::commit();
            $responseCode = Response::HTTP_OK;
            $message = 'Berhasil menambahkan gudang baru.';
            $response = $warehouse;
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        try {
            $response = $this->warehouses->where('id', $id)->first();
            $message = 'Berhasil menampilkan gudang.';
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
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        DB::beginTransaction();
        try {
            $warehouse = $this->warehouses->find($id);
            if($warehouse != null) {
                // dd($request->all());
                $warehouse->name_warehouse = $request->get('name') != null ? $request->get('name') : $warehouse->name_warehouse;
                $warehouse->address_warehouse = $request->get('address') != null ? $request->get('address') : $warehouse->address_warehouse;
                $warehouse->updated_at = now();
                $warehouse->save();

                DB::commit();
                $responseCode = Response::HTTP_OK;
                $message = 'Berhasil mengubah detail gudang.';
                $response = $warehouse;
            } else {
                $responseCode = Response::HTTP_NOT_FOUND;
                $message = 'Data gudang tidak ditemukan.';
                $response = null;
            }
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        DB::beginTransaction();
        try {
            $warehouse = $this->warehouses->find($id);
            if($warehouse != null) {
                $warehouse->delete();

                DB::commit();
                $responseCode = Response::HTTP_OK;
                $message = 'Berhasil menghapus gudang.';
                $response = $warehouse;
            } else {
                $responseCode = Response::HTTP_NOT_FOUND;
                $message = 'Data gudang tidak ditemukan.';
                $response = null;
            }
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
}
