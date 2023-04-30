<?php

namespace App\Http\Controllers;

use App\Account;
use App\AllocatedLoan;
use App\AllocatedLoanInstallment;
use App\Company;
use App\Fund;
use App\Http\Requests\StoreTransaction;
use App\Picture;
use App\Setting;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Transaction;
use App\TransactionStatus;
use App\TransactionType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    use Filter, CommonCRUD;

    public function __construct()
    {
//        $this->middleware('can:view transactions', ['only' => ['index']]);
        $this->middleware('can:create transactions', ['only' => ['store']]);
        $this->middleware('can:edit transactions', ['only' => ['update', 'payPeriodicPayrollDeductionForChargeFund', 'rollbackPayPeriodicPayrollDeduction']]);
        $this->middleware('can:delete transactions', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $transactionType = ($request->has('transaction_type')) ? $request->get('transaction_type') : null; // payer - recipient

        $config = [
            'returnModelQuery' => true,
            'eagerLoads'=> [
                'transactionStatus',
                'transactionType',
                'relatedPayers.transactionPayers',
                'relatedRecipients.transactionRecipients'
            ],
            'filterDate'=> [
                'paid_at'
            ],
            'filterKeys'=> [
                'cost',
                'deadline_at',
                'manager_comment',
                'user_comment',
                'paid_as_payroll_deduction'
            ],
            'filterKeysExact'=> [
                'transaction_status_id',
                'transaction_type_id',
                'parent_transaction_id',
            ],
            'filterRelationIds'=> [
//                [
//                    'requestKey' => 'user_id',
//                    'orWhereHas' => true,
//                    'relationNames' => [
//                        'userPayers',
//                        'userRecipients'
//                    ]
//                ],
//                [
//                    'requestKey' => 'account_id',
//                    'orWhereHas' => true,
//                    'relationNames' => [
//                        'accountPayers',
//                        'accountRecipients'
//                    ]
//                ],
                [
                    'requestKey' => 'loan_id',
                    'relationName' => 'allocatedLoanRecipients.loan'
                ],
//                [
//                    'requestKey' => 'company_id',
//                    'orWhereHas' => true,
//                    'relationNames' => [
//                        'accountPayers.company',
//                        'accountRecipients.company'
//                    ]
//                ],
//                [
//                    'requestKey' => 'fund_id',
//                    'orWhereHas' => true,
//                    'relationNames' => [
//                        'fundPayers',
//                        'fundRecipients'
//                    ]
//                ]
            ],
        ];

        if(!Auth::user()->can('view transactions')) {
            $request->offsetSet('user_id', Auth::user()->id);
        }

        $data = $this->commonIndex($request,
            Transaction::class,
            $config
        );
        $transactionModelQuery = $data['modelQuery'];
        $responseWithAttachedCollection = $data['responseWithAttachedCollection'];

        $companyId = ($request->has('company_id')) ? $request->get('company_id') : null;
        if (isset($companyId)) {
            if ($transactionType === 'payer') {
                $transactionModelQuery->hasCompanyAsPayer($companyId);
            } else if ($transactionType === 'recipient') {
                $transactionModelQuery->hasCompanyAsRecipient($companyId);
            }
        }

        $fundId = ($request->has('fund_id')) ? $request->get('fund_id') : null;
        if (isset($fundId)) {
            if ($transactionType === 'payer') {
                $transactionModelQuery->hasFundAsPayer($fundId);
            } else if ($transactionType === 'recipient') {
                $transactionModelQuery->hasFundAsRecipient($fundId);
            }
        }

        $userId = ($request->has('user_id')) ? $request->get('user_id') : null;
        $fName = ($request->has('f_name')) ? $request->get('f_name') : null;
        $lName = ($request->has('l_name')) ? $request->get('l_name') : null;
        if (isset($userId) || isset($fName) || isset($lName)) {
            if ($transactionType === 'payer') {
                $transactionModelQuery->hasUserAsPayer($userId, $fName, $lName);
            } else if ($transactionType === 'recipient') {
                $transactionModelQuery->hasUserAsRecipient($userId, $fName, $lName);
            }
        }

//        die(Str::replaceArray('?', $transactionModelQuery->getBindings(), $transactionModelQuery->toSql()));

        $accountId = ($request->has('account_id')) ? $request->get('account_id') : null;
        if (isset($accountId)) {
            if ($transactionType === 'payer') {
                $transactionModelQuery->hasAccountAsPayer($accountId);
            } else if ($transactionType === 'recipient') {
                $transactionModelQuery->hasAccountAsRecipient($accountId);
            }
        }

        return $responseWithAttachedCollection($transactionModelQuery);

//        return $this->commonIndex($request, Transaction::class, $config);
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
            'payroll_deduction_id' => $request->get('payroll_deduction_id'),
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
        if ($transaction_type === 'account_charge_fund') {
            $constantType = config('constants.TRANSACTION_TYPE_ACCOUNT_CHARGE_FUND');
        } else if ($transaction_type === 'account_pay_the_fund_tuition') {
            $constantType = config('constants.TRANSACTION_TYPE_ACCOUNT_PAY_THE_FUND_TUITION');
        } else if ($transaction_type === 'user_withdraw_from_account') {
            $constantType = config('constants.TRANSACTION_TYPE_USER_WITHDRAW_FROM_ACCOUNT');
        } else if ($transaction_type === 'company_charge_fund') {
            $constantType = config('constants.TRANSACTION_TYPE_COMPANY_CHARGE_FUND');
        } else if ($transaction_type === 'fund_pay_loan') {
            $constantType = config('constants.TRANSACTION_TYPE_FUND_PAY_LOAN');
        } else if ($transaction_type === 'pay_fund_expenses') {
            $constantType = config('constants.TRANSACTION_TYPE_PAY_FUND_EXPENSES');
        } else if ($transaction_type === 'account_pay_installment') {
            $constantType = config('constants.TRANSACTION_TYPE_ACCOUNT_PAY_INSTALLMENT');
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

        if ($transaction_type === 'account_charge_fund' || $transaction_type === 'account_pay_the_fund_tuition') {
            $dBTransactionValidator = $this->accountChargeFund($request, $transaction);
        } else if ($transaction_type === 'user_withdraw_from_account') {
            $dBTransactionValidator = $this->userWithdrawAccount($request, $transaction);
        } else if ($transaction_type === 'company_charge_fund') {
            $dBTransactionValidator = $this->companyChargeFund($request, $transaction);
        } else if ($transaction_type === 'fund_pay_loan') {
            $dBTransactionValidator = $this->fundPayLoan($request, $transaction);
        } else if ($transaction_type === 'pay_fund_expenses') {
            $dBTransactionValidator = $this->payFundExpenses($request, $transaction);
        } else if ($transaction_type === 'account_pay_installment') {
            $dBTransactionValidator = $this->accountPayInstallment($request, $transaction);
        }

        return $dBTransactionValidator;
    }

    private function accountChargeFund(Request $request, Transaction $transaction) {
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

    private function accountPayInstallment(Request $request, Transaction $transaction) {
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

        $account = $allocatedLoanInstallment->allocatedLoan->account;
        $transaction->accountPayers()->attach($account, ['cost'=> $cost]);
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
        $transaction = Transaction::with([
            'transactionType',
            'transactionStatus',
            'userPayers:id,f_name,l_name',
            'relatedPayers.transactionPayers',
            'relatedRecipients.transactionRecipients'
        ])->findOrFail($id);

        return $this->jsonResponseOk($transaction);
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
        $request->request->remove('cost');
        $request->request->remove('paid_at');
        return $this->commonUpdate($request, $transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Transaction $transaction
     * @return Response
     */
    public function destroy($id): Response
    {
        $transaction = Transaction::with([
            'transactionStatus',
            'userPayers:id,f_name,l_name',
            'relatedPayers.transactionPayers',
            'relatedRecipients.transactionRecipients'
        ])->findOrFail($id);

        $transaction->relatedPayers->each( function ($payer) {
            if ($payer->transaction_payers_type === 'App\\Fund') {
                $fund = $payer->transactionPayers;
                $fund->deposit($payer->cost);
            }
        });
        $transaction->relatedRecipients->each( function ($recipient) {
            if ($recipient->transaction_recipients_type === 'App\\Fund') {
                $fund = $recipient->transactionRecipients;
                $fund->withdrawal($recipient->cost);
            }
            if ($recipient->transaction_recipients_type === 'App\\AllocatedLoanInstallment') {
                $allocatedLoanInstallment = $recipient->transactionRecipients;
                $fund = $allocatedLoanInstallment->allocatedLoan->loan->fund;
                $fund->withdrawal($recipient->cost);
            }
        });

        return $this->commonDestroy($transaction);
    }

    private function withdrawalAndRecalculateFundAnsUserBalance(Transaction $transaction) {
        $transaction_type = $transaction->transactionType->display_name;
        $dBTransactionValidator = true;

//        if ($transaction_type === config('constants.TRANSACTION_TYPE_ACCOUNT_CHARGE_FUND')) {
//            $dBTransactionValidator = $this->userChargeFund($request, $transaction);
//        } else if ($transaction_type === 'user_withdraw_from_account') {
//            $dBTransactionValidator = $this->userWithdrawAccount($request, $transaction);
//        } else if ($transaction_type === 'company_charge_fund') {
//            $dBTransactionValidator = $this->companyChargeFund($request, $transaction);
//        } else if ($transaction_type === 'fund_pay_loan') {
//            $dBTransactionValidator = $this->fundPayLoan($request, $transaction);
//        } else if ($transaction_type === 'pay_fund_expenses') {
//            $dBTransactionValidator = $this->payFundExpenses($request, $transaction);
//        } else if ($transaction_type === 'account_pay_installment') {
//            $dBTransactionValidator = $this->accountPayInstallment($request, $transaction);
//        }
//
//        return $dBTransactionValidator;
    }

    public function addPicture(Request $request) {
        $transaction = Transaction::find($request->get('transaction_id'));
        $createdPic = Picture::create(['picture' => File::get($request->file('picture'))]);
        $transaction->pictures()->attach($createdPic);
    }

    public function getPictures($id) {
        $transaction = Transaction::find($id);
        $pictures = $transaction->pictures()->get()->map(function ($picture) {
            return 'data:image/jpeg;base64,'.base64_encode( $picture->picture );
        });

        return $pictures;
    }
}
