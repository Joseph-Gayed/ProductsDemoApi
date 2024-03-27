<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $signupValidationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users,email',
            'password' => 'required|string|confirmed',
        ];
        $validatedData = $this->validateAuthRequest($request,$signupValidationRules);
        if($validatedData){
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $createdUser = User::create($validatedData);
        // Create a token for the guest user
        $token = $createdUser->createToken($createdUser['email'])->plainTextToken;
        $createdUser['token'] = $token;
        return response()->json(['message' => 'user registered successfully', 'data' => $createdUser], 201);
    }

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

    public function logout(Request $request)
    {
        // Delete the current token that was used for the request
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'user logged out successfully'], 200);
    }



    /**
     * Validate incoming request data for the any login/signup operation .
     */
    public function validateAuthRequest(Request $request , $validationRules)
    {
        // Validate incoming request data
        $validatedData = $request->validate($validationRules);
        return  $validatedData;
    }
}

