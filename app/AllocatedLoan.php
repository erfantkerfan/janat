<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllocatedLoan extends Model
{
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
//        'is_settled',
//        'total_payments',
//        'remaining_payable_amount',
//        'count_of_paid_installments',
//        'count_of_remaining_installments',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function installments()
    {
        return $this->hasMany(AllocatedLoanInstallment::class);
    }

    public function settledInstallments()
    {
        return $this->hasMany(AllocatedLoanInstallment::class)->where('is_settled', true);
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->installments->sum('total_payments');
    }

    public function getCountOfPaidInstallmentsAttribute()
    {
        return $this->installments->where('is_settled', true)->count();
    }

    public function getCountOfRemainingInstallmentsAttribute()
    {
        return $this->number_of_installments - $this->getCountOfPaidInstallmentsAttribute();
    }

    public function getRemainingPayableAmountAttribute()
    {
        return $this->loan_amount - $this->getTotalPaymentsAttribute();
    }

    public function getIsSettledAttribute()
    {
        $payableAmount = $this->loan->loan_amount + $this->loan->interest_amount;
        return $this->getTotalPaymentsAttribute() >= $payableAmount;
    }

    public function scopeSettled($query)
    {
//        return $query->where('is_settled', true);
        return $query->installments()->sum('total_payments')->where('is_settled', true);
    }
}
