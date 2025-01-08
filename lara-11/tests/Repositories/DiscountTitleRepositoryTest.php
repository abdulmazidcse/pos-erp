<?php namespace Tests\Repositories;

use App\Models\DiscountTitle;
use App\Repositories\DiscountTitleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DiscountTitleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DiscountTitleRepository
     */
    protected $discountTitleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->discountTitleRepo = \App::make(DiscountTitleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->make()->toArray();

        $createdDiscountTitle = $this->discountTitleRepo->create($discountTitle);

        $createdDiscountTitle = $createdDiscountTitle->toArray();
        $this->assertArrayHasKey('id', $createdDiscountTitle);
        $this->assertNotNull($createdDiscountTitle['id'], 'Created DiscountTitle must have id specified');
        $this->assertNotNull(DiscountTitle::find($createdDiscountTitle['id']), 'DiscountTitle with given id must be in DB');
        $this->assertModelData($discountTitle, $createdDiscountTitle);
    }

    /**
     * @test read
     */
    public function test_read_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->create();

        $dbDiscountTitle = $this->discountTitleRepo->find($discountTitle->id);

        $dbDiscountTitle = $dbDiscountTitle->toArray();
        $this->assertModelData($discountTitle->toArray(), $dbDiscountTitle);
    }

    /**
     * @test update
     */
    public function test_update_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->create();
        $fakeDiscountTitle = DiscountTitle::factory()->make()->toArray();

        $updatedDiscountTitle = $this->discountTitleRepo->update($fakeDiscountTitle, $discountTitle->id);

        $this->assertModelData($fakeDiscountTitle, $updatedDiscountTitle->toArray());
        $dbDiscountTitle = $this->discountTitleRepo->find($discountTitle->id);
        $this->assertModelData($fakeDiscountTitle, $dbDiscountTitle->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_discount_title()
    {
        $discountTitle = DiscountTitle::factory()->create();

        $resp = $this->discountTitleRepo->delete($discountTitle->id);

        $this->assertTrue($resp);
        $this->assertNull(DiscountTitle::find($discountTitle->id), 'DiscountTitle should not exist in DB');
    }
}
