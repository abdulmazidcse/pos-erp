<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\UsersPoints;

class UsersPointsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_users_points()
    {
        $usersPoints = UsersPoints::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/users_points', $usersPoints
        );

        $this->assertApiResponse($usersPoints);
    }

    /**
     * @test
     */
    public function test_read_users_points()
    {
        $usersPoints = UsersPoints::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/users_points/'.$usersPoints->id
        );

        $this->assertApiResponse($usersPoints->toArray());
    }

    /**
     * @test
     */
    public function test_update_users_points()
    {
        $usersPoints = UsersPoints::factory()->create();
        $editedUsersPoints = UsersPoints::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/users_points/'.$usersPoints->id,
            $editedUsersPoints
        );

        $this->assertApiResponse($editedUsersPoints);
    }

    /**
     * @test
     */
    public function test_delete_users_points()
    {
        $usersPoints = UsersPoints::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/users_points/'.$usersPoints->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/users_points/'.$usersPoints->id
        );

        $this->response->assertStatus(404);
    }
}
