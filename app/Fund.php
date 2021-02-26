<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Fund extends Model
{
    use SoftDeletes, PowerJoins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'undertaker',
        'balance'
    ];

    public function deposit($money) {
        $this->balance += $money;
        return $this->save();
    }

    public function withdrawal($money) {
        $this->balance -= $money;
        return $this->save();
    }

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
