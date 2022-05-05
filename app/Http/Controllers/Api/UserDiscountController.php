<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserDiscountRequest;
use App\Http\Requests\GetAllUserDiscountRequest;
use App\Http\Resources\UserResource;
use App\Jobs\ApplyDiscount;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserDiscountController extends Controller
{
    /**
     * Apply Discount.
     *
     * Tries to apply discount on user's wallet.
     *
     * @authenticated
     * @group User
     *
     * @response 200 {"data": []}
     */
    public function store(CreateUserDiscountRequest $request, User $user): JsonResponse
    {
        $discount = Discount::firstWhere('code', $request->code);

        ApplyDiscount::dispatch($user, $discount);

        return $this->ok();
    }

    /**
     * Discounted Users.
     *
     * Get all users that discount has applied on their wallet.
     *
     * @authenticated
     * @group Discount
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function index(GetAllUserDiscountRequest $request, Discount $discount): JsonResponse
    {
        $users = User::find($discount->wallets->pluck('user_id'))->load('wallet');

        return $this->ok(UserResource::collection($users));
    }
}
