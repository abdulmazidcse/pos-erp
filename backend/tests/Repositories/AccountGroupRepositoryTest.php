<?php namespace Tests\Repositories;

use App\Models\AccountGroup;
use App\Repositories\AccountGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AccountGroupRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountGroupRepository
     */
    protected $accountGroupRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->accountGroupRepo = \App::make(AccountGroupRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_account_group()
    {
        $accountGroup = AccountGroup::factory()->make()->toArray();

        $createdAccountGroup = $this->accountGroupRepo->create($accountGroup);

        $createdAccountGroup = $createdAccountGroup->toArray();
        $this->assertArrayHasKey('id', $createdAccountGroup);
        $this->assertNotNull($createdAccountGroup['id'], 'Created AccountGroup must have id specified');
        $this->assertNotNull(AccountGroup::find($createdAccountGroup['id']), 'AccountGroup with given id must be in DB');
        $this->assertModelData($accountGroup, $createdAccountGroup);
    }

    /**
     * @test read
     */
    public function test_read_account_group()
    {
        $accountGroup = AccountGroup::factory()->create();

        $dbAccountGroup = $this->accountGroupRepo->find($accountGroup->id);

        $dbAccountGroup = $dbAccountGroup->toArray();
        $this->assertModelData($accountGroup->toArray(), $dbAccountGroup);
    }

    /**
     * @test update
     */
    public function test_update_account_group()
    {
        $accountGroup = AccountGroup::factory()->create();
        $fakeAccountGroup = AccountGroup::factory()->make()->toArray();

        $updatedAccountGroup = $this->accountGroupRepo->update($fakeAccountGroup, $accountGroup->id);

        $this->assertModelData($fakeAccountGroup, $updatedAccountGroup->toArray());
        $dbAccountGroup = $this->accountGroupRepo->find($accountGroup->id);
        $this->assertModelData($fakeAccountGroup, $dbAccountGroup->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_account_group()
    {
        $accountGroup = AccountGroup::factory()->create();

        $resp = $this->accountGroupRepo->delete($accountGroup->id);

        $this->assertTrue($resp);
        $this->assertNull(AccountGroup::find($accountGroup->id), 'AccountGroup should not exist in DB');
    }
}
