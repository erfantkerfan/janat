<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocatedLoanInstallment extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_settled',
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

    public function getTotalPaymentsAttribute()
    {
        return $this->receivedTransactions()->paid()->sum('cost');
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
