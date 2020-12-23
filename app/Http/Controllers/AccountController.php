<?php

namespace App\Http\Controllers;

use App\Account;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
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
            'eagerLoads'=> [
                'user:id,f_name,l_name', 'fund', 'allocatedLoans'
            ]
        ];

        return $this->commonIndex($request, Account::query(), $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
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
}
