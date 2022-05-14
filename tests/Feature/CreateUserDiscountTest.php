<?php

namespace App\Tests\Feature;

use App\Jobs\ApplyDiscount;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class CreateUserDiscountTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/user/discount';

    public function test_it_apply_discount_queue_works_properly()
    {
        Redis::flushDB();
        Queue::fake();

        $user = User::factory()->create();
        $discount = Discount::factory(['amount' => 150])->create();

        $this->assertEquals(0, $user->wallet->balance);
        $this->assertCount(0, $user->wallet->transactions);

        $this->postJson($this->url, [
            'code' => $discount->code,
            'phone_number' => $user->phone_number,
        ])->assertOk();

        Queue::assertPushed(fn (ApplyDiscount $job) => $job->discount->id === $discount->id);
    }

    public function test_it_registers_user_if_not_exists()
    {
        Queue::fake();

        $discount = Discount::factory(['amount' => 150])->create();

        $this->assertFalse(User::where('phone_number', '99999999999')->exists());

        $this->postJson($this->url, [
            'code' => $discount->code,
            'phone_number' => '99999999999',
        ])->assertOk();

        $this->assertTrue(User::where('phone_number', '99999999999')->exists());
    }

    public function test_it_returns_422_when_code_is_invalid()
    {
        $user = User::factory()->create();
        Discount::factory()->create();

        $this->postJson($this->url, [
            'code' => 0,
            'phone_number' => $user->phone_number,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);

        $this->postJson($this->url, [
            'code' => 'some_code',
            'phone_number' => $user->phone_number,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
    }
}
