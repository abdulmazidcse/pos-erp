<?php namespace Tests\Repositories;

use App\Models\PermissionModule;
use App\Repositories\PermissionModuleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PermissionModuleRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PermissionModuleRepository
     */
    protected $permissionModuleRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->permissionModuleRepo = \App::make(PermissionModuleRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_permission_module()
    {
        $permissionModule = PermissionModule::factory()->make()->toArray();

        $createdPermissionModule = $this->permissionModuleRepo->create($permissionModule);

        $createdPermissionModule = $createdPermissionModule->toArray();
        $this->assertArrayHasKey('id', $createdPermissionModule);
        $this->assertNotNull($createdPermissionModule['id'], 'Created PermissionModule must have id specified');
        $this->assertNotNull(PermissionModule::find($createdPermissionModule['id']), 'PermissionModule with given id must be in DB');
        $this->assertModelData($permissionModule, $createdPermissionModule);
    }

    /**
     * @test read
     */
    public function test_read_permission_module()
    {
        $permissionModule = PermissionModule::factory()->create();

        $dbPermissionModule = $this->permissionModuleRepo->find($permissionModule->id);

        $dbPermissionModule = $dbPermissionModule->toArray();
        $this->assertModelData($permissionModule->toArray(), $dbPermissionModule);
    }

    /**
     * @test update
     */
    public function test_update_permission_module()
    {
        $permissionModule = PermissionModule::factory()->create();
        $fakePermissionModule = PermissionModule::factory()->make()->toArray();

        $updatedPermissionModule = $this->permissionModuleRepo->update($fakePermissionModule, $permissionModule->id);

        $this->assertModelData($fakePermissionModule, $updatedPermissionModule->toArray());
        $dbPermissionModule = $this->permissionModuleRepo->find($permissionModule->id);
        $this->assertModelData($fakePermissionModule, $dbPermissionModule->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_permission_module()
    {
        $permissionModule = PermissionModule::factory()->create();

        $resp = $this->permissionModuleRepo->delete($permissionModule->id);

        $this->assertTrue($resp);
        $this->assertNull(PermissionModule::find($permissionModule->id), 'PermissionModule should not exist in DB');
    }
}
