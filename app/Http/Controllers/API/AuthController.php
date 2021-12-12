<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function index(){
        $users = User::all();
        $response = [
            'message' => 'All data',
            'data' => $users
        ];
        return response()->json($response, 200);
    }

    public function register(Request $request) {
        $validation = $request->validate([
            'name' => 'required|string|max:225',
            'email' => 'required|string|email|max:225',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $validation['name'],
            'email' => $validation['email'],
            'password' => Hash::make($validation['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register Successfull',
            'access_tokeen' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password')))
        {
            return response()->json([
                'message' => 'Invailid login'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Successfull login',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
        
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
