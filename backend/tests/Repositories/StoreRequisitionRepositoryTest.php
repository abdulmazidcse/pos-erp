<?php namespace Tests\Repositories;

use App\Models\StoreRequisition;
use App\Repositories\StoreRequisitionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class StoreRequisitionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var StoreRequisitionRepository
     */
    protected $storeRequisitionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->storeRequisitionRepo = \App::make(StoreRequisitionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->make()->toArray();

        $createdStoreRequisition = $this->storeRequisitionRepo->create($storeRequisition);

        $createdStoreRequisition = $createdStoreRequisition->toArray();
        $this->assertArrayHasKey('id', $createdStoreRequisition);
        $this->assertNotNull($createdStoreRequisition['id'], 'Created StoreRequisition must have id specified');
        $this->assertNotNull(StoreRequisition::find($createdStoreRequisition['id']), 'StoreRequisition with given id must be in DB');
        $this->assertModelData($storeRequisition, $createdStoreRequisition);
    }

    /**
     * @test read
     */
    public function test_read_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->create();

        $dbStoreRequisition = $this->storeRequisitionRepo->find($storeRequisition->id);

        $dbStoreRequisition = $dbStoreRequisition->toArray();
        $this->assertModelData($storeRequisition->toArray(), $dbStoreRequisition);
    }

    /**
     * @test update
     */
    public function test_update_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->create();
        $fakeStoreRequisition = StoreRequisition::factory()->make()->toArray();

        $updatedStoreRequisition = $this->storeRequisitionRepo->update($fakeStoreRequisition, $storeRequisition->id);

        $this->assertModelData($fakeStoreRequisition, $updatedStoreRequisition->toArray());
        $dbStoreRequisition = $this->storeRequisitionRepo->find($storeRequisition->id);
        $this->assertModelData($fakeStoreRequisition, $dbStoreRequisition->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->create();

        $resp = $this->storeRequisitionRepo->delete($storeRequisition->id);

        $this->assertTrue($resp);
        $this->assertNull(StoreRequisition::find($storeRequisition->id), 'StoreRequisition should not exist in DB');
    }
}
