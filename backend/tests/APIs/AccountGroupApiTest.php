<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AccountGroup;

class AccountGroupApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_account_group()
    {
        $accountGroup = AccountGroup::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/account_groups', $accountGroup
        );

        $this->assertApiResponse($accountGroup);
    }

    /**
     * @test
     */
    public function test_read_account_group()
    {
        $accountGroup = AccountGroup::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/account_groups/'.$accountGroup->id
        );

        $this->assertApiResponse($accountGroup->toArray());
    }

    /**
     * @test
     */
    public function test_update_account_group()
    {
        $accountGroup = AccountGroup::factory()->create();
        $editedAccountGroup = AccountGroup::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/account_groups/'.$accountGroup->id,
            $editedAccountGroup
        );

        $this->assertApiResponse($editedAccountGroup);
    }

    /**
     * @test
     */
    public function test_delete_account_group()
    {
        $accountGroup = AccountGroup::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/account_groups/'.$accountGroup->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/account_groups/'.$accountGroup->id
        );

        $this->response->assertStatus(404);
    }
}
