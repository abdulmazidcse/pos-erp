<?php namespace Tests\Repositories;

use App\Models\GeneralSetting;
use App\Repositories\GeneralSettingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class GeneralSettingRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var GeneralSettingRepository
     */
    protected $generalSettingRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->generalSettingRepo = \App::make(GeneralSettingRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->make()->toArray();

        $createdGeneralSetting = $this->generalSettingRepo->create($generalSetting);

        $createdGeneralSetting = $createdGeneralSetting->toArray();
        $this->assertArrayHasKey('id', $createdGeneralSetting);
        $this->assertNotNull($createdGeneralSetting['id'], 'Created GeneralSetting must have id specified');
        $this->assertNotNull(GeneralSetting::find($createdGeneralSetting['id']), 'GeneralSetting with given id must be in DB');
        $this->assertModelData($generalSetting, $createdGeneralSetting);
    }

    /**
     * @test read
     */
    public function test_read_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->create();

        $dbGeneralSetting = $this->generalSettingRepo->find($generalSetting->id);

        $dbGeneralSetting = $dbGeneralSetting->toArray();
        $this->assertModelData($generalSetting->toArray(), $dbGeneralSetting);
    }

    /**
     * @test update
     */
    public function test_update_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->create();
        $fakeGeneralSetting = GeneralSetting::factory()->make()->toArray();

        $updatedGeneralSetting = $this->generalSettingRepo->update($fakeGeneralSetting, $generalSetting->id);

        $this->assertModelData($fakeGeneralSetting, $updatedGeneralSetting->toArray());
        $dbGeneralSetting = $this->generalSettingRepo->find($generalSetting->id);
        $this->assertModelData($fakeGeneralSetting, $dbGeneralSetting->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_general_setting()
    {
        $generalSetting = GeneralSetting::factory()->create();

        $resp = $this->generalSettingRepo->delete($generalSetting->id);

        $this->assertTrue($resp);
        $this->assertNull(GeneralSetting::find($generalSetting->id), 'GeneralSetting should not exist in DB');
    }
}
