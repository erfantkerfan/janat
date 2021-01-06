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
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransacionController extends Controller
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
                'transactionStatus',
                'relatedPayers.transactionPayers',
                'relatedRecipients.transactionRecipients'
            ],
            'filterKeys'=> [
                'cost',
                'deadline_at',
                'manager_comment',
                'user_comment',
                'transaction_status_id',
                'parent_transaction_id'
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'user_id',
                    'relationName' => 'userPayers'
                ],
                [
                    'requestKey' => 'loan_id',
                    'relationName' => 'allocatedLoanRecipients.loan'
                ],
                [
                    'requestKey' => 'company_id',
                    'relationName' => 'companyPayers'
                ],
                [
                    'requestKey' => 'fund_id',
                    'orWhereHas' => true,
                    'relationNames' => [
                        'fundPayers',
                        'fundRecipients'
                    ]
                ]
            ],
        ];

        return $this->commonIndex($request, Transaction::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $transactionStatus = TransactionStatus::findOrFail($request->get('transaction_status_id'));
        $cost = $request->get('cost');
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $request->get('manager_comment'),
            'user_comment' => $request->get('user_comment'),
            'transaction_status_id' => $transactionStatus->id,
            'deadline_at' => $request->get('deadline_at'),
            'paid_at' => $request->get('paid_at')
        ]);
        $this->attachTransaction($request, $transaction);
        return $this->show($transaction->id);
    }

    private function attachTransaction(Request $request, Transaction $transaction) {
        $transaction_type = $request->get('transaction_type');

        if ($transaction_type === 'user_charge_fund') {
            $this->attachAndChangeFundBalance_userChargeFund($request, $transaction);
        } else if ($transaction_type === 'company_charge_fund') {
            $this->attachAndChangeFundBalance_companyChargeFund($request, $transaction);
        } else if ($transaction_type === 'fund_pay_loan') {
            $this->attachAndChangeFundBalance_fundPayLoan($request, $transaction);
        } else if ($transaction_type === 'user_pay_installment') {
            $this->attachAndChangeFundBalance_userPayInstallment($request, $transaction);
        }
    }

    private function attachAndChangeFundBalance_userChargeFund(Request $request, Transaction $transaction) {
        $user = User::findOrFail($request->get('user_id'));
        $account = User::findOrFail($request->get('account_id'));
        $fund = $account->fund()->first();
        $cost = $request->get('cost');
        $transaction->userPayers()->attach($user, ['cost'=> $cost]);
        $transaction->fundRecipients()->attach($fund, ['cost'=> $cost]);
        $fund->deposit($cost);
    }

    private function attachAndChangeFundBalance_companyChargeFund(Request $request, Transaction $transaction) {
        $company = Company::findOrFail($request->get('company_id'));
        $fund = $company->fund()->first();
        $cost = $request->get('cost');
        $transaction->companyPayers()->attach($company, ['cost'=> $cost]);
        $transaction->fundRecipients()->attach($fund, ['cost'=> $cost]);
        $fund->deposit($cost);
    }

    private function attachAndChangeFundBalance_fundPayLoan(Request $request, Transaction $transaction) {
        $allocatedLoan = AllocatedLoan::findOrFail($request->get('allocated_loan_id'));
        $fund = $allocatedLoan->account->fund()->first();
        $cost = $request->get('cost');
        $transaction->fundPayers()->attach($fund, ['cost'=> $cost]);
        $transaction->allocatedLoanRecipients()->attach($allocatedLoan, ['cost'=> $cost]);
        $fund->withdrawal($cost);
    }

    private function attachAndChangeFundBalance_userPayInstallment(Request $request, Transaction $transaction) {
        $allocatedLoanInstallment = AllocatedLoanInstallment::findOrFail($request->get('allocated_loan_installment_id'));
        $user = $allocatedLoanInstallment->allocatedLoan->account->user()->first();
        $cost = $request->get('cost');
        $transaction->userPayers()->attach($user, ['cost'=> $cost]);
        $transaction->allocatedLoanInstallmentRecipients()->attach($allocatedLoanInstallment, ['cost'=> $cost]);
        $fund = $allocatedLoanInstallment->allocatedLoan->loan->fund;
        $fund->deposit($cost);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $data = Transaction::with([
            'transactionStatus',
            'userPayers:id,f_name,l_name',
            'relatedPayers.transactionPayers',
            'relatedRecipients.transactionRecipients'
        ])->find($id);
        return $this->jsonResponseOk($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Transaction  $transacion
     * @return Response
     */
    public function update(Request $request, Transaction $transacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transacion
     * @return Response
     */
    public function destroy(Transaction $transacion)
    {
        //
    }
}
