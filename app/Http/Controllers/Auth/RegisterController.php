<?php

namespace App\Http\Controllers\Auth;

use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Providers\RouteServiceProvider;
use App\User;
use App\UserStatus;
use App\UserType;
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
        $userType = UserType::where('name', 'Temporary member')->first();

        $request->merge([
            'status_id' => $pendingStatus->id,
            'user_type_id' => $userType->id
        ]);

        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request)));
//        event(new Registered($user = $this->create($request->all())));

        $this->registered($request, $user);

        $registeredMessage = 'ثبت نام شما با موفقیت انجام شد';
        return view('auth.registeredMessage', compact('registeredMessage'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, (new StoreUserRequest())->rules());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param Request $request
     * @return User
     */
    protected function create(Request $request)
    {
        return User::create($request->all());
//        return (new User())->fill($request->all());

        return User::create([
            'f_name' => $data['f_name'],
            'l_name' => $data['l_name'],
            'father_name' => $data['father_name'],
            'SSN' => $data['SSN'],
            'staff_code' => $data['staff_code'],
            'password' => Hash::make($data['password']),
            'salary' => $data['salary'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'description' => $data['description'],
            'company_id' => $data['company_id'],
            'status_id' => $data['status_id'],
        ]);
    }
}
