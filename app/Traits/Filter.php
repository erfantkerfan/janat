<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait Filter
{
    private function filterByRelationId(Request $request, $requestKey, $relationName, & $modelQuery) {
        $relationIds = $request->get($requestKey);
        if (!isset($relationIds)) {
            return;
        }
        if (is_array($relationIds) && count($relationIds) === 0) {
            return;
        }
        if (!is_array($relationIds)) {
            $relationIds = [$relationIds];
        }
        $modelQuery->whereHas($relationName, function (Builder $query) use ($relationIds) {
            $query->whereIn('id', $relationIds);
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
            $modelQuery       = $modelQuery->whereBetween('created_at', [$createdSinceDate, $createdTillDate])->orderBy('created_at', 'Desc');
        } else if (strlen($createdSinceDate) > 0) {
            $createdSinceDate = Carbon::parse($createdSinceDate)->format('Y-m-d') . ' 00:00:00';
            $modelQuery       = $modelQuery->whereDate('created_at', '>=', $createdSinceDate)->orderBy('created_at', 'Desc');
        } else if (strlen($createdTillDate) > 0) {
            $createdTillDate  = Carbon::parse($createdTillDate)->format('Y-m-d') . ' 23:59:59';
            $modelQuery       = $modelQuery->whereDate('created_at', '<=', $createdTillDate)->orderBy('created_at', 'Desc');
        } else {
            $modelQuery = $modelQuery->orderBy('created_at', 'Desc');
        }
    }

    private function jsonResponseOk($response) {
        return response(json_encode($response), Response::HTTP_OK)->header('Content-Type', 'application/json');
    }

    private function jsonResponseError($message) {
        return response(json_encode($message), Response::HTTP_INTERNAL_SERVER_ERROR)->header('Content-Type', 'application/json');
    }
}
