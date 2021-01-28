<?php

namespace App\Http\Controllers;

use App\AllocatedLoan;
use App\AllocatedLoanInstallment;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

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
            'allocated_loan_id' => $allocatedLoan->id,
            'rate' => $allocatedLoan->installment_rate
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
        $allocatedLoanInstallment = AllocatedLoanInstallment::with('receivedTransactions.transactionStatus')
            ->findOrFail($id)
            ->setAppends([
                'is_settled',
                'total_payments',
                'remaining_payable_amount'
            ]);
        return $this->jsonResponseOk($allocatedLoanInstallment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AllocatedLoanInstallment $allocatedLoanInstallment
     * @return Response
     */
    public function update(Request $request, AllocatedLoanInstallment $allocatedLoanInstallment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        Validator::make([
            'allocatedLoanInstallment_id' => $id
        ], [
            'allocatedLoanInstallment_id' => 'required|exists:allocated_loan_installments,id'
        ])->validate();
        if (AllocatedLoanInstallment::find($id)->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'حذف با موفقیت انجام شد.' ]);
        } else {
            return $this->jsonResponseError('مشکلی در حذف اطلاعات رخ داده است.');
        }
    }
}
