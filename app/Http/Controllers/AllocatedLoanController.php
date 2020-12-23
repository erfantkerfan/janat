<?php

namespace App\Http\Controllers;

use App\Account;
use App\AllocatedLoan;
use App\Fund;
use App\Loan;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
            'eagerLoads'=> [
                'account.user:id,f_name,l_name', 'loan', 'loan.fund'
            ],
            'filterKeys'=> [
                'account_id',
                'loan_id',
                'loan_amount',
                'installment_rate',
                'number_of_installments'
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'user_id',
                    'relationName' => 'account.user'
                ],
                [
                    'requestKey' => 'loan_id',
                    'relationName' => 'loan'
                ]
            ],
            'setAppends'=> [
                'is_settled'
//            'total_payments',
//            'remaining_payable_amount',
//            'count_of_paid_installments',
//            'count_of_remaining_installments'
            ]
        ];

//        $scopes = function ( & $modelQuery)
//        {
//            $modelQuery->settled();
//        };

        return $this->commonIndex($request,
            AllocatedLoan::class,
            $config
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->commonStore($request, AllocatedLoan::class);
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
