<?php

namespace App\Jobs;

use App\Actions\ApplyDiscount as ApplyDiscountAction;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ApplyDiscount implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public User $user, public Discount $discount)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(ApplyDiscountAction::class)->apply($this->discount, $this->user);
    }

    public function uniqueId()
    {
        return $this->user->id;
    }

    public function uniqueVia()
    {
        return Cache::driver('redis');
    }

    public function middleware()
    {
        return [(new WithoutOverlapping($this->discount->id))->expireAfter(2)];
    }
}
