<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AttributeValue;

class AttributeValueApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/attribute_values', $attributeValue
        );

        $this->assertApiResponse($attributeValue);
    }

    /**
     * @test
     */
    public function test_read_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/attribute_values/'.$attributeValue->id
        );

        $this->assertApiResponse($attributeValue->toArray());
    }

    /**
     * @test
     */
    public function test_update_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->create();
        $editedAttributeValue = AttributeValue::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/attribute_values/'.$attributeValue->id,
            $editedAttributeValue
        );

        $this->assertApiResponse($editedAttributeValue);
    }

    /**
     * @test
     */
    public function test_delete_attribute_value()
    {
        $attributeValue = AttributeValue::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/attribute_values/'.$attributeValue->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/attribute_values/'.$attributeValue->id
        );

        $this->response->assertStatus(404);
    }
}
