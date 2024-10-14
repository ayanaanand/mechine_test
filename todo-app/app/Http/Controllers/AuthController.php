<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Confirm password field
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Hash the password
        ]);

        // Log the user in
        Auth::login($user);

        // Return a success response
        return response()->json(['message' => 'User registered successfully', 'user' => $user]);
    }

    /**
     * Login a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check the credentials
        if (Auth::attempt($request->only('email', 'password'))) {
            // Return a success response if authentication is successful
            return response()->json(['message' => 'Logged in successfully', 'user' => Auth::user()]);
        }

        // If authentication fails
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    /**
     * Logout the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Log the user out
        Auth::logout();

        // Return a success response
        return response()->json(['message' => 'Logged out successfully']);
    }
}
