<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Sales;

class SalesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales()
    {
        $sales = Sales::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales', $sales
        );

        $this->assertApiResponse($sales);
    }

    /**
     * @test
     */
    public function test_read_sales()
    {
        $sales = Sales::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/sales/'.$sales->id
        );

        $this->assertApiResponse($sales->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales()
    {
        $sales = Sales::factory()->create();
        $editedSales = Sales::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales/'.$sales->id,
            $editedSales
        );

        $this->assertApiResponse($editedSales);
    }

    /**
     * @test
     */
    public function test_delete_sales()
    {
        $sales = Sales::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales/'.$sales->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales/'.$sales->id
        );

        $this->response->assertStatus(404);
    }
}
