<?php

namespace App\Tests\Feature;

use App\Events\DiscountCreated;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDiscountTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/discounts';
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_it_can_create_discount()
    {
        $this->expectsEvents(DiscountCreated::class);

        $this->assertCount(0, Discount::all());

        $startTime = now()->addMinute()->toDateTimeString();
        $expirationTime = now()->addDays(3)->toDateTimeString();

        $this->setUser($this->user)
        ->postJson($this->url, [
            'code' => 'some_code',
            'amount' => 10.500,
            'count' => 5,
            'start_time' => $startTime,
            'expiration_time' => $expirationTime,
        ])->assertCreated()
        ->assertJson([
            'data' => [
                'id' => Discount::first()->id,
                'code' => 'some_code',
                'amount' => 10.500,
                'count' => 5,
                'start_time' => $startTime,
                'expiration_time' => $expirationTime,
            ],
        ]);
    }

    public function test_it_returns_422_when_code_is_invalid()
    {
        $this->doesntExpectEvents(DiscountCreated::class);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'code' => 0,
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['code']);

        Discount::factory(['code' => 'some_code'])->create();

        $this->postJson($this->url, [
            'code' => 'some_code',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
    }

    public function test_it_returns_422_when_amount_is_invalid()
    {
        $this->doesntExpectEvents(DiscountCreated::class);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'amount' => 0,
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);

        $this->assertCount(0, Discount::all());
    }

    public function test_it_returns_422_when_count_is_invalid()
    {
        $this->doesntExpectEvents(DiscountCreated::class);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'count' => 0,
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['count']);

        $this->assertCount(0, Discount::all());
    }

    public function test_it_returns_422_when_start_time_is_invalid()
    {
        $this->doesntExpectEvents(DiscountCreated::class);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'start_time' => 'hello',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['start_time']);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'start_time' => '2020-01-01',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['start_time']);

        $this->assertCount(0, Discount::all());
    }

    public function test_it_returns_422_when_expiration_time_is_invalid()
    {
        $this->doesntExpectEvents(DiscountCreated::class);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'expiration_time' => 'hello',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['expiration_time']);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'expiration_time' => '2020-01-01',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['expiration_time']);

        $this->setUser($this->user)
        ->postJson($this->url, [
            'start_time' => '2020-01-02 00:00:00',
            'expiration_time' => '2020-01-01 00:00:00',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['expiration_time']);

        $this->assertCount(0, Discount::all());
    }

    public function test_it_cannot_create_discount_without_auth()
    {
        $this->postJson($this->url)
        ->assertStatus(422)
        ->assertJsonValidationErrors('phone_number');
    }
}
