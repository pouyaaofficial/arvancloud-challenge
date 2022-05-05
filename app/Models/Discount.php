<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'count',
        'start_time',
        'expiration_time',
    ];

    public function wallets()
    {
        return $this->morphToMany(Wallet::class, 'transactionable');
    }
}
