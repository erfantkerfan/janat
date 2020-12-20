<?php

namespace App\Http\Controllers;

use App\Traits\CommonCRUD;
use App\UserStatus;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserStatusController extends Controller
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
        $filterKeys = ['name'];
        return $this->commonIndex($request, UserStatus::query(), $filterKeys);
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
     * @param  \App\UserStatus  $userStatus
     * @return Response
     */
    public function show(UserStatus $userStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserStatus  $userStatus
     * @return Response
     */
    public function edit(UserStatus $userStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserStatus  $userStatus
     * @return Response
     */
    public function update(Request $request, UserStatus $userStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserStatus  $userStatus
     * @return Response
     */
    public function destroy(UserStatus $userStatus)
    {
        //
    }
}
