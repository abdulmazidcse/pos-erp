<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AllComment;

class AllCommentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_all_comment()
    {
        $allComment = AllComment::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/all_comments', $allComment
        );

        $this->assertApiResponse($allComment);
    }

    /**
     * @test
     */
    public function test_read_all_comment()
    {
        $allComment = AllComment::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/all_comments/'.$allComment->id
        );

        $this->assertApiResponse($allComment->toArray());
    }

    /**
     * @test
     */
    public function test_update_all_comment()
    {
        $allComment = AllComment::factory()->create();
        $editedAllComment = AllComment::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/all_comments/'.$allComment->id,
            $editedAllComment
        );

        $this->assertApiResponse($editedAllComment);
    }

    /**
     * @test
     */
    public function test_delete_all_comment()
    {
        $allComment = AllComment::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/all_comments/'.$allComment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/all_comments/'.$allComment->id
        );

        $this->response->assertStatus(404);
    }
}
