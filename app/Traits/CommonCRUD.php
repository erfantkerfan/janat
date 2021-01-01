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
     * @param $modelClass
     * @param array $config
     * @return Response
     */
    public function commonIndex(Request $request, $modelClass, array $config = [])
    {
        $modelQuery = $modelClass::query();
        $select = $this->getDefault($config, 'select', []);
        $scopes = $this->getDefault($config, 'scopes', []);
        $eagerLoads = $this->getDefault($config, 'eagerLoads', []);
        $filterKeys = $this->getDefault($config, 'filterKeys', []);
        $setAppends = $this->getDefault($config, 'setAppends', []);
        $returnModelQuery = $this->getDefault($config, 'returnModelQuery', []);
        $filterRelationIds = $this->getDefault($config, 'filterRelationIds', []);
        $filterRelationKeys = $this->getDefault($config, 'filterRelationKeys', []);

        $perPage = ($request->has('length')) ? $request->get('length') : 10;

        $modelQuery->with($eagerLoads);

        $this->sorting($request,$modelQuery);

        $this->select($select,$modelQuery, $modelClass);

        $this->filterByDate($request, $modelQuery);

        foreach ($scopes as $item) {
            $scopeItem = ($request->has($item)) ? $request->get($item) : false;
            if ($scopeItem) {
                $modelQuery->$item();
            }
        }

        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }

        foreach ($filterRelationKeys as $item) {
            $this->filterByRelationKey($request, $item, $modelQuery);
        }

        foreach ($filterRelationIds as $item) {
            $this->filterByRelationId($request, $item, $modelQuery);
        }

//        $this->join($modelQuery, $joins);
//        $this->join1($modelQuery, $joins1);

        if ($returnModelQuery) {
            return $modelQuery;
        }

        if (count($setAppends) > 0) {
            $attachedCoolection = $modelQuery->paginate($perPage)->getCollection()->map(function (& $item) use ($setAppends) {
                return $item->setAppends($setAppends);
            });
            return $this->jsonResponseOk($modelQuery->paginate($perPage)->setCollection($attachedCoolection));
        }


        return $this->jsonResponseOk($modelQuery->paginate($perPage));

    }

    private function select(array $select, & $modelQuery, $modelClass) {
        $tableName = (new $modelClass())->getTable();
        foreach ($select as $item) {
            if (!strpos($item, '.')) {
                $item = $tableName.'.'.$item;
            }
            $modelQuery->addSelect($item);
        }
    }

    private function sorting(Request $request, & $modelQuery) {
        $sortation_field = $request->get('sortation_field');
        $sortation_order = $request->get('sortation_order');

        if (!isset($sortation_field) || !isset($sortation_order)) {
            return;
        }

        if (!strpos($sortation_field, '.')) {
            $modelQuery->orderBy($sortation_field, strtoupper($sortation_order));
        } else {
            $modelQuery->orderByPowerJoins($sortation_field, strtoupper($sortation_order));
        }
    }

    private function getDefault(array $config = [], $key, $default) {
        return isset($config[$key]) ? $config[$key] : $default;
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
