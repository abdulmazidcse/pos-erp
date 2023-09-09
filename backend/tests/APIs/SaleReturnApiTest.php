<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SaleReturn;

class SaleReturnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sale_return()
    {
        $saleReturn = SaleReturn::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sale_returns', $saleReturn
        );

        $this->assertApiResponse($saleReturn);
    }

    /**
     * @test
     */
    public function test_read_sale_return()
    {
        $saleReturn = SaleReturn::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/sale_returns/'.$saleReturn->id
        );

        $this->assertApiResponse($saleReturn->toArray());
    }

    /**
     * @test
     */
    public function test_update_sale_return()
    {
        $saleReturn = SaleReturn::factory()->create();
        $editedSaleReturn = SaleReturn::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sale_returns/'.$saleReturn->id,
            $editedSaleReturn
        );

        $this->assertApiResponse($editedSaleReturn);
    }

    /**
     * @test
     */
    public function test_delete_sale_return()
    {
        $saleReturn = SaleReturn::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sale_returns/'.$saleReturn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sale_returns/'.$saleReturn->id
        );

        $this->response->assertStatus(404);
    }
}
