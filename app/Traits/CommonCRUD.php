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
     * @param $config
     * @return Response
     */
    public function commonIndex(Request $request, $modelQuery, array $config = [])
    {
        $eagerLoads = $this->getDefault($config, 'eagerLoads', []);
        $filterKeys = $this->getDefault($config, 'filterKeys', []);
        $filterRelationIds = $this->getDefault($config, 'filterRelationIds', []);
        $filterRelationKeys = $this->getDefault($config, 'filterRelationKeys', []);
        $select = $this->getDefault($config, 'select', []);
        $returnModelQuery = $this->getDefault($config, 'returnModelQuery', []);
        $filterBySetAppends = $this->getDefault($config, 'filterBySetAppends', []);
        $setAppends = $this->getDefault($config, 'setAppends', []);

        $perPage = ($request->has('length')) ? $request->get('length') : 10;
        $sortation_field = $request->get('sortation_field');
        $sortation_order = $request->get('sortation_order');

        $modelQuery->with($eagerLoads);
//        $sortation = true;
//        foreach ($with as $key=>$item) {
//            $this->removeSelectSecion($item);
//            if ($item === $this->removeColumnSecion($item, $sortation_field) && strlen($this->removeColumnSecion($item, $sortation_field)) > 0) {
//                $sortation = false;
//                $column = str_replace('.', '', str_replace($item, '', $sortation_field));
//                $with[$key] = function(Illuminate\Database\Eloquent\Builder $query) use ($column, $sortation_order) {
//                    $query->orderBy($column, strtoupper($sortation_order));
//                };
//            }
//        }
//        $modelQuery->with($with);

        if ($sortation_field && $sortation_order) {
            $modelQuery->orderBy($sortation_field, strtoupper($sortation_order));
        }

        foreach ($select as $item) {
            $modelQuery->addSelect($item);
        }

        $this->filterByDate($request, $modelQuery);

        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }

        foreach ($filterRelationKeys as $item) {
            $this->filterByRelationKey($request, $item['requestKey'], $item['relationName'], $item['relationColumn'], $modelQuery);
        }

        foreach ($filterRelationIds as $item) {
            $this->filterByRelationId($request, $item['requestKey'], $item['relationName'], $modelQuery);
        }

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

    private function getDefault(array $config = [], $key, $default) {
        return isset($config[$key]) ? $config[$key] : $default;
    }

    private function removeSelectSecion(& $string) {
        if (strpos($string,':')) {
            $string = substr($string, 0, strpos($string,':'));
        }
    }

    private function removeColumnSecion($item, $sortation_field) {
        return str_replace(str_replace($item, '', $sortation_field), '', $sortation_field);
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
