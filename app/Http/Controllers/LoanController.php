<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Traits\Filter;
use App\Traits\CommonCRUD;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Classes\LoanCalculator;
use App\Http\Requests\StoreLoan;

class LoanController extends Controller
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
                'fund',
                'loanType'
            ],
            'filterKeys'=> [
                'name',
                'loan_amount',
                'interest_rate',
                'interest_amount',
                'installment_rate',
                'number_of_installments',
            ],
            'filterRelationIds'=> [
                [
                    'requestKey'=> 'fund_id',
                    'relationName'=> 'fund'
                ],
                [
                    'requestKey'=> 'loan_type_id',
                    'relationName'=> 'loanType'
                ]
            ]
        ];

        return $this->commonIndex($request, Loan::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLoan $request
     * @return Response
     */
    public function store(StoreLoan $request)
    {
        $loanCalculator = new LoanCalculator();
        $interestAmount = $loanCalculator->getInterestRate($request->get('loan_amount'),
            $request->get('interest_rate'),
            $request->get('number_of_installments'));
        $roundedInstallmentsRate = $loanCalculator->getRoundedInstallmentsRate($request->get('loan_amount'),
            $interestAmount,
            $request->get('number_of_installments'));
        $request->offsetSet('installment_rate', $roundedInstallmentsRate);
        $request->offsetSet('interest_amount', $interestAmount);
        return $this->commonStore($request, Loan::class);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $fund = Loan::with(['fund', 'loanType'])->find($id);
        return $this->jsonResponseOk($fund);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Loan $loan
     * @return Response
     */
    public function update(Request $request, Loan $loan)
    {
        return $this->commonUpdate($request, $loan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Loan $loan
     * @return Response
     */
    public function destroy(Loan $loan)
    {
        return $this->commonDestroy($loan);
    }
}
