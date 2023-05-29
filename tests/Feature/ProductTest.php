<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function testFactoryProduct()
    {
        $product = Product::factory()->create();
        $response = $this->post('/api/products', $product->toArray());

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $product->toArray());
    }
    
    public function testGetProduct()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testCreateProduct()
    {
        $productData = [
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'product_price' => 9.99,
        ];

        $response = $this->post('/api/products', $productData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $productData); 
    }

    public function testUpdateProduct()
    {
        $product = new Product();
        $product->product_name = 'Original Product Name';
        $product->product_description = 'Original Product Description';
        $product->product_price = 500.25;
        $product->save();

        $productData = [
            'product_name' => 'Updated Product Name',
            'product_description' => 'Updated Product Description',
            'product_price' => 123.45,
        ];

        $response = $this->put('/api/products/' . $product->id . '/update', $productData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $productData);
        $this->assertDatabaseMissing('products', $product->toArray());
    }

    public function testDeleteProduct()
    {
        $product = new Product();
        $product->product_name = 'Coca Cola';
        $product->product_description = 'Coca Cola Product Description';
        $product->product_price = 2.25;
        $product->save();

        $response = $this->delete('/api/products/' . $product->id . '/delete');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
