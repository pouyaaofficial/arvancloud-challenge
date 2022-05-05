<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
    ];

    protected $with = [
        'transactions.transactionable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discounts()
    {
        return $this->morphedByMany(Discount::class, 'transactionable')
        ->using(Transactionable::class)
        ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transactionable::class);
    }

    public function applyDiscount(Discount $discount)
    {
        \DB::transaction(function () use ($discount) {
            $this->discounts()->attach($discount);
            $this->balance += $discount->amount;
            $this->save();
        });
    }
}
