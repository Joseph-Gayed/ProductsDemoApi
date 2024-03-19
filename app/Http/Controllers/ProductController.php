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
        $product = Product::find($id);
        $validatedData = $this->validateProductUpdatesRequest($request);
        $product->update($validatedData);
        $responseData["data"] =  $product;
        return response()->json($responseData);
    }

    /**
     * Validate incoming request data for the updating operation and generate slug.
     */
    public function validateProductUpdatesRequest(Request $request){
        $fields = $request->all();

        $rules = [ 'name' => 'string|max:255',
        'price' => 'numeric|min:0',
        'description' => 'nullable|string'];


        // Validate each field based on provided rules
        $validatedData = Validator::make($fields, $rules)->validate();

        // Generate slug from the provided name, if name is provided
        if (isset($fields['name'])) {
            $validatedData['slug'] = Str::slug($fields['name']);
        }

        return $validatedData;
    }
    
    

    /**
     * Remove the specified resource from storage. 
     */
    public function destroy(string $id)
    {   
        return response()->json(Product::destroy($id));
    }

    /**
     * Search in products names, description by keyword.
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
    
        // Query products where name or description contains the keyword
        $products = Product::query()
        ->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                  ->orWhere('description', 'like', "%$keyword%");
        })
        ->get();

        $responseData = [];
        if ($products->isEmpty() ||$products ==null) {
            $responseData["message"] = "No products";
        }
        $responseData["data"] =  $products;
        return response()->json($responseData);
    }

    
}
