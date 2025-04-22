<?php

use App\Http\Controllers\DailyStatisticController;
use App\Http\Controllers\UserStatisticController;
use Illuminate\Support\Facades\Route;

Route::middleware(['validate.token'])->group(function () {
    // Роуты для получения статистики
    Route::get('/user-statistics/{userId}', [UserStatisticController::class, 'show']);
    Route::get('/daily-statistics/{userId}', [DailyStatisticController::class, 'show']);
});
