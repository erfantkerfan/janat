<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Account;
use App\AllocatedLoan;
use App\Traits\Filter;
use App\Traits\CommonCRUD;
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

        // fund withdrawal
        $fund = $account->fund()->first();
        $withdrawalResult = $fund->withdrawal($loan->loan_amount);

        if ($createdAllocatedLoan && $withdrawalResult) {
            DB::commit();
            return $this->show($createdAllocatedLoan->id);
        } else {
            DB::rollBack();
            return $this->jsonResponseServerError([
                'message' => 'مشکلی در تخصیص وام در پایگاه داده رخ داده است.'
            ]);
        }
//        return $this->commonStore($request, AllocatedLoan::class);


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
            ->find($id)
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
}
