<?php namespace Tests\Repositories;

use App\Models\AttributeValue;
use App\Repositories\AttributeValueRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AttributeValueRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttributeValueRepository
     */
    protected $attributeValueRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->attributeValueRepo = \App::make(AttributeValueRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->make()->toArray();

        $createdAttributeValue = $this->attributeValueRepo->create($attributeValue);

        $createdAttributeValue = $createdAttributeValue->toArray();
        $this->assertArrayHasKey('id', $createdAttributeValue);
        $this->assertNotNull($createdAttributeValue['id'], 'Created AttributeValue must have id specified');
        $this->assertNotNull(AttributeValue::find($createdAttributeValue['id']), 'AttributeValue with given id must be in DB');
        $this->assertModelData($attributeValue, $createdAttributeValue);
    }

    /**
     * @test read
     */
    public function test_read_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->create();

        $dbAttributeValue = $this->attributeValueRepo->find($attributeValue->id);

        $dbAttributeValue = $dbAttributeValue->toArray();
        $this->assertModelData($attributeValue->toArray(), $dbAttributeValue);
    }

    /**
     * @test update
     */
    public function test_update_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->create();
        $fakeAttributeValue = AttributeValue::factory()->make()->toArray();

        $updatedAttributeValue = $this->attributeValueRepo->update($fakeAttributeValue, $attributeValue->id);

        $this->assertModelData($fakeAttributeValue, $updatedAttributeValue->toArray());
        $dbAttributeValue = $this->attributeValueRepo->find($attributeValue->id);
        $this->assertModelData($fakeAttributeValue, $dbAttributeValue->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->create();

        $resp = $this->attributeValueRepo->delete($attributeValue->id);

        $this->assertTrue($resp);
        $this->assertNull(AttributeValue::find($attributeValue->id), 'AttributeValue should not exist in DB');
    }
}
