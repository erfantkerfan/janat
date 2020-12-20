<?php

namespace App\Http\Controllers;

use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Transaction;
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
        $filterKeys = [
            'cost',
            'deadline_at',
            'manager_comment',
            'user_comment',
            'transaction_status_id',
            'parent_transaction_id'
        ];
        $filterRelationIds = [];
        return $this->commonIndex($request, Transaction::with('status', 'userPayers:id,f_name,l_name', 'companyPayers', 'fundRecipients', 'allocatedLoanRecipients'));
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
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $data = Transaction::with('transactionStatus', 'userPayers:id,f_name,l_name', 'companyPayers', 'fundPayers', 'fundRecipients', 'allocatedLoanRecipients', 'allocatedLoanInstallmentRecipients')->find($id);
        return $this->jsonResponseOk($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transacion
     * @return Response
     */
    public function edit(Transaction $transacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
