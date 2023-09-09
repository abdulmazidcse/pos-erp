<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProductColor;

class ProductColorApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_product_color()
    {
        $productColor = ProductColor::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/product_colors', $productColor
        );

        $this->assertApiResponse($productColor);
    }

    /**
     * @test
     */
    public function test_read_product_color()
    {
        $productColor = ProductColor::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/product_colors/'.$productColor->id
        );

        $this->assertApiResponse($productColor->toArray());
    }

    /**
     * @test
     */
    public function test_update_product_color()
    {
        $productColor = ProductColor::factory()->create();
        $editedProductColor = ProductColor::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/product_colors/'.$productColor->id,
            $editedProductColor
        );

        $this->assertApiResponse($editedProductColor);
    }

    /**
     * @test
     */
    public function test_delete_product_color()
    {
        $productColor = ProductColor::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/product_colors/'.$productColor->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/product_colors/'.$productColor->id
        );

        $this->response->assertStatus(404);
    }
}
