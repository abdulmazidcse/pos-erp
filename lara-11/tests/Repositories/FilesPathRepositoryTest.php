<?php namespace Tests\Repositories;

use App\Models\FilesPath;
use App\Repositories\FilesPathRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FilesPathRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FilesPathRepository
     */
    protected $filesPathRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->filesPathRepo = \App::make(FilesPathRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_files_path()
    {
        $filesPath = FilesPath::factory()->make()->toArray();

        $createdFilesPath = $this->filesPathRepo->create($filesPath);

        $createdFilesPath = $createdFilesPath->toArray();
        $this->assertArrayHasKey('id', $createdFilesPath);
        $this->assertNotNull($createdFilesPath['id'], 'Created FilesPath must have id specified');
        $this->assertNotNull(FilesPath::find($createdFilesPath['id']), 'FilesPath with given id must be in DB');
        $this->assertModelData($filesPath, $createdFilesPath);
    }

    /**
     * @test read
     */
    public function test_read_files_path()
    {
        $filesPath = FilesPath::factory()->create();

        $dbFilesPath = $this->filesPathRepo->find($filesPath->id);

        $dbFilesPath = $dbFilesPath->toArray();
        $this->assertModelData($filesPath->toArray(), $dbFilesPath);
    }

    /**
     * @test update
     */
    public function test_update_files_path()
    {
        $filesPath = FilesPath::factory()->create();
        $fakeFilesPath = FilesPath::factory()->make()->toArray();

        $updatedFilesPath = $this->filesPathRepo->update($fakeFilesPath, $filesPath->id);

        $this->assertModelData($fakeFilesPath, $updatedFilesPath->toArray());
        $dbFilesPath = $this->filesPathRepo->find($filesPath->id);
        $this->assertModelData($fakeFilesPath, $dbFilesPath->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_files_path()
    {
        $filesPath = FilesPath::factory()->create();

        $resp = $this->filesPathRepo->delete($filesPath->id);

        $this->assertTrue($resp);
        $this->assertNull(FilesPath::find($filesPath->id), 'FilesPath should not exist in DB');
    }
}
