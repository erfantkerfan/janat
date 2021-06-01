<?php

namespace App\Http\Controllers;

use App\Account;
use App\AllocatedLoan;
use App\Http\Requests\PeriodicPayrollDeductionRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\StoreTransaction;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Transaction;
use App\TransactionType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    use Filter, CommonCRUD;

    public function __construct()
    {
        $this->middleware('can:view accounts', ['only' => ['showPeriodicPayrollDeductionForChargeFund']]);
        $this->middleware('can:create accounts', ['only' => ['store']]);
        $this->middleware('can:edit accounts', ['only' => ['update', 'payPeriodicPayrollDeductionForChargeFund', 'rollbackPayPeriodicPayrollDeduction']]);
        $this->middleware('can:delete accounts', ['only' => ['destroy']]);
    }

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
            'filterRelationIds'=> [
                [
                    'requestKey' => 'user_id',
                    'relationName' => 'user'
                ]
            ],
            'eagerLoads'=> [
                'user:id,f_name,l_name', 'fund', 'allocatedLoans'
            ]
        ];

        if(!Auth::user()->can('view accounts')) {
            $request->offsetSet('user_id', Auth::user()->id);
        }

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
        $this->checkOwner($account->user->id);
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

    public function showPeriodicPayrollDeductionForChargeFund(PeriodicPayrollDeductionRequest $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');
        $companyId = $request->get('company_id');

        $targetAccount = Account::with(['user:id,f_name,l_name,staff_code', 'fund', 'allocatedLoans'])
            ->hasPayrollDeduction()
            ->hasCompany($companyId)
            ->lastPayrollDeductionForChargeFundPaidAt('>=', $lastPaidAtAfter, '<=', $lastPaidAtBefore)
            ->get();
        $setAppends = ['balance'];
        $targetAccount->map(function (& $item) use ($setAppends) {
            return $item->setAppends($setAppends);
        });

        return $this->jsonResponseOk($targetAccount);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function payPeriodicPayrollDeductionForChargeFund(PeriodicPayrollDeductionRequest $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');
        $companyId = $request->get('company_id');

        $targetAccount = Account::with(['user:id,f_name,l_name,staff_code', 'fund', 'allocatedLoans'])
            ->hasPayrollDeduction()
            ->hasCompany($companyId)
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
            $setAppends = ['balance'];
            $targetAccount->map(function (& $item) use ($setAppends) {
                return $item->setAppends($setAppends);
            });
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
     * @param Request $request
     * @return Response
     */
    public function rollbackPayPeriodicPayrollDeduction(PeriodicPayrollDeductionRequest $request)
    {
        $lastPaidAtAfter = $request->get('pay_since_date');
        $lastPaidAtBefore = $request->get('pay_till_date');
        $companyId = $request->get('company_id');

        Transaction::whereHas('transactionType', function ($query) use ($lastPaidAtAfter, $lastPaidAtBefore) {
            $query->where('transaction_types.name', '=', config('constants.TRANSACTION_TYPE_USER_CHARGE_FUND'));
        })
        ->where('transaction_status_id', '=', 1)
        ->where('paid_as_payroll_deduction', '=', 1)
        ->where('paid_at', '>=', $lastPaidAtAfter)
        ->where('paid_at', '<=', $lastPaidAtBefore)
        ->delete();

        return $this->jsonResponseOk(null);
    }

    /**
     * @param Account $account
     * @return Response
     */
    public function getBalance(Account $account)
    {
        $account->setAppends(['balance']);
        return $this->jsonResponseOk($account->balance);
    }
}
