<?php

namespace App\Http\Controllers;

use App\LoanType;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanTypeController extends Controller
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
                'name',
                'display_name'
            ]
        ];

        return $this->commonIndex($request, LoanType::class, $config);
    }
}
