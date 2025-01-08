<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProductBarcodes;

class ProductBarcodesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/product_barcodes', $productBarcodes
        );

        $this->assertApiResponse($productBarcodes);
    }

    /**
     * @test
     */
    public function test_read_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/product_barcodes/'.$productBarcodes->id
        );

        $this->assertApiResponse($productBarcodes->toArray());
    }

    /**
     * @test
     */
    public function test_update_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->create();
        $editedProductBarcodes = ProductBarcodes::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/product_barcodes/'.$productBarcodes->id,
            $editedProductBarcodes
        );

        $this->assertApiResponse($editedProductBarcodes);
    }

    /**
     * @test
     */
    public function test_delete_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/product_barcodes/'.$productBarcodes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/product_barcodes/'.$productBarcodes->id
        );

        $this->response->assertStatus(404);
    }
}
