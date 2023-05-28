<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::paginate(10);
        return new ProductResource($products);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_name' => 'required|max:255',
            'product_description' => 'required',
            'product_price' => 'required|numeric'
        ]);
        
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 400);
        } else {
            $data = $request->all();               
            $product = new Product($data);
            $product->save();

            return [
                'message' => 'Product added successfully.',
                'product' => $product,
            ];
        }
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        $validate = Validator::make($request->all(), [
            'product_name' => 'required|max:255',
            'product_description' => 'required',
            'product_price' => 'required|numeric'
        ]);
        
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 400);
        } else {
            $data = $request->all();               
            $product = Product::where('id', $product->id)->firstOrFail();
            $product->fill($data);
            $product->save();

            return [
                'message' => 'Product updated successfully.',
                'product' => $product,
            ];
        } 
    }

    public function destroy(Product $product)
    {
        $delete_product = Product::where('id', $product->id)->firstOrFail();
        $delete_product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
