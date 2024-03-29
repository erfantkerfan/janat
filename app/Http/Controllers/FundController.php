<?php

namespace App\Http\Controllers;

use App\Fund;
use App\Http\Requests\StoreFund;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Transaction;
use App\TransactionType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FundController extends Controller
{
    use Filter, CommonCRUD;

    public function __construct()
    {
        $this->middleware('can:view funds', ['only' => ['show', 'getIncomesAndExpenses', 'getExpenseTransactions']]);
        $this->middleware('can:create funds', ['only' => ['store']]);
        $this->middleware('can:edit funds', ['only' => ['update']]);
        $this->middleware('can:delete funds', ['only' => ['destroy']]);
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
                'name'
            ]
        ];

        return $this->commonIndex($request, Fund::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFund $request
     * @return Response
     */
    public function store(StoreFund $request)
    {
        $request->offsetSet('balance', 0);
        $fund = Fund::create($request->all());

        return $this->jsonResponseOk($fund);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $fund = Fund::find($id);
        return $this->jsonResponseOk($fund);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Fund $fund
     * @return Response
     */
    public function update(Request $request, Fund $fund)
    {
        $fund->fill($request->all());

        if ($fund->save()) {
            return $this->show($fund->id);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'fund_update' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Fund $fund
     * @return Response
     * @throws Exception
     */
    public function destroy(Fund $fund)
    {
        if ($fund->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'صندوق با موفقیت حذف شد' ]);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'fund_destroy' => [
                        'مشکلی در حذف اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    public function getIncomesAndExpenses($id) {
        $fund = Fund::findorfail($id)->setAppends(['incomes', 'expenses', 'demands']);

        return $this->jsonResponseOk($fund);
    }

    public function getExpenseTransactions($id) {
        $perPage = 10;
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_PAY_FUND_EXPENSES'))->first();
        $transactions = Transaction::with('transactionStatus')
        ->where('transactions.transaction_type_id', '=', $transactionType->id)
            ->whereHas('fundPayers', function($query) use ($id) {
                $query->where('funds.id', '=', $id);
            })
            ->paginate($perPage);

        return $this->jsonResponseOk($transactions);
    }
}
