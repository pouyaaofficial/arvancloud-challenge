<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function hasDiscount(Discount $discount): bool
    {
        return $this->wallet->discounts->contains($discount);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (User $model) {
            $model->wallet()->create(['balance' => 0]);
        });
    }
}
