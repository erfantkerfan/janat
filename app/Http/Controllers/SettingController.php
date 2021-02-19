<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingController extends Controller
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
                'value',
                'order'
            ]
        ];

        return $this->commonIndex($request, Setting::class, $config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $setting = Setting::create($request->all());

        return $this->jsonResponseOk($setting);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        return $this->jsonResponseOk($setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Setting $setting
     * @return Response
     */
    public function update(Request $request, Setting $setting)
    {
        $setting->fill($request->all());

        if ($setting->save()) {
            return $this->show($setting->id);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'setting_update' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Setting $setting
     * @return Response
     * @throws Exception
     */
    public function destroy(Setting $setting)
    {
        if ($setting->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'تنظیمات با موفقیت حذف شد' ]);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'setting_destroy' => [
                        'مشکلی در حذف اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }
}
