<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DiscountTitle;

class DiscountTitleApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/discount_titles', $discountTitle
        );

        $this->assertApiResponse($discountTitle);
    }

    /**
     * @test
     */
    public function test_read_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/discount_titles/'.$discountTitle->id
        );

        $this->assertApiResponse($discountTitle->toArray());
    }

    /**
     * @test
     */
    public function test_update_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->create();
        $editedDiscountTitle = DiscountTitle::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/discount_titles/'.$discountTitle->id,
            $editedDiscountTitle
        );

        $this->assertApiResponse($editedDiscountTitle);
    }

    /**
     * @test
     */
    public function test_delete_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/discount_titles/'.$discountTitle->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/discount_titles/'.$discountTitle->id
        );

        $this->response->assertStatus(404);
    }
}
