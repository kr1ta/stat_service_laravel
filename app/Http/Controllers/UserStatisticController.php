<?php

namespace App\Http\Controllers;

use App\Models\UserStatistic;
use App\Services\ResponseHelperService;

class UserStatisticController extends Controller
{
    public function show($userId)
    {
        $statistic = UserStatistic::where('user_id', $userId)->first();

        if (! $statistic) {
            return ResponseHelperService::error([
                [
                    'code' => 'not_found',
                    'message' => 'Statistics not found',
                ],
            ], 404);
        }

        return ResponseHelperService::success($statistic);
    }
}
