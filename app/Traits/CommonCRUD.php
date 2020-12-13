<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait CommonCRUD
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $modelQuery
     * @param array $filterKeys
     * @param array $filterRelationKeys
     * @param array $select
     * @return Response
     */
    public function commonIndex(Request $request, $modelQuery, array $filterKeys = [], array $filterRelationKeys = [], array $select = [])
    {
        $perPage = ($request->has('length')) ? $request->get('length') : 10;

        foreach ($select as $item) {
            $modelQuery->addSelect($item);
        }

        $this->filterByDate($request, $modelQuery);

        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }

        foreach ($filterRelationKeys as $item) {
            $this->filterByRelationId($request, $item['requestKey'], $item['relationName'], $modelQuery);
        }

        return $this->jsonResponseOk($modelQuery->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $modelClass
     * @return Response
     */
    public function commonStore(Request $request, $modelClass)
    {
        $createdModel = $modelClass::create($request->all());
        return $this->show($createdModel->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $model
     * @return Response
     */
    public function commonUpdate(Request $request, $model)
    {
        $model->fill($request->all());

        if ($model->save()) {
            return $this->show($model->id);
        } else {
            return $this->jsonResponseError('مشکلی در ویرایش اطلاعات رخ داده است.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $model
     * @return Response
     */
    public function commonDestroy($model)
    {
        if ($model->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'حذف با موفقیت انجام شد.' ]);
        } else {
            return $this->jsonResponseError('مشکلی در حذف اطلاعات رخ داده است.');
        }
    }
}
