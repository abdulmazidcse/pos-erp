<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SalesDiscount;

class SalesDiscountApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales_discounts', $salesDiscount
        );

        $this->assertApiResponse($salesDiscount);
    }

    /**
     * @test
     */
    public function test_read_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/sales_discounts/'.$salesDiscount->id
        );

        $this->assertApiResponse($salesDiscount->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->create();
        $editedSalesDiscount = SalesDiscount::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales_discounts/'.$salesDiscount->id,
            $editedSalesDiscount
        );

        $this->assertApiResponse($editedSalesDiscount);
    }

    /**
     * @test
     */
    public function test_delete_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales_discounts/'.$salesDiscount->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales_discounts/'.$salesDiscount->id
        );

        $this->response->assertStatus(404);
    }
}
