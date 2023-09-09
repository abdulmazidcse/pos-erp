<?php namespace Tests\Repositories;

use App\Models\Size;
use App\Repositories\SizeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SizeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SizeRepository
     */
    protected $sizeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sizeRepo = \App::make(SizeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_size()
    {
        $size = Size::factory()->make()->toArray();

        $createdSize = $this->sizeRepo->create($size);

        $createdSize = $createdSize->toArray();
        $this->assertArrayHasKey('id', $createdSize);
        $this->assertNotNull($createdSize['id'], 'Created Size must have id specified');
        $this->assertNotNull(Size::find($createdSize['id']), 'Size with given id must be in DB');
        $this->assertModelData($size, $createdSize);
    }

    /**
     * @test read
     */
    public function test_read_size()
    {
        $size = Size::factory()->create();

        $dbSize = $this->sizeRepo->find($size->id);

        $dbSize = $dbSize->toArray();
        $this->assertModelData($size->toArray(), $dbSize);
    }

    /**
     * @test update
     */
    public function test_update_size()
    {
        $size = Size::factory()->create();
        $fakeSize = Size::factory()->make()->toArray();

        $updatedSize = $this->sizeRepo->update($fakeSize, $size->id);

        $this->assertModelData($fakeSize, $updatedSize->toArray());
        $dbSize = $this->sizeRepo->find($size->id);
        $this->assertModelData($fakeSize, $dbSize->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_size()
    {
        $size = Size::factory()->create();

        $resp = $this->sizeRepo->delete($size->id);

        $this->assertTrue($resp);
        $this->assertNull(Size::find($size->id), 'Size should not exist in DB');
    }
}
