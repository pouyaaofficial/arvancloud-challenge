<?php

namespace App\Tests\Feature;

use App\Jobs\ApplyDiscount;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateUserDiscountTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/users/{id}/discounts';

    public function test_it_apply_discount_queue_works_properly()
    {
        Queue::fake();

        $user = User::factory()->create();
        $discount = Discount::factory(['amount' => 150])->create();

        $this->assertEquals(0, $user->wallet->balance);
        $this->assertCount(0, $user->wallet->transactions);

        $url = str_replace('{id}', $user->id, $this->url);

        $this->setUser($user)
        ->postJson($url, [
            'code' => $discount->code,
        ])->assertOk();

        Queue::assertPushed(fn (ApplyDiscount $job) => $job->discount->id === $discount->id);
    }

    public function test_it_returns_422_when_code_is_invalid()
    {
        $user = User::factory()->create();
        Discount::factory()->create();

        $url = str_replace('{id}', $user->id, $this->url);

        $this->setUser($user)
        ->postJson($url, [
            'code' => 0,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);

        $this->setUser($user)
        ->postJson($url, [
            'code' => 'some_code',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
    }

    public function test_it_cannot_apply_discount_without_auth()
    {
        $user = User::factory()->create();

        $url = str_replace('{id}', $user->id, $this->url);

        $this->postJson($url)
        ->assertStatus(422)
        ->assertJsonValidationErrors('phone_number');
    }
}
