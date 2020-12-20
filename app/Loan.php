<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function allocatedLoans()
    {
        return $this->hasMany(AllocatedLoan::class);
    }
}
