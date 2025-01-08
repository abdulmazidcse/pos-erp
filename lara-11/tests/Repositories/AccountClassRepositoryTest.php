<?php namespace Tests\Repositories;

use App\Models\AccountClass;
use App\Repositories\AccountClassRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AccountClassRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountClassRepository
     */
    protected $accountClassRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->accountClassRepo = \App::make(AccountClassRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_account_class()
    {
        $accountClass = AccountClass::factory()->make()->toArray();

        $createdAccountClass = $this->accountClassRepo->create($accountClass);

        $createdAccountClass = $createdAccountClass->toArray();
        $this->assertArrayHasKey('id', $createdAccountClass);
        $this->assertNotNull($createdAccountClass['id'], 'Created AccountClass must have id specified');
        $this->assertNotNull(AccountClass::find($createdAccountClass['id']), 'AccountClass with given id must be in DB');
        $this->assertModelData($accountClass, $createdAccountClass);
    }

    /**
     * @test read
     */
    public function test_read_account_class()
    {
        $accountClass = AccountClass::factory()->create();

        $dbAccountClass = $this->accountClassRepo->find($accountClass->id);

        $dbAccountClass = $dbAccountClass->toArray();
        $this->assertModelData($accountClass->toArray(), $dbAccountClass);
    }

    /**
     * @test update
     */
    public function test_update_account_class()
    {
        $accountClass = AccountClass::factory()->create();
        $fakeAccountClass = AccountClass::factory()->make()->toArray();

        $updatedAccountClass = $this->accountClassRepo->update($fakeAccountClass, $accountClass->id);

        $this->assertModelData($fakeAccountClass, $updatedAccountClass->toArray());
        $dbAccountClass = $this->accountClassRepo->find($accountClass->id);
        $this->assertModelData($fakeAccountClass, $dbAccountClass->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_account_class()
    {
        $accountClass = AccountClass::factory()->create();

        $resp = $this->accountClassRepo->delete($accountClass->id);

        $this->assertTrue($resp);
        $this->assertNull(AccountClass::find($accountClass->id), 'AccountClass should not exist in DB');
    }
}
