<?php

namespace App\Http\Controllers;

use App\Fund;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FundController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = ($request->has('length')) ? $request->get('length') : 10;

        $modelQuery = Fund::query();
        $this->filterByDate($request, $modelQuery);

        $filterKeys = [
            'name',
            'monthly_payment'
        ];

        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }

        return $this->jsonResponseOk($modelQuery->paginate($perPage));
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
        $fund = Fund::with(['companies'])->find($id);
        return $this->jsonResponseOk($fund);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Fund $fund
     * @return Response
     */
    public function edit(Fund $fund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Fund $fund
     * @return Response
     */
    public function update(Request $request, Fund $fund)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Fund $fund
     * @return Response
     */
    public function destroy(Fund $fund)
    {
        //
    }
}
