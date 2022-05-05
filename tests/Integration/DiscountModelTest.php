<?php

namespace Tests\Integration;

use App\Models\Discount;
use App\Models\User;
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

    public function test_discount_has_count()
    {
        $this->model->update(['count' => 1]);
        $this->assertTrue($this->model->hasCapacity());

        $this->model->update(['count' => 0]);
        $this->assertFalse($this->model->hasCapacity());
    }

    public function test_discount_has_capacity()
    {
        $user = User::factory()->create();

        $this->model->update(['count' => 1]);
        $this->assertTrue($this->model->hasCapacity());

        $user->wallet->applyDiscount($this->model);
        $this->assertFalse($this->model->fresh()->hasCapacity());
    }
}
