<?php namespace Tests\Repositories;

use App\Models\FiscalYear;
use App\Repositories\FiscalYearRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FiscalYearRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FiscalYearRepository
     */
    protected $fiscalYearRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->fiscalYearRepo = \App::make(FiscalYearRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->make()->toArray();

        $createdFiscalYear = $this->fiscalYearRepo->create($fiscalYear);

        $createdFiscalYear = $createdFiscalYear->toArray();
        $this->assertArrayHasKey('id', $createdFiscalYear);
        $this->assertNotNull($createdFiscalYear['id'], 'Created FiscalYear must have id specified');
        $this->assertNotNull(FiscalYear::find($createdFiscalYear['id']), 'FiscalYear with given id must be in DB');
        $this->assertModelData($fiscalYear, $createdFiscalYear);
    }

    /**
     * @test read
     */
    public function test_read_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->create();

        $dbFiscalYear = $this->fiscalYearRepo->find($fiscalYear->id);

        $dbFiscalYear = $dbFiscalYear->toArray();
        $this->assertModelData($fiscalYear->toArray(), $dbFiscalYear);
    }

    /**
     * @test update
     */
    public function test_update_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->create();
        $fakeFiscalYear = FiscalYear::factory()->make()->toArray();

        $updatedFiscalYear = $this->fiscalYearRepo->update($fakeFiscalYear, $fiscalYear->id);

        $this->assertModelData($fakeFiscalYear, $updatedFiscalYear->toArray());
        $dbFiscalYear = $this->fiscalYearRepo->find($fiscalYear->id);
        $this->assertModelData($fakeFiscalYear, $dbFiscalYear->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->create();

        $resp = $this->fiscalYearRepo->delete($fiscalYear->id);

        $this->assertTrue($resp);
        $this->assertNull(FiscalYear::find($fiscalYear->id), 'FiscalYear should not exist in DB');
    }
}
