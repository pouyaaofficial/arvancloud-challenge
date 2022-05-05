<?php

namespace Tests\Integration;

use App\Models\Discount;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountModelTest extends TestCase
{
    use RefreshDatabase;

    private $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = Discount::factory()->create();
    }

    public function test_model_factory_works_properly()
    {
        $this->assertInstanceOf(Discount::class, $this->model);
    }

    public function test_code_column_is_unique()
    {
        $this->expectException(QueryException::class);

        Discount::factory(['code' => $this->model->code])->create();

        $this->assertEquals(1, Discount::count());
    }
}
