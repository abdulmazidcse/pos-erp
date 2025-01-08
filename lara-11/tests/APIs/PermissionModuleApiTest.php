<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PermissionModule;

class PermissionModuleApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_permission_module()
    {
        $permissionModule = PermissionModule::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/permission_modules', $permissionModule
        );

        $this->assertApiResponse($permissionModule);
    }

    /**
     * @test
     */
    public function test_read_permission_module()
    {
        $permissionModule = PermissionModule::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/permission_modules/'.$permissionModule->id
        );

        $this->assertApiResponse($permissionModule->toArray());
    }

    /**
     * @test
     */
    public function test_update_permission_module()
    {
        $permissionModule = PermissionModule::factory()->create();
        $editedPermissionModule = PermissionModule::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/permission_modules/'.$permissionModule->id,
            $editedPermissionModule
        );

        $this->assertApiResponse($editedPermissionModule);
    }

    /**
     * @test
     */
    public function test_delete_permission_module()
    {
        $permissionModule = PermissionModule::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/permission_modules/'.$permissionModule->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/permission_modules/'.$permissionModule->id
        );

        $this->response->assertStatus(404);
    }
}
