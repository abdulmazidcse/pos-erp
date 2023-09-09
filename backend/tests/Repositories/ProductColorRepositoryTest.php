<?php namespace Tests\Repositories;

use App\Models\ProductColor;
use App\Repositories\ProductColorRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProductColorRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductColorRepository
     */
    protected $productColorRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->productColorRepo = \App::make(ProductColorRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_product_color()
    {
        $productColor = ProductColor::factory()->make()->toArray();

        $createdProductColor = $this->productColorRepo->create($productColor);

        $createdProductColor = $createdProductColor->toArray();
        $this->assertArrayHasKey('id', $createdProductColor);
        $this->assertNotNull($createdProductColor['id'], 'Created ProductColor must have id specified');
        $this->assertNotNull(ProductColor::find($createdProductColor['id']), 'ProductColor with given id must be in DB');
        $this->assertModelData($productColor, $createdProductColor);
    }

    /**
     * @test read
     */
    public function test_read_product_color()
    {
        $productColor = ProductColor::factory()->create();

        $dbProductColor = $this->productColorRepo->find($productColor->id);

        $dbProductColor = $dbProductColor->toArray();
        $this->assertModelData($productColor->toArray(), $dbProductColor);
    }

    /**
     * @test update
     */
    public function test_update_product_color()
    {
        $productColor = ProductColor::factory()->create();
        $fakeProductColor = ProductColor::factory()->make()->toArray();

        $updatedProductColor = $this->productColorRepo->update($fakeProductColor, $productColor->id);

        $this->assertModelData($fakeProductColor, $updatedProductColor->toArray());
        $dbProductColor = $this->productColorRepo->find($productColor->id);
        $this->assertModelData($fakeProductColor, $dbProductColor->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_product_color()
    {
        $productColor = ProductColor::factory()->create();

        $resp = $this->productColorRepo->delete($productColor->id);

        $this->assertTrue($resp);
        $this->assertNull(ProductColor::find($productColor->id), 'ProductColor should not exist in DB');
    }
}
