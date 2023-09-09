<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProductSize;

class ProductSizeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_product_size()
    {
        $productSize = ProductSize::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/product_sizes', $productSize
        );

        $this->assertApiResponse($productSize);
    }

    /**
     * @test
     */
    public function test_read_product_size()
    {
        $productSize = ProductSize::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/product_sizes/'.$productSize->id
        );

        $this->assertApiResponse($productSize->toArray());
    }

    /**
     * @test
     */
    public function test_update_product_size()
    {
        $productSize = ProductSize::factory()->create();
        $editedProductSize = ProductSize::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/product_sizes/'.$productSize->id,
            $editedProductSize
        );

        $this->assertApiResponse($editedProductSize);
    }

    /**
     * @test
     */
    public function test_delete_product_size()
    {
        $productSize = ProductSize::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/product_sizes/'.$productSize->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/product_sizes/'.$productSize->id
        );

        $this->response->assertStatus(404);
    }
}
