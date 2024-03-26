<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

//Public routes (Apis that can be called without token)
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products',[ProductController::class,'index']);
Route::get('/products/{id}',[ProductController::class,'show']);

Route::post('/guest_login',[AuthController::class,'guestLogin']);
 
//Protected routes (Apis that Must be called with token)
Route::group(['middleware' => ['auth:sanctum']],function(){
    Route::post('/products',[ProductController::class,'store']);
    Route::put('/products/{id}',[ProductController::class,'update']);
    Route::delete('/products/{id}',[ProductController::class,'destroy']);

});


