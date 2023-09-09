<?php namespace Tests\Repositories;

use App\Models\HoldSaleItem;
use App\Repositories\HoldSaleItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HoldSaleItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HoldSaleItemRepository
     */
    protected $holdSaleItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->holdSaleItemRepo = \App::make(HoldSaleItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->make()->toArray();

        $createdHoldSaleItem = $this->holdSaleItemRepo->create($holdSaleItem);

        $createdHoldSaleItem = $createdHoldSaleItem->toArray();
        $this->assertArrayHasKey('id', $createdHoldSaleItem);
        $this->assertNotNull($createdHoldSaleItem['id'], 'Created HoldSaleItem must have id specified');
        $this->assertNotNull(HoldSaleItem::find($createdHoldSaleItem['id']), 'HoldSaleItem with given id must be in DB');
        $this->assertModelData($holdSaleItem, $createdHoldSaleItem);
    }

    /**
     * @test read
     */
    public function test_read_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->create();

        $dbHoldSaleItem = $this->holdSaleItemRepo->find($holdSaleItem->id);

        $dbHoldSaleItem = $dbHoldSaleItem->toArray();
        $this->assertModelData($holdSaleItem->toArray(), $dbHoldSaleItem);
    }

    /**
     * @test update
     */
    public function test_update_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->create();
        $fakeHoldSaleItem = HoldSaleItem::factory()->make()->toArray();

        $updatedHoldSaleItem = $this->holdSaleItemRepo->update($fakeHoldSaleItem, $holdSaleItem->id);

        $this->assertModelData($fakeHoldSaleItem, $updatedHoldSaleItem->toArray());
        $dbHoldSaleItem = $this->holdSaleItemRepo->find($holdSaleItem->id);
        $this->assertModelData($fakeHoldSaleItem, $dbHoldSaleItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hold_sale_item()
    {
        $holdSaleItem = HoldSaleItem::factory()->create();

        $resp = $this->holdSaleItemRepo->delete($holdSaleItem->id);

        $this->assertTrue($resp);
        $this->assertNull(HoldSaleItem::find($holdSaleItem->id), 'HoldSaleItem should not exist in DB');
    }
}
