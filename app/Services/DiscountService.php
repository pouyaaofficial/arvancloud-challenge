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
        if (!$this->discount->isActive()) {
            return;
        }

        if ($user->hasDiscount($this->discount)) {
            return;
        }

        if (!$this->discount->hasCapacity()) {
            return;
        }

        \DB::transaction(function () use ($user) {
            $user->wallet->applyDiscount($this->discount);
            DiscountApplied::dispatch($user, $this->discount);
        });
    }
}
