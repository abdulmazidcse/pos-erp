<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AllLike;

class AllLikeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_all_like()
    {
        $allLike = AllLike::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/all_likes', $allLike
        );

        $this->assertApiResponse($allLike);
    }

    /**
     * @test
     */
    public function test_read_all_like()
    {
        $allLike = AllLike::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/all_likes/'.$allLike->id
        );

        $this->assertApiResponse($allLike->toArray());
    }

    /**
     * @test
     */
    public function test_update_all_like()
    {
        $allLike = AllLike::factory()->create();
        $editedAllLike = AllLike::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/all_likes/'.$allLike->id,
            $editedAllLike
        );

        $this->assertApiResponse($editedAllLike);
    }

    /**
     * @test
     */
    public function test_delete_all_like()
    {
        $allLike = AllLike::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/all_likes/'.$allLike->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/all_likes/'.$allLike->id
        );

        $this->response->assertStatus(404);
    }
}
