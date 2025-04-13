<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserStatisticController;
use App\Http\Controllers\DailyStatisticController;

Route::middleware(['validate.token'])->group(function () {
    // Роуты для получения статистики
    Route::get('/user-statistics/{userId}', [UserStatisticController::class, 'show']);
    Route::get('/daily-statistics/{userId}', [DailyStatisticController::class, 'show']);
});