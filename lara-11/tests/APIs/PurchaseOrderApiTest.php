<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PurchaseOrder;

class PurchaseOrderApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/purchase_orders', $purchaseOrder
        );

        $this->assertApiResponse($purchaseOrder);
    }

    /**
     * @test
     */
    public function test_read_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/purchase_orders/'.$purchaseOrder->id
        );

        $this->assertApiResponse($purchaseOrder->toArray());
    }

    /**
     * @test
     */
    public function test_update_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $editedPurchaseOrder = PurchaseOrder::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/purchase_orders/'.$purchaseOrder->id,
            $editedPurchaseOrder
        );

        $this->assertApiResponse($editedPurchaseOrder);
    }

    /**
     * @test
     */
    public function test_delete_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/purchase_orders/'.$purchaseOrder->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/purchase_orders/'.$purchaseOrder->id
        );

        $this->response->assertStatus(404);
    }
}
