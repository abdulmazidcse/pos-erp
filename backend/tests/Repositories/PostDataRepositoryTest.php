<?php namespace Tests\Repositories;

use App\Models\PostData;
use App\Repositories\PostDataRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PostDataRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PostDataRepository
     */
    protected $postDataRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->postDataRepo = \App::make(PostDataRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_post_data()
    {
        $postData = PostData::factory()->make()->toArray();

        $createdPostData = $this->postDataRepo->create($postData);

        $createdPostData = $createdPostData->toArray();
        $this->assertArrayHasKey('id', $createdPostData);
        $this->assertNotNull($createdPostData['id'], 'Created PostData must have id specified');
        $this->assertNotNull(PostData::find($createdPostData['id']), 'PostData with given id must be in DB');
        $this->assertModelData($postData, $createdPostData);
    }

    /**
     * @test read
     */
    public function test_read_post_data()
    {
        $postData = PostData::factory()->create();

        $dbPostData = $this->postDataRepo->find($postData->id);

        $dbPostData = $dbPostData->toArray();
        $this->assertModelData($postData->toArray(), $dbPostData);
    }

    /**
     * @test update
     */
    public function test_update_post_data()
    {
        $postData = PostData::factory()->create();
        $fakePostData = PostData::factory()->make()->toArray();

        $updatedPostData = $this->postDataRepo->update($fakePostData, $postData->id);

        $this->assertModelData($fakePostData, $updatedPostData->toArray());
        $dbPostData = $this->postDataRepo->find($postData->id);
        $this->assertModelData($fakePostData, $dbPostData->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_post_data()
    {
        $postData = PostData::factory()->create();

        $resp = $this->postDataRepo->delete($postData->id);

        $this->assertTrue($resp);
        $this->assertNull(PostData::find($postData->id), 'PostData should not exist in DB');
    }
}
