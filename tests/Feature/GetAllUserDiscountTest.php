<?php

namespace App\Tests\Feature;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllUserDiscountTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/discounts/{id}/users';

    public function test_it_can_get_all_discounted_users_properly()
    {
        $discount = Discount::factory()->create();

        $users = User::factory(10)->create();
        $users->each(fn ($user) => $user->wallet->applyDiscount($discount));

        $url = str_replace('{id}', $discount->id, $this->url);

        $this->setUser($users->first())
        ->getJson($url)
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'phone_number',
                    'balance',
                    'transactions' => [
                        '*' => [
                            'amount',
                            'datetime',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_it_cannot_get_discounted_users_without_auth()
    {
        $discount = Discount::factory()->create();
        $user = User::factory()->create();

        $url = str_replace('{id}', $discount->id, $this->url);

        $this->getJson($url)
        ->assertStatus(422)
        ->assertJsonValidationErrors('phone_number');
    }

    public function test_it_returns_404_when_discount_not_found()
    {
        $discount = Discount::factory()->create();

        $user = User::factory()->create();
        $user->wallet->applyDiscount($discount);

        $url = str_replace('{id}', 0, $this->url);

        $this->setUser($user)
        ->getJson($url)
        ->assertNotFound();
    }
}
