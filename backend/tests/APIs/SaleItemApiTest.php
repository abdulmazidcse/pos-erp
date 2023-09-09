<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SaleItem;

class SaleItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sale_item()
    {
        $saleItem = SaleItem::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sale_items', $saleItem
        );

        $this->assertApiResponse($saleItem);
    }

    /**
     * @test
     */
    public function test_read_sale_item()
    {
        $saleItem = SaleItem::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/sale_items/'.$saleItem->id
        );

        $this->assertApiResponse($saleItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_sale_item()
    {
        $saleItem = SaleItem::factory()->create();
        $editedSaleItem = SaleItem::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sale_items/'.$saleItem->id,
            $editedSaleItem
        );

        $this->assertApiResponse($editedSaleItem);
    }

    /**
     * @test
     */
    public function test_delete_sale_item()
    {
        $saleItem = SaleItem::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sale_items/'.$saleItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sale_items/'.$saleItem->id
        );

        $this->response->assertStatus(404);
    }
}
