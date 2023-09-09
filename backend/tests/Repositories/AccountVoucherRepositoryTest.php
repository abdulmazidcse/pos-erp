<?php namespace Tests\Repositories;

use App\Models\AccountVoucher;
use App\Repositories\AccountVoucherRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AccountVoucherRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AccountVoucherRepository
     */
    protected $accountVoucherRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->accountVoucherRepo = \App::make(AccountVoucherRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->make()->toArray();

        $createdAccountVoucher = $this->accountVoucherRepo->create($accountVoucher);

        $createdAccountVoucher = $createdAccountVoucher->toArray();
        $this->assertArrayHasKey('id', $createdAccountVoucher);
        $this->assertNotNull($createdAccountVoucher['id'], 'Created AccountVoucher must have id specified');
        $this->assertNotNull(AccountVoucher::find($createdAccountVoucher['id']), 'AccountVoucher with given id must be in DB');
        $this->assertModelData($accountVoucher, $createdAccountVoucher);
    }

    /**
     * @test read
     */
    public function test_read_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->create();

        $dbAccountVoucher = $this->accountVoucherRepo->find($accountVoucher->id);

        $dbAccountVoucher = $dbAccountVoucher->toArray();
        $this->assertModelData($accountVoucher->toArray(), $dbAccountVoucher);
    }

    /**
     * @test update
     */
    public function test_update_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->create();
        $fakeAccountVoucher = AccountVoucher::factory()->make()->toArray();

        $updatedAccountVoucher = $this->accountVoucherRepo->update($fakeAccountVoucher, $accountVoucher->id);

        $this->assertModelData($fakeAccountVoucher, $updatedAccountVoucher->toArray());
        $dbAccountVoucher = $this->accountVoucherRepo->find($accountVoucher->id);
        $this->assertModelData($fakeAccountVoucher, $dbAccountVoucher->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->create();

        $resp = $this->accountVoucherRepo->delete($accountVoucher->id);

        $this->assertTrue($resp);
        $this->assertNull(AccountVoucher::find($accountVoucher->id), 'AccountVoucher should not exist in DB');
    }
}
