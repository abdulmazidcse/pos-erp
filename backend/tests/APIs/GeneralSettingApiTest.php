<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\GeneralSetting;

class GeneralSettingApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/general_settings', $generalSetting
        );

        $this->assertApiResponse($generalSetting);
    }

    /**
     * @test
     */
    public function test_read_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/general_settings/'.$generalSetting->id
        );

        $this->assertApiResponse($generalSetting->toArray());
    }

    /**
     * @test
     */
    public function test_update_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->create();
        $editedGeneralSetting = GeneralSetting::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/general_settings/'.$generalSetting->id,
            $editedGeneralSetting
        );

        $this->assertApiResponse($editedGeneralSetting);
    }

    /**
     * @test
     */
    public function test_delete_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/general_settings/'.$generalSetting->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/general_settings/'.$generalSetting->id
        );

        $this->response->assertStatus(404);
    }
}
