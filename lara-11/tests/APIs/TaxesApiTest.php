<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Taxes;

class TaxesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_taxes()
    {
        $taxes = Taxes::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/taxes', $taxes
        );

        $this->assertApiResponse($taxes);
    }

    /**
     * @test
     */
    public function test_read_taxes()
    {
        $taxes = Taxes::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/taxes/'.$taxes->id
        );

        $this->assertApiResponse($taxes->toArray());
    }

    /**
     * @test
     */
    public function test_update_taxes()
    {
        $taxes = Taxes::factory()->create();
        $editedTaxes = Taxes::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/taxes/'.$taxes->id,
            $editedTaxes
        );

        $this->assertApiResponse($editedTaxes);
    }

    /**
     * @test
     */
    public function test_delete_taxes()
    {
        $taxes = Taxes::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/taxes/'.$taxes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/taxes/'.$taxes->id
        );

        $this->response->assertStatus(404);
    }
}
