<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PostData;

class PostDataApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_post_data()
    {
        $postData = PostData::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/post_datas', $postData
        );

        $this->assertApiResponse($postData);
    }

    /**
     * @test
     */
    public function test_read_post_data()
    {
        $postData = PostData::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/post_datas/'.$postData->id
        );

        $this->assertApiResponse($postData->toArray());
    }

    /**
     * @test
     */
    public function test_update_post_data()
    {
        $postData = PostData::factory()->create();
        $editedPostData = PostData::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/post_datas/'.$postData->id,
            $editedPostData
        );

        $this->assertApiResponse($editedPostData);
    }

    /**
     * @test
     */
    public function test_delete_post_data()
    {
        $postData = PostData::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/post_datas/'.$postData->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/post_datas/'.$postData->id
        );

        $this->response->assertStatus(404);
    }
}
