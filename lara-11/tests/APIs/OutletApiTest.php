<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Outlet;

class OutletApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_outlet()
    {
        $outlet = Outlet::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/outlets', $outlet
        );

        $this->assertApiResponse($outlet);
    }

    /**
     * @test
     */
    public function test_read_outlet()
    {
        $outlet = Outlet::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/outlets/'.$outlet->id
        );

        $this->assertApiResponse($outlet->toArray());
    }

    /**
     * @test
     */
    public function test_update_outlet()
    {
        $outlet = Outlet::factory()->create();
        $editedOutlet = Outlet::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/outlets/'.$outlet->id,
            $editedOutlet
        );

        $this->assertApiResponse($editedOutlet);
    }

    /**
     * @test
     */
    public function test_delete_outlet()
    {
        $outlet = Outlet::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/outlets/'.$outlet->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/outlets/'.$outlet->id
        );

        $this->response->assertStatus(404);
    }
}
