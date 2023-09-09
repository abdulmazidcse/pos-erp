<?php namespace Tests\Repositories;

use App\Models\UsersPoints;
use App\Repositories\UsersPointsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class UsersPointsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var UsersPointsRepository
     */
    protected $usersPointsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->usersPointsRepo = \App::make(UsersPointsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_users_points()
    {
        $usersPoints = UsersPoints::factory()->make()->toArray();

        $createdUsersPoints = $this->usersPointsRepo->create($usersPoints);

        $createdUsersPoints = $createdUsersPoints->toArray();
        $this->assertArrayHasKey('id', $createdUsersPoints);
        $this->assertNotNull($createdUsersPoints['id'], 'Created UsersPoints must have id specified');
        $this->assertNotNull(UsersPoints::find($createdUsersPoints['id']), 'UsersPoints with given id must be in DB');
        $this->assertModelData($usersPoints, $createdUsersPoints);
    }

    /**
     * @test read
     */
    public function test_read_users_points()
    {
        $usersPoints = UsersPoints::factory()->create();

        $dbUsersPoints = $this->usersPointsRepo->find($usersPoints->id);

        $dbUsersPoints = $dbUsersPoints->toArray();
        $this->assertModelData($usersPoints->toArray(), $dbUsersPoints);
    }

    /**
     * @test update
     */
    public function test_update_users_points()
    {
        $usersPoints = UsersPoints::factory()->create();
        $fakeUsersPoints = UsersPoints::factory()->make()->toArray();

        $updatedUsersPoints = $this->usersPointsRepo->update($fakeUsersPoints, $usersPoints->id);

        $this->assertModelData($fakeUsersPoints, $updatedUsersPoints->toArray());
        $dbUsersPoints = $this->usersPointsRepo->find($usersPoints->id);
        $this->assertModelData($fakeUsersPoints, $dbUsersPoints->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_users_points()
    {
        $usersPoints = UsersPoints::factory()->create();

        $resp = $this->usersPointsRepo->delete($usersPoints->id);

        $this->assertTrue($resp);
        $this->assertNull(UsersPoints::find($usersPoints->id), 'UsersPoints should not exist in DB');
    }
}
