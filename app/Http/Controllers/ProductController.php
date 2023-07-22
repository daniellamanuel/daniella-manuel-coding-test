<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::paginate($request->input('perPage'));
        return response()->json(['products' => $products]);
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
            $product_data = $request->all();               
            $add_product = new Product($product_data);
            $add_product->save();

            //Cache new product
            $cache_key = 'product_' . $add_product->id;
            Cache::put($cache_key, $add_product);

            return response()->json([
                'message' => 'Product added successfully.',
                'product' => $add_product,
            ]);
        }
    }

    public function show(Product $product)
    {
        //Check if cache exists
        $cache_key = 'product_' . $product->id;

        if (Cache::has($cache_key)) {
            Cache::get($cache_key);
        } else {
            Cache::put($cache_key, $product);
        }
        
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        //Remove the previous cache (if there's any)
        $cache_key = 'product_' . $product->id;
        Cache::forget($cache_key);

        $validate = Validator::make($request->all(), [
            'product_name' => 'required|max:255',
            'product_description' => 'required',
            'product_price' => 'required|numeric'
        ]);
        
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 400);
        } else {
            $product_data = $request->all();               
            $update_product = Product::where('id', $product->id)->firstOrFail();
            $update_product->fill($product_data);
            $update_product->save();

            //Add new cache
            Cache::put($cache_key, $product);

            return response()->json([
                'message' => 'Product updated successfully.',
                'product' => $product,
            ]);
        } 
    }

    public function destroy(Product $product)
    {
        //Remove cache (if there's any)
        $cache_key = 'product_' . $product->id;
        Cache::forget($cache_key);

        $delete_product = Product::where('id', $product->id)->firstOrFail();
        $delete_product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
