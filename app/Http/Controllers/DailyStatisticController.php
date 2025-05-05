<?php

namespace App\Http\Controllers;

use App\Models\DailyStatistic;
use Illuminate\Support\Facades\Response;

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
            return Response::json(['message' => 'Daily statistics not found'], 404);
        }

        return Response::json($statistic);
    }
}
