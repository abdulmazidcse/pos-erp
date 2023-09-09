<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProductKeywords;

class ProductKeywordsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/product_keywords', $productKeywords
        );

        $this->assertApiResponse($productKeywords);
    }

    /**
     * @test
     */
    public function test_read_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/product_keywords/'.$productKeywords->id
        );

        $this->assertApiResponse($productKeywords->toArray());
    }

    /**
     * @test
     */
    public function test_update_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->create();
        $editedProductKeywords = ProductKeywords::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/product_keywords/'.$productKeywords->id,
            $editedProductKeywords
        );

        $this->assertApiResponse($editedProductKeywords);
    }

    /**
     * @test
     */
    public function test_delete_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/product_keywords/'.$productKeywords->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/product_keywords/'.$productKeywords->id
        );

        $this->response->assertStatus(404);
    }
}
