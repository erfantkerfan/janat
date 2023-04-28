<?php

namespace App\Http\Controllers;

use App\Account;
use Carbon\Carbon;
use App\Transaction;
use App\Traits\Filter;
use App\AllocatedLoan;
use App\PayrollDeduction;
use App\Traits\CommonCRUD;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\AllocatedLoanInstallment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreTransaction;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreAllocatedLoanInstallment;
use App\Http\Requests\PeriodicPayrollDeductionRequest;

class PayrollDeductionController extends Controller
{
    use Filter, CommonCRUD;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payrollType = $request->get('payroll_type');
        if($payrollType === 'loan') {
            $request->offsetSet('paid_for_loan', 1);
            $request->offsetSet('paid_for_monthly_payment', 0);
        } else if($payrollType === 'monthly_payment') {
            $request->offsetSet('paid_for_loan', 0);
            $request->offsetSet('paid_for_monthly_payment', 1);
        }

        $config = [
            'returnModelQuery' => true,
            'eagerLoads'=> [
                'transactions.accountPayers',
                'transactions.allocatedLoanInstallmentRecipients.allocatedLoan.account.user',
            ],
            'filterKeys'=> [
                'paid_for_loan',
                'paid_for_monthly_payment'
            ],
//            'filterKeysIn'=> [
//                'id',
//            ],
            'filterRelationIds'=> [],
//            'filterRelationKeys'=> [
//                [
//                    'requestKey' => 'f_name',
//                    'relationName' => 'account.user',
//                    'relationColumn' => 'f_name'
//                ],
//                [
//                    'requestKey' => 'l_name',
//                    'relationName' => 'account.user',
//                    'relationColumn' => 'l_name'
//                ],
//                [
//                    'requestKey' => 'SSN',
//                    'relationName' => 'account.user',
//                    'relationColumn' => 'SSN'
//                ]
//            ]
        ];

        if($payrollType === 'loan') {
            $config['filterRelationIds'][] = [
                'requestKey' => 'company_id',
                'relationName' => 'transactions.allocatedLoanInstallmentRecipients.allocatedLoan.account.company'
            ];
        } else if($payrollType === 'monthly_payment') {
            $config['filterRelationIds'][] = [
                'requestKey' => 'company_id',
                'relationName' => 'transactions.accountPayers.company'
            ];
        }

        $user = Auth::user();
        if(!$user->can('view allocated_loans')) {
            $request->offsetSet('user_id', $user->id);
        }

        $data = $this->commonIndex($request,
            PayrollDeduction::class,
            $config
        );
        $modelQuery = $data['modelQuery'];
        $responseWithAttachedCollection = $data['responseWithAttachedCollection'];

        $from = ($request->has('from')) ? $request->get('from') : null;
        if (strlen($from) > 0) {
            $fromDate  = Carbon::parse($from)->format('Y-m-d H:m:s');
            $modelQuery = $modelQuery->where('from', '>=', $fromDate);
        }
        $to = ($request->has('to')) ? $request->get('to') : null;
        if (strlen($to) > 0) {
            $toDate  = Carbon::parse($to)->format('Y-m-d H:m:s');
            $modelQuery = $modelQuery->where('to', '<=', $toDate);
        }

//        dd(Str::replaceArray('?', $modelQuery->getBindings(), $modelQuery->toSql()));

//        return $this->commonIndex($request, PayrollDeduction::class, $config);
        return $responseWithAttachedCollection($modelQuery);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function store(Request $request)
    {
        $to = $request->get('to');
        $from = $request->get('from');
        $paidAt = $request->get('paid_at');
        $isLoan = $request->get('paid_for_loan') === 1 || $request->get('paid_for_monthly_payment') === 0;


        $createdPayrollDeduction = PayrollDeduction::create([
            'paid_for_loan' => ($isLoan ? 1 : 0),
            'paid_for_monthly_payment' => ($isLoan ? 0 : 1),
            'from' => $from,
            'to' => $to,
            'paid_at' => $paidAt
        ]);

        return $createdPayrollDeduction;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PayrollDeduction  $payrollDeduction
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = PayrollDeduction::with([
//            'transactions.allocatedLoanInstallmentRecipients.allocatedLoan.account.user'
        ])->findOrFail($id);

        return $this->jsonResponseOk($transaction);
    }

    public function sumOfTransactions ($id) {
        $modelQuery = $this->getTransactionModelQuery($id);
        $attachedCollection = $modelQuery->sum('cost');

        return $this->jsonResponseOk($attachedCollection);
    }

    public function payPeriodicForMonthlyPayment(PeriodicPayrollDeductionRequest $request)
    {
        $request->request->add(['paid_for_loan' => 0]);
        $request->request->add(['paid_for_monthly_payment' => 1]);

        $from = $request->get('from');
        $to = $request->get('to');
        $paidAt = $request->get('paid_at');
        $companyId = $request->get('company_id');

        // find Accounts
        $targetAccount = Account::with(['user:id,f_name,l_name,staff_code', 'fund', 'allocatedLoans'])
            ->where('monthly_payment', '>', 0)
            ->hasPayrollDeduction()
            ->hasCompany($companyId)
            ->withoutPayrollDeduction($from, $to)
            ->get();
        // end of find Accounts

        // create PayrollDeduction
        $createdPayrollDeduction = $this->store($request);
        $createdPayrollDeductionId = $createdPayrollDeduction->id;

        $hasProblem = false;
        foreach ($targetAccount as $accountItem) {
            $request = new StoreTransaction();
            $request->replace([
                'transaction_status_id' => 1,
                'paid_as_payroll_deduction' => 1,
                'payroll_deduction_id' => $createdPayrollDeductionId,
                'cost' => $accountItem->monthly_payment,
                'paid_at' => $paidAt,
                'transaction_type' => 'account_pay_the_fund_tuition',
                'account_id' => $accountItem->id
            ]);
            $transactionController = new TransactionController();
            $storeTransactionResult = $transactionController->store($request);
            if ($storeTransactionResult->getStatusCode() !== 200) {
                $hasProblem = true;
            }
        }

        if (!$hasProblem) {
            return $this->jsonResponseOk($createdPayrollDeduction);
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
    public function payPeriodicForLoan(PeriodicPayrollDeductionRequest $request)
    {
        $request->request->add(['paid_for_loan' => 1]);
        $request->request->add(['paid_for_monthly_payment' => 0]);

        $from = $request->get('from');
        $to = $request->get('to');
        $paidAt = $request->get('paid_at');
        $companyId = $request->get('company_id');

        // find AllocatedLoans
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
            ->withoutPayrollDeduction($from, $to)
            ->get();
//        dd(Str::replaceArray('?', $targetAllocatedLoan->getBindings(), $targetAllocatedLoan->toSql()));
//
//        dd($targetAllocatedLoan);

        $targetAllocatedLoan->each(function ($item) {
            return $item->setAppends(['allocated_loan_paid_at']);
        });
        foreach ($targetAllocatedLoan as $key => $value) {
            if (isset($value->allocated_loan_paid_at) && $value->allocated_loan_paid_at > $to) {
                $targetAllocatedLoan->forget($key);
            }
        }
        // end of find AllocatedLoans


        // create PayrollDeduction
        $createdPayrollDeduction = $this->store($request);
        $createdPayrollDeductionId = $createdPayrollDeduction->id;

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
            $createTransactionResult = $this->createTransactionForPayPeriodicPayrollDeduction($cost, $paidAt, $notSettledInstallment->id, $createdPayrollDeductionId);
            if ($createTransactionResult === false) {
                $hasProblem = true;
            }

            if ($cost >= $allocatedLoanItem->payroll_deduction_amount) {
                continue;
            }
            $remainOfPayrollDeduction = $allocatedLoanItem->payroll_deduction_amount - $cost;
            $createTransactionResult = $this->payRemainingPayrollDeductionAmount($paidAt, $allocatedLoanItem, $remainOfPayrollDeduction, $createdPayrollDeductionId);
            if ($createTransactionResult === false) {
                $hasProblem = true;
            }
        }

        if (!$hasProblem) {
            return $this->jsonResponseOk($createdPayrollDeduction);
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
    private function getNotSettledInstallment ($allocatedLoanItem) {
        return $allocatedLoanItem->installments()->get()
            ->filter(function ($value) {
                return ($value->is_settled === false);
            })
            ->sortByDesc('created_at');
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
    private function createTransactionForPayPeriodicPayrollDeduction ($cost, $paidAt, $notSettledInstallmentId, $createdPayrollDeductionId): bool
    {
        $storeTransactionRequest = new StoreTransaction();
        $storeTransactionRequest->replace([
            'transaction_status_id' => 1,
            'paid_as_payroll_deduction' => 1,
            'payroll_deduction_id' => $createdPayrollDeductionId,
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
    private function payRemainingPayrollDeductionAmount ($paidAt, $allocatedLoan, $remainOfPayrollDeduction, $createdPayrollDeductionId): bool
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
        $createTransactionResult = $this->createTransactionForPayPeriodicPayrollDeduction($cost, $paidAt, $notSettledInstallment->id, $createdPayrollDeductionId);
        $newRemainOfPayrollDeduction = $remainOfPayrollDeduction - $cost;
        if ($newRemainOfPayrollDeduction > 0) {
            return $this->payRemainingPayrollDeductionAmount($paidAt, $allocatedLoan, $newRemainOfPayrollDeduction, $createdPayrollDeductionId);
        }

        return $createTransactionResult;
    }

    public function getTransactions (Request $request)
    {
        $payrollDeductionId = $request->payroll_deduction;

        $setAppends = [
            'is_settled',
            'total_payments',
            'remaining_payable_amount',
            'count_of_paid_installments',
            'count_of_remaining_installments'
        ];
        $modelQuery = $this->getTransactionModelQuery($payrollDeductionId);

        $perPage = 10;
        $attachedCollection = $modelQuery
            ->paginate($perPage)
            ->getCollection()
            ->map(function ($item) use ($setAppends) {
                if ($item->relatedRecipients[0]->transactionRecipients->allocatedLoan) {
                    $item->relatedRecipients[0]->transactionRecipients->allocatedLoan = $item->relatedRecipients[0]->transactionRecipients->allocatedLoan->setAppends($setAppends);
                }
                return $item;
            });

        return $this->jsonResponseOk(
            $modelQuery->paginate($perPage)
                ->setCollection($attachedCollection)
        );
    }

    private function getTransactionModelQuery ($payrollDeductionId) {

        $request = new Request();
        $request->offsetSet('payroll_deduction_id', $payrollDeductionId);

        $config = [
            'returnModelQuery' => true,
            'eagerLoads'=> [
                'transactionStatus',
                'relatedPayers.transactionPayers',
                'relatedRecipients.transactionRecipients'
            ],
            'filterKeysExact'=> [
                'payroll_deduction_id',
            ]
        ];

        if(!Auth::user()->can('view transactions')) {
            $request->offsetSet('user_id', Auth::user()->id);
        }

        $data = $this->commonIndex($request,
            Transaction::class,
            $config
        );
        $modelQuery = $data['modelQuery'];

//        dd($modelQuery->toSql());
//        die(Str::replaceArray('?', $modelQuery->getBindings(), $modelQuery->toSql()));
//        die($targetAllocatedLoan);

        return $modelQuery;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PayrollDeduction  $payrollDeduction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayrollDeduction $payrollDeduction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PayrollDeduction  $payrollDeduction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response
    {
        $payrollDeduction = PayrollDeduction::findOrFail($id);
        $payrollDeductionId = $payrollDeduction->id;

        $modelQuery = $this->getTransactionModelQuery($payrollDeductionId);

        $transactionsCollection = $modelQuery
            ->get();

        $transactionsCollection->each( function ($transaction) {
            $transactionController = new TransactionController();
            $transactionController->destroy($transaction->id);
        });

        return $this->commonDestroy($payrollDeduction);
//        return $this->jsonResponseOk([]);
    }
}
