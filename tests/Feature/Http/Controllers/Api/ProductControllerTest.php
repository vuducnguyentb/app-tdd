<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_create_a_product()
    {
        $faker = Factory::create();
        $response = $this->json('POST','/api/products',[
            'name'=>$name = $faker->company,
            'slug'=>Str::slug($name),
            'price'=>$price = rand(10,100),
        ]);

        $response->assertJsonStructure([
            'id','name','slug','price','created_at'
        ])
            ->assertJson([
                'name'=>$name,
                'slug'=>Str::slug($name),
                'price'=>$price
            ])
            ->assertStatus(201);

        #check db
        $this->assertDatabaseHas('products',[
           'name'=>$name,
            'slug'=>Str::slug($name),
           'price'=>$price
        ]);
    }

    /**
     *
     * @test
     */
    public function will_fail_with_a_404_if_product_is_not_found(){
        $reponse = $this->json('GET',"api/products/-1");
        $reponse->assertStatus(404);
    }

    /**
     *
     * @test
     */
    public function can_return_a_product(){
        //Given
            $product = $this->create('Product');
        //When
             $reponse = $this->json('GET',"api/products/$product->id");
        //Then
        $reponse->assertStatus(200)
        ->assertExactJson([
            'id'=>$product->id,
            'name'=>$product->name,
            'slug'=>$product->slug,
            'price'=>$product->price,
            'created_at'=>(string)$product->created_at
        ]);
    }
}
