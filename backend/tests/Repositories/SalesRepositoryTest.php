<?php namespace Tests\Repositories;

use App\Models\Sales;
use App\Repositories\SalesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SalesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalesRepository
     */
    protected $salesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salesRepo = \App::make(SalesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sales()
    {
        $sales = Sales::factory()->make()->toArray();

        $createdSales = $this->salesRepo->create($sales);

        $createdSales = $createdSales->toArray();
        $this->assertArrayHasKey('id', $createdSales);
        $this->assertNotNull($createdSales['id'], 'Created Sales must have id specified');
        $this->assertNotNull(Sales::find($createdSales['id']), 'Sales with given id must be in DB');
        $this->assertModelData($sales, $createdSales);
    }

    /**
     * @test read
     */
    public function test_read_sales()
    {
        $sales = Sales::factory()->create();

        $dbSales = $this->salesRepo->find($sales->id);

        $dbSales = $dbSales->toArray();
        $this->assertModelData($sales->toArray(), $dbSales);
    }

    /**
     * @test update
     */
    public function test_update_sales()
    {
        $sales = Sales::factory()->create();
        $fakeSales = Sales::factory()->make()->toArray();

        $updatedSales = $this->salesRepo->update($fakeSales, $sales->id);

        $this->assertModelData($fakeSales, $updatedSales->toArray());
        $dbSales = $this->salesRepo->find($sales->id);
        $this->assertModelData($fakeSales, $dbSales->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sales()
    {
        $sales = Sales::factory()->create();

        $resp = $this->salesRepo->delete($sales->id);

        $this->assertTrue($resp);
        $this->assertNull(Sales::find($sales->id), 'Sales should not exist in DB');
    }
}
