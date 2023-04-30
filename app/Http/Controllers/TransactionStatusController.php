<?php

namespace App\Http\Controllers;

use App\Traits\Filter;
use App\Traits\CommonCRUD;
use App\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionStatusController extends Controller
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
                'display_name',
                'description'
            ],
            'filterKeysExact'=> [
                'id',
            ],
        ];

        return $this->commonIndex($request, TransactionStatus::class, $config);
    }
}
