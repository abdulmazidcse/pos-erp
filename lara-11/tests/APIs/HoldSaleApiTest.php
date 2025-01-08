<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HoldSale;

class HoldSaleApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hold_sale()
    {
        $holdSale = HoldSale::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hold_sales', $holdSale
        );

        $this->assertApiResponse($holdSale);
    }

    /**
     * @test
     */
    public function test_read_hold_sale()
    {
        $holdSale = HoldSale::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/hold_sales/'.$holdSale->id
        );

        $this->assertApiResponse($holdSale->toArray());
    }

    /**
     * @test
     */
    public function test_update_hold_sale()
    {
        $holdSale = HoldSale::factory()->create();
        $editedHoldSale = HoldSale::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hold_sales/'.$holdSale->id,
            $editedHoldSale
        );

        $this->assertApiResponse($editedHoldSale);
    }

    /**
     * @test
     */
    public function test_delete_hold_sale()
    {
        $holdSale = HoldSale::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hold_sales/'.$holdSale->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hold_sales/'.$holdSale->id
        );

        $this->response->assertStatus(404);
    }
}
