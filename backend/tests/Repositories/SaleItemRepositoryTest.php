<?php namespace Tests\Repositories;

use App\Models\SaleItem;
use App\Repositories\SaleItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SaleItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SaleItemRepository
     */
    protected $saleItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->saleItemRepo = \App::make(SaleItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sale_item()
    {
        $saleItem = SaleItem::factory()->make()->toArray();

        $createdSaleItem = $this->saleItemRepo->create($saleItem);

        $createdSaleItem = $createdSaleItem->toArray();
        $this->assertArrayHasKey('id', $createdSaleItem);
        $this->assertNotNull($createdSaleItem['id'], 'Created SaleItem must have id specified');
        $this->assertNotNull(SaleItem::find($createdSaleItem['id']), 'SaleItem with given id must be in DB');
        $this->assertModelData($saleItem, $createdSaleItem);
    }

    /**
     * @test read
     */
    public function test_read_sale_item()
    {
        $saleItem = SaleItem::factory()->create();

        $dbSaleItem = $this->saleItemRepo->find($saleItem->id);

        $dbSaleItem = $dbSaleItem->toArray();
        $this->assertModelData($saleItem->toArray(), $dbSaleItem);
    }

    /**
     * @test update
     */
    public function test_update_sale_item()
    {
        $saleItem = SaleItem::factory()->create();
        $fakeSaleItem = SaleItem::factory()->make()->toArray();

        $updatedSaleItem = $this->saleItemRepo->update($fakeSaleItem, $saleItem->id);

        $this->assertModelData($fakeSaleItem, $updatedSaleItem->toArray());
        $dbSaleItem = $this->saleItemRepo->find($saleItem->id);
        $this->assertModelData($fakeSaleItem, $dbSaleItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sale_item()
    {
        $saleItem = SaleItem::factory()->create();

        $resp = $this->saleItemRepo->delete($saleItem->id);

        $this->assertTrue($resp);
        $this->assertNull(SaleItem::find($saleItem->id), 'SaleItem should not exist in DB');
    }
}
