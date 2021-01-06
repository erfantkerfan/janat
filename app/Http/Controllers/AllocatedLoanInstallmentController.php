<?php

namespace App\Http\Controllers;

use App\AllocatedLoan;
use App\AllocatedLoanInstallment;
use App\Company;
use App\Fund;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Transaction;
use App\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AllocatedLoanInstallmentController extends Controller
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
                'allocatedLoan',
                'allocatedLoan.account',
                'allocatedLoan.loan',
                'allocatedLoan.account.user:id,f_name,l_name',
            ]
        ];

        return $this->commonIndex($request,
            AllocatedLoanInstallment::class,
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
        $allocatedLoan = AllocatedLoan::findOrFail($request->get('allocated_loan_id'));
        $allocatedLoanInstallment = AllocatedLoanInstallment::create([
            'allocated_loan_id' => $allocatedLoan->id
        ]);
        return $this->show($allocatedLoanInstallment->id);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $allocatedLoanInstallment = AllocatedLoanInstallment::findOrFail($id);
        return $this->jsonResponseOk($allocatedLoanInstallment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AllocatedLoanInstallment  $allocatedLoanInstallment
     * @return Response
     */
    public function edit(AllocatedLoanInstallment $allocatedLoanInstallment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\AllocatedLoanInstallment  $allocatedLoanInstallment
     * @return Response
     */
    public function update(Request $request, AllocatedLoanInstallment $allocatedLoanInstallment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AllocatedLoanInstallment  $allocatedLoanInstallment
     * @return Response
     */
    public function destroy(AllocatedLoanInstallment $allocatedLoanInstallment)
    {
        //
    }
}
