<?php

namespace Tests\Integration;

use App\Events\DiscountApplied;
use App\Models\Discount;
use App\Models\User;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_apply_discount()
    {
        $this->expectsEvents(DiscountApplied::class);

        $user = User::factory()->create();
        $discount = Discount::factory(['amount' => 150])->create();

        (new DiscountService($discount))->apply($user);

        $this->assertEquals(150, $user->fresh()->wallet->balance);
        $this->assertCount(1, $user->fresh()->wallet->transactions);
    }

    public function test_it_cannot_apply_discount_twice()
    {
        $this->expectsEvents(DiscountApplied::class);

        $user = User::factory()->create();
        $discount = Discount::factory(['amount' => 150])->create();

        (new DiscountService($discount))->apply($user);
        $user->refresh();
        (new DiscountService($discount))->apply($user);

        $this->assertEquals(150, $user->fresh()->wallet->balance);
        $this->assertCount(1, $user->fresh()->wallet->transactions);
    }

    public function test_it_cannot_apply_discount_over_capacity()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $discount = Discount::factory(['count' => 1])->create();

        (new DiscountService($discount))->apply($user);
        $this->assertCount(1, $user->fresh()->wallet->transactions);

        $this->doesntExpectEvents(DiscountApplied::class);

        $discount->refresh();
        (new DiscountService($discount))->apply($anotherUser);
        $this->assertCount(0, $anotherUser->fresh()->wallet->transactions);
    }
}
