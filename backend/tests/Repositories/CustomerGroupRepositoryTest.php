<?php namespace Tests\Repositories;

use App\Models\CustomerGroup;
use App\Repositories\CustomerGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerGroupRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerGroupRepository
     */
    protected $customerGroupRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerGroupRepo = \App::make(CustomerGroupRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->make()->toArray();

        $createdCustomerGroup = $this->customerGroupRepo->create($customerGroup);

        $createdCustomerGroup = $createdCustomerGroup->toArray();
        $this->assertArrayHasKey('id', $createdCustomerGroup);
        $this->assertNotNull($createdCustomerGroup['id'], 'Created CustomerGroup must have id specified');
        $this->assertNotNull(CustomerGroup::find($createdCustomerGroup['id']), 'CustomerGroup with given id must be in DB');
        $this->assertModelData($customerGroup, $createdCustomerGroup);
    }

    /**
     * @test read
     */
    public function test_read_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();

        $dbCustomerGroup = $this->customerGroupRepo->find($customerGroup->id);

        $dbCustomerGroup = $dbCustomerGroup->toArray();
        $this->assertModelData($customerGroup->toArray(), $dbCustomerGroup);
    }

    /**
     * @test update
     */
    public function test_update_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();
        $fakeCustomerGroup = CustomerGroup::factory()->make()->toArray();

        $updatedCustomerGroup = $this->customerGroupRepo->update($fakeCustomerGroup, $customerGroup->id);

        $this->assertModelData($fakeCustomerGroup, $updatedCustomerGroup->toArray());
        $dbCustomerGroup = $this->customerGroupRepo->find($customerGroup->id);
        $this->assertModelData($fakeCustomerGroup, $dbCustomerGroup->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_group()
    {
        $customerGroup = CustomerGroup::factory()->create();

        $resp = $this->customerGroupRepo->delete($customerGroup->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerGroup::find($customerGroup->id), 'CustomerGroup should not exist in DB');
    }
}
