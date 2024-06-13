<?php

namespace App\Http\Controllers;

use App\Models\MsLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        // Log the request input to check the data
    // \Log::info($request->all());
    // dd($request->input('Name'));
    // return $request->UserName;
        $validator = Validator::make($request->all(), [
            'UserName' => 'required|string|max:100',
            'Password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $username = $request->input('UserName');
        $password = Hash::make($request->input('Password'));
        $name = $request->input('Name');
        $phone = $request->input('Phone');
        $email = $request->input('Email');
        $userID = $request->input('UserID');

        // Call the stored procedure
        $ress = DB::select('CALL sp_mslogin_submit(?, ?, ?, ?, ?, ?, ?)', ['ADD',$username, $password, $name, $phone, $email, $userID]);

        return response()->json($ress[0], 201);
    }

    public function login(Request $request)
    {
        // \Log::info($request->all()); // Logging input request
        // return $request->Password;
        $validator = Validator::make($request->all(), [
            'UserName' => 'required|string',
            'Password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = MsLogin::where('UserName', $request->UserName)->first();
        if (!$user) {
            return response()->json(['xStatus' => '0', 'xMessage' => 'UserName not found'], 401);
        }
        if (!Hash::check($request->Password, $user->Password)) {
            return response()->json(['xStatus' => '0', 'xMessage' => 'Wrong Password!'], 401);
        }

        // return $user;
        // if (!$user || !Hash::check($request->Password, $user->Password)) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        // Log untuk debugging
        // \Log::info('User found: ' . $user->ID);

        // Generate token dengan Laravel Sanctum
        $token = $user->createToken(env('SECRET_KEY'))->plainTextToken;

        // \Log::info('Token created: ' . $token);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}