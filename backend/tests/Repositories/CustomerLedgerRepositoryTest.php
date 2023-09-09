<?php namespace Tests\Repositories;

use App\Models\CustomerLedger;
use App\Repositories\CustomerLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerLedgerRepository
     */
    protected $customerLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerLedgerRepo = \App::make(CustomerLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->make()->toArray();

        $createdCustomerLedger = $this->customerLedgerRepo->create($customerLedger);

        $createdCustomerLedger = $createdCustomerLedger->toArray();
        $this->assertArrayHasKey('id', $createdCustomerLedger);
        $this->assertNotNull($createdCustomerLedger['id'], 'Created CustomerLedger must have id specified');
        $this->assertNotNull(CustomerLedger::find($createdCustomerLedger['id']), 'CustomerLedger with given id must be in DB');
        $this->assertModelData($customerLedger, $createdCustomerLedger);
    }

    /**
     * @test read
     */
    public function test_read_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->create();

        $dbCustomerLedger = $this->customerLedgerRepo->find($customerLedger->id);

        $dbCustomerLedger = $dbCustomerLedger->toArray();
        $this->assertModelData($customerLedger->toArray(), $dbCustomerLedger);
    }

    /**
     * @test update
     */
    public function test_update_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->create();
        $fakeCustomerLedger = CustomerLedger::factory()->make()->toArray();

        $updatedCustomerLedger = $this->customerLedgerRepo->update($fakeCustomerLedger, $customerLedger->id);

        $this->assertModelData($fakeCustomerLedger, $updatedCustomerLedger->toArray());
        $dbCustomerLedger = $this->customerLedgerRepo->find($customerLedger->id);
        $this->assertModelData($fakeCustomerLedger, $dbCustomerLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->create();

        $resp = $this->customerLedgerRepo->delete($customerLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerLedger::find($customerLedger->id), 'CustomerLedger should not exist in DB');
    }
}
