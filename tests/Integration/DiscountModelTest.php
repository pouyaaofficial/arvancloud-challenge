<?php

namespace Tests\Integration;

use App\Models\Discount;
use App\Models\User;
use Carbon\Carbon;
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

    public function test_discount_is_active()
    {
        $this->model->update([
            'start_time' => '2020-01-01 00:00:00',
            'expiration_time' => '2020-01-02 00:00:00',
        ]);

        Carbon::setTestNow('2020-01-01 00:00:00');
        $this->assertTrue($this->model->isActive());

        Carbon::setTestNow('2019-12-29 00:00:00');
        $this->assertFalse($this->model->isActive());

        Carbon::setTestNow('2020-01-03 00:00:00');
        $this->assertFalse($this->model->isActive());
    }
}
