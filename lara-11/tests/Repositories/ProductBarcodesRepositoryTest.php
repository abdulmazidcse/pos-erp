<?php namespace Tests\Repositories;

use App\Models\ProductBarcodes;
use App\Repositories\ProductBarcodesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProductBarcodesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductBarcodesRepository
     */
    protected $productBarcodesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->productBarcodesRepo = \App::make(ProductBarcodesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->make()->toArray();

        $createdProductBarcodes = $this->productBarcodesRepo->create($productBarcodes);

        $createdProductBarcodes = $createdProductBarcodes->toArray();
        $this->assertArrayHasKey('id', $createdProductBarcodes);
        $this->assertNotNull($createdProductBarcodes['id'], 'Created ProductBarcodes must have id specified');
        $this->assertNotNull(ProductBarcodes::find($createdProductBarcodes['id']), 'ProductBarcodes with given id must be in DB');
        $this->assertModelData($productBarcodes, $createdProductBarcodes);
    }

    /**
     * @test read
     */
    public function test_read_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->create();

        $dbProductBarcodes = $this->productBarcodesRepo->find($productBarcodes->id);

        $dbProductBarcodes = $dbProductBarcodes->toArray();
        $this->assertModelData($productBarcodes->toArray(), $dbProductBarcodes);
    }

    /**
     * @test update
     */
    public function test_update_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->create();
        $fakeProductBarcodes = ProductBarcodes::factory()->make()->toArray();

        $updatedProductBarcodes = $this->productBarcodesRepo->update($fakeProductBarcodes, $productBarcodes->id);

        $this->assertModelData($fakeProductBarcodes, $updatedProductBarcodes->toArray());
        $dbProductBarcodes = $this->productBarcodesRepo->find($productBarcodes->id);
        $this->assertModelData($fakeProductBarcodes, $dbProductBarcodes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_product_barcodes()
    {
        $productBarcodes = ProductBarcodes::factory()->create();

        $resp = $this->productBarcodesRepo->delete($productBarcodes->id);

        $this->assertTrue($resp);
        $this->assertNull(ProductBarcodes::find($productBarcodes->id), 'ProductBarcodes should not exist in DB');
    }
}
