<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\User;
use Hash;
class AuthController extends Controller
{
    public function register(request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'address' => 'required',
            'birthday' => 'required',
            'role' => 'required',
            'password' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'massage' => $validator->errors(),
            ]);
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'role' => $request->role,
            'birthday' => $request->birthday,
        ];
        try {
            $insert = User::create($data);
            return response()->json(['status' => true, 'massage' => 'Data Berhasil Ditambahkan']);

        } catch (Exception $e) {
            return response()->json(['status' => false, 'massage' => $e]);
        }
    }
    public function getUser()
    {
        try {
            $user = User::get();
            return response()->json([
                'status' => true,
                'message' => 'berhasil load data user',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'gagal load data user.' . $e,
            ]);
        }
    }
    public function getDetailUser($id)
    {
        try {
            $user = User::where('id', $id)->first();
            return response()->json([
                'status' => true,
                'message' => 'berhasil load data detail user',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'gagal load data detail user. ' . $e,
            ]);
        }
    }
    public function update_user($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($id)],
            "address" => 'required',
            "birthday" => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
            "address" => $request->get("address"),
            "birthday" => $request->get("birthday"),
        ];
        try {
            $update = User::where('id', $id)->update($data);
            return Response()->json([
                "status" => true,
                'message' => 'Data berhasil diupdate'
            ]);


        } catch (Exception $e) {
            return Response()->json([
                "status" => false,
                'message' => $e
            ]);
        }
    }
    public function hapus_user($id)
    {
        try {
            User::where('id', $id)->delete();
            return Response()->json([
                "status" => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return Response()->json([
                "status" => false,
                'message' => 'gagal hapus user. ' . $e,
            ]);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $credentials = $request->only('email', 'password');
        $token = Auth::guard('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }


        $user = Auth::guard('api')->user();
        return response()->json([
            'status' => true,
            'message' => 'Sukses login',
            'data' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }



}
