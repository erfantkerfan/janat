<?php

namespace App\Http\Controllers;

use App\Account;
use App\Traits\Filter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    use Filter;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
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
        Account::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Account $account
     * @return Response
     */
    public function update(Request $request, Account $account)
    {
        $account->fill($request->all());

        if ($account->save()) {
            $account = Account::find($account->id);
            return $this->jsonResponseOk($account);
        } else {
            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
        }
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
        if ($account->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'حساب با موفقیت حذف شد' ]);
        } else {
            return $this->jsonResponseError('مشکلی در حذف اطلاعات رخ داده است.');
        }
    }
}
