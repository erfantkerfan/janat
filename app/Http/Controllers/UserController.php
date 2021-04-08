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

class UserController extends Controller
{
    use Filter, CommonCRUD;

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
                'id',
                'f_name',
                'l_name',
                'staff_code',
                'SSN',
                'phone',
                'mobile',
                'company_id',
                'status_id'
            ],
            'filterRelationIds'=> [
                [
                    'requestKey' => 'fund_id',
                    'relationName' => 'accounts.fund'
                ]
            ],
            'select'=> [
                'id', 'f_name','l_name','SSN', 'staff_code', 'phone', 'mobile', 'created_at'
            ],
            'scopes'=> [
                'hasLoanPayrollDeduction',
                'hasAccountPayrollDeduction'
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
        $user = User::with([
                'accounts.fund',
                'accounts.allocatedLoans',
                'accounts.allocatedLoans.loan',
                'accounts.allocatedLoans.installments',
                'company',
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


        $user->accounts->map(function (& $account) {
            return $account->allocatedLoans->map(function (& $allocatedLoan) {
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
        $oldPass = $request->get('oldPass');
        $newPass = Hash::make($request->get('newPass'));
        $user = User::findOrFail($user->id)->makeHidden('user_pic')->makeVisible('password');
        $oldUserPass = $user->password;

        if (!$request->user()->hasRole('admin') && !Hash::check($oldPass, $oldUserPass)) {
            return $this->jsonResponseServerError([
                'errors' => [
                    'user_resetPass' => [
                        'نام کاربری قبلی صحیح نمی باشد'
                    ]
                ]
            ]);
        }

        $user->password = $newPass;

        if ($user->save()) {
            $user = User::with(['accounts', 'company', 'status'])->findOrFail($user->id)->makeHidden('user_pic');
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

        $user->accounts->map(function (& $item) {
            return $item->setAppends(['balance']);
        });

        return $this->jsonResponseOk($user->accounts->sum('balance'));
    }
}
