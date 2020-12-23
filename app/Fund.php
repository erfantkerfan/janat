<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Fund extends Model
{
    use SoftDeletes, PowerJoins;

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function receivedTransactions()
    {
        return $this->morphToMany(Transaction::class, 'transaction_recipients');
    }
}
