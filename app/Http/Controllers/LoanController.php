<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLoanRequest;
use App\Loan;
use App\Setting;
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
        $this->prepareLoanData($request);
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
     * @param UpdateLoanRequest $request
     * @param Loan $loan
     * @return Response
     */
    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        $this->prepareLoanData($request);
        return $this->commonUpdate($request, $loan);
    }

    private function prepareLoanData(& $request) {
        // LoanCalculator
        $loanCalculator = new LoanCalculator();
        $loanInterestPerMonth = Setting::where('name', 'loan_interest_per_month')->first()->value;
        $interestRate = $loanCalculator->getInterestRate($request->get('loan_amount'),
            $loanInterestPerMonth,
            $request->get('number_of_installments'));
        $interestAmount = $loanCalculator->getInterestAmount($loanInterestPerMonth,
            $request->get('number_of_installments'));
        $roundedInstallmentsRate = $loanCalculator->getRoundedInstallmentsRate($request->get('loan_amount'),
            $interestAmount,
            $request->get('number_of_installments'));

        // $request offsetSet
        $request->offsetSet('installment_rate', $roundedInstallmentsRate);
        $request->offsetSet('interest_amount', $interestAmount);
        $request->offsetSet('interest_rate', $interestRate);
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
