<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\StoreRequisition;

class StoreRequisitionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/store_requisitions', $storeRequisition
        );

        $this->assertApiResponse($storeRequisition);
    }

    /**
     * @test
     */
    public function test_read_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/store_requisitions/'.$storeRequisition->id
        );

        $this->assertApiResponse($storeRequisition->toArray());
    }

    /**
     * @test
     */
    public function test_update_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->create();
        $editedStoreRequisition = StoreRequisition::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/store_requisitions/'.$storeRequisition->id,
            $editedStoreRequisition
        );

        $this->assertApiResponse($editedStoreRequisition);
    }

    /**
     * @test
     */
    public function test_delete_store_requisition()
    {
        $storeRequisition = StoreRequisition::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/store_requisitions/'.$storeRequisition->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/store_requisitions/'.$storeRequisition->id
        );

        $this->response->assertStatus(404);
    }
}
