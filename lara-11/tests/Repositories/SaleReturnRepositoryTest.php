<?php namespace Tests\Repositories;

use App\Models\SaleReturn;
use App\Repositories\SaleReturnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SaleReturnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SaleReturnRepository
     */
    protected $saleReturnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->saleReturnRepo = \App::make(SaleReturnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_sale_return()
    {
        $saleReturn = SaleReturn::factory()->make()->toArray();

        $createdSaleReturn = $this->saleReturnRepo->create($saleReturn);

        $createdSaleReturn = $createdSaleReturn->toArray();
        $this->assertArrayHasKey('id', $createdSaleReturn);
        $this->assertNotNull($createdSaleReturn['id'], 'Created SaleReturn must have id specified');
        $this->assertNotNull(SaleReturn::find($createdSaleReturn['id']), 'SaleReturn with given id must be in DB');
        $this->assertModelData($saleReturn, $createdSaleReturn);
    }

    /**
     * @test read
     */
    public function test_read_sale_return()
    {
        $saleReturn = SaleReturn::factory()->create();

        $dbSaleReturn = $this->saleReturnRepo->find($saleReturn->id);

        $dbSaleReturn = $dbSaleReturn->toArray();
        $this->assertModelData($saleReturn->toArray(), $dbSaleReturn);
    }

    /**
     * @test update
     */
    public function test_update_sale_return()
    {
        $saleReturn = SaleReturn::factory()->create();
        $fakeSaleReturn = SaleReturn::factory()->make()->toArray();

        $updatedSaleReturn = $this->saleReturnRepo->update($fakeSaleReturn, $saleReturn->id);

        $this->assertModelData($fakeSaleReturn, $updatedSaleReturn->toArray());
        $dbSaleReturn = $this->saleReturnRepo->find($saleReturn->id);
        $this->assertModelData($fakeSaleReturn, $dbSaleReturn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_sale_return()
    {
        $saleReturn = SaleReturn::factory()->create();

        $resp = $this->saleReturnRepo->delete($saleReturn->id);

        $this->assertTrue($resp);
        $this->assertNull(SaleReturn::find($saleReturn->id), 'SaleReturn should not exist in DB');
    }
}
