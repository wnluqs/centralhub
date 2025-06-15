<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Get user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate token (if using Sanctum or Passport)
        $token = $user->createToken('MyApp')->plainTextToken;

        // Return response
        return response()->json([
            'token' => $token,
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'staff_id' => $user->staff_id,
                'branch'   => $user->branch ?? 'Unknown',  // ✅ Explicitly added
                'role'     => $user->getRoleNames()->first() ?? null,
            ],
            'message' => 'Login successful'
        ], 200);
    }
}
