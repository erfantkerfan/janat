<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAllocatedLoanInstallment;
use App\Http\Requests\StoreTransaction;
use App\Loan;
use App\Account;
use App\AllocatedLoan;
use App\Setting;
use App\Traits\Filter;
use App\Traits\CommonCRUD;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreAllocatedLoan;

class AllocatedLoanController extends Controller
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
            'returnModelQuery' => true,
            'eagerLoads'=> [
                'account.user:id,f_name,l_name', 'loan', 'loan.fund'
            ],
            'filterKeys'=> [
                'account_id',
                'loan_id',
                'is_settled',
                'loan_amount',
                'installment_rate',
                'number_of_installments',
                'payroll_deduction'
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'user_id',
                    'relationName' => 'account.user'
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

        // create allocated loan
        $createdAllocatedLoan = AllocatedLoan::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'loan_amount' => $loan->loan_amount,
            'interest_rate' => $loan->interest_rate,
            'interest_amount' => $loan->interest_amount,
            'installment_rate' => $loan->installment_rate,
            'number_of_installments' => $loan->number_of_installments,
            'payroll_deduction' => $request->get('payroll_deduction')
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
        $allocatedLoan = AllocatedLoan::with(
                'installments',
                'installments.receivedTransactions',
                'installments.receivedTransactions.transactionStatus',
                'account.user:id,f_name,l_name',
                'loan',
                'loan.fund'
            )
            ->findOrFail($id)
            ->setAppends([
                'is_settled',
                'total_payments',
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
        return $this->commonUpdate($request, $allocatedLoan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AllocatedLoan $allocatedLoan
     * @return Response
     */
    public function destroy(AllocatedLoan $allocatedLoan)
    {
        return $this->commonDestroy($allocatedLoan);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Response
     */
    public function payPeriodicPayrollDeduction(Request $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');

        $targetAllocatedLoan = AllocatedLoan::with('account.user:id,f_name,l_name,staff_code', 'loan', 'loan.fund', 'installments')
            ->notSettled()
            ->where('payroll_deduction', '=', '1')
            ->lastPayrollDeductionForChargeFundNotPaidAt('>=', $lastPaidAtAfter, '<=', $lastPaidAtBefore)
            ->get();

        $hasProblem = false;
        foreach ($targetAllocatedLoan as $allocatedLoanItem) {
            $notSettledInstallment = $this->getNotSettledInstallment($allocatedLoanItem);

            if (isset($notSettledInstallment)) {
                $request = new StoreTransaction();
                $request->replace([
                    'transaction_status_id' => 1,
                    'paid_as_payroll_deduction' => 1,
                    'cost' => $notSettledInstallment->rate,
                    'paid_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'transaction_type' => 'user_pay_installment',
                    'allocated_loan_installment_id' => $notSettledInstallment->id
                ]);
                $transactionController = new TransactionController();
                $storeTransactionResult = $transactionController->store($request);
                if ($storeTransactionResult->getStatusCode() !== 200) {
                    $hasProblem = true;
                }
            } else {
                $hasProblem = true;
            }
        }

        if (!$hasProblem) {
            $setAppends = [
                'is_settled',
                'total_payments',
                'remaining_payable_amount',
                'count_of_paid_installments',
                'count_of_remaining_installments'
            ];
            $targetAllocatedLoan->map(function (& $item) use ($setAppends) {
                return $item->setAppends($setAppends);
            });
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

    /**
     * @param Request $request
     * @return Response
     */
    public function rollbackPayPeriodicPayrollDeduction(Request $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');

        Transaction::whereHas('transactionType', function ($query) use ($lastPaidAtAfter, $lastPaidAtBefore) {
            $query->where('transaction_types.name', '=', config('constants.TRANSACTION_TYPE_USER_PAY_INSTALLMENT'));
        })
            ->where('transaction_status_id', '=', 1)
            ->where('paid_as_payroll_deduction', '=', 1)
            ->where('paid_at', '>=', $lastPaidAtAfter)
            ->where('paid_at', '<=', $lastPaidAtBefore)
            ->delete();

        return $this->jsonResponseOk(null);
    }

    private function getNotSettledInstallment($allocatedLoanItem) {
        $notSettledInstallment = null;
        $notSettledInstallments = $allocatedLoanItem->installments
            ->filter(function ($value) {
                return ($value->is_settled === false);
            })
            ->sortByDesc('created_at');
        if ($notSettledInstallments->count() > 0) {
            $notSettledInstallment = $notSettledInstallments->first();
        } else {
            $request = new StoreAllocatedLoanInstallment();
            $request->replace(['allocated_loan_id' => $allocatedLoanItem->id]);
            $allocatedLoanInstallmentController = new AllocatedLoanInstallmentController();
            $storeAllocatedLoanResult = $allocatedLoanInstallmentController->store($request);
            if ($storeAllocatedLoanResult->getStatusCode() === 200) {
                $notSettledInstallment = json_decode($storeAllocatedLoanResult->getContent());
            }
        }

        return $notSettledInstallment;
    }
}
