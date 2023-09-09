<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DiscountSetting;

class DiscountSettingApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/discount_settings', $discountSetting
        );

        $this->assertApiResponse($discountSetting);
    }

    /**
     * @test
     */
    public function test_read_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/discount_settings/'.$discountSetting->id
        );

        $this->assertApiResponse($discountSetting->toArray());
    }

    /**
     * @test
     */
    public function test_update_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->create();
        $editedDiscountSetting = DiscountSetting::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/discount_settings/'.$discountSetting->id,
            $editedDiscountSetting
        );

        $this->assertApiResponse($editedDiscountSetting);
    }

    /**
     * @test
     */
    public function test_delete_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/discount_settings/'.$discountSetting->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/discount_settings/'.$discountSetting->id
        );

        $this->response->assertStatus(404);
    }
}
