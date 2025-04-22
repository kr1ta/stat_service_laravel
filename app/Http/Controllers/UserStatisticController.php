<?php

namespace App\Http\Controllers;

use App\Models\UserStatistic;

class UserStatisticController extends Controller
{
    public function show($userId)
    {
        $statistic = UserStatistic::where('user_id', $userId)->first();

        if (! $statistic) {
            return response()->json(['message' => 'Statistics not found'], 404);
        }

        return response()->json($statistic);
    }
}
