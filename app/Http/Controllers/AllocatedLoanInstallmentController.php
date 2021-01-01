<?php

namespace App\Http\Controllers;

use App\AllocatedLoanInstallment;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
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
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AllocatedLoanInstallment  $allocatedLoanInstallment
     * @return Response
     */
    public function show(AllocatedLoanInstallment $allocatedLoanInstallment)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
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
