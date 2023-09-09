<?php namespace Tests\Repositories;

use App\Models\Taxes;
use App\Repositories\TaxesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TaxesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TaxesRepository
     */
    protected $taxesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->taxesRepo = \App::make(TaxesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_taxes()
    {
        $taxes = Taxes::factory()->make()->toArray();

        $createdTaxes = $this->taxesRepo->create($taxes);

        $createdTaxes = $createdTaxes->toArray();
        $this->assertArrayHasKey('id', $createdTaxes);
        $this->assertNotNull($createdTaxes['id'], 'Created Taxes must have id specified');
        $this->assertNotNull(Taxes::find($createdTaxes['id']), 'Taxes with given id must be in DB');
        $this->assertModelData($taxes, $createdTaxes);
    }

    /**
     * @test read
     */
    public function test_read_taxes()
    {
        $taxes = Taxes::factory()->create();

        $dbTaxes = $this->taxesRepo->find($taxes->id);

        $dbTaxes = $dbTaxes->toArray();
        $this->assertModelData($taxes->toArray(), $dbTaxes);
    }

    /**
     * @test update
     */
    public function test_update_taxes()
    {
        $taxes = Taxes::factory()->create();
        $fakeTaxes = Taxes::factory()->make()->toArray();

        $updatedTaxes = $this->taxesRepo->update($fakeTaxes, $taxes->id);

        $this->assertModelData($fakeTaxes, $updatedTaxes->toArray());
        $dbTaxes = $this->taxesRepo->find($taxes->id);
        $this->assertModelData($fakeTaxes, $dbTaxes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_taxes()
    {
        $taxes = Taxes::factory()->create();

        $resp = $this->taxesRepo->delete($taxes->id);

        $this->assertTrue($resp);
        $this->assertNull(Taxes::find($taxes->id), 'Taxes should not exist in DB');
    }
}
