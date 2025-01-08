<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerGroup;

class CustomerGroupApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_groups', $customerGroup
        );

        $this->assertApiResponse($customerGroup);
    }

    /**
     * @test
     */
    public function test_read_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_groups/'.$customerGroup->id
        );

        $this->assertApiResponse($customerGroup->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();
        $editedCustomerGroup = CustomerGroup::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_groups/'.$customerGroup->id,
            $editedCustomerGroup
        );

        $this->assertApiResponse($editedCustomerGroup);
    }

    /**
     * @test
     */
    public function test_delete_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_groups/'.$customerGroup->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_groups/'.$customerGroup->id
        );

        $this->response->assertStatus(404);
    }
}
