<?php

namespace App\Http\Controllers;

use App\Company;
use App\Traits\Filter;
use App\Traits\CommonCRUD;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    use Filter;
    use CommonCRUD;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $filterKeys = ['name'];
        $filterRelationKeys = [
            [
                'requestKey'=> 'fund_id',
                'relationName'=> 'fund'
            ]
        ];
        return $this->commonIndex($request, Company::with('fund'), $filterKeys, $filterRelationKeys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->commonStore($request, Company::class);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $fund = Company::with(['fund'])->find($id);
        return $this->jsonResponseOk($fund);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Company $company
     * @return Response
     */
    public function update(Request $request, Company $company)
    {
        return $this->commonUpdate($request, $company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Company $company
     * @return Response
     * @throws Exception
     */
    public function destroy(Company $company)
    {
        return $this->commonDestroy($company);
    }
}
