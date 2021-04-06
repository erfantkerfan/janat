<?php

namespace App\Http\Controllers;

use App\Account;
use App\AllocatedLoan;
use App\AllocatedLoanInstallment;
use App\Company;
use App\Fund;
use App\Http\Requests\StoreTransaction;
use App\Setting;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Transaction;
use App\TransactionStatus;
use App\TransactionType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    use Filter, CommonCRUD;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $config = [
            'eagerLoads'=> [
                'transactionStatus',
                'relatedPayers.transactionPayers',
                'relatedRecipients.transactionRecipients'
            ],
            'filterKeys'=> [
                'cost',
                'deadline_at',
                'manager_comment',
                'user_comment',
                'transaction_status_id',
                'parent_transaction_id'
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'user_id',
                    'relationName' => 'userPayers'
                ],
                [
                    'requestKey' => 'loan_id',
                    'relationName' => 'allocatedLoanRecipients.loan'
                ],
                [
                    'requestKey' => 'company_id',
                    'relationName' => 'companyPayers'
                ],
                [
                    'requestKey' => 'fund_id',
                    'orWhereHas' => true,
                    'relationNames' => [
                        'fundPayers',
                        'fundRecipients'
                    ]
                ]
            ],
        ];

        return $this->commonIndex($request, Transaction::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTransaction $request
     * @return Response
     */
    public function store(StoreTransaction $request)
    {
        $transactionStatus = TransactionStatus::findOrFail($request->get('transaction_status_id'));
        $cost = $request->get('cost');
        $paid_as_payroll_deduction = 0;
        if ($request->has('paid_as_payroll_deduction') && $request->get('paid_as_payroll_deduction')) {
            $paid_as_payroll_deduction = 1;
        }

        $transactionType = $this->getTransactionType($request);

        if (!$transactionType) {
            return $this->jsonResponseValidateError([
                'errors' => [
                    'transaction_type' => [
                        'نوع تراکنش معتبر نیست.'
                    ]
                ]
            ]);
        }

        DB::beginTransaction();
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $request->get('manager_comment'),
            'paid_as_payroll_deduction' => $paid_as_payroll_deduction,
            'user_comment' => $request->get('user_comment'),
            'transaction_status_id' => $transactionStatus->id,
            'transaction_type_id' => $transactionType->id,
            'deadline_at' => $request->get('deadline_at'),
            'paid_at' => $request->get('paid_at')
        ]);

        $dBTransactionValidator = $this->attachTransactionAndChangeFundBalance($request, $transaction);

        if ($dBTransactionValidator->fails()) {
            DB::rollBack();
            return $this->jsonResponseValidateError([
                'errors' => $dBTransactionValidator->errors()
            ]);
        } else {
            DB::commit();
            return $this->show($transaction->id);
        }
    }

    private function getTransactionType(Request $request) {
        $transaction_type = $request->get('transaction_type');
        $constantType = '';
        if ($transaction_type === 'user_charge_fund') {
            $constantType = config('constants.TRANSACTION_TYPE_USER_CHARGE_FUND');
        } else if ($transaction_type === 'user_withdraw_from_account') {
            $constantType = config('constants.TRANSACTION_TYPE_USER_WITHDRAW_FROM_ACCOUNT');
        } else if ($transaction_type === 'company_charge_fund') {
            $constantType = config('constants.TRANSACTION_TYPE_COMPANY_CHARGE_FUND');
        } else if ($transaction_type === 'fund_pay_loan') {
            $constantType = config('constants.TRANSACTION_TYPE_FUND_PAY_LOAN');
        } else if ($transaction_type === 'pay_fund_expenses') {
            $constantType = config('constants.TRANSACTION_TYPE_PAY_FUND_EXPENSES');
        } else if ($transaction_type === 'user_pay_installment') {
            $constantType = config('constants.TRANSACTION_TYPE_USER_PAY_INSTALLMENT');
        } else {
            return false;
        }
        $transactionType = TransactionType::where('name', $constantType)->first();

        if (isset($transactionType)) {
            return $transactionType;
        } else {
            return false;
        }
    }

    private function attachTransactionAndChangeFundBalance(Request $request, Transaction $transaction) {
        $transaction_type = $request->get('transaction_type');
        $dBTransactionValidator = true;

        if ($transaction_type === 'user_charge_fund') {
            $dBTransactionValidator = $this->userChargeFund($request, $transaction);
        } else if ($transaction_type === 'user_withdraw_from_account') {
            $dBTransactionValidator = $this->userWithdrawAccount($request, $transaction);
        } else if ($transaction_type === 'company_charge_fund') {
            $dBTransactionValidator = $this->companyChargeFund($request, $transaction);
        } else if ($transaction_type === 'fund_pay_loan') {
            $dBTransactionValidator = $this->fundPayLoan($request, $transaction);
        } else if ($transaction_type === 'pay_fund_expenses') {
            $dBTransactionValidator = $this->payFundExpenses($request, $transaction);
        } else if ($transaction_type === 'user_pay_installment') {
            $dBTransactionValidator = $this->userPayInstallment($request, $transaction);
        }

        return $dBTransactionValidator;
    }

    private function userChargeFund(Request $request, Transaction $transaction) {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id',
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $account = Account::findOrFail($request->get('account_id'));
        $fund = $account->fund()->first();
        $cost = $request->get('cost');
        $transaction->accountPayers()->attach($account, ['cost'=> $cost]);
        $transaction->fundRecipients()->attach($fund, ['cost'=> $cost]);
        $fund->deposit($cost);

        return $validator;
    }

    private function userWithdrawAccount(Request $request, Transaction $transaction) {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id',
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $currencyUnit = Setting::where('name', 'currency_unit')->first()->value;
        $cost = $request->get('cost');
        $account = Account::findOrFail($request->get('account_id'));
        $validator = Validator::make($request->all(), [
            'cost' => 'numeric|max:'.($account->totalPaidSalaries())
        ], $messages = [
            'max' => [
                'numeric' => "موجودی حساب کاربر :max $currencyUnit و مبلغ درخواستی $cost $currencyUnit می باشد که از موجودی حساب کاربر بیشتر است.",
            ]
        ]);
        if($validator->fails()) {
            return $validator;
        }
        $fund = $account->fund()->first();
        $transaction->fundPayers()->attach($fund, ['cost'=> $cost]);
        $transaction->accountRecipients()->attach($account, ['cost'=> $cost]);
        $fund->withdrawal($cost);

        return $validator;
    }

    private function companyChargeFund(Request $request, Transaction $transaction) {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id'
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $company = Company::findOrFail($request->get('company_id'));
        $fund = $company->fund()->first();
        $cost = $request->get('cost');
        $transaction->companyPayers()->attach($company, ['cost'=> $cost]);
        $transaction->fundRecipients()->attach($fund, ['cost'=> $cost]);
        $fund->deposit($cost);

        return $validator;
    }

    private function fundPayLoan(Request $request, Transaction $transaction) {
        $validator = Validator::make($request->all(), [
            'allocated_loan_id' => 'required|exists:allocated_loans,id'
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $allocatedLoan = AllocatedLoan::findOrFail($request->get('allocated_loan_id'));
        $fund = $allocatedLoan->account->fund()->first();
        $cost = $request->get('cost');
        $transaction->fundPayers()->attach($fund, ['cost'=> $cost]);
        $transaction->allocatedLoanRecipients()->attach($allocatedLoan, ['cost'=> $cost]);
        $fund->withdrawal($cost);

        return $validator;
    }

    private function payFundExpenses(Request $request, Transaction $transaction) {
        $validator = Validator::make($request->all(), [
            'fund_id' => 'required|exists:funds,id'
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $currencyUnit = Setting::where('name', 'currency_unit')->first()->value;
        $fund = Fund::findOrFail($request->get('fund_id'))->setAppends(['incomes', 'expenses']);
        $cost = $request->get('cost');
        $maxCost = $fund->incomes['sum_of_all'] - $fund->expenses;
            $validator = Validator::make($request->all(), [
            'cost' => 'numeric|max:'.$maxCost
        ], $messages = [
            'max' => [
                'numeric' => "مجموع کل درآمد های صندوق با احتساب هزینه ها $maxCost $currencyUnit می باشد و مبلغ درخواستی برای هزینه جدید $cost $currencyUnit می باشد که از درآمد صندوق بیشتر است.",
            ]
        ]);

        $user = User::where('SSN', '=', 'admin')->first();
        $transaction->fundPayers()->attach($fund, ['cost'=> $cost]);
        $transaction->userRecipients()->attach($user, ['cost'=> $cost]);
        $fund->withdrawal($cost);

        return $validator;
    }

    private function userPayInstallment(Request $request, Transaction $transaction) {
        $validator = Validator::make($request->all(), [
            'allocated_loan_installment_id' => 'required|exists:allocated_loan_installments,id'
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $allocatedLoanInstallment = AllocatedLoanInstallment::findOrFail($request->get('allocated_loan_installment_id'))
            ->setAppends([
                'is_settled',
                'total_payments',
                'remaining_payable_amount'
            ]);
        $cost = $request->get('cost');
        $validator = Validator::make($request->all(), [
            'cost' => 'numeric|max:'.($allocatedLoanInstallment->remaining_payable_amount)
        ]);
        if($validator->fails()) {
            return $validator;
        }

        $user = $allocatedLoanInstallment->allocatedLoan->account->user()->first();
        $transaction->userPayers()->attach($user, ['cost'=> $cost]);
        $transaction->allocatedLoanInstallmentRecipients()->attach($allocatedLoanInstallment, ['cost'=> $cost]);
        $fund = $allocatedLoanInstallment->allocatedLoan->loan->fund;
        $fund->deposit($cost);

        return $validator;
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $data = Transaction::with([
            'transactionStatus',
            'userPayers:id,f_name,l_name',
            'relatedPayers.transactionPayers',
            'relatedRecipients.transactionRecipients'
        ])->find($id);

        return $this->jsonResponseOk($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Transaction $transaction
     * @return Response
     */
    public function update(Request $request, Transaction $transaction): Response
    {
        return $this->commonUpdate($request, $transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Transaction $transaction
     * @return Response
     */
    public function destroy(Transaction $transaction): Response
    {
        return $this->commonDestroy($transaction);
    }
}
