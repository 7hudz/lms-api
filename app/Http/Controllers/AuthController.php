<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,instructor,student'
        ]);

        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before storing
            'role' => $request->role
        ]);

        // Return a response after successful registration
        return response()->json(['message' => 'User registered successfully']);
    }

    // Log in and issue a JWT token
    public function login(Request $request)
    {
        // Validate the credentials
        $credentials = $request->only('email', 'password');

        // Check if the credentials are correct and issue a token
        if ($token = JWTAuth::attempt($credentials)) {
            return response()->json(['token' => $token]);
        }

        // If authentication fails, return an error response
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Get the authenticated user's profile
    public function profile()
    {
        // Return the authenticated user's data
        return response()->json(auth()->user());
    }

    // Log out the user and invalidate the JWT token
    public function logout()
    {
        // Log the user out
        auth()->logout();

        // Return a success message
        return response()->json(['message' => 'Logged out successfully']);
    }
}
