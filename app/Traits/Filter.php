<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait Filter
{
    private function filterByRelationId(Request $request, $filterData, & $modelQuery) {
        $requestKey = $filterData['requestKey'];
        $relationName = (isset($filterData['relationName'])) ? $filterData['relationName'] : null;
        $relationNames = (isset($filterData['relationNames'])) ? $filterData['relationNames'] : null;
        $orWhereHas = (isset($filterData['orWhereHas'])) ? $filterData['orWhereHas'] : false;

        $relationIds = $request->get($requestKey);
        if (!isset($relationIds)) {
            return;
        }
        if (is_array($relationIds) && count($relationIds) === 0) {
            return;
        }
        if ($orWhereHas && is_array($relationNames) && count($relationNames) === 0) {
            return;
        }

        if (!is_array($relationNames)) {
            $relationNames = [$relationNames];
        }
        if (!is_array($relationIds)) {
            $relationIds = [$relationIds];
        }

        if ($orWhereHas) {
            foreach ($relationNames as $relationNameItem) {
                $modelQuery->orWhereHas($relationNameItem, function (Builder $query) use ($relationIds) {
                    $tableName = with($query)->getModel()->getTable();
                    $query->whereIn($tableName.'.id', $relationIds);
                });
            }
        } else {
            $modelQuery->whereHas($relationName, function (Builder $query) use ($relationIds) {
                $tableName = with($query)->getModel()->getTable();
                $query->whereIn($tableName.'.id', $relationIds);
            });
        }
    }

    private function filterByRelationKey(Request $request, $filterData, & $modelQuery) {

        $requestKey = $filterData['requestKey'];
        $relationName = (isset($filterData['relationName'])) ? $filterData['relationName'] : null;
        $relationColumn = (isset($filterData['relationColumn'])) ? $filterData['relationColumn'] : null;

        $name = $request->get($requestKey);
        if (!isset($name)) {
            return;
        }
        $modelQuery->whereHas($relationName, function (Builder $query) use ($name, $relationColumn) {
            $query->where($relationColumn, 'like', '%' . $name . '%');
        });
    }

    private function filterByKey($request, $key, & $modelQuery) {
        $keyValue = trim($request->get($key));
        if (isset($keyValue) && strlen($keyValue) > 0) {
            $modelQuery = $modelQuery->where($key, 'like', '%' . $keyValue . '%');
        }
    }

    private function filterByDate($request, & $modelQuery) {
        $createdSinceDate  = $request->get('createdSinceDate');
        $createdTillDate   = $request->get('createdTillDate');
        if (strlen($createdSinceDate) > 0 && strlen($createdTillDate) > 0) {
            $createdSinceDate = Carbon::parse($createdSinceDate)->format('Y-m-d') . ' 00:00:00';
            $createdTillDate  = Carbon::parse($createdTillDate)->format('Y-m-d') . ' 23:59:59';
            $modelQuery       = $modelQuery->whereBetween('created_at', [$createdSinceDate, $createdTillDate]);
        } else if (strlen($createdSinceDate) > 0) {
            $createdSinceDate = Carbon::parse($createdSinceDate)->format('Y-m-d') . ' 00:00:00';
            $modelQuery       = $modelQuery->whereDate('created_at', '>=', $createdSinceDate);
        } else if (strlen($createdTillDate) > 0) {
            $createdTillDate  = Carbon::parse($createdTillDate)->format('Y-m-d') . ' 23:59:59';
            $modelQuery       = $modelQuery->whereDate('created_at', '<=', $createdTillDate);
        }
    }

    /**
     * @param $response
     * @return ResponseFactory|Response
     */
    private function jsonResponseOk($response) {
        return response(json_encode($response), Response::HTTP_OK)->header('Content-Type', 'application/json');
    }

    private function jsonResponseValidateError($response) {
        return response(json_encode($response), Response::HTTP_UNPROCESSABLE_ENTITY)->header('Content-Type', 'application/json');
    }

    private function jsonResponseServerError($response) {
        return response(json_encode($response), Response::HTTP_INTERNAL_SERVER_ERROR)->header('Content-Type', 'application/json');
    }
}
