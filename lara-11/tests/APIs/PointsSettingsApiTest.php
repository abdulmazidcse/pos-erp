<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PointsSettings;

class PointsSettingsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/points_settings', $pointsSettings
        );

        $this->assertApiResponse($pointsSettings);
    }

    /**
     * @test
     */
    public function test_read_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/points_settings/'.$pointsSettings->id
        );

        $this->assertApiResponse($pointsSettings->toArray());
    }

    /**
     * @test
     */
    public function test_update_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->create();
        $editedPointsSettings = PointsSettings::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/points_settings/'.$pointsSettings->id,
            $editedPointsSettings
        );

        $this->assertApiResponse($editedPointsSettings);
    }

    /**
     * @test
     */
    public function test_delete_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/points_settings/'.$pointsSettings->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/points_settings/'.$pointsSettings->id
        );

        $this->response->assertStatus(404);
    }
}
