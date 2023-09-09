<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SaleReturnItem;

class SaleReturnItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sale_return_items', $saleReturnItem
        );

        $this->assertApiResponse($saleReturnItem);
    }

    /**
     * @test
     */
    public function test_read_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/sale_return_items/'.$saleReturnItem->id
        );

        $this->assertApiResponse($saleReturnItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->create();
        $editedSaleReturnItem = SaleReturnItem::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sale_return_items/'.$saleReturnItem->id,
            $editedSaleReturnItem
        );

        $this->assertApiResponse($editedSaleReturnItem);
    }

    /**
     * @test
     */
    public function test_delete_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sale_return_items/'.$saleReturnItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sale_return_items/'.$saleReturnItem->id
        );

        $this->response->assertStatus(404);
    }
}
