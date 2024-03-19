<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $responseData = [];
        if ($products->isEmpty()) {
            $responseData["message"] = "No products";
        }
        $responseData["data"] =  $products;
        return response()->json($responseData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateProductInsertionRequest($request);
        $createdProduct = Product::create($validatedData);
        return response()->json(['message' => 'Product created successfully', 'data' => $createdProduct], 201);
    }
    
    /**
     * Validate incoming request data for the insertaion operation and generate slug.
     */
    public function validateProductInsertionRequest(Request $request){
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Generate slug from the provided name
        $validatedData['slug'] = Str::slug($validatedData['name']);
        return  $validatedData;
    }

    
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        $responseData["data"] =  $product;
        return response()->json($responseData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }


    /**
     * Remove the specified resource from storage. 
     */
    public function destroy(string $id)
    {   

    }


}
