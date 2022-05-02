<?php

namespace App\Tests\Feature;

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/users';

    public function test_it_creates_new_user()
    {
        $this->expectsEvents(UserCreated::class);

        $this->assertCount(0, User::all());

        $this->postJson($this->url, [
            'phone_number' => '11111111111',
        ])->assertCreated()
        ->assertJson([
            'data' => [
                'id' => User::first()->id,
                'phone_number' => '11111111111',
            ],
        ]);

        $this->assertEquals('11111111111', User::first()->phone_number);
        $this->assertSame(0.0, User::first()->wallet->balance);
    }

    public function test_it_doesnt_create_user_if_exists()
    {
        $this->doesntExpectEvents(UserCreated::class);

        $user = User::factory(['phone_number' => '11111111111'])->create();
        $user->wallet()->update(['balance' => '1000']);

        $this->postJson($this->url, [
            'phone_number' => '11111111111',
        ])->assertCreated();

        $this->assertCount(1, User::all());
        $this->assertSame(1000.0, User::first()->wallet->balance);
    }

    public function test_it_returns_422_if_phone_number_is_invalid()
    {
        $this->doesntExpectEvents(UserCreated::class);

        $this->postJson($this->url, [
            'phone_number' => 0,
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number']);

        $this->postJson($this->url, [
            'phone_number' => '123456789',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number']);

        $this->postJson($this->url, [
            'phone_number' => 'something',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number']);

        $this->assertCount(0, User::all());
    }
}
