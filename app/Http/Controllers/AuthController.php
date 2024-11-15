<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

       
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);

       
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken(env('SECREPT'))->accessToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
        ]);
    }

    public function logout(Request $request)
{
    $user = Auth::guard('api')->user();
    if ($user) {
        // Revoke all the user's tokens (or revoke the current token)
        $user->tokens->each(function ($token) {
            $token->delete();  // Delete the token, logging out the user
        });

        // Return a success response (JSON)
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    } else {
        // Return an error response if no authenticated user
        return response()->json([
            'status' => 'error',
            'message' => 'Unable to log out, no user authenticated'
        ], 400);  
    }
}


}
