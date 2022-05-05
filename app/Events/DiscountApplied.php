<?php

namespace App\Events;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscountApplied
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Discount $discount)
    {
    }
}
