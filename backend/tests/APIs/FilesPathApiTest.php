<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FilesPath;

class FilesPathApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_files_path()
    {
        $filesPath = FilesPath::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/files_paths', $filesPath
        );

        $this->assertApiResponse($filesPath);
    }

    /**
     * @test
     */
    public function test_read_files_path()
    {
        $filesPath = FilesPath::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/files_paths/'.$filesPath->id
        );

        $this->assertApiResponse($filesPath->toArray());
    }

    /**
     * @test
     */
    public function test_update_files_path()
    {
        $filesPath = FilesPath::factory()->create();
        $editedFilesPath = FilesPath::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/files_paths/'.$filesPath->id,
            $editedFilesPath
        );

        $this->assertApiResponse($editedFilesPath);
    }

    /**
     * @test
     */
    public function test_delete_files_path()
    {
        $filesPath = FilesPath::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/files_paths/'.$filesPath->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/files_paths/'.$filesPath->id
        );

        $this->response->assertStatus(404);
    }
}
