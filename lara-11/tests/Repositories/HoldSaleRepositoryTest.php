<?php namespace Tests\Repositories;

use App\Models\HoldSale;
use App\Repositories\HoldSaleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HoldSaleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HoldSaleRepository
     */
    protected $holdSaleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->holdSaleRepo = \App::make(HoldSaleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hold_sale()
    {
        $holdSale = HoldSale::factory()->make()->toArray();

        $createdHoldSale = $this->holdSaleRepo->create($holdSale);

        $createdHoldSale = $createdHoldSale->toArray();
        $this->assertArrayHasKey('id', $createdHoldSale);
        $this->assertNotNull($createdHoldSale['id'], 'Created HoldSale must have id specified');
        $this->assertNotNull(HoldSale::find($createdHoldSale['id']), 'HoldSale with given id must be in DB');
        $this->assertModelData($holdSale, $createdHoldSale);
    }

    /**
     * @test read
     */
    public function test_read_hold_sale()
    {
        $holdSale = HoldSale::factory()->create();

        $dbHoldSale = $this->holdSaleRepo->find($holdSale->id);

        $dbHoldSale = $dbHoldSale->toArray();
        $this->assertModelData($holdSale->toArray(), $dbHoldSale);
    }

    /**
     * @test update
     */
    public function test_update_hold_sale()
    {
        $holdSale = HoldSale::factory()->create();
        $fakeHoldSale = HoldSale::factory()->make()->toArray();

        $updatedHoldSale = $this->holdSaleRepo->update($fakeHoldSale, $holdSale->id);

        $this->assertModelData($fakeHoldSale, $updatedHoldSale->toArray());
        $dbHoldSale = $this->holdSaleRepo->find($holdSale->id);
        $this->assertModelData($fakeHoldSale, $dbHoldSale->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hold_sale()
    {
        $holdSale = HoldSale::factory()->create();

        $resp = $this->holdSaleRepo->delete($holdSale->id);

        $this->assertTrue($resp);
        $this->assertNull(HoldSale::find($holdSale->id), 'HoldSale should not exist in DB');
    }
}
