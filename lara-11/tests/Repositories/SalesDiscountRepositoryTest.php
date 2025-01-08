<?php namespace Tests\Repositories;

use App\Models\SalesDiscount;
use App\Repositories\SalesDiscountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesDiscountRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesDiscountRepository
     */
    protected $salesDiscountRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesDiscountRepo = \App::make(SalesDiscountRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->make()->toArray();

        $createdSalesDiscount = $this->salesDiscountRepo->create($salesDiscount);

        $createdSalesDiscount = $createdSalesDiscount->toArray();
        $this->assertArrayHasKey('id', $createdSalesDiscount);
        $this->assertNotNull($createdSalesDiscount['id'], 'Created SalesDiscount must have id specified');
        $this->assertNotNull(SalesDiscount::find($createdSalesDiscount['id']), 'SalesDiscount with given id must be in DB');
        $this->assertModelData($salesDiscount, $createdSalesDiscount);
    }

    /**
     * @test read
     */
    public function test_read_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->create();

        $dbSalesDiscount = $this->salesDiscountRepo->find($salesDiscount->id);

        $dbSalesDiscount = $dbSalesDiscount->toArray();
        $this->assertModelData($salesDiscount->toArray(), $dbSalesDiscount);
    }

    /**
     * @test update
     */
    public function test_update_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->create();
        $fakeSalesDiscount = SalesDiscount::factory()->make()->toArray();

        $updatedSalesDiscount = $this->salesDiscountRepo->update($fakeSalesDiscount, $salesDiscount->id);

        $this->assertModelData($fakeSalesDiscount, $updatedSalesDiscount->toArray());
        $dbSalesDiscount = $this->salesDiscountRepo->find($salesDiscount->id);
        $this->assertModelData($fakeSalesDiscount, $dbSalesDiscount->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales_discount()
    {
        $salesDiscount = SalesDiscount::factory()->create();

        $resp = $this->salesDiscountRepo->delete($salesDiscount->id);

        $this->assertTrue($resp);
        $this->assertNull(SalesDiscount::find($salesDiscount->id), 'SalesDiscount should not exist in DB');
    }
}
