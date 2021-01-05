<?php

namespace App\Http\Controllers;

use App\AllocatedLoan;
use App\AllocatedLoanInstallment;
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
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $allocatedLoan = AllocatedLoan::findOrFail($request->get('allocated_loan_id'));
        $transactionStatus = TransactionStatus::findOrFail($request->get('transaction_status_id'));

        $allocatedLoanInstallment = AllocatedLoanInstallment::create([
            'allocated_loan_id' => $allocatedLoan->id
        ]);
        $user = $allocatedLoanInstallment->allocatedLoan->account->user()->first();
        $cost = $request->get('cost');
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $request->get('manager_comment'),
            'user_comment' => $request->get('user_comment'),
            'transaction_status_id' => $transactionStatus->id,
            'deadline_at' => $request->get('deadline_at'),
            'paid_at' => $request->get('paid_at')
        ]);
        $transaction->userPayers()->attach($user, ['cost'=> $cost]);
        $transaction->allocatedLoanInstallmentRecipients()->attach($allocatedLoanInstallment, ['cost'=> $cost]);

        $fund = $allocatedLoanInstallment->allocatedLoan->loan->fund;
        $fund->deposit($cost);
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
