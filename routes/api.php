<?php

use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

//Public routes
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products',[ProductController::class,'index']);
Route::get('/products/{id}',[ProductController::class,'show']);

//Protected routes
Route::group(['middleware' => ['auth:sanctum']],function(){
    //here add the apis that should use token 
    Route::post('/products',[ProductController::class,'store']);
    Route::put('/products/{id}',[ProductController::class,'update']);
    Route::delete('/products/{id}',[ProductController::class,'destroy']);
});


//Guest Login
Route::post('/guest/login', function (Request $request) {
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
                'email' => $guestIdentifier , // Use unique identifier as email
                'password' => Hash::make($guestIdentifier), // Use unique identifier as password
            ]);
        }

    // Revoke existing tokens associated with the guest user
    $guestUser->tokens()->delete();
    
    // Create a token for the guest user
    $token = $guestUser->createToken($guestIdentifier)->plainTextToken;

    // Return the token as the response
    return response()->json(['token' => $token, 'id' => $guestIdentifier]);
});

