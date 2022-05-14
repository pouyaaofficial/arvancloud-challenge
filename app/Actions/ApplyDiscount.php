<?php

namespace App\Actions;

use App\Events\DiscountApplied;
use App\Models\Discount;
use App\Models\User;

class ApplyDiscount
{
    public function apply(Discount $discount, User $user)
    {
        if (!$discount->isActive()) {
            return;
        }

        if ($user->hasDiscount($discount)) {
            return;
        }

        if (!$discount->hasCapacity()) {
            return;
        }

        \DB::beginTransaction();

        $user->wallet->applyDiscount($discount);
        DiscountApplied::dispatch($user, $discount);

        \DB::commit();
    }
}
