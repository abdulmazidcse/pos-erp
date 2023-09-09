<?php namespace Tests\Repositories;

use App\Models\ProductSupplier;
use App\Repositories\ProductSupplierRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProductSupplierRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductSupplierRepository
     */
    protected $productSupplierRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->productSupplierRepo = \App::make(ProductSupplierRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->make()->toArray();

        $createdProductSupplier = $this->productSupplierRepo->create($productSupplier);

        $createdProductSupplier = $createdProductSupplier->toArray();
        $this->assertArrayHasKey('id', $createdProductSupplier);
        $this->assertNotNull($createdProductSupplier['id'], 'Created ProductSupplier must have id specified');
        $this->assertNotNull(ProductSupplier::find($createdProductSupplier['id']), 'ProductSupplier with given id must be in DB');
        $this->assertModelData($productSupplier, $createdProductSupplier);
    }

    /**
     * @test read
     */
    public function test_read_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->create();

        $dbProductSupplier = $this->productSupplierRepo->find($productSupplier->id);

        $dbProductSupplier = $dbProductSupplier->toArray();
        $this->assertModelData($productSupplier->toArray(), $dbProductSupplier);
    }

    /**
     * @test update
     */
    public function test_update_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->create();
        $fakeProductSupplier = ProductSupplier::factory()->make()->toArray();

        $updatedProductSupplier = $this->productSupplierRepo->update($fakeProductSupplier, $productSupplier->id);

        $this->assertModelData($fakeProductSupplier, $updatedProductSupplier->toArray());
        $dbProductSupplier = $this->productSupplierRepo->find($productSupplier->id);
        $this->assertModelData($fakeProductSupplier, $dbProductSupplier->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_product_supplier()
    {
        $productSupplier = ProductSupplier::factory()->create();

        $resp = $this->productSupplierRepo->delete($productSupplier->id);

        $this->assertTrue($resp);
        $this->assertNull(ProductSupplier::find($productSupplier->id), 'ProductSupplier should not exist in DB');
    }
}
