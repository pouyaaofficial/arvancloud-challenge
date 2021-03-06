<?php

namespace Tests\Integration;

use App\Models\Discount;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    private $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = User::factory()->create();
    }

    public function test_model_factory_works_properly()
    {
        $this->assertInstanceOf(User::class, $this->model);
    }

    public function test_it_creates_a_wallet_with_user_properly()
    {
        $this->assertInstanceOf(Wallet::class, $this->model->wallet);
    }

    public function test_user_has_discount()
    {
        $discount = Discount::factory()->create();
        $this->assertFalse($this->model->hasDiscount($discount));

        $this->model->wallet->applyDiscount($discount);
        $this->assertTrue($this->model->fresh()->hasDiscount($discount));
    }
}
