<?php

namespace App\Http\Controllers;

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
    use Filter;
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function index(Request $request)
    {
        $perPage = ($request->has('length')) ? $request->get('length') : 10;

        $modelQuery = User::select('id', 'f_name','l_name','SSN', 'phone', 'mobile');
        $this->filterByDate($request, $modelQuery);

        $filterKeys = [
            'f_name',
            'l_name',
            'SSN',
            'phone',
            'mobile',
            'company_id',
            'status_id'
        ];

        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }

        return $this->jsonResponseOk($modelQuery->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $user = User::create($request->all());

        return $this->jsonResponseOk($user);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return ResponseFactory|Response
     */
    public function show($id)
    {
        $user = User::with(['accounts.fund', 'company', 'status', 'roles'])->findOrFail($id)->makeHidden('user_pic');
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
        $user = User::select('user_pic')->findOrFail($id);
        $blobData = '';
        if (strlen($user->user_pic) === 0) {
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
            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
        }

        $user = User::findOrFail($request->get('id'));
        $user->user_pic = File::get($request->file('user_pic'));

        if ($user->save()) {
            return $this->getUserPic($user->id);
        } else {
            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return ResponseFactory|Response
     */
    public function update(Request $request, User $user)
    {
        $user->fill($request->all());

        if ($user->save()) {
            $user = User::with(['accounts', 'company', 'status'])->findOrFail($user->id)->makeHidden('user_pic');
            return $this->jsonResponseOk($user);
        } else {
            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return ResponseFactory|Response
     */
    public function resetPass(Request $request, User $user)
    {
        $oldPass = $request->get('oldPass');
        $newPass = Hash::make($request->get('newPass'));
        $user = User::findOrFail($user->id)->makeHidden('user_pic')->makeVisible('password');
        $oldUserPass = $user->password;

        if (!$request->user()->hasRole('admin') && !Hash::check($oldPass, $oldUserPass)) {
            return $this->jsonResponseError('نام کاربری قبلی صحیح نمی باشد');
        }

        $user->password = $newPass;

        if ($user->save()) {
            $user = User::with(['accounts', 'company', 'status'])->findOrFail($user->id)->makeHidden('user_pic');
            return $this->jsonResponseOk($user);
        } else {
            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
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
        if ($user->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'کاربر با موفقیت حذف شد' ]);
        } else {
            return $this->jsonResponseError('مشکلی در حذف اطلاعات رخ داده است.');
        }
    }
}
