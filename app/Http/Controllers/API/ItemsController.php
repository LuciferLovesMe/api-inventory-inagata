<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ItemsController extends Controller
{
    private $items;

    public function __construct(Item $item)
    {
        $this->items = $item;
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
            $response = $this->items->orderBy('id', 'desc')->get();
            $message = 'Berhasil menampilkan list barang.';
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
                'code' => 'required|string'
            ]);
            
            $item = $this->items
                ->insert([
                    'name_item' => $request->get('name'),
                    'code_item' => $request->get('code'),
                    'created_at' => now()
                ]);
            
            DB::commit();
            $responseCode = Response::HTTP_OK;
            $message = 'Berhasil menambahkan barang baru.';
            $response = $item;
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
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        try {
            $response = $this->items->where('id', $id)->first();
            $message = 'Berhasil menampilkan barang.';
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
    {$response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        DB::beginTransaction();
        try {
            $item = $this->items->find($id);
            if($item != null) {
                // dd($request->all());
                $item->code_item = $request->get('code') != null ? $request->get('code') : $item->code_item;
                $item->name_item = $request->get('name') != null ? $request->get('name') : $item->name_item;
                $item->updated_at = now();
                $item->save();

                DB::commit();
                $responseCode = Response::HTTP_OK;
                $message = 'Berhasil mengubah detail barang.';
                $response = $item;
            } else {
                $responseCode = Response::HTTP_NOT_FOUND;
                $message = 'Data barang tidak ditemukan.';
                $response = null;
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        DB::beginTransaction();
        try {
            $item = $this->items->find($id);
            if($item != null) {
                $item->delete();

                DB::commit();
                $responseCode = Response::HTTP_OK;
                $message = 'Berhasil menghapus barang.';
                $response = $item;
            } else {
                $responseCode = Response::HTTP_NOT_FOUND;
                $message = 'Data barang tidak ditemukan.';
                $response = null;
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
}
