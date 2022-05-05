<?php

namespace App\Tests\Feature;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetDiscountsTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/discounts';

    public function test_can_get_all_discounts_properly()
    {
        Discount::factory(5)->create();
        $user = User::factory()->create();

        $this->setUser($user)
        ->getJson($this->url)
        ->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'amount',
                    'count',
                    'start_time',
                    'expiration_time',
                ],
            ],
        ]);
    }
}
