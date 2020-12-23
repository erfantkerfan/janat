<?php

namespace App\Http\Controllers;

use App\Fund;
use App\Loan;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\CommonCRUD;

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
                'fund'
            ],
            'filterKeys'=> [
                'name',
                'loan_amount',
                'installment_rate',
                'number_of_installments',
            ],
            'filterRelationIds'=> [
                [
                    'requestKey'=> 'fund_id',
                    'relationName'=> 'fund'
                ]
            ]
        ];

        return $this->commonIndex($request, Loan::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
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
        $fund = Loan::with(['fund'])->find($id);
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
//        $loan->fill($request->all());
//
//        dd($loan);
//        if ($loan->save()) {
//            return $this->show($loan->id);
//        } else {
//            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
//        }

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
