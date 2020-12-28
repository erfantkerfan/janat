<?php

namespace App\Http\Controllers;

use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\UserStatus;
use App\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserTypeController extends Controller
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
            ]
        ];

        return $this->commonIndex($request, UserType::class, $config);
    }
}
