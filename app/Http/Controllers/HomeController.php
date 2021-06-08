<?php

namespace App\Http\Controllers;

use App\Fund;
use App\Loan;
use App\User;
use App\Setting;
use App\Company;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    use Filter;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['welcome', 'index']
        ]);
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
        $user = User::with(['accounts.fund', 'status', 'roles'])
            ->find($request->user()->id)
            ->makeHidden('user_pic');
        $settings = Setting::get();
        return view('dashboard', compact('user', 'settings'));
    }

    public function debug(Request $request)
    {

//        dd(Auth::user()->hasRole('Super Admin'));
        dd(Auth::user()->can('edit users'));
//        dd(Loan::all()[0]->number_of_installments);
    }

    public function dashboardData() {
        $funds = Fund::all();
        $counts = [
            'users' => User::count(),
            'funds' => Fund::count(),
            'companies' => Company::count(),
            'loans' => Loan::count()
        ];
        return $this->jsonResponseOk([
            'funds' => $funds,
            'counts' => $counts
        ]);
    }
}
