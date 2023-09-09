<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierType;

class SupplierTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_type()
    {
        $supplierType = SupplierType::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_types', $supplierType
        );

        $this->assertApiResponse($supplierType);
    }

    /**
     * @test
     */
    public function test_read_supplier_type()
    {
        $supplierType = SupplierType::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_types/'.$supplierType->id
        );

        $this->assertApiResponse($supplierType->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_type()
    {
        $supplierType = SupplierType::factory()->create();
        $editedSupplierType = SupplierType::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_types/'.$supplierType->id,
            $editedSupplierType
        );

        $this->assertApiResponse($editedSupplierType);
    }

    /**
     * @test
     */
    public function test_delete_supplier_type()
    {
        $supplierType = SupplierType::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_types/'.$supplierType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_types/'.$supplierType->id
        );

        $this->response->assertStatus(404);
    }
}
