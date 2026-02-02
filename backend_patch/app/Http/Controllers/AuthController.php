<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $req->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('web')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function me(Request $req)
    {
        return $req->user();
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
