<?php namespace Tests\Repositories;

use App\Models\PurchaseReceive;
use App\Repositories\PurchaseReceiveRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PurchaseReceiveRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseReceiveRepository
     */
    protected $purchaseReceiveRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->purchaseReceiveRepo = \App::make(PurchaseReceiveRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->make()->toArray();

        $createdPurchaseReceive = $this->purchaseReceiveRepo->create($purchaseReceive);

        $createdPurchaseReceive = $createdPurchaseReceive->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseReceive);
        $this->assertNotNull($createdPurchaseReceive['id'], 'Created PurchaseReceive must have id specified');
        $this->assertNotNull(PurchaseReceive::find($createdPurchaseReceive['id']), 'PurchaseReceive with given id must be in DB');
        $this->assertModelData($purchaseReceive, $createdPurchaseReceive);
    }

    /**
     * @test read
     */
    public function test_read_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->create();

        $dbPurchaseReceive = $this->purchaseReceiveRepo->find($purchaseReceive->id);

        $dbPurchaseReceive = $dbPurchaseReceive->toArray();
        $this->assertModelData($purchaseReceive->toArray(), $dbPurchaseReceive);
    }

    /**
     * @test update
     */
    public function test_update_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->create();
        $fakePurchaseReceive = PurchaseReceive::factory()->make()->toArray();

        $updatedPurchaseReceive = $this->purchaseReceiveRepo->update($fakePurchaseReceive, $purchaseReceive->id);

        $this->assertModelData($fakePurchaseReceive, $updatedPurchaseReceive->toArray());
        $dbPurchaseReceive = $this->purchaseReceiveRepo->find($purchaseReceive->id);
        $this->assertModelData($fakePurchaseReceive, $dbPurchaseReceive->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_purchase_receive()
    {
        $purchaseReceive = PurchaseReceive::factory()->create();

        $resp = $this->purchaseReceiveRepo->delete($purchaseReceive->id);

        $this->assertTrue($resp);
        $this->assertNull(PurchaseReceive::find($purchaseReceive->id), 'PurchaseReceive should not exist in DB');
    }
}
