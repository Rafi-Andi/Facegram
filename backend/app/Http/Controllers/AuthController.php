<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "full_name" => "required",
            "bio" => "required|max:100",
            "username" => "required|min:3|unique:users,username|regex:/^[a-zA-Z0-9._]+$/",
            "password" => "required|min:6",
            "is_private" => "boolean"
        ]);

        $validated = $validator->validate();

        if ($validator->fails()) {
            return response()->json([
                "Invalid Field",
                "errors" => $validator->errors()
            ], 422);
        }

        $user = User::create([
            "full_name" => $validated['full_name'],
            "bio" => $validated['bio'],
            "username" => $validated['username'],
            "password" => bcrypt($validated['password']),
            "is_private" => $validated['is_private'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => "Register Success",
            "token" => $token,
            "user" => [
                "full_name" => $user->full_name,
                "bio" => $user->bio,
                "username" => $user->username,
                "is_private" => $user->is_private,
                "id" => $user->id,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $user = User::where('username', $validated['username'])->first();
        if (!$user) {
            return response()->json([
                "message" => "Wrong username or password"
            ], 401);
        }
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                "message" => "Wrong username or password"
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => "Login Success",
            "token" => $token,
            "user" => [
                "id" => $user->id,
                "full_name" => $user->full_name,
                "username" => $user->username,
                "bio" => $user->bio,
                "is_private" => $user->is_private,
                "created_at" => $user->created_at
            ]
        ], 200);
    }

    public function logout(Request $request){
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            "message" => "Logout Success"
        ], 200);
    }
}
