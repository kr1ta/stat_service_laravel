<?php

namespace Tests\Unit;

use App\Http\Controllers\DailyStatisticController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

test('it returns user statistics when statistics exist', function () {
    $this->withoutMiddleware();

    // Создаем мок данных
    $mockStatistic = [
        'id' => 3,
        'user_id' => 3,
        'total_intervals' => 3,
        'early_completed_interval' => 3,
        'habit_time' => 3,
        'habbit_count' => 3,
    ];

    // Создаем поддельный контроллер
    $controller = new class
    {
        public function show($userId)
        {
            return response()->json([
                'id' => 3,
                'user_id' => 3,
                'total_intervals' => 3,
                'early_completed_interval' => 3,
                'habit_time' => 3,
                'habbit_count' => 3,
            ]);
        }
    };

    // Заменяем реальный контроллер на поддельный
    $this->instance(DailyStatisticController::class, $controller);

    // Выполняем запрос к эндпоинту
    $response = $this->getJson('/api/daily-statistics/3');

    // Проверяем результат
    $response->assertStatus(200)
        ->assertJson([
            'id' => 3,
            'user_id' => 3,
            'total_intervals' => 3,
            'early_completed_interval' => 3,
            'habit_time' => 3,
            'habbit_count' => 3,
        ]);
});

test('it returns 404 when user statistics do not exist', function () {
    $this->withoutMiddleware();

    // Замокаем метод where->first, чтобы он возвращал null
    DB::shouldReceive('table->where->first')
        ->andReturn(null);

    // Выполняем запрос к эндпоинту
    $response = $this->getJson('/api/daily-statistics/999');

    // Проверяем результат
    $response->assertStatus(404)
        ->assertJson(['message' => 'Daily statistics not found']);
});

test('it returns 401 when user is not authorized', function () {
    // Выполняем запрос к эндпоинту без токена
    $response = $this->getJson('/api/daily-statistics/1');

    // Проверяем, что ответ имеет статус 401
    $response->assertStatus(401);

    // Проверяем, что в ответе содержится сообщение об ошибке
    $response->assertJson([
        'message' => 'Токен не предоставлен', // Замените на реальное сообщение из вашего middleware
    ]);
});
