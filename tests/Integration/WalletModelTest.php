<?php

namespace Tests\Integration;

use App\Models\Discount;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletModelTest extends TestCase
{
    use RefreshDatabase;

    private $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = Wallet::factory()->create();
    }

    public function test_model_factory_works_properly()
    {
        $this->assertInstanceOf(Wallet::class, $this->model);
    }

    public function test_it_related_to_user_properly()
    {
        $user = User::factory()->create();
        $this->model->user()->associate($user);

        $this->assertTrue($this->model->user->is($user));
    }

    public function test_wallet_can_apply_discount()
    {
        $this->model->update(['balance' => 0]);

        $discount = Discount::factory(['amount' => 10.2])->create();

        $this->model->applyDiscount($discount);

        $this->assertTrue($this->model->discounts->first()->is($discount));
        $this->assertEquals(10.2, $this->model->balance);
    }
}
