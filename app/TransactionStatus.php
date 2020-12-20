<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionStatus extends Model
{
    use SoftDeletes;

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
