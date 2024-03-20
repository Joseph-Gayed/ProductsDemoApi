<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//make the search api protected by token
Route::middleware('auth:sanctum')->get('/products/search', [ProductController::class, 'search']);

//this will create routes apis for all crud operations
Route::Resource('products',ProductController::class);
