<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function paidTransactions()
    {
        return $this->morphToMany(Transaction::class, 'transaction_payers');
    }
}
