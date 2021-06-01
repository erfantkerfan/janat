<?php

namespace App\Http\Controllers;

use Exception;
use App\Loan;
use App\Setting;
use App\Traits\Filter;
use App\Traits\CommonCRUD;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Classes\LoanCalculator;

class SettingController extends Controller
{
    use Filter, CommonCRUD;

    public function __construct()
    {
        $this->middleware('can:view settings', ['only' => ['index', 'show']]);
        $this->middleware('can:create settings', ['only' => ['store']]);
        $this->middleware('can:edit settings', ['only' => ['update']]);
        $this->middleware('can:delete settings', ['only' => ['destroy']]);
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
                'name',
                'display_name',
                'value',
                'order'
            ]
        ];

        return $this->commonIndex($request, Setting::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $setting = Setting::create($request->all());

        return $this->jsonResponseOk($setting);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        return $this->jsonResponseOk($setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Setting $setting
     * @return Response
     */
    public function update(Request $request, Setting $setting)
    {
        $setting->fill($request->all());

        if ($setting->save()) {
            $this->updateAllLoan();
            return $this->show($setting->id);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'setting_update' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    private function updateAllLoan () {
        $loans = Loan::all();
        $loans->each(function ($loanItem) {
            // LoanCalculator
            $loanCalculator = new LoanCalculator();
            [
                $installmentRate,
                $interestAmount,
                $interestRate
            ] = $loanCalculator->prepareLoanData($loanItem->loan_amount, $loanItem->number_of_installments);
            $loanItem->interest_rate = $interestRate;
            $loanItem->interest_amount = $interestAmount;
            $loanItem->installment_rate = $installmentRate;
            $loanItem->save();
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Setting $setting
     * @return Response
     * @throws Exception
     */
    public function destroy(Setting $setting)
    {
        if ($setting->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'تنظیمات با موفقیت حذف شد' ]);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'setting_destroy' => [
                        'مشکلی در حذف اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }
}
