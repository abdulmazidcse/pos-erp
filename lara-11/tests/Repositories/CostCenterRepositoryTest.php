<?php namespace Tests\Repositories;

use App\Models\CostCenter;
use App\Repositories\CostCenterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CostCenterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CostCenterRepository
     */
    protected $costCenterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->costCenterRepo = \App::make(CostCenterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cost_center()
    {
        $costCenter = CostCenter::factory()->make()->toArray();

        $createdCostCenter = $this->costCenterRepo->create($costCenter);

        $createdCostCenter = $createdCostCenter->toArray();
        $this->assertArrayHasKey('id', $createdCostCenter);
        $this->assertNotNull($createdCostCenter['id'], 'Created CostCenter must have id specified');
        $this->assertNotNull(CostCenter::find($createdCostCenter['id']), 'CostCenter with given id must be in DB');
        $this->assertModelData($costCenter, $createdCostCenter);
    }

    /**
     * @test read
     */
    public function test_read_cost_center()
    {
        $costCenter = CostCenter::factory()->create();

        $dbCostCenter = $this->costCenterRepo->find($costCenter->id);

        $dbCostCenter = $dbCostCenter->toArray();
        $this->assertModelData($costCenter->toArray(), $dbCostCenter);
    }

    /**
     * @test update
     */
    public function test_update_cost_center()
    {
        $costCenter = CostCenter::factory()->create();
        $fakeCostCenter = CostCenter::factory()->make()->toArray();

        $updatedCostCenter = $this->costCenterRepo->update($fakeCostCenter, $costCenter->id);

        $this->assertModelData($fakeCostCenter, $updatedCostCenter->toArray());
        $dbCostCenter = $this->costCenterRepo->find($costCenter->id);
        $this->assertModelData($fakeCostCenter, $dbCostCenter->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cost_center()
    {
        $costCenter = CostCenter::factory()->create();

        $resp = $this->costCenterRepo->delete($costCenter->id);

        $this->assertTrue($resp);
        $this->assertNull(CostCenter::find($costCenter->id), 'CostCenter should not exist in DB');
    }
}
