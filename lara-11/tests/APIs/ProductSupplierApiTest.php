<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProductSupplier;

class ProductSupplierApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/product_suppliers', $productSupplier
        );

        $this->assertApiResponse($productSupplier);
    }

    /**
     * @test
     */
    public function test_read_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/product_suppliers/'.$productSupplier->id
        );

        $this->assertApiResponse($productSupplier->toArray());
    }

    /**
     * @test
     */
    public function test_update_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->create();
        $editedProductSupplier = ProductSupplier::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/product_suppliers/'.$productSupplier->id,
            $editedProductSupplier
        );

        $this->assertApiResponse($editedProductSupplier);
    }

    /**
     * @test
     */
    public function test_delete_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/product_suppliers/'.$productSupplier->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/product_suppliers/'.$productSupplier->id
        );

        $this->response->assertStatus(404);
    }
}
