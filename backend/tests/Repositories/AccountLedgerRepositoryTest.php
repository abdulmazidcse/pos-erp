<?php namespace Tests\Repositories;

use App\Models\AccountLedger;
use App\Repositories\AccountLedgerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AccountLedgerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountLedgerRepository
     */
    protected $accountLedgerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->accountLedgerRepo = \App::make(AccountLedgerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->make()->toArray();

        $createdAccountLedger = $this->accountLedgerRepo->create($accountLedger);

        $createdAccountLedger = $createdAccountLedger->toArray();
        $this->assertArrayHasKey('id', $createdAccountLedger);
        $this->assertNotNull($createdAccountLedger['id'], 'Created AccountLedger must have id specified');
        $this->assertNotNull(AccountLedger::find($createdAccountLedger['id']), 'AccountLedger with given id must be in DB');
        $this->assertModelData($accountLedger, $createdAccountLedger);
    }

    /**
     * @test read
     */
    public function test_read_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->create();

        $dbAccountLedger = $this->accountLedgerRepo->find($accountLedger->id);

        $dbAccountLedger = $dbAccountLedger->toArray();
        $this->assertModelData($accountLedger->toArray(), $dbAccountLedger);
    }

    /**
     * @test update
     */
    public function test_update_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->create();
        $fakeAccountLedger = AccountLedger::factory()->make()->toArray();

        $updatedAccountLedger = $this->accountLedgerRepo->update($fakeAccountLedger, $accountLedger->id);

        $this->assertModelData($fakeAccountLedger, $updatedAccountLedger->toArray());
        $dbAccountLedger = $this->accountLedgerRepo->find($accountLedger->id);
        $this->assertModelData($fakeAccountLedger, $dbAccountLedger->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->create();

        $resp = $this->accountLedgerRepo->delete($accountLedger->id);

        $this->assertTrue($resp);
        $this->assertNull(AccountLedger::find($accountLedger->id), 'AccountLedger should not exist in DB');
    }
}
