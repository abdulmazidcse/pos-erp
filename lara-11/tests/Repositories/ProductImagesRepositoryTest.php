<?php namespace Tests\Repositories;

use App\Models\ProductImages;
use App\Repositories\ProductImagesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProductImagesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductImagesRepository
     */
    protected $productImagesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->productImagesRepo = \App::make(ProductImagesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_product_images()
    {
        $productImages = ProductImages::factory()->make()->toArray();

        $createdProductImages = $this->productImagesRepo->create($productImages);

        $createdProductImages = $createdProductImages->toArray();
        $this->assertArrayHasKey('id', $createdProductImages);
        $this->assertNotNull($createdProductImages['id'], 'Created ProductImages must have id specified');
        $this->assertNotNull(ProductImages::find($createdProductImages['id']), 'ProductImages with given id must be in DB');
        $this->assertModelData($productImages, $createdProductImages);
    }

    /**
     * @test read
     */
    public function test_read_product_images()
    {
        $productImages = ProductImages::factory()->create();

        $dbProductImages = $this->productImagesRepo->find($productImages->id);

        $dbProductImages = $dbProductImages->toArray();
        $this->assertModelData($productImages->toArray(), $dbProductImages);
    }

    /**
     * @test update
     */
    public function test_update_product_images()
    {
        $productImages = ProductImages::factory()->create();
        $fakeProductImages = ProductImages::factory()->make()->toArray();

        $updatedProductImages = $this->productImagesRepo->update($fakeProductImages, $productImages->id);

        $this->assertModelData($fakeProductImages, $updatedProductImages->toArray());
        $dbProductImages = $this->productImagesRepo->find($productImages->id);
        $this->assertModelData($fakeProductImages, $dbProductImages->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_product_images()
    {
        $productImages = ProductImages::factory()->create();

        $resp = $this->productImagesRepo->delete($productImages->id);

        $this->assertTrue($resp);
        $this->assertNull(ProductImages::find($productImages->id), 'ProductImages should not exist in DB');
    }
}
