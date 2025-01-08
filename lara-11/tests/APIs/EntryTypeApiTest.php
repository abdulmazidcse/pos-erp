<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EntryType;

class EntryTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_entry_type()
    {
        $entryType = EntryType::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/entry_types', $entryType
        );

        $this->assertApiResponse($entryType);
    }

    /**
     * @test
     */
    public function test_read_entry_type()
    {
        $entryType = EntryType::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/entry_types/'.$entryType->id
        );

        $this->assertApiResponse($entryType->toArray());
    }

    /**
     * @test
     */
    public function test_update_entry_type()
    {
        $entryType = EntryType::factory()->create();
        $editedEntryType = EntryType::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/entry_types/'.$entryType->id,
            $editedEntryType
        );

        $this->assertApiResponse($editedEntryType);
    }

    /**
     * @test
     */
    public function test_delete_entry_type()
    {
        $entryType = EntryType::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/entry_types/'.$entryType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/entry_types/'.$entryType->id
        );

        $this->response->assertStatus(404);
    }
}
