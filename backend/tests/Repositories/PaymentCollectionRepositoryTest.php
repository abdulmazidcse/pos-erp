<?php namespace Tests\Repositories;

use App\Models\PaymentCollection;
use App\Repositories\PaymentCollectionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PaymentCollectionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymentCollectionRepository
     */
    protected $paymentCollectionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentCollectionRepo = \App::make(PaymentCollectionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->make()->toArray();

        $createdPaymentCollection = $this->paymentCollectionRepo->create($paymentCollection);

        $createdPaymentCollection = $createdPaymentCollection->toArray();
        $this->assertArrayHasKey('id', $createdPaymentCollection);
        $this->assertNotNull($createdPaymentCollection['id'], 'Created PaymentCollection must have id specified');
        $this->assertNotNull(PaymentCollection::find($createdPaymentCollection['id']), 'PaymentCollection with given id must be in DB');
        $this->assertModelData($paymentCollection, $createdPaymentCollection);
    }

    /**
     * @test read
     */
    public function test_read_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->create();

        $dbPaymentCollection = $this->paymentCollectionRepo->find($paymentCollection->id);

        $dbPaymentCollection = $dbPaymentCollection->toArray();
        $this->assertModelData($paymentCollection->toArray(), $dbPaymentCollection);
    }

    /**
     * @test update
     */
    public function test_update_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->create();
        $fakePaymentCollection = PaymentCollection::factory()->make()->toArray();

        $updatedPaymentCollection = $this->paymentCollectionRepo->update($fakePaymentCollection, $paymentCollection->id);

        $this->assertModelData($fakePaymentCollection, $updatedPaymentCollection->toArray());
        $dbPaymentCollection = $this->paymentCollectionRepo->find($paymentCollection->id);
        $this->assertModelData($fakePaymentCollection, $dbPaymentCollection->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_payment_collection()
    {
        $paymentCollection = PaymentCollection::factory()->create();

        $resp = $this->paymentCollectionRepo->delete($paymentCollection->id);

        $this->assertTrue($resp);
        $this->assertNull(PaymentCollection::find($paymentCollection->id), 'PaymentCollection should not exist in DB');
    }
}
