<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Account;
use App\Setting;
use Carbon\Carbon;
use App\Transaction;
use App\AllocatedLoan;
use App\Traits\Filter;
use App\Traits\CommonCRUD;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\AllocatedLoanInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreTransaction;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreAllocatedLoan;
use App\Http\Requests\StoreAllocatedLoanInstallment;
use App\Http\Requests\PeriodicPayrollDeductionRequest;

class AllocatedLoanController extends Controller
{
    use Filter, CommonCRUD;

    public function __construct()
    {
        $this->middleware('can:view allocated_loans', ['only' => ['showPeriodicPayrollDeduction']]);
        $this->middleware('can:create allocated_loans', ['only' => ['store']]);
        $this->middleware('can:edit allocated_loans', ['only' => ['update', 'payPeriodicPayrollDeduction', 'rollbackPayPeriodicPayrollDeduction']]);
        $this->middleware('can:delete allocated_loans', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $config = [
            'returnModelQuery' => true,
            'eagerLoads'=> [
                'account.user:id,f_name,l_name', 'loan', 'loan.fund'
            ],
            'filterKeys'=> [
                'is_settled',
                'loan_amount',
                'installment_rate',
                'number_of_installments',
                'payroll_deduction'
            ],
            'filterKeysExact'=> [
                'account_id',
                'loan_id',
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'user_id',
                    'relationName' => 'account.user'
                ],
                [
                    'requestKey' => 'company_id',
                    'relationName' => 'account.company'
                ],
                [
                    'requestKey' => 'loan_id',
                    'relationName' => 'loan'
                ],
                [
                    'requestKey' => 'fund_id',
                    'relationName' => 'loan.fund'
                ]
            ],
            'filterRelationKeys'=> [
                [
                    'requestKey' => 'f_name',
                    'relationName' => 'account.user',
                    'relationColumn' => 'f_name'
                ],
                [
                    'requestKey' => 'l_name',
                    'relationName' => 'account.user',
                    'relationColumn' => 'l_name'
                ],
                [
                    'requestKey' => 'SSN',
                    'relationName' => 'account.user',
                    'relationColumn' => 'SSN'
                ]
            ],
            'setAppends'=> [
                'is_settled',
                'last_payment',
//                'total_payments',
//                'remaining_payable_amount',
//                'count_of_paid_installments',
//                'count_of_remaining_installments'
            ],
            'scopes'=> [
                'settled',
                'notSettled'
            ],
        ];

        $user = Auth::user();
        if(!$user->can('view allocated_loans')) {
            $request->offsetSet('user_id', $user->id);
        }

        $data = $this->commonIndex($request,
            AllocatedLoan::class,
            $config
        );
        $allocatedLoanModelQuery = $data['modelQuery'];
        $responseWithAttachedCollection = $data['responseWithAttachedCollection'];

        $lastPaidAtAfter = ($request->has('last_paid_at_after')) ? $request->get('last_paid_at_after') : null;
        $lastPaidAtBefore = ($request->has('last_paid_at_before')) ? $request->get('last_paid_at_before') : null;
        if (isset($lastPaidAtAfter) || isset($lastPaidAtBefore)) {
            $allocatedLoanModelQuery->lastPaymentPaidAt('>=', $lastPaidAtAfter, '<=', $lastPaidAtBefore);
        }

        return $responseWithAttachedCollection($allocatedLoanModelQuery);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAllocatedLoan $request
     * @return Response
     */
    public function store(StoreAllocatedLoan $request)
    {
        DB::beginTransaction();
        $account = Account::findOrFail($request->get('account_id'));
        $loan = Loan::findOrFail($request->get('loan_id'));

        $errors = [];
//        if ($fund->balance < ($loan->loan_amount - $loan->interest_amount)) {
//            $currencyUnit = Setting::where('name', 'currency_unit')->first()->value;
//            $errors['fund_deficit'] = [
//                'مبلغ مورد نظر برای پرداخت وام عبارت است از '.
//                number_format($loan->loan_amount).$currencyUnit.
//                ' و مبلغ کارمزد وام عبارت است از '.
//                number_format($loan->interest_amount).$currencyUnit.
//                ' و پرداختی صندوق به کاربر برابر است با '.
//                number_format($loan->loan_amount - $loan->interest_amount).$currencyUnit.
//                ' در حالی که موجودی صندوق عبارت است از '.
//                number_format($fund->balance).$currencyUnit.
//                ' که بیش تر از مبلغ قابل پرداخت صندوق به کاربر است.'
//            ];
//        }

        if ($loan->fund->id !== $account->fund->id) {
            $errors['account_fund_mismatch'] = [
                ' نام صندوق مربوط به حساب کاربر عبارت است از '.
                $account->fund->name.
                ' در حالی که نام صندوق وام انتخاب شده عبارت از '.
                $loan->fund->name.'. '.
                ' لطفا وام را متناسب با صندوق حساب کاربر انتخاب نمایید.'
            ];
        }
        if (count($errors) > 0) {
            return $this->jsonResponseValidateError([
                'errors' => $errors
            ]);
        }

        // create allocated loan
        $createdAllocatedLoan = AllocatedLoan::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'loan_amount' => $loan->loan_amount,
            'interest_rate' => $loan->interest_rate,
            'interest_amount' => $loan->interest_amount,
            'installment_rate' => $loan->installment_rate,
            'number_of_installments' => $loan->number_of_installments,
            'payroll_deduction' => $request->get('payroll_deduction'),
            'payroll_deduction_amount' => $loan->installment_rate
        ]);
        $typeOfLoanInterestPayment = Setting::where('name', 'type_of_loan_interest_payment')->first()->value;

        if ($typeOfLoanInterestPayment === 'monthly_payment') {
            $transactionCost = $loan->loan_amount;
        } else if ($typeOfLoanInterestPayment === 'paid_at_first') {
            $transactionCost = $loan->loan_amount - $loan->interest_amount;
        } else {
            $transactionCost = $loan->loan_amount;
        }

        // create transaction and fund withdrawal
        $storeTransactionRequest = new StoreTransaction();
        $storeTransactionRequest->replace([
            'cost' => $transactionCost,
            'transaction_status_id' => 1,
            'paid_as_payroll_deduction' => 0,
            'paid_at' => $request->get('paid_at'),
            'manager_comment' => $request->get('manager_comment'),
            'transaction_type' => 'fund_pay_loan',
            'allocated_loan_id' => $createdAllocatedLoan->id
        ]);
        $transactionController = new TransactionController();
        $storeTransactionResult = $transactionController->store($storeTransactionRequest);

        if ($createdAllocatedLoan && $storeTransactionResult->getStatusCode() === 200) {
            DB::commit();
            return $this->show($createdAllocatedLoan->id);
        } else {
            DB::rollBack();
            return $this->jsonResponseServerError([
                'message' => 'مشکلی در تخصیص وام در پایگاه داده رخ داده است.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $allocatedLoan = AllocatedLoan::with([
                'loan.fund.paidTransactions' => function ($query1) use ($id) {
                    $query1->whereHas('allocatedLoanRecipients', function ($query2) use ($id) {
                        $query2->where('allocated_loans.id', '=', $id);
                    });
                },
                'loan.fund.paidTransactions.allocatedLoanRecipients',
                'installments',
                'installments.receivedTransactions',
                'installments.receivedTransactions.transactionStatus',
                'account.user:id,f_name,l_name',
                'loan',
                'loan.fund'
            ])
            ->findOrFail($id)
            ->setAppends([
                'is_settled',
                'total_payments',
                'allocated_loan_paid_at',
                'remaining_payable_amount',
                'count_of_paid_installments',
                'count_of_remaining_installments'
            ]);

        return $this->jsonResponseOk($allocatedLoan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AllocatedLoan $allocatedLoan
     * @return Response
     */
    public function update(Request $request, AllocatedLoan $allocatedLoan)
    {
        $allocatedLoan->payroll_deduction_amount = $request->get('payroll_deduction_amount');
        $allocatedLoan->payroll_deduction = $request->get('payroll_deduction');
        if ($allocatedLoan->save()) {
            return $this->show($allocatedLoan->id);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'commonUpdate' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AllocatedLoan $allocatedLoan
     * @return Response
     */
    public function destroy(AllocatedLoan $allocatedLoan)
    {
        $allocatedLoanId = $allocatedLoan->id;
        $allocatedLoan = AllocatedLoan::with([
            'loan.fund.paidTransactions' => function ($query1) use ($allocatedLoanId) {
                $query1->whereHas('allocatedLoanRecipients', function ($query2) use ($allocatedLoanId) {
                    $query2->where('allocated_loans.id', '=', $allocatedLoanId);
                });
            },
            'loan.fund.paidTransactions.allocatedLoanRecipients',
            'installments',
            'installments.receivedTransactions',
            'installments.receivedTransactions.transactionStatus',
            'account.user:id,f_name,l_name',
            'loan',
            'loan.fund'
        ])
            ->findOrFail($allocatedLoanId)
            ->setAppends(['total_payments']);


        $errors = [];
        if ($allocatedLoan->total_payments - $allocatedLoan->interest_amount > 0) {
            $errors['has_paid_transaction'] = [
                'وام تخصیص داده شده مورد نظر دارای تراکنش پرداخت شده می باشد.'
            ];
        }
        if (count($errors) > 0) {
            return $this->jsonResponseValidateError([
                'errors' => $errors
            ]);
        }

        $allocatedLoan->loan->fund->paidTransactions->each( function ($transaction) {
            $transactionController = new TransactionController();
            $transactionController->destroy($transaction);
        });

        return $this->commonDestroy($allocatedLoan);
    }

    public function showPeriodicPayrollDeduction(PeriodicPayrollDeductionRequest $request) {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');
        $companyId = $request->get('company_id');

        $targetAllocatedLoan = AllocatedLoan::where('payroll_deduction', '=', '1')
            ->whereHas('account.company', function (Builder $query) use ($companyId) {
                $query->whereIn('companies.id', [$companyId]);
            })
            ->lastPayrollDeductionForChargeFundPaidAt('>=', $lastPaidAtAfter, '<=', $lastPaidAtBefore)
            ->get();

        $targetAllocatedLoan = $this->getTargetAllocatedLoanForShow($targetAllocatedLoan->pluck('id'), $lastPaidAtAfter, $lastPaidAtBefore);
        return $this->jsonResponseOk($targetAllocatedLoan);
    }

    public function payPeriodicPayrollDeduction(PeriodicPayrollDeductionRequest $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');
        $paidAt = $request->get('paid_at');
        $companyId = $request->get('company_id');

        $targetAllocatedLoan = AllocatedLoan::with([
            'account.user:id,f_name,l_name,staff_code',
            'loan.fund.paidTransactions.allocatedLoanRecipients',
            'installments'
        ])
            ->notSettled()
            ->where('payroll_deduction', '=', '1')
            ->whereHas('account.company', function (Builder $query) use ($companyId) {
                $query->where('companies.id', $companyId);
            })
            ->lastPaymentForChargeFundNotPaidAt('>=', $lastPaidAtAfter, '<=', $lastPaidAtBefore)
            ->get();
//        die(Str::replaceArray('?', $targetAllocatedLoan->getBindings(), $targetAllocatedLoan->toSql()));
//        die($targetAllocatedLoan);

        $targetAllocatedLoan->each(function ($item) {
            return $item->setAppends(['allocated_loan_paid_at']);
        });
        foreach ($targetAllocatedLoan as $key => $value) {
            if (isset($value->allocated_loan_paid_at) && $value->allocated_loan_paid_at > $lastPaidAtBefore) {
                $targetAllocatedLoan->forget($key);
            }
        }

        $hasProblem = false;
        foreach ($targetAllocatedLoan as $allocatedLoanItem) {
            $notSettledInstallment = $this->getOrCreateNotSettledInstallment($allocatedLoanItem);
            if ($notSettledInstallment === null) {
                continue;
            }

            if (!isset($notSettledInstallment)) {
                $hasProblem = true;
                break;
            }
            $cost = $this->getCostForPayPeriodicPayrollDeduction($allocatedLoanItem, $notSettledInstallment);
            $createTransactionResult = $this->createTransactionForPayPeriodicPayrollDeduction($cost, $paidAt, $notSettledInstallment->id);
            if ($createTransactionResult === false) {
                $hasProblem = true;
            }

            if ($cost >= $allocatedLoanItem->payroll_deduction_amount) {
                continue;
            }
            $remainOfPayrollDeduction = $allocatedLoanItem->payroll_deduction_amount - $cost;
            $createTransactionResult = $this->payRemainingPayrollDeductionAmount($paidAt, $allocatedLoanItem, $remainOfPayrollDeduction);
            if ($createTransactionResult === false) {
                $hasProblem = true;
            }
        }

        if (!$hasProblem) {
            $targetAllocatedLoan = $this->getTargetAllocatedLoanForShow($targetAllocatedLoan->pluck('id'), $lastPaidAtAfter, $lastPaidAtBefore);
            return $this->jsonResponseOk($targetAllocatedLoan);
        } else {
            return $this->jsonResponseValidateError([
                'errors' => [
                    'has_unsettled_installment' => [
                        'مشکلی در ثبت پرداخت ها رخ داده است. لطفا مجددا تلاش کنید.'
                    ]
                ]
            ]);
        }
    }

    private function getCostForPayPeriodicPayrollDeduction ($allocatedLoan, $notSettledInstallment) {
        $roundedInstallmentPadding = Config::get('app.rounded_installment_padding');
        $remainingPayableAmount = $notSettledInstallment->remainingPayableAmount;
        $payrollDeductionAmount = $allocatedLoan->payroll_deduction_amount;
        if ($remainingPayableAmount < $payrollDeductionAmount || ($remainingPayableAmount - $payrollDeductionAmount) <= $roundedInstallmentPadding) {
            $cost = $remainingPayableAmount;
        } else {
            $cost = $payrollDeductionAmount;
        }

        return $cost;
    }

    private function payRemainingPayrollDeductionAmount ($paidAt, $allocatedLoan, $remainOfPayrollDeduction): bool
    {
        if ($remainOfPayrollDeduction === 0) {
            // all the payroll deduction is paid
            return true;
        }
        $notSettledInstallment = $this->getOrCreateNotSettledInstallment($allocatedLoan);
        if ($notSettledInstallment === null) {
            // there is nothing to pay for this allocatedLoan
            return true;
        }
        $remainingPayableAmount = $notSettledInstallment->remainingPayableAmount;
        $cost = min($remainingPayableAmount, $remainOfPayrollDeduction);
        $createTransactionResult = $this->createTransactionForPayPeriodicPayrollDeduction($cost, $paidAt, $notSettledInstallment->id);
        $newRemainOfPayrollDeduction = $remainOfPayrollDeduction - $cost;
        if ($newRemainOfPayrollDeduction > 0) {
            return $this->payRemainingPayrollDeductionAmount($paidAt, $allocatedLoan, $newRemainOfPayrollDeduction);
        }

        return $createTransactionResult;
    }

    private function payRemainingPayrollDeductionAmountOld ($paidAt, $allocatedLoan, $lastPaidInstallmentRemainingPayableAmount, $lastPaidCost, $remainOfPayrollDeduction): bool
    {
        $payrollDeductionAmount = $allocatedLoan->payroll_deduction_amount;
        if ($payrollDeductionAmount <= $lastPaidInstallmentRemainingPayableAmount) {
            // all of $payrollDeductionAmount is paid
            return true;
        }

        $recursionFlag = false;
        $recursionCost = 0;
        $createTransactionResult = true;
        $notSettledInstallment = $this->getOrCreateNotSettledInstallment($allocatedLoan);
        $recLastPaidInstallmentRemainingPayableAmount = $notSettledInstallment->remainingPayableAmount;
        $cost = 0;
        if ($notSettledInstallment !== null) {
            if ($payrollDeductionAmount < $notSettledInstallment->remainingPayableAmount) {
                $cost = $payrollDeductionAmount - ($notSettledInstallment->remainingPayableAmount % $payrollDeductionAmount);
            } else {
                $remainToPaid = $payrollDeductionAmount - $lastPaidCost;
                if ($remainToPaid <= $notSettledInstallment->remainingPayableAmount) {
                    $cost = $payrollDeductionAmount - $notSettledInstallment->remainingPayableAmount;
                } else {
                    $cost = $payrollDeductionAmount - $notSettledInstallment->remainingPayableAmount;
                    $recursionFlag = true;
                }
            }

            $createTransactionResult = $this->createTransactionForPayPeriodicPayrollDeduction($cost, $paidAt, $notSettledInstallment->id);
        }

        if ($recursionFlag && $cost > 0) {
            $createTransactionResult = $this->payRemainingPayrollDeductionAmount($paidAt, $allocatedLoan, $recLastPaidInstallmentRemainingPayableAmount, $cost);
        }

        return $createTransactionResult;
    }

    private function createTransactionForPayPeriodicPayrollDeduction ($cost, $paidAt, $notSettledInstallmentId): bool
    {
        $storeTransactionRequest = new StoreTransaction();
        $storeTransactionRequest->replace([
            'transaction_status_id' => 1,
            'paid_as_payroll_deduction' => 1,
            'cost' => $cost,
            'paid_at' => $paidAt,
            'transaction_type' => 'account_pay_installment',
            'allocated_loan_installment_id' => $notSettledInstallmentId
        ]);
        $transactionController = new TransactionController();
        $storeTransactionResult = $transactionController->store($storeTransactionRequest);
        $createTransactionResult = $storeTransactionResult->getStatusCode() === 200;

//        if ($createTransactionResult === false) {
//            dd($storeTransactionResult);
//        }
        return $createTransactionResult;
    }

    private function getTargetAllocatedLoanForShow ($targetAllocatedLoanIds, $lastPaidAtAfter, $lastPaidAtBefore) {

        $targetAllocatedLoan = AllocatedLoan::with([
            'account.user:id,f_name,l_name,staff_code',
            'loan.fund.paidTransactions.allocatedLoanRecipients',
            'installments'
        ])
            ->whereIn('id', $targetAllocatedLoanIds)
            ->get();

        $setAppends = [
            'is_settled',
            'total_payments',
            'allocated_loan_paid_at',
            'remaining_payable_amount',
            'count_of_paid_installments',
            'count_of_remaining_installments'
        ];

        $targetAllocatedLoan->each(function ($item) use ($setAppends, $lastPaidAtAfter, $lastPaidAtBefore) {
            $installmentInDateRange = $item->installments->filter(function ($installment) use ($lastPaidAtAfter, $lastPaidAtBefore) {
                return (
                    $installment['last_payment'] !== null &&
                    ($installment['last_payment']['paid_at'] >= $lastPaidAtAfter) &&
                    ($installment['last_payment']['paid_at'] <= $lastPaidAtBefore) &&
                    $installment['last_payment']['paid_as_payroll_deduction'] === 1
                );
            });

            $installmentInDateRange->each(function ($item2) use ($setAppends, $lastPaidAtAfter, $lastPaidAtBefore) {
                return $item2['sum_of_paid_payments_as_payroll_deduction'] = $item2->paidPayments->filter(function ($paidPayment) use ($lastPaidAtAfter, $lastPaidAtBefore) {
                    return (
                        ($paidPayment['paid_at'] >= $lastPaidAtAfter) &&
                        ($paidPayment['paid_at'] <= $lastPaidAtBefore) &&
                        $paidPayment['paid_as_payroll_deduction'] === 1
                    );
                })->sum('cost');
            });
            $item['installments_in_date_range'] = $installmentInDateRange;
            $item['count_of_paid_payments_as_payroll_deduction_in_date_range'] = $installmentInDateRange->count();
            $item['sum_of_paid_payments_as_payroll_deduction_in_date_range'] = $installmentInDateRange->sum('sum_of_paid_payments_as_payroll_deduction');
            return $item->setAppends($setAppends);
        });


        foreach ($targetAllocatedLoan as $key => $value) {
            if (isset($value->allocated_loan_paid_at) && $value->allocated_loan_paid_at > $lastPaidAtBefore) {
                $targetAllocatedLoan->forget($key);
            }
        }

        return $targetAllocatedLoan;
    }

    public function rollbackPayPeriodicPayrollDeduction(PeriodicPayrollDeductionRequest $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');
        $companyId = $request->get('company_id');

        $transactions = Transaction::whereHas('transactionType', function ($query) use ($lastPaidAtAfter, $lastPaidAtBefore) {
            $query->where('transaction_types.name', '=', config('constants.TRANSACTION_TYPE_ACCOUNT_PAY_INSTALLMENT'));
        })
            ->with('allocatedLoanInstallmentRecipients.allocatedLoan.account.company')
            ->where('transaction_status_id', '=', 1)
            ->where('paid_as_payroll_deduction', '=', 1)
            ->where('paid_at', '>=', $lastPaidAtAfter)
            ->where('paid_at', '<=', $lastPaidAtBefore)
            ->whereHas('allocatedLoanInstallmentRecipients.allocatedLoan.account', function (Builder $query) use ($companyId) {
                $query->where('accounts.company_id', $companyId);
            })
            ->get();

        $transactions->each( function ($transaction) {
            $transactionController = new TransactionController();
            $transactionController->destroy($transaction->id);
        });

        return $this->jsonResponseOk($transactions);
    }

    private function getNotSettledInstallment ($allocatedLoanItem) {
        return $allocatedLoanItem->installments()->get()
            ->filter(function ($value) {
                return ($value->is_settled === false);
            })
            ->sortByDesc('created_at');
    }
    private function getOrCreateNotSettledInstallment($allocatedLoanItem) {
        $notSettledInstallment = null;
        $notSettledInstallments = $this->getNotSettledInstallment($allocatedLoanItem);
        if ($notSettledInstallments->count() > 0) {
            $notSettledInstallment = $notSettledInstallments->first();
        } else {
            $allocatedLoanItem->setAppends(['count_of_remaining_installments']);
            if ($allocatedLoanItem->count_of_remaining_installments > 0) {
                $request = new StoreAllocatedLoanInstallment();
                $request->replace(['allocated_loan_id' => $allocatedLoanItem->id]);
                $allocatedLoanInstallmentController = new AllocatedLoanInstallmentController();
                $storeAllocatedLoanResult = $allocatedLoanInstallmentController->store($request);
                if ($storeAllocatedLoanResult->getStatusCode() === 200) {
                    $notSettledInstallment = json_decode($storeAllocatedLoanResult->getContent());
                    $notSettledInstallment = AllocatedLoanInstallment::find($notSettledInstallment->id);
                }
            } else {
                $notSettledInstallment = null;
            }
        }

        return $notSettledInstallment;
    }
}
