<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerLedger;

class CustomerLedgerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_ledgers', $customerLedger
        );

        $this->assertApiResponse($customerLedger);
    }

    /**
     * @test
     */
    public function test_read_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_ledgers/'.$customerLedger->id
        );

        $this->assertApiResponse($customerLedger->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->create();
        $editedCustomerLedger = CustomerLedger::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_ledgers/'.$customerLedger->id,
            $editedCustomerLedger
        );

        $this->assertApiResponse($editedCustomerLedger);
    }

    /**
     * @test
     */
    public function test_delete_customer_ledger()
    {
        $customerLedger = CustomerLedger::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_ledgers/'.$customerLedger->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_ledgers/'.$customerLedger->id
        );

        $this->response->assertStatus(404);
    }
}
