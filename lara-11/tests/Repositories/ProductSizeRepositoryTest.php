<?php namespace Tests\Repositories;

use App\Models\ProductSize;
use App\Repositories\ProductSizeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProductSizeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductSizeRepository
     */
    protected $productSizeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->productSizeRepo = \App::make(ProductSizeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_product_size()
    {
        $productSize = ProductSize::factory()->make()->toArray();

        $createdProductSize = $this->productSizeRepo->create($productSize);

        $createdProductSize = $createdProductSize->toArray();
        $this->assertArrayHasKey('id', $createdProductSize);
        $this->assertNotNull($createdProductSize['id'], 'Created ProductSize must have id specified');
        $this->assertNotNull(ProductSize::find($createdProductSize['id']), 'ProductSize with given id must be in DB');
        $this->assertModelData($productSize, $createdProductSize);
    }

    /**
     * @test read
     */
    public function test_read_product_size()
    {
        $productSize = ProductSize::factory()->create();

        $dbProductSize = $this->productSizeRepo->find($productSize->id);

        $dbProductSize = $dbProductSize->toArray();
        $this->assertModelData($productSize->toArray(), $dbProductSize);
    }

    /**
     * @test update
     */
    public function test_update_product_size()
    {
        $productSize = ProductSize::factory()->create();
        $fakeProductSize = ProductSize::factory()->make()->toArray();

        $updatedProductSize = $this->productSizeRepo->update($fakeProductSize, $productSize->id);

        $this->assertModelData($fakeProductSize, $updatedProductSize->toArray());
        $dbProductSize = $this->productSizeRepo->find($productSize->id);
        $this->assertModelData($fakeProductSize, $dbProductSize->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_product_size()
    {
        $productSize = ProductSize::factory()->create();

        $resp = $this->productSizeRepo->delete($productSize->id);

        $this->assertTrue($resp);
        $this->assertNull(ProductSize::find($productSize->id), 'ProductSize should not exist in DB');
    }
}
