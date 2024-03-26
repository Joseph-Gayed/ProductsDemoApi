<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{

    public function guestLogin(Request $request)
    {
        // Retrieve the guest identifier from the request data
        $guestIdentifier = $request->input('guest_identifier');

        // Generate a unique identifier if not provided in the request
        if (empty($guestIdentifier)) {
            $guestIdentifier = uniqid('guest_');
        }

        // Check if the guest user already exists
        $guestUser = User::where('email', $guestIdentifier)->first();

        // If the guest user doesn't exist, create it
        if (!$guestUser) {
            $guestUser = User::create([
                'name' => $guestIdentifier,
                'email' => $guestIdentifier, // Use unique identifier as email
                'password' => Hash::make($guestIdentifier), // Use unique identifier as password
            ]);
        }

        // Revoke existing tokens associated with the guest user
        $guestUser->tokens()->delete();

        // Create a token for the guest user
        $token = $guestUser->createToken($guestIdentifier)->plainTextToken;

        // Return the token as the response
        return response()->json(['token' => $token, 'id' => $guestIdentifier]);
    }

}
