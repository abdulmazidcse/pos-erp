<?php namespace Tests\Repositories;

use App\Models\SaleReturnItem;
use App\Repositories\SaleReturnItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SaleReturnItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SaleReturnItemRepository
     */
    protected $saleReturnItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->saleReturnItemRepo = \App::make(SaleReturnItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->make()->toArray();

        $createdSaleReturnItem = $this->saleReturnItemRepo->create($saleReturnItem);

        $createdSaleReturnItem = $createdSaleReturnItem->toArray();
        $this->assertArrayHasKey('id', $createdSaleReturnItem);
        $this->assertNotNull($createdSaleReturnItem['id'], 'Created SaleReturnItem must have id specified');
        $this->assertNotNull(SaleReturnItem::find($createdSaleReturnItem['id']), 'SaleReturnItem with given id must be in DB');
        $this->assertModelData($saleReturnItem, $createdSaleReturnItem);
    }

    /**
     * @test read
     */
    public function test_read_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->create();

        $dbSaleReturnItem = $this->saleReturnItemRepo->find($saleReturnItem->id);

        $dbSaleReturnItem = $dbSaleReturnItem->toArray();
        $this->assertModelData($saleReturnItem->toArray(), $dbSaleReturnItem);
    }

    /**
     * @test update
     */
    public function test_update_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->create();
        $fakeSaleReturnItem = SaleReturnItem::factory()->make()->toArray();

        $updatedSaleReturnItem = $this->saleReturnItemRepo->update($fakeSaleReturnItem, $saleReturnItem->id);

        $this->assertModelData($fakeSaleReturnItem, $updatedSaleReturnItem->toArray());
        $dbSaleReturnItem = $this->saleReturnItemRepo->find($saleReturnItem->id);
        $this->assertModelData($fakeSaleReturnItem, $dbSaleReturnItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sale_return_item()
    {
        $saleReturnItem = SaleReturnItem::factory()->create();

        $resp = $this->saleReturnItemRepo->delete($saleReturnItem->id);

        $this->assertTrue($resp);
        $this->assertNull(SaleReturnItem::find($saleReturnItem->id), 'SaleReturnItem should not exist in DB');
    }
}
