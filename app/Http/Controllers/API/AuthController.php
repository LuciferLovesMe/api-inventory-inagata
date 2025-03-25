<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $users;

    public function __construct(User $users)
    {
        $this->users = $users;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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

    function login(Request $request) {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';
        // return $request->all();

        DB::beginTransaction();
        try {
            $user = $this->users
                ->where('email', $request->get('email'))
                ->with('warehouse')
                ->select(
                    'id',
                    'name',
                    'email'
                )
                ->first();
            
            if(!$user) {
                $responseCode = Response::HTTP_UNAUTHORIZED;
                $message = 'Email tidak ditemukan.';
            } else {
                $response = $user;
                // if (Hash::check($request->get('password'), $user->password)){
                if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])){
                    $responseCode = Response::HTTP_OK;
                    $message = 'Selamat datang ' . $user->name . '.';
                    $response = [
                        'user' => $user,
                        'token' => $user->createToken('tokenize')->plainTextToken
                    ];
                    DB::commit();
                } else {
                    $responseCode = Response::HTTP_UNAUTHORIZED;
                    $message = 'Password salah.';
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

    public function getUser (Request $request) {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        try {
            $response = $request->user();
            $message = 'Berhasil menampilkan.';
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

    public function logout(Request $request) {
        $response = null;
        $responseCode = Response::HTTP_OK;
        $message = '';

        DB::beginTransaction();
        try {
            $request->user()->currentAccessToken()->delete();
            $message = 'Berhasil logout.';
            DB::commit();
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
