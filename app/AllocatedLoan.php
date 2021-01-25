<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Kirschbaum\PowerJoins\PowerJoins;

class AllocatedLoan extends Model
{
    use SoftDeletes, PowerJoins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'loan_id',
        'loan_amount',
        'interest_rate',
        'interest_amount',
        'installment_rate',
        'number_of_installments',
        'payroll_deduction'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'payable_amount'
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
        return $this->getPayableAmountAttribute() - $this->getTotalPaymentsAttribute();
    }

    public function getPayableAmountAttribute()
    {
        return $this->loan_amount + $this->interest_amount;
    }

    public function getIsSettledAttribute()
    {
        return $this->getTotalPaymentsAttribute() >= $this->getPayableAmountAttribute();
    }

    public function scopeSettled($query) {
        return $query->whereHas('installments', function($q) {
            $q->whereHas(
                'receivedTransactions',
                function ($query) {
                    $query->select('transactions.id', DB::raw('SUM(transactions.cost) as total_paid'))
                        ->groupBy('transactions.id')
                        ->havingRaw( DB::raw('SUM(`transactions`.`cost`) >= (`allocated_loans`.`loan_amount` + `allocated_loans`.`interest_amount`)'));
//                        ->where('sum(transactions.cost)', '>', $payableAmount);
                }
            );
//            $q->where('rate', '>', $payableAmount);
//            $q->where('sum(total_payments)', '>', $payableAmount);
        });
//        return $query->where('is_settled', true);
//        return $query->installments->sum('total_payments')->where('is_settled', true);
    }

    public function scopeNotSettled($query) {
        return $query->whereHas('installments', function($q) {
            $q->whereHas(
                'receivedTransactions',
                function ($query) {
                    $query->select('transactions.id', DB::raw('SUM(transactions.cost) as total_paid'))
                        ->groupBy('transactions.id')
                        ->havingRaw( DB::raw('SUM(`transactions`.`cost`) < (`allocated_loans`.`loan_amount` + `allocated_loans`.`interest_amount`)'));
//                        ->where('sum(transactions.cost)', '>', $payableAmount);
                }
            );
//            $q->where('rate', '>', $payableAmount);
//            $q->where('sum(total_payments)', '>', $payableAmount);
        });
//        return $query->where('is_settled', true);
//        return $query->installments->sum('total_payments')->where('is_settled', true);
    }
}
