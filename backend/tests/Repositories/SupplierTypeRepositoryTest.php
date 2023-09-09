<?php namespace Tests\Repositories;

use App\Models\SupplierType;
use App\Repositories\SupplierTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierTypeRepository
     */
    protected $supplierTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierTypeRepo = \App::make(SupplierTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_type()
    {
        $supplierType = SupplierType::factory()->make()->toArray();

        $createdSupplierType = $this->supplierTypeRepo->create($supplierType);

        $createdSupplierType = $createdSupplierType->toArray();
        $this->assertArrayHasKey('id', $createdSupplierType);
        $this->assertNotNull($createdSupplierType['id'], 'Created SupplierType must have id specified');
        $this->assertNotNull(SupplierType::find($createdSupplierType['id']), 'SupplierType with given id must be in DB');
        $this->assertModelData($supplierType, $createdSupplierType);
    }

    /**
     * @test read
     */
    public function test_read_supplier_type()
    {
        $supplierType = SupplierType::factory()->create();

        $dbSupplierType = $this->supplierTypeRepo->find($supplierType->id);

        $dbSupplierType = $dbSupplierType->toArray();
        $this->assertModelData($supplierType->toArray(), $dbSupplierType);
    }

    /**
     * @test update
     */
    public function test_update_supplier_type()
    {
        $supplierType = SupplierType::factory()->create();
        $fakeSupplierType = SupplierType::factory()->make()->toArray();

        $updatedSupplierType = $this->supplierTypeRepo->update($fakeSupplierType, $supplierType->id);

        $this->assertModelData($fakeSupplierType, $updatedSupplierType->toArray());
        $dbSupplierType = $this->supplierTypeRepo->find($supplierType->id);
        $this->assertModelData($fakeSupplierType, $dbSupplierType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_type()
    {
        $supplierType = SupplierType::factory()->create();

        $resp = $this->supplierTypeRepo->delete($supplierType->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierType::find($supplierType->id), 'SupplierType should not exist in DB');
    }
}
