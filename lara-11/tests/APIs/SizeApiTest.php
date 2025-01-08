<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Size;

class SizeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_size()
    {
        $size = Size::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sizes', $size
        );

        $this->assertApiResponse($size);
    }

    /**
     * @test
     */
    public function test_read_size()
    {
        $size = Size::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/sizes/'.$size->id
        );

        $this->assertApiResponse($size->toArray());
    }

    /**
     * @test
     */
    public function test_update_size()
    {
        $size = Size::factory()->create();
        $editedSize = Size::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sizes/'.$size->id,
            $editedSize
        );

        $this->assertApiResponse($editedSize);
    }

    /**
     * @test
     */
    public function test_delete_size()
    {
        $size = Size::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sizes/'.$size->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sizes/'.$size->id
        );

        $this->response->assertStatus(404);
    }
}
