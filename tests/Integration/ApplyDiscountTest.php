<?php

namespace Tests\Integration;

use App\Actions\ApplyDiscount;
use App\Events\DiscountApplied;
use App\Models\Discount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplyDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_apply_discount()
    {
        $this->expectsEvents(DiscountApplied::class);

        $user = User::factory()->create();
        $discount = Discount::factory(['amount' => 150])->create();

        app(ApplyDiscount::class)->apply($discount, $user);

        $this->assertEquals(150, $user->fresh()->wallet->balance);
        $this->assertCount(1, $user->fresh()->wallet->transactions);
    }

    public function test_it_cannot_apply_discount_twice()
    {
        $this->expectsEvents(DiscountApplied::class);

        $user = User::factory()->create();
        $discount = Discount::factory(['amount' => 150])->create();

        app(ApplyDiscount::class)->apply($discount, $user);
        $user->refresh();
        app(ApplyDiscount::class)->apply($discount, $user);

        $this->assertEquals(150, $user->fresh()->wallet->balance);
        $this->assertCount(1, $user->fresh()->wallet->transactions);
    }

    public function test_it_cannot_apply_discount_over_capacity()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $discount = Discount::factory(['count' => 1])->create();

        app(ApplyDiscount::class)->apply($discount, $user);
        $this->assertCount(1, $user->fresh()->wallet->transactions);

        $this->doesntExpectEvents(DiscountApplied::class);

        $discount->refresh();
        app(ApplyDiscount::class)->apply($discount, $anotherUser);
        $this->assertCount(0, $anotherUser->fresh()->wallet->transactions);
    }

    public function test_it_cannot_apply_discount_over_active_time()
    {
        Carbon::setTestNow('2020-01-03 00:00:00');
        $this->doesntExpectEvents(DiscountApplied::class);

        $user = User::factory()->create();

        $discount = Discount::factory([
            'start_time' => '2020-01-01 00:00:00',
            'expiration_time' => '2020-01-02 00:00:00',
        ])->create();

        app(ApplyDiscount::class)->apply($discount, $user);

        $this->assertCount(0, $user->fresh()->wallet->transactions);
        $this->assertSame(0.0, $user->fresh()->wallet->balance);
    }
}
