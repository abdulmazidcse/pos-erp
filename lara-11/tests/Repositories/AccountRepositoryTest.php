<?php namespace Tests\Repositories;

use App\Models\BankAccount;
use App\Repositories\BankAccountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AccountRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BankAccountRepository
     */
    protected $accountRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->accountRepo = \App::make(BankAccountRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_account()
    {
        $account = BankAccount::factory()->make()->toArray();

        $createdAccount = $this->accountRepo->create($account);

        $createdAccount = $createdAccount->toArray();
        $this->assertArrayHasKey('id', $createdAccount);
        $this->assertNotNull($createdAccount['id'], 'Created Account must have id specified');
        $this->assertNotNull(BankAccount::find($createdAccount['id']), 'Account with given id must be in DB');
        $this->assertModelData($account, $createdAccount);
    }

    /**
     * @test read
     */
    public function test_read_account()
    {
        $account = BankAccount::factory()->create();

        $dbAccount = $this->accountRepo->find($account->id);

        $dbAccount = $dbAccount->toArray();
        $this->assertModelData($account->toArray(), $dbAccount);
    }

    /**
     * @test update
     */
    public function test_update_account()
    {
        $account = BankAccount::factory()->create();
        $fakeAccount = BankAccount::factory()->make()->toArray();

        $updatedAccount = $this->accountRepo->update($fakeAccount, $account->id);

        $this->assertModelData($fakeAccount, $updatedAccount->toArray());
        $dbAccount = $this->accountRepo->find($account->id);
        $this->assertModelData($fakeAccount, $dbAccount->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_account()
    {
        $account = BankAccount::factory()->create();

        $resp = $this->accountRepo->delete($account->id);

        $this->assertTrue($resp);
        $this->assertNull(BankAccount::find($account->id), 'Account should not exist in DB');
    }
}
