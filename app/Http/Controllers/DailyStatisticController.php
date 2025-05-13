<?php

namespace App\Http\Controllers;

use App\Models\DailyStatistic;
use App\Services\ResponseHelperService;

class DailyStatisticController extends Controller
{
    public function show($userId, $date = null)
    {
        // Если дата не указана — берем текущую
        $date = $date ?? now()->toDateString();

        $statistic = DailyStatistic::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        if (! $statistic) {
            return ResponseHelperService::error([
                [
                    'code' => 'not_found',
                    'message' => 'Daily statistics not found',
                ],
            ], 404);
        }

        return ResponseHelperService::success($statistic);
    }
}
