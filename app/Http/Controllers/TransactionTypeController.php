<?php

namespace App\Http\Controllers;

use App\Traits\Filter;
use App\TransactionType;
use App\Traits\CommonCRUD;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionTypeController extends Controller
{
    use Filter, CommonCRUD;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request): Response
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

        return $this->commonIndex($request, TransactionType::class, $config);
    }
}
