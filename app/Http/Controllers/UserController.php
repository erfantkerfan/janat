<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\CommonCRUD;
use App\User;
use Exception;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    use Filter, CommonCRUD;

    public function __construct()
    {
        $this->middleware('can:view users', ['only' => ['index']]);
        $this->middleware('can:create users', ['only' => ['store']]);
        $this->middleware('can:edit users', ['only' => ['update', 'setUserPic']]);
        $this->middleware('can:delete users', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function index(Request $request)
    {
        $config = [
            'filterKeys'=> [
                'f_name',
                'l_name',
                'staff_code',
                'SSN',
                'phone',
                'mobile'
            ],
            'filterKeysExact'=> [
                'id',
                'status_id'
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'fund_id',
                    'relationName' => 'accounts.fund'
                ],
                [
                    'requestKey' => 'company_id',
                    'relationName' => 'accounts.company'
                ]
            ],
            'select'=> [
                'id', 'f_name','l_name','SSN', 'staff_code', 'phone', 'mobile', 'created_at'
            ],
            'scopes'=> [
                'hasLoanPayrollDeduction',
                'hasAccountPayrollDeduction'
            ],
            'eagerLoads'=> [
                'accounts.company'
            ]
        ];

        return $this->commonIndex($request, User::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return Response
     */
    public function store(StoreUserRequest $request)
    {
        $request->offsetSet('password', Hash::make($request->get('password')));

        return $this->commonStore($request, User::class);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return ResponseFactory|Response
     */
    public function show($id)
    {
        $this->checkOwner($id);

        $user = User::with([
                'accounts.fund',
                'accounts.company',
                'accounts.allocatedLoans',
                'accounts.allocatedLoans.loan',
                'accounts.allocatedLoans.installments',
                'status',
                'userType',
                'roles'
            ])
            ->findOrFail($id)
            ->setAppends([
                'count_of_allocated_loans',
                'count_of_settled_allocated_loans'
            ]);
//            ->makeHidden('user_pic');

        $user->accounts->map(function ($account) {
            return $account->allocatedLoans->map(function ($allocatedLoan) {
                return $allocatedLoan->setAppends([
                    'is_settled',
                    'total_payments',
                    'remaining_payable_amount',
                    'count_of_paid_installments',
                    'count_of_remaining_installments'
                ]);
            });
        });

        return $this->jsonResponseOk($user);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return string
     */
    public function getUserPic($id)
    {
        $this->checkOwner($id);

        $user = User::select('user_pic')->find($id);

        if (isset($user)) {
            $user->makeVisible('user_pic');
        }

        $blobData = '';
        if (!isset($user) || strlen($user->user_pic) === 0) {
            $blobData = File::get(public_path('img/faces/sample-avatar.png'));
        } else {
            $blobData = $user->user_pic;
        }
        return 'data:image/jpeg;base64,'.base64_encode( $blobData );
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return string
     */
    public function setUserPic(Request $request)
    {
        if (!$request->file('user_pic') || !$request->get('id')) {
            return $this->jsonResponseServerError([
                'errors' => [
                    'user_setUserPic' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }

        $user = User::findOrFail($request->get('id'))->makeVisible('user_pic');
        $user->user_pic = File::get($request->file('user_pic'));

        if ($user->save()) {
            return $this->getUserPic($user->id);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'user_setUserPic' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return ResponseFactory|Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        return $this->commonUpdate($request, $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ResetUserPasswordRequest $request
     * @param User $user
     * @return ResponseFactory|Response
     */
    public function resetPass(ResetUserPasswordRequest $request, User $user)
    {
        $this->checkOwner($user->id);

        $oldPassword = $request->get('old_password');
        $newPassword = Hash::make($request->get('new_password'));
        $user = User::findOrFail($user->id)->makeHidden('user_pic')->makeVisible('password');
        $oldUserPass = $user->password;

        if (!$request->user()->hasRole('Super Admin') && !Hash::check($oldPassword, $oldUserPass)) {
            return $this->jsonResponseValidateError([
                'errors' => [
                    'user_resetPass' => [
                        'نام کاربری قبلی صحیح نمی باشد'
                    ]
                ]
            ]);
        }

        $user->password = $newPassword;

        if ($user->save()) {
            $user = User::with(['accounts', 'status'])->findOrFail($user->id)->makeHidden('user_pic');
            return $this->jsonResponseOk($user);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'user_resetPass' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $userWithHasNotSettledLoan = $user->setAppends(['hasNotSettledLoan', 'totalCredit']);
        $errors = [];
        if ($userWithHasNotSettledLoan->hasNotSettledLoan) {
            $errors['has_relations'] = [
                'کاربر انتخاب شده دارای وام تسویه نشده است.'
            ];
        }
        if ($userWithHasNotSettledLoan->totalCredit > 0) {
            $errors['has_credit'] = [
                'کاربر انتخاب شده دارای موجودی در صندوق است.'
            ];
        }

        if (count($errors) > 0) {
            return $this->jsonResponseValidateError([
                'errors' => $errors
            ]);
        }

        return $this->commonDestroy($user);
    }

    public function getTotalBalance($id) {
        $user = User::with([
            'accounts'
        ])
            ->findOrFail($id);

        $user->accounts->map(function ($item) {
            return $item->setAppends(['balance']);
        });

        return $this->jsonResponseOk($user->accounts->sum('balance'));
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function import()
    {
        try {
            Excel::import(new UsersImport, request()->file('users'));
            return $this->jsonResponseOk([
                'message'=> 'ورود اطلاعات موفقیت آمیز بود.'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors [] = [
                    'row' => 'برای ردیف شماره ' . ($failure->row() - 1) . ' ' . implode(' - ',$failure->errors()),
                    'values' => $failure->values()
                ];
//                $failure->row(); // row that went wrong
//                $failure->attribute(); // either heading key (if using heading row concern) or column index
//                $failure->errors(); // Actual error messages from Laravel validator
//                $failure->values(); // The values of the row that has failed.
            }

            return $this->jsonResponseValidateError([
                'errors' => $errors
            ]);
        }
    }
}
