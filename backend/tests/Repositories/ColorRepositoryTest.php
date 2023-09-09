<?php namespace Tests\Repositories;

use App\Models\Color;
use App\Repositories\ColorRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ColorRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ColorRepository
     */
    protected $colorRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->colorRepo = \App::make(ColorRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_color()
    {
        $color = Color::factory()->make()->toArray();

        $createdColor = $this->colorRepo->create($color);

        $createdColor = $createdColor->toArray();
        $this->assertArrayHasKey('id', $createdColor);
        $this->assertNotNull($createdColor['id'], 'Created Color must have id specified');
        $this->assertNotNull(Color::find($createdColor['id']), 'Color with given id must be in DB');
        $this->assertModelData($color, $createdColor);
    }

    /**
     * @test read
     */
    public function test_read_color()
    {
        $color = Color::factory()->create();

        $dbColor = $this->colorRepo->find($color->id);

        $dbColor = $dbColor->toArray();
        $this->assertModelData($color->toArray(), $dbColor);
    }

    /**
     * @test update
     */
    public function test_update_color()
    {
        $color = Color::factory()->create();
        $fakeColor = Color::factory()->make()->toArray();

        $updatedColor = $this->colorRepo->update($fakeColor, $color->id);

        $this->assertModelData($fakeColor, $updatedColor->toArray());
        $dbColor = $this->colorRepo->find($color->id);
        $this->assertModelData($fakeColor, $dbColor->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_color()
    {
        $color = Color::factory()->create();

        $resp = $this->colorRepo->delete($color->id);

        $this->assertTrue($resp);
        $this->assertNull(Color::find($color->id), 'Color should not exist in DB');
    }
}
