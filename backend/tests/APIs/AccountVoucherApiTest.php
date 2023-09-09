<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AccountVoucher;

class AccountVoucherApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/account_vouchers', $accountVoucher
        );

        $this->assertApiResponse($accountVoucher);
    }

    /**
     * @test
     */
    public function test_read_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/account_vouchers/'.$accountVoucher->id
        );

        $this->assertApiResponse($accountVoucher->toArray());
    }

    /**
     * @test
     */
    public function test_update_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->create();
        $editedAccountVoucher = AccountVoucher::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/account_vouchers/'.$accountVoucher->id,
            $editedAccountVoucher
        );

        $this->assertApiResponse($editedAccountVoucher);
    }

    /**
     * @test
     */
    public function test_delete_account_voucher()
    {
        $accountVoucher = AccountVoucher::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/account_vouchers/'.$accountVoucher->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/account_vouchers/'.$accountVoucher->id
        );

        $this->response->assertStatus(404);
    }
}
