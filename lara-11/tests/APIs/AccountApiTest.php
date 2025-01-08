<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BankAccount;

class AccountApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_account()
    {
        $account = BankAccount::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/accounts', $account
        );

        $this->assertApiResponse($account);
    }

    /**
     * @test
     */
    public function test_read_account()
    {
        $account = BankAccount::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/accounts/'.$account->id
        );

        $this->assertApiResponse($account->toArray());
    }

    /**
     * @test
     */
    public function test_update_account()
    {
        $account = BankAccount::factory()->create();
        $editedAccount = BankAccount::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/accounts/'.$account->id,
            $editedAccount
        );

        $this->assertApiResponse($editedAccount);
    }

    /**
     * @test
     */
    public function test_delete_account()
    {
        $account = BankAccount::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/accounts/'.$account->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/accounts/'.$account->id
        );

        $this->response->assertStatus(404);
    }
}
