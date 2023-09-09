<?php namespace Tests\Repositories;

use App\Models\DiscountSetting;
use App\Repositories\DiscountSettingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DiscountSettingRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DiscountSettingRepository
     */
    protected $discountSettingRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->discountSettingRepo = \App::make(DiscountSettingRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->make()->toArray();

        $createdDiscountSetting = $this->discountSettingRepo->create($discountSetting);

        $createdDiscountSetting = $createdDiscountSetting->toArray();
        $this->assertArrayHasKey('id', $createdDiscountSetting);
        $this->assertNotNull($createdDiscountSetting['id'], 'Created DiscountSetting must have id specified');
        $this->assertNotNull(DiscountSetting::find($createdDiscountSetting['id']), 'DiscountSetting with given id must be in DB');
        $this->assertModelData($discountSetting, $createdDiscountSetting);
    }

    /**
     * @test read
     */
    public function test_read_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->create();

        $dbDiscountSetting = $this->discountSettingRepo->find($discountSetting->id);

        $dbDiscountSetting = $dbDiscountSetting->toArray();
        $this->assertModelData($discountSetting->toArray(), $dbDiscountSetting);
    }

    /**
     * @test update
     */
    public function test_update_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->create();
        $fakeDiscountSetting = DiscountSetting::factory()->make()->toArray();

        $updatedDiscountSetting = $this->discountSettingRepo->update($fakeDiscountSetting, $discountSetting->id);

        $this->assertModelData($fakeDiscountSetting, $updatedDiscountSetting->toArray());
        $dbDiscountSetting = $this->discountSettingRepo->find($discountSetting->id);
        $this->assertModelData($fakeDiscountSetting, $dbDiscountSetting->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_discount_setting()
    {
        $discountSetting = DiscountSetting::factory()->create();

        $resp = $this->discountSettingRepo->delete($discountSetting->id);

        $this->assertTrue($resp);
        $this->assertNull(DiscountSetting::find($discountSetting->id), 'DiscountSetting should not exist in DB');
    }
}
