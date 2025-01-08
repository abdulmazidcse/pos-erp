<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PaymentCollection;

class PaymentCollectionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/payment_collections', $paymentCollection
        );

        $this->assertApiResponse($paymentCollection);
    }

    /**
     * @test
     */
    public function test_read_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/payment_collections/'.$paymentCollection->id
        );

        $this->assertApiResponse($paymentCollection->toArray());
    }

    /**
     * @test
     */
    public function test_update_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->create();
        $editedPaymentCollection = PaymentCollection::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/payment_collections/'.$paymentCollection->id,
            $editedPaymentCollection
        );

        $this->assertApiResponse($editedPaymentCollection);
    }

    /**
     * @test
     */
    public function test_delete_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/payment_collections/'.$paymentCollection->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/payment_collections/'.$paymentCollection->id
        );

        $this->response->assertStatus(404);
    }
}
