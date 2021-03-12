<?php

namespace App\Http\Controllers;

use App\Account;
use App\AllocatedLoan;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\StoreTransaction;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
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
            'filterKeys'=> [
                'payroll_deduction',
                'monthly_payment'
            ],
            'eagerLoads'=> [
                'user:id,f_name,l_name', 'fund', 'allocatedLoans'
            ]
        ];

        return $this->commonIndex($request, Account::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAccountRequest $request
     * @return Response
     */
    public function store(StoreAccountRequest $request)
    {
        return $this->commonStore($request, Account::class);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $account = Account::find($id);
        return $this->jsonResponseOk($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Account $account
     * @return Response
     */
    public function update(Request $request, Account $account)
    {
        return $this->commonUpdate($request, $account);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     * @return Response
     * @throws Exception
     */
    public function destroy(Account $account)
    {
        return $this->commonDestroy($account);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function payPeriodicPayrollDeductionForChargeFund(Request $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');

        $targetAccount = Account::with(['user:id,f_name,l_name', 'fund', 'allocatedLoans'])
            ->hasPayrollDeduction()
            ->lastPayrollDeductionForChargeFundNotPaidAt('>=', $lastPaidAtAfter, '<=', $lastPaidAtBefore)
            ->get();

        $hasProblem = false;
        foreach ($targetAccount as $accountItem) {
            $request = new StoreTransaction();
            $request->replace([
                'transaction_status_id' => 1,
                'paid_as_payroll_deduction' => 1,
                'cost' => $accountItem->monthly_payment,
                'paid_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'transaction_type' => 'user_charge_fund',
                'account_id' => $accountItem->id
            ]);
            $transactionController = new TransactionController();
            $storeTransactionResult = $transactionController->store($request);
            if ($storeTransactionResult->getStatusCode() !== 200) {
                $hasProblem = true;
            }
        }

        if (!$hasProblem) {
            return $this->jsonResponseOk($targetAccount);
        } else {
            return $this->jsonResponseValidateError([
                'errors' => [
                    'has_unsettled_installment' => [
                        'مشکلی در ثبت پرداخت ها رخ داده است. لطفا مجددا تلاش کنید.'
                    ]
                ]
            ]);
        }
    }

    /**
     * @param Account $account
     * @return Response
     */
    public function getBalance(Account $account)
    {
        return $this->jsonResponseOk($account->totalPaidSalaries());
    }
}
