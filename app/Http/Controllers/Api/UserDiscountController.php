<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateUser;
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
     * Tries to apply the discount on the user's wallet.
     * This endpoint uses a job queue to prevent Race Condition problem.
     * so It always returns a success status (200).
     * For checking that discount has applied, use api/v1/users/{id} endpoint.
     * It can be better by using Webhook or Notification Systems.
     *
     * @group User
     *
     * @response 200 {"data": []}
     */
    public function store(CreateUserDiscountRequest $request, CreateUser $creator): JsonResponse
    {
        $user = $creator->create($request->validated());
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
