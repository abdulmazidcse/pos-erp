<?php namespace Tests\Repositories;

use App\Models\EntryType;
use App\Repositories\EntryTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EntryTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EntryTypeRepository
     */
    protected $entryTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->entryTypeRepo = \App::make(EntryTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_entry_type()
    {
        $entryType = EntryType::factory()->make()->toArray();

        $createdEntryType = $this->entryTypeRepo->create($entryType);

        $createdEntryType = $createdEntryType->toArray();
        $this->assertArrayHasKey('id', $createdEntryType);
        $this->assertNotNull($createdEntryType['id'], 'Created EntryType must have id specified');
        $this->assertNotNull(EntryType::find($createdEntryType['id']), 'EntryType with given id must be in DB');
        $this->assertModelData($entryType, $createdEntryType);
    }

    /**
     * @test read
     */
    public function test_read_entry_type()
    {
        $entryType = EntryType::factory()->create();

        $dbEntryType = $this->entryTypeRepo->find($entryType->id);

        $dbEntryType = $dbEntryType->toArray();
        $this->assertModelData($entryType->toArray(), $dbEntryType);
    }

    /**
     * @test update
     */
    public function test_update_entry_type()
    {
        $entryType = EntryType::factory()->create();
        $fakeEntryType = EntryType::factory()->make()->toArray();

        $updatedEntryType = $this->entryTypeRepo->update($fakeEntryType, $entryType->id);

        $this->assertModelData($fakeEntryType, $updatedEntryType->toArray());
        $dbEntryType = $this->entryTypeRepo->find($entryType->id);
        $this->assertModelData($fakeEntryType, $dbEntryType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_entry_type()
    {
        $entryType = EntryType::factory()->create();

        $resp = $this->entryTypeRepo->delete($entryType->id);

        $this->assertTrue($resp);
        $this->assertNull(EntryType::find($entryType->id), 'EntryType should not exist in DB');
    }
}
