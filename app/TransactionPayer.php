<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionPayer extends Model
{
    public function transactionPayers()
    {
        return $this->morphTo();
    }
}
