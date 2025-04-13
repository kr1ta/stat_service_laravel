<?php

namespace App\Http\Controllers;

use App\Models\DailyStatistic;
use Illuminate\Http\Request;

class DailyStatisticController extends Controller
{
    public function show($userId)
    {
        $date = now()->toDateString(); // Текущая дата
        $statistic = DailyStatistic::where('user_id', $userId)
            // ->where('date', $date)
            ->first();

        if (!$statistic) {
            return response()->json(['message' => 'Daily statistics not found'], 404);
        }

        return response()->json($statistic);
    }
}