<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class TransactionStatus extends Model
{
    use SoftDeletes, PowerJoins;

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
