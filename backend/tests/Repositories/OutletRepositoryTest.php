<?php namespace Tests\Repositories;

use App\Models\Outlet;
use App\Repositories\OutletRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class OutletRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var OutletRepository
     */
    protected $outletRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->outletRepo = \App::make(OutletRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_outlet()
    {
        $outlet = Outlet::factory()->make()->toArray();

        $createdOutlet = $this->outletRepo->create($outlet);

        $createdOutlet = $createdOutlet->toArray();
        $this->assertArrayHasKey('id', $createdOutlet);
        $this->assertNotNull($createdOutlet['id'], 'Created Outlet must have id specified');
        $this->assertNotNull(Outlet::find($createdOutlet['id']), 'Outlet with given id must be in DB');
        $this->assertModelData($outlet, $createdOutlet);
    }

    /**
     * @test read
     */
    public function test_read_outlet()
    {
        $outlet = Outlet::factory()->create();

        $dbOutlet = $this->outletRepo->find($outlet->id);

        $dbOutlet = $dbOutlet->toArray();
        $this->assertModelData($outlet->toArray(), $dbOutlet);
    }

    /**
     * @test update
     */
    public function test_update_outlet()
    {
        $outlet = Outlet::factory()->create();
        $fakeOutlet = Outlet::factory()->make()->toArray();

        $updatedOutlet = $this->outletRepo->update($fakeOutlet, $outlet->id);

        $this->assertModelData($fakeOutlet, $updatedOutlet->toArray());
        $dbOutlet = $this->outletRepo->find($outlet->id);
        $this->assertModelData($fakeOutlet, $dbOutlet->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_outlet()
    {
        $outlet = Outlet::factory()->create();

        $resp = $this->outletRepo->delete($outlet->id);

        $this->assertTrue($resp);
        $this->assertNull(Outlet::find($outlet->id), 'Outlet should not exist in DB');
    }
}
