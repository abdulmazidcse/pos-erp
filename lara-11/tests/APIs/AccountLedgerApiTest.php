<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AccountLedger;

class AccountLedgerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/account_ledgers', $accountLedger
        );

        $this->assertApiResponse($accountLedger);
    }

    /**
     * @test
     */
    public function test_read_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/account_ledgers/'.$accountLedger->id
        );

        $this->assertApiResponse($accountLedger->toArray());
    }

    /**
     * @test
     */
    public function test_update_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->create();
        $editedAccountLedger = AccountLedger::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/account_ledgers/'.$accountLedger->id,
            $editedAccountLedger
        );

        $this->assertApiResponse($editedAccountLedger);
    }

    /**
     * @test
     */
    public function test_delete_account_ledger()
    {
        $accountLedger = AccountLedger::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/account_ledgers/'.$accountLedger->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/account_ledgers/'.$accountLedger->id
        );

        $this->response->assertStatus(404);
    }
}
