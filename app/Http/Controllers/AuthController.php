<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => "required|string|max:255",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|string|min:8|confirmed",
        ]);


        $user = User::create([
            "name" => $validated['name'],
            "email" => $validated['email'],
            "password" => Hash::make($validated['password']),
        ]);

        //? generate token for authorize
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "user" => new UserResource($user),
            "token" => $token,
        ]);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        //? get the user with that email
        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "These credentials are incorrect",
                "status" => false,
            ]);
        }

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "user" => new UserResource($user),
            "token" => $token,
        ]);
    }


    /**
     * Get the authenticated user
     */
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "You have been logged out",
            "success" => true,
        ]);
    }
}