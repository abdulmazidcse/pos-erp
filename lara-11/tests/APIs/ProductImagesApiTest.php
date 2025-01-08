<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProductImages;

class ProductImagesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_product_images()
    {
        $productImages = ProductImages::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/product_images', $productImages
        );

        $this->assertApiResponse($productImages);
    }

    /**
     * @test
     */
    public function test_read_product_images()
    {
        $productImages = ProductImages::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/product_images/'.$productImages->id
        );

        $this->assertApiResponse($productImages->toArray());
    }

    /**
     * @test
     */
    public function test_update_product_images()
    {
        $productImages = ProductImages::factory()->create();
        $editedProductImages = ProductImages::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/product_images/'.$productImages->id,
            $editedProductImages
        );

        $this->assertApiResponse($editedProductImages);
    }

    /**
     * @test
     */
    public function test_delete_product_images()
    {
        $productImages = ProductImages::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/product_images/'.$productImages->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/product_images/'.$productImages->id
        );

        $this->response->assertStatus(404);
    }
}
