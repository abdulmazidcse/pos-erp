<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\MobileWallet;

class MobileWalletApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/mobile_wallets', $mobileWallet
        );

        $this->assertApiResponse($mobileWallet);
    }

    /**
     * @test
     */
    public function test_read_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/mobile_wallets/'.$mobileWallet->id
        );

        $this->assertApiResponse($mobileWallet->toArray());
    }

    /**
     * @test
     */
    public function test_update_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->create();
        $editedMobileWallet = MobileWallet::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/mobile_wallets/'.$mobileWallet->id,
            $editedMobileWallet
        );

        $this->assertApiResponse($editedMobileWallet);
    }

    /**
     * @test
     */
    public function test_delete_mobile_wallet()
    {
        $mobileWallet = MobileWallet::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/mobile_wallets/'.$mobileWallet->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/mobile_wallets/'.$mobileWallet->id
        );

        $this->response->assertStatus(404);
    }
}
