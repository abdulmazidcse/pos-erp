<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HoldSaleDiscount;

class HoldSaleDiscountApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hold_sale_discounts', $holdSaleDiscount
        );

        $this->assertApiResponse($holdSaleDiscount);
    }

    /**
     * @test
     */
    public function test_read_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/hold_sale_discounts/'.$holdSaleDiscount->id
        );

        $this->assertApiResponse($holdSaleDiscount->toArray());
    }

    /**
     * @test
     */
    public function test_update_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->create();
        $editedHoldSaleDiscount = HoldSaleDiscount::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hold_sale_discounts/'.$holdSaleDiscount->id,
            $editedHoldSaleDiscount
        );

        $this->assertApiResponse($editedHoldSaleDiscount);
    }

    /**
     * @test
     */
    public function test_delete_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hold_sale_discounts/'.$holdSaleDiscount->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hold_sale_discounts/'.$holdSaleDiscount->id
        );

        $this->response->assertStatus(404);
    }
}
