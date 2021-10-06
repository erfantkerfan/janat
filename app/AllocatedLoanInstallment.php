<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class AllocatedLoanInstallment extends Model
{
    use SoftDeletes, PowerJoins;

    protected $with = ['allocatedLoan'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'allocated_loan_id',
        'rate'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_settled',
        'last_payment',
        'total_payments',
        'remaining_payable_amount'
    ];

    public function allocatedLoan()
    {
        return $this->belongsTo(AllocatedLoan::class);
    }

    public function receivedTransactions()
    {
        return $this->morphToMany(Transaction::class, 'transaction_recipients');
    }

    public function getPaidPaymentsAttribute($operator = null, $date = null, $operator2 = null, $date2 = null)
    {
        return $this->receivedTransactions->filter(function ($value) {
            return $value['transaction_status_id'] === 1;
        });
//        return $this->receivedTransactions()->paid()->sum('cost');
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->receivedTransactions->filter(function ($value) {
            return $value['transaction_status_id'] === 1;
        })->sum('cost');
//        return $this->receivedTransactions()->paid()->sum('cost');
    }

    public function getLastPaymentAttribute()
    {
        return $this->receivedTransactions()->orderBy('paid_at', 'desc')->first();
//        return $this->receivedTransactions()->paid()->sum('cost');
    }

    public function getRemainingPayableAmountAttribute()
    {
        return $this->rate - $this->getTotalPaymentsAttribute();
    }

    public function getIsSettledAttribute()
    {
        return $this->getTotalPaymentsAttribute() >= $this->rate;
    }
}
