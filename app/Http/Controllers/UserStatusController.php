<?php

namespace App\Http\Controllers;

use App\UserStatus;
use App\Traits\Filter;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    use Filter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modelQuery = UserStatus::query();
        $this->filterByDate($request, $modelQuery);

        $filterKeys = ['name'];

        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }

        return $this->jsonResponseOk($modelQuery->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserStatus  $userStatus
     * @return \Illuminate\Http\Response
     */
    public function show(UserStatus $userStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserStatus  $userStatus
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserStatus $userStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserStatus  $userStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserStatus $userStatus)
    {
        //
    }
}
