<?php

namespace App\Http\Controllers\Auth;

use App\Company;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\UserStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Show the application registration form.
     *
     * @return Factory|View
     */
    public function showRegistrationForm()
    {
        $companies = Company::all();
        return view('auth.register', compact('companies'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return string
     */
    public function register(Request $request)
    {
        $pendingStatus = UserStatus::where('name', 'pending')->first();

        $request->offsetSet('status_id', $pendingStatus->id);

        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->registered($request, $user);

        return 'ثبت نام شما با موفقیت انحام شد';
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'SSN' => ['required', 'string', 'max:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'salary' => ['required', 'numeric'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'mobile' => ['required', 'string'],
            'company_id' => ['required', 'exists:company,id'],
            'status_id' => ['required', 'exists:user_status,id']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'f_name' => $data['f_name'],
            'l_name' => $data['l_name'],
            'SSN' => $data['SSN'],
            'password' => Hash::make($data['password']),
            'salary' => $data['salary'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'mobile' => $data['mobile'],
            'company_id' => $data['company_id'],
            'status_id' => $data['status_id'],
        ]);
    }
}
