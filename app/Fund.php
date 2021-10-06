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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
//        'incomes'
        //        'is_settled',
        //        'has_unsettled_installment',
        //        'last_payment',
        //        'total_payments',
        //        'remaining_payable_amount',
        //        'count_of_paid_installments',
        //        'count_of_remaining_installments',
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

    public function paidTransactions()
    {
        return $this->morphToMany(Transaction::class, 'transaction_payers')
            ->where('transactions.transaction_status_id', 1);
    }

    public function receivedTransactions()
    {
        return $this->morphToMany(Transaction::class, 'transaction_recipients');
    }

    public function getIncomesAttribute()
    {
        $transactionType_userChargeFund = TransactionType::where('name', config('constants.TRANSACTION_TYPE_ACCOUNT_CHARGE_FUND'))->first();
        $transactionType_companyChargeFund = TransactionType::where('name', config('constants.TRANSACTION_TYPE_COMPANY_CHARGE_FUND'))->first();
        $transactionType_accountPayInstallment = TransactionType::where('name', config('constants.TRANSACTION_TYPE_ACCOUNT_PAY_INSTALLMENT'))->first();
        $transactionType_userPayTheFundTuition = TransactionType::where('name', config('constants.TRANSACTION_TYPE_ACCOUNT_PAY_THE_FUND_TUITION'))->first();

        $sumOfUsersPayTheFundTuition = $this->receivedTransactions()->whereIn('transaction_type_id', [
            $transactionType_userPayTheFundTuition->id
        ])->sum('transactions.cost');
        $sumOfUsersPayTheFundTuition = floatval ($sumOfUsersPayTheFundTuition);

        $sumOfChargeFund = $this->receivedTransactions()->whereIn('transaction_type_id', [
            $transactionType_userChargeFund->id,
            $transactionType_companyChargeFund->id
        ])->sum('transactions.cost');
        $sumOfChargeFund = floatval ($sumOfChargeFund);

        $userChargeFundTransaction = $this->loans()
            ->with([
                'allocatedLoans.installments.receivedTransactions' => function ($query) use ($transactionType_accountPayInstallment) {
                    $query->where('transaction_type_id', '=', $transactionType_accountPayInstallment->id);
                }
            ])->get();

        $typeOfLoanInterestPayment = Setting::where('name', 'type_of_loan_interest_payment')->first()->value;
        $sumOfInstallmentsInterest = 0;
        if ($typeOfLoanInterestPayment === 'monthly_payment') {
            $userChargeFundTransaction->each(function ($loanItem) use (& $sumOfInstallmentsInterest) {
                $loanItem->allocatedLoans->each(function ($allocatedLoanItem) use (& $sumOfInstallmentsInterest) {
                    $interestAmountPerMonth = $allocatedLoanItem->interest_amount / $allocatedLoanItem->number_of_installments;
                    $allocatedLoanItem->installments->each(function ($installmentItem) use (& $sumOfInstallmentsInterest, $interestAmountPerMonth) {
                        $installmentItem->receivedTransactions->each(function ($received_transactionItem) use (& $sumOfInstallmentsInterest, $interestAmountPerMonth) {
                            $sumOfInstallmentsInterest += $interestAmountPerMonth;
                        });
                    });
                });
            });
        } else if ($typeOfLoanInterestPayment === 'paid_at_first') {
            $userChargeFundTransaction->each(function ($loanItem) use (& $sumOfInstallmentsInterest) {
                $loanItem->allocatedLoans->each(function ($allocatedLoanItem) use (& $sumOfInstallmentsInterest) {
                    $sumOfInstallmentsInterest += $allocatedLoanItem->interest_amount;
                });
            });
        } else {
            $userChargeFundTransaction->each(function ($loanItem) use (& $sumOfInstallmentsInterest) {
                $loanItem->allocatedLoans->each(function ($allocatedLoanItem) use (& $sumOfInstallmentsInterest) {
                    $interestAmountPerMonth = $allocatedLoanItem->interest_amount / $allocatedLoanItem->number_of_installments;
                    $allocatedLoanItem->installments->each(function ($installmentItem) use (& $sumOfInstallmentsInterest, $interestAmountPerMonth) {
                        $installmentItem->receivedTransactions->each(function ($received_transactionItem) use (& $sumOfInstallmentsInterest, $interestAmountPerMonth) {
                            $sumOfInstallmentsInterest += $interestAmountPerMonth;
                        });
                    });
                });
            });
        }

        $sumOfall = $sumOfChargeFund + $sumOfUsersPayTheFundTuition + $sumOfInstallmentsInterest;

        return [
            'sum_of_charge_fund' => $sumOfChargeFund,
            'sum_of_users_pay_the_fund_tuition' => $sumOfUsersPayTheFundTuition,
            'sum_of_installments_interest' => $sumOfInstallmentsInterest,
            'sum_of_all' => $sumOfall
        ];
    }

    public function getExpensesAttribute()
    {
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_PAY_FUND_EXPENSES'))->first();

        $sumOfExpenses = $this->paidTransactions()->whereIn('transaction_type_id', [
            $transactionType->id,
        ])->sum('transactions.cost');
        $sumOfExpenses = floatval ($sumOfExpenses);
//        $sumOfExpenses = $this->paidTransactions()->whereIn('transaction_type_id', [
//            $transactionType->id,
//        ])->dump();


        return $sumOfExpenses;
    }

    public function getDemandsAttribute()
    {
        // set is_settled for allocated loans of accounts
        $accounts = $this->accounts->map( function (&$account) {
            $account['allocated_loans'] = $account->allocatedLoans->map( function (&$allocatedLoan) {
                return $allocatedLoan->setAppends(['is_settled']);
            });
            return $account;
        });

        // remove settled loans
        // 1- remove accounts has not unsettled loans
        foreach ($accounts as $key => $account) {
            $countOfNotSettledLoans = $account['allocated_loans']->filter( function($allocatedLoan) {
                return !$allocatedLoan['is_settled'];
            })->count();
            if ($countOfNotSettledLoans === 0) {
                $accounts->forget($key);
            } else {
                // 2- remove settled loans of account
                foreach ($account['allocated_loans'] as $key2 => $allocatedLoanOfAccount) {
                    if ($allocatedLoanOfAccount['is_settled']) {
                        $account['allocated_loans']->forget($key2);
                    }
                }
            }
        }

        // set remaining_payable_amount for allocated loans of accounts
        $accounts = $this->accounts->map( function (&$account) {
            $account['allocated_loans'] = $account->allocatedLoans->map( function (&$allocatedLoan) {
                return $allocatedLoan->setAppends(['remaining_payable_amount']);
            });
            return $account;
        });

        $demands = 0;
        $accounts->each( function ($account) use (&$demands) {
            $demands += $account['allocated_loans']->sum('remaining_payable_amount');
        });

        return $demands;
    }

}
