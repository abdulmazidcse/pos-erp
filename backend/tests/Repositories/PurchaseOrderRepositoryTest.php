<?php namespace Tests\Repositories;

use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PurchaseOrderRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderRepository
     */
    protected $purchaseOrderRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->purchaseOrderRepo = \App::make(PurchaseOrderRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->make()->toArray();

        $createdPurchaseOrder = $this->purchaseOrderRepo->create($purchaseOrder);

        $createdPurchaseOrder = $createdPurchaseOrder->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrder);
        $this->assertNotNull($createdPurchaseOrder['id'], 'Created PurchaseOrder must have id specified');
        $this->assertNotNull(PurchaseOrder::find($createdPurchaseOrder['id']), 'PurchaseOrder with given id must be in DB');
        $this->assertModelData($purchaseOrder, $createdPurchaseOrder);
    }

    /**
     * @test read
     */
    public function test_read_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $dbPurchaseOrder = $this->purchaseOrderRepo->find($purchaseOrder->id);

        $dbPurchaseOrder = $dbPurchaseOrder->toArray();
        $this->assertModelData($purchaseOrder->toArray(), $dbPurchaseOrder);
    }

    /**
     * @test update
     */
    public function test_update_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $fakePurchaseOrder = PurchaseOrder::factory()->make()->toArray();

        $updatedPurchaseOrder = $this->purchaseOrderRepo->update($fakePurchaseOrder, $purchaseOrder->id);

        $this->assertModelData($fakePurchaseOrder, $updatedPurchaseOrder->toArray());
        $dbPurchaseOrder = $this->purchaseOrderRepo->find($purchaseOrder->id);
        $this->assertModelData($fakePurchaseOrder, $dbPurchaseOrder->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $resp = $this->purchaseOrderRepo->delete($purchaseOrder->id);

        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrder::find($purchaseOrder->id), 'PurchaseOrder should not exist in DB');
    }
}
