<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
//        'has_unsettled_installment',
//        'last_payment',
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
        $totalPayments = 0;
        $typeOfLoanInterestPayment = Setting::where('name', 'type_of_loan_interest_payment')->first()->value;

        if ($typeOfLoanInterestPayment === 'monthly_payment') {
            $totalPayments = $this->installments->sum('total_payments');
        } else if ($typeOfLoanInterestPayment === 'paid_at_first') {
            $totalPayments = $this->installments->sum('total_payments') + $this->interest_amount;
        } else {
            $totalPayments = $this->installments->sum('total_payments');
        }

        return $totalPayments;
    }

    public function getHasUnsettledInstallmentAttribute()
    {
        return $this->installments->contains('is_settled', '===', false);
    }

    public function getLastPaymentAttribute()
    {
        $installments = $this->installments()->get();
        $lastInstallmentsPayments = $installments->pluck('last_payment')
            ->filter(function ($value) {
                return isset($value);
            })
            ->flatten()
            ->sortByDesc('paid_at')
            ->flatten()
            ->first();

        return $lastInstallmentsPayments;
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
        $payableAmount = 0;
        $typeOfLoanInterestPayment = Setting::where('name', 'type_of_loan_interest_payment')->first()->value;

        if ($typeOfLoanInterestPayment === 'monthly_payment') {
            $payableAmount = $this->loan_amount + $this->interest_amount;
        } else if ($typeOfLoanInterestPayment === 'paid_at_first') {
            $payableAmount = $this->loan_amount + $this->interest_amount;
        } else {
            $payableAmount = $this->loan_amount + $this->interest_amount;
        }
        return $payableAmount;
    }

    public function getIsSettledAttribute()
    {
//        return $this->getTotalPaymentsAttribute();
        return $this->getTotalPaymentsAttribute() >= $this->getPayableAmountAttribute();
    }

    private function getSettledIds() {
        $rawQuery = "
            SELECT `id`
            FROM (
                SELECT `allocated_loans`.`id`,
                SUM(`transactions`.`cost`) AS total_paid,
                (`allocated_loans`.`loan_amount` + `allocated_loans`.`interest_amount`) AS payable_amount,
                `allocated_loans`.`loan_amount`,
                `allocated_loans`.`interest_amount`
                FROM `transactions`
                INNER JOIN `transaction_recipients` ON `transactions`.`id` = `transaction_recipients`.`transaction_id`
                INNER JOIN `allocated_loan_installments` ON `transaction_recipients`.`transaction_recipients_id` = `allocated_loan_installments`.`id`
                AND `transaction_recipients`.`transaction_recipients_type` = 'App\\\AllocatedLoanInstallment'
                AND `allocated_loan_installments`.`deleted_at` IS NULL
                INNER JOIN `allocated_loans` ON `allocated_loan_installments`.`allocated_loan_id` = `allocated_loans`.`id`
                AND `allocated_loans`.`deleted_at` IS NULL
                WHERE `transactions`.`deleted_at` IS NULL
                AND `transactions`.`transaction_status_id`=1
                GROUP BY `allocated_loans`.`id`
                HAVING total_paid >= payable_amount
            ) AS allocated_loans";

        $result = DB::select($rawQuery);
        return collect($result)->map(function($x){ return (array) $x; })->toArray();
    }

    public function scopeSettled($query) {
        $settledIds = $this->getSettledIds();
        $query->whereIn('id', $settledIds);
//        $query->whereHas('installments', function($q) {
//            $q->whereHas('receivedTransactions', function ($query) {
//                    $query->select('transactions.id', DB::raw('SUM(transactions.cost) as total_paid'))
//                        ->groupBy('transactions.id')
//                        ->havingRaw( DB::raw('SUM(`transactions`.`cost`) >= (`allocated_loans`.`loan_amount` + `allocated_loans`.`interest_amount`)'));
////                        ->where('sum(transactions.cost)', '>', $payableAmount);
//                }
//            );
////            $q->where('rate', '>', $payableAmount);
////            $q->where('sum(total_payments)', '>', $payableAmount);
//        });
    }

    public function scopeNotSettled($query) {
        $settledIds = $this->getSettledIds();
        $query->whereNotIn('id', $settledIds);
//        return $query->whereHas('installments', function($q) {
//            $q->whereHas(
//                'receivedTransactions',
//                function ($query) {
//                    $query->select('transactions.id', DB::raw('SUM(transactions.cost) as total_paid'))
//                        ->groupBy('transactions.id')
//                        ->havingRaw( DB::raw('SUM(`transactions`.`cost`) < (`allocated_loans`.`loan_amount` + `allocated_loans`.`interest_amount`)'));
////                        ->where('sum(transactions.cost)', '>', $payableAmount);
//                }
//            );
////            $q->where('rate', '>', $payableAmount);
////            $q->where('sum(total_payments)', '>', $payableAmount);
//        });
    }

    public function scopeLastPaymentPaidAt($query, $operator, $date, $operator2 = null, $date2 = null) {
        $whereClause1 = "AND `transactions`.`paid_at` $operator '$date'";
        $whereClause2 = (isset($operator2) && isset($date2)) ? "AND `transactions`.`paid_at` $operator2 '$date2'" : '';
        $rawQuery = "
            SELECT `id`
            FROM (
                SELECT `allocated_loans`.`id`, `transactions`.`paid_at`
                FROM `transactions`
                INNER JOIN `transaction_recipients` ON `transactions`.`id` = `transaction_recipients`.`transaction_id`
                INNER JOIN `allocated_loan_installments` ON `transaction_recipients`.`transaction_recipients_id` = `allocated_loan_installments`.`id`
                AND `transaction_recipients`.`transaction_recipients_type` = 'App\\\AllocatedLoanInstallment'
                AND `allocated_loan_installments`.`deleted_at` IS NULL
                INNER JOIN `transaction_payers` ON `transactions`.`id` = `transaction_payers`.`transaction_id`
                INNER JOIN `users` ON `transaction_payers`.`transaction_payers_id` = `users`.`id`
                AND `transaction_payers`.`transaction_payers_type` = 'App\\\User'
                AND `users`.`deleted_at` IS NULL
                INNER JOIN `allocated_loans` ON `allocated_loan_installments`.`allocated_loan_id` = `allocated_loans`.`id`
                AND `allocated_loans`.`deleted_at` IS NULL
                WHERE `transactions`.`deleted_at` IS NULL
                $whereClause1
                $whereClause2
                ORDER BY `transactions`.`paid_at` DESC
            ) AS tbl";

        $result = DB::select($rawQuery);
        $query->whereIn('id', collect($result)->map(function($x){ return (array) $x; })->toArray());
//        dd($query->whereIn('id', [22])->toSql());
//        $query->whereIn('id', [123123123]);
    }

    public function scopeLastPayrollDeductionForChargeFundPaidAt($query, $operator, $date, $operator2 = null, $date2 = null) {
        $whereClause1 = "AND `transactions`.`paid_at` $operator '$date'";
        $whereClause2 = (isset($operator2) && isset($date2)) ? "AND `transactions`.`paid_at` $operator2 '$date2'" : '';
        $rawQuery = "
            SELECT `id`
            FROM (
                SELECT `allocated_loans`.`id`, `transactions`.`paid_at`
                FROM `transactions`
                INNER JOIN `transaction_recipients` ON `transactions`.`id` = `transaction_recipients`.`transaction_id`
                INNER JOIN `allocated_loan_installments` ON `transaction_recipients`.`transaction_recipients_id` = `allocated_loan_installments`.`id`
                AND `transaction_recipients`.`transaction_recipients_type` = 'App\\\AllocatedLoanInstallment'
                AND `allocated_loan_installments`.`deleted_at` IS NULL
                INNER JOIN `transaction_payers` ON `transactions`.`id` = `transaction_payers`.`transaction_id`
                INNER JOIN `users` ON `transaction_payers`.`transaction_payers_id` = `users`.`id`
                AND `transaction_payers`.`transaction_payers_type` = 'App\\\User'
                AND `users`.`deleted_at` IS NULL
                INNER JOIN `allocated_loans` ON `allocated_loan_installments`.`allocated_loan_id` = `allocated_loans`.`id`
                AND `allocated_loans`.`deleted_at` IS NULL
                WHERE `transactions`.`paid_as_payroll_deduction` = 1
                AND `transactions`.`deleted_at` IS NULL
                $whereClause1
                $whereClause2
                ORDER BY `transactions`.`paid_at` DESC
            ) AS tbl";

        $result = DB::select($rawQuery);
        $query->whereIn('id', collect($result)->map(function($x){ return (array) $x; })->toArray());
    }

    public function scopeLastPayrollDeductionForChargeFundNotPaidAt($query, $operator, $date, $operator2 = null, $date2 = null) {
        $whereClause1 = "AND `transactions`.`paid_at` $operator '$date'";
        $whereClause2 = (isset($operator2) && isset($date2)) ? "AND `transactions`.`paid_at` $operator2 '$date2'" : '';
        $rawQuery = "
            SELECT `id`
            FROM (
                SELECT `allocated_loans`.`id`, `transactions`.`paid_at`
                FROM `transactions`
                INNER JOIN `transaction_recipients` ON `transactions`.`id` = `transaction_recipients`.`transaction_id`
                INNER JOIN `allocated_loan_installments` ON `transaction_recipients`.`transaction_recipients_id` = `allocated_loan_installments`.`id`
                AND `transaction_recipients`.`transaction_recipients_type` = 'App\\\AllocatedLoanInstallment'
                AND `allocated_loan_installments`.`deleted_at` IS NULL
                INNER JOIN `transaction_payers` ON `transactions`.`id` = `transaction_payers`.`transaction_id`
                INNER JOIN `users` ON `transaction_payers`.`transaction_payers_id` = `users`.`id`
                AND `transaction_payers`.`transaction_payers_type` = 'App\\\User'
                AND `users`.`deleted_at` IS NULL
                INNER JOIN `allocated_loans` ON `allocated_loan_installments`.`allocated_loan_id` = `allocated_loans`.`id`
                AND `allocated_loans`.`deleted_at` IS NULL
                WHERE `transactions`.`paid_as_payroll_deduction` = 1
                AND `transactions`.`deleted_at` IS NULL
                $whereClause1
                $whereClause2
                ORDER BY `transactions`.`paid_at` DESC
            ) AS tbl";

        $result = DB::select($rawQuery);
        $query->whereNotIn('id', collect($result)->map(function($x){ return (array) $x; })->toArray());
//        dd($query->whereIn('id', [22])->toSql());
//        $query->whereIn('id', [123123123]);
    }
}
