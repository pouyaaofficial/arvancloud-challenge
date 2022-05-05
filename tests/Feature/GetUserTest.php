<?php

namespace App\Tests\Feature;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/users';

    public function test_it_gets_user_profile_properly()
    {
        $user = User::factory()->create();

        $discounts = Discount::factory(2)
        ->sequence(
            ['amount' => 10.100],
            ['amount' => 20.200],
        )->create();

        $user->wallet->applyDiscount($discounts->first());
        $user->wallet->applyDiscount($discounts->second());

        $this->setUser($user)
        ->getJson("{$this->url}/{$user->id}")
        ->assertOk()
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'phone_number' => $user->phone_number,
                'balance' => 30.300,
                'transactions' => [
                    ['amount' => 10.100],
                    ['amount' => 20.200],
                ],
            ],
        ]);
    }

    public function test_it_cannot_get_user_data_without_auth()
    {
        $user = User::factory()->create();

        $this->getJson("{$this->url}/{$user->id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors('phone_number');
    }

    public function test_it_cannot_access_to_another_user_data()
    {
        $users = User::factory(2)->create();

        $this->setUser($users->first())
        ->getJson("$this->url/{$users->second()->id}")
        ->assertForbidden();
    }

    public function test_it_returns_404_when_user_not_found()
    {
        $user = User::factory()->create();

        $this->setUser($user)
        ->getJson("$this->url/0")
        ->assertNotFound();
    }
}
