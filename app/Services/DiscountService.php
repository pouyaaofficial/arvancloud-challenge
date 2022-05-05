<?php

namespace App\Services;

use App\Events\DiscountApplied;
use App\Models\Discount;
use App\Models\User;

class DiscountService
{
    public function __construct(private Discount $discount)
    {
    }

    public function apply(User $user)
    {
        \DB::beginTransaction();

        if ($this->discount->hasCapacity() && !$user->hasDiscount($this->discount)) {
            $user->wallet->applyDiscount($this->discount);
            DiscountApplied::dispatch($user, $this->discount);
        }

        \DB::commit();
    }
}
