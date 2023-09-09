<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HoldSaleItem;

class HoldSaleItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hold_sale_items', $holdSaleItem
        );

        $this->assertApiResponse($holdSaleItem);
    }

    /**
     * @test
     */
    public function test_read_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/hold_sale_items/'.$holdSaleItem->id
        );

        $this->assertApiResponse($holdSaleItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->create();
        $editedHoldSaleItem = HoldSaleItem::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hold_sale_items/'.$holdSaleItem->id,
            $editedHoldSaleItem
        );

        $this->assertApiResponse($editedHoldSaleItem);
    }

    /**
     * @test
     */
    public function test_delete_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hold_sale_items/'.$holdSaleItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hold_sale_items/'.$holdSaleItem->id
        );

        $this->response->assertStatus(404);
    }
}
