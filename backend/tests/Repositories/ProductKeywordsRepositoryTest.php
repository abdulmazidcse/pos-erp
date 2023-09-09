<?php namespace Tests\Repositories;

use App\Models\ProductKeywords;
use App\Repositories\ProductKeywordsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProductKeywordsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductKeywordsRepository
     */
    protected $productKeywordsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->productKeywordsRepo = \App::make(ProductKeywordsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->make()->toArray();

        $createdProductKeywords = $this->productKeywordsRepo->create($productKeywords);

        $createdProductKeywords = $createdProductKeywords->toArray();
        $this->assertArrayHasKey('id', $createdProductKeywords);
        $this->assertNotNull($createdProductKeywords['id'], 'Created ProductKeywords must have id specified');
        $this->assertNotNull(ProductKeywords::find($createdProductKeywords['id']), 'ProductKeywords with given id must be in DB');
        $this->assertModelData($productKeywords, $createdProductKeywords);
    }

    /**
     * @test read
     */
    public function test_read_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->create();

        $dbProductKeywords = $this->productKeywordsRepo->find($productKeywords->id);

        $dbProductKeywords = $dbProductKeywords->toArray();
        $this->assertModelData($productKeywords->toArray(), $dbProductKeywords);
    }

    /**
     * @test update
     */
    public function test_update_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->create();
        $fakeProductKeywords = ProductKeywords::factory()->make()->toArray();

        $updatedProductKeywords = $this->productKeywordsRepo->update($fakeProductKeywords, $productKeywords->id);

        $this->assertModelData($fakeProductKeywords, $updatedProductKeywords->toArray());
        $dbProductKeywords = $this->productKeywordsRepo->find($productKeywords->id);
        $this->assertModelData($fakeProductKeywords, $dbProductKeywords->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_product_keywords()
    {
        $productKeywords = ProductKeywords::factory()->create();

        $resp = $this->productKeywordsRepo->delete($productKeywords->id);

        $this->assertTrue($resp);
        $this->assertNull(ProductKeywords::find($productKeywords->id), 'ProductKeywords should not exist in DB');
    }
}
