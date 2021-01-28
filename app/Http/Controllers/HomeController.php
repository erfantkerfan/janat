<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the welcome page.
     *
     * @return Renderable
     */
    public function welcome()
    {
//        return redirect('/login');
        return view('landing');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return Renderable
     */
    public function dashboard(Request $request)
    {
        $user = User::with(['accounts.fund', 'company', 'status', 'roles'])->find($request->user()->id)->makeHidden('user_pic');
        return view('dashboard', ['user'=> $user]);
    }
}
