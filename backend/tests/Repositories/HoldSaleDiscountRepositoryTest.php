<?php namespace Tests\Repositories;

use App\Models\HoldSaleDiscount;
use App\Repositories\HoldSaleDiscountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HoldSaleDiscountRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HoldSaleDiscountRepository
     */
    protected $holdSaleDiscountRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->holdSaleDiscountRepo = \App::make(HoldSaleDiscountRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->make()->toArray();

        $createdHoldSaleDiscount = $this->holdSaleDiscountRepo->create($holdSaleDiscount);

        $createdHoldSaleDiscount = $createdHoldSaleDiscount->toArray();
        $this->assertArrayHasKey('id', $createdHoldSaleDiscount);
        $this->assertNotNull($createdHoldSaleDiscount['id'], 'Created HoldSaleDiscount must have id specified');
        $this->assertNotNull(HoldSaleDiscount::find($createdHoldSaleDiscount['id']), 'HoldSaleDiscount with given id must be in DB');
        $this->assertModelData($holdSaleDiscount, $createdHoldSaleDiscount);
    }

    /**
     * @test read
     */
    public function test_read_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->create();

        $dbHoldSaleDiscount = $this->holdSaleDiscountRepo->find($holdSaleDiscount->id);

        $dbHoldSaleDiscount = $dbHoldSaleDiscount->toArray();
        $this->assertModelData($holdSaleDiscount->toArray(), $dbHoldSaleDiscount);
    }

    /**
     * @test update
     */
    public function test_update_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->create();
        $fakeHoldSaleDiscount = HoldSaleDiscount::factory()->make()->toArray();

        $updatedHoldSaleDiscount = $this->holdSaleDiscountRepo->update($fakeHoldSaleDiscount, $holdSaleDiscount->id);

        $this->assertModelData($fakeHoldSaleDiscount, $updatedHoldSaleDiscount->toArray());
        $dbHoldSaleDiscount = $this->holdSaleDiscountRepo->find($holdSaleDiscount->id);
        $this->assertModelData($fakeHoldSaleDiscount, $dbHoldSaleDiscount->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hold_sale_discount()
    {
        $holdSaleDiscount = HoldSaleDiscount::factory()->create();

        $resp = $this->holdSaleDiscountRepo->delete($holdSaleDiscount->id);

        $this->assertTrue($resp);
        $this->assertNull(HoldSaleDiscount::find($holdSaleDiscount->id), 'HoldSaleDiscount should not exist in DB');
    }
}
