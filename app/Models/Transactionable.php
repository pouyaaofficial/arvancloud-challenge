<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Transactionable extends MorphPivot
{
    protected $table = 'transactionables';

    public function transactionable()
    {
        return $this->morphTo();
    }
}
