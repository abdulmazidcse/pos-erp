<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PurchaseReceive;

class PurchaseReceiveApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/purchase_receives', $purchaseReceive
        );

        $this->assertApiResponse($purchaseReceive);
    }

    /**
     * @test
     */
    public function test_read_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/purchase_receives/'.$purchaseReceive->id
        );

        $this->assertApiResponse($purchaseReceive->toArray());
    }

    /**
     * @test
     */
    public function test_update_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->create();
        $editedPurchaseReceive = PurchaseReceive::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/purchase_receives/'.$purchaseReceive->id,
            $editedPurchaseReceive
        );

        $this->assertApiResponse($editedPurchaseReceive);
    }

    /**
     * @test
     */
    public function test_delete_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/purchase_receives/'.$purchaseReceive->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/purchase_receives/'.$purchaseReceive->id
        );

        $this->response->assertStatus(404);
    }
}
