<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CostCenter;

class CostCenterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cost_center()
    {
        $costCenter = CostCenter::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cost_centers', $costCenter
        );

        $this->assertApiResponse($costCenter);
    }

    /**
     * @test
     */
    public function test_read_cost_center()
    {
        $costCenter = CostCenter::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/cost_centers/'.$costCenter->id
        );

        $this->assertApiResponse($costCenter->toArray());
    }

    /**
     * @test
     */
    public function test_update_cost_center()
    {
        $costCenter = CostCenter::factory()->create();
        $editedCostCenter = CostCenter::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cost_centers/'.$costCenter->id,
            $editedCostCenter
        );

        $this->assertApiResponse($editedCostCenter);
    }

    /**
     * @test
     */
    public function test_delete_cost_center()
    {
        $costCenter = CostCenter::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cost_centers/'.$costCenter->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cost_centers/'.$costCenter->id
        );

        $this->response->assertStatus(404);
    }
}
