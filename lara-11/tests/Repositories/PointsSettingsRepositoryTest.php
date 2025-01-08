<?php namespace Tests\Repositories;

use App\Models\PointsSettings;
use App\Repositories\PointsSettingsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PointsSettingsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PointsSettingsRepository
     */
    protected $pointsSettingsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pointsSettingsRepo = \App::make(PointsSettingsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->make()->toArray();

        $createdPointsSettings = $this->pointsSettingsRepo->create($pointsSettings);

        $createdPointsSettings = $createdPointsSettings->toArray();
        $this->assertArrayHasKey('id', $createdPointsSettings);
        $this->assertNotNull($createdPointsSettings['id'], 'Created PointsSettings must have id specified');
        $this->assertNotNull(PointsSettings::find($createdPointsSettings['id']), 'PointsSettings with given id must be in DB');
        $this->assertModelData($pointsSettings, $createdPointsSettings);
    }

    /**
     * @test read
     */
    public function test_read_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->create();

        $dbPointsSettings = $this->pointsSettingsRepo->find($pointsSettings->id);

        $dbPointsSettings = $dbPointsSettings->toArray();
        $this->assertModelData($pointsSettings->toArray(), $dbPointsSettings);
    }

    /**
     * @test update
     */
    public function test_update_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->create();
        $fakePointsSettings = PointsSettings::factory()->make()->toArray();

        $updatedPointsSettings = $this->pointsSettingsRepo->update($fakePointsSettings, $pointsSettings->id);

        $this->assertModelData($fakePointsSettings, $updatedPointsSettings->toArray());
        $dbPointsSettings = $this->pointsSettingsRepo->find($pointsSettings->id);
        $this->assertModelData($fakePointsSettings, $dbPointsSettings->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_points_settings()
    {
        $pointsSettings = PointsSettings::factory()->create();

        $resp = $this->pointsSettingsRepo->delete($pointsSettings->id);

        $this->assertTrue($resp);
        $this->assertNull(PointsSettings::find($pointsSettings->id), 'PointsSettings should not exist in DB');
    }
}
