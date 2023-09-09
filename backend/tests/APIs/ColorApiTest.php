<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Color;

class ColorApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_color()
    {
        $color = Color::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/colors', $color
        );

        $this->assertApiResponse($color);
    }

    /**
     * @test
     */
    public function test_read_color()
    {
        $color = Color::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/colors/'.$color->id
        );

        $this->assertApiResponse($color->toArray());
    }

    /**
     * @test
     */
    public function test_update_color()
    {
        $color = Color::factory()->create();
        $editedColor = Color::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/colors/'.$color->id,
            $editedColor
        );

        $this->assertApiResponse($editedColor);
    }

    /**
     * @test
     */
    public function test_delete_color()
    {
        $color = Color::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/colors/'.$color->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/colors/'.$color->id
        );

        $this->response->assertStatus(404);
    }
}
