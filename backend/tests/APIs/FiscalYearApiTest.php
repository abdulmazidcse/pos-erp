<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FiscalYear;

class FiscalYearApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/fiscal_years', $fiscalYear
        );

        $this->assertApiResponse($fiscalYear);
    }

    /**
     * @test
     */
    public function test_read_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/fiscal_years/'.$fiscalYear->id
        );

        $this->assertApiResponse($fiscalYear->toArray());
    }

    /**
     * @test
     */
    public function test_update_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->create();
        $editedFiscalYear = FiscalYear::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/fiscal_years/'.$fiscalYear->id,
            $editedFiscalYear
        );

        $this->assertApiResponse($editedFiscalYear);
    }

    /**
     * @test
     */
    public function test_delete_fiscal_year()
    {
        $fiscalYear = FiscalYear::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/fiscal_years/'.$fiscalYear->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/fiscal_years/'.$fiscalYear->id
        );

        $this->response->assertStatus(404);
    }
}
