<?php namespace Tests\Repositories;

use App\Models\MobileWallet;
use App\Repositories\MobileWalletRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MobileWalletRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MobileWalletRepository
     */
    protected $mobileWalletRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mobileWalletRepo = \App::make(MobileWalletRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->make()->toArray();

        $createdMobileWallet = $this->mobileWalletRepo->create($mobileWallet);

        $createdMobileWallet = $createdMobileWallet->toArray();
        $this->assertArrayHasKey('id', $createdMobileWallet);
        $this->assertNotNull($createdMobileWallet['id'], 'Created MobileWallet must have id specified');
        $this->assertNotNull(MobileWallet::find($createdMobileWallet['id']), 'MobileWallet with given id must be in DB');
        $this->assertModelData($mobileWallet, $createdMobileWallet);
    }

    /**
     * @test read
     */
    public function test_read_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->create();

        $dbMobileWallet = $this->mobileWalletRepo->find($mobileWallet->id);

        $dbMobileWallet = $dbMobileWallet->toArray();
        $this->assertModelData($mobileWallet->toArray(), $dbMobileWallet);
    }

    /**
     * @test update
     */
    public function test_update_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->create();
        $fakeMobileWallet = MobileWallet::factory()->make()->toArray();

        $updatedMobileWallet = $this->mobileWalletRepo->update($fakeMobileWallet, $mobileWallet->id);

        $this->assertModelData($fakeMobileWallet, $updatedMobileWallet->toArray());
        $dbMobileWallet = $this->mobileWalletRepo->find($mobileWallet->id);
        $this->assertModelData($fakeMobileWallet, $dbMobileWallet->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->create();

        $resp = $this->mobileWalletRepo->delete($mobileWallet->id);

        $this->assertTrue($resp);
        $this->assertNull(MobileWallet::find($mobileWallet->id), 'MobileWallet should not exist in DB');
    }
}
