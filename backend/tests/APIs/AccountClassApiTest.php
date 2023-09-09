<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AccountClass;

class AccountClassApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_account_class()
    {
        $accountClass = AccountClass::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/account_classes', $accountClass
        );

        $this->assertApiResponse($accountClass);
    }

    /**
     * @test
     */
    public function test_read_account_class()
    {
        $accountClass = AccountClass::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/account_classes/'.$accountClass->id
        );

        $this->assertApiResponse($accountClass->toArray());
    }

    /**
     * @test
     */
    public function test_update_account_class()
    {
        $accountClass = AccountClass::factory()->create();
        $editedAccountClass = AccountClass::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/account_classes/'.$accountClass->id,
            $editedAccountClass
        );

        $this->assertApiResponse($editedAccountClass);
    }

    /**
     * @test
     */
    public function test_delete_account_class()
    {
        $accountClass = AccountClass::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/account_classes/'.$accountClass->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/account_classes/'.$accountClass->id
        );

        $this->response->assertStatus(404);
    }
}
