<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetDiscountsRequest;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    /**
     * Get Discounts.
     *
     * Gets all discounts.
     *
     * @authenticated
     * @group Discount
     *
     * @apiResource App\Http\Resources\DiscountResource
     * @apiResourceModel App\Models\Discount
     */
    public function index(GetDiscountsRequest $request): JsonResponse
    {
        $discounts = Discount::all();

        return $this->ok(DiscountResource::collection($discounts));
    }

}
