<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionRecipient extends Model
{
    public function transactionRecipients()
    {
        return $this->morphTo();
    }
}
