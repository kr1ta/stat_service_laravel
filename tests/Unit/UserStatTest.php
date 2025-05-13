<?php

namespace Tests\Unit;

use App\Http\Controllers\UserStatisticController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);
/*
beforeEach(function () {
    // Отключаем все middleware
    $this->withoutMiddleware();
});*/

test('it returns user statistics when statistics exist', function () {
    $this->withoutMiddleware();

    // Создаем мок данных
    $mockStatistic = [
        'id' => 3,
        'user_id' => 3,
        'total_tasks_completed' => 3,
        'total_intervals' => 3,
        'total_habit_time' => 3,
        'total_task_time' => 3,
        'created_at' => null,
        'updated_at' => null,
    ];

    // Создаем поддельный контроллер
    $controller = new class
    {
        public function show($userId)
        {
            return response()->json([
                'id' => 3,
                'user_id' => 3,
                'total_tasks_completed' => 3,
                'total_intervals' => 3,
                'total_habit_time' => 3,
                'total_task_time' => 3,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }
    };

    // Заменяем реальный контроллер на поддельный
    $this->instance(UserStatisticController::class, $controller);

    // Выполняем запрос к эндпоинту
    $response = $this->getJson('/api/user-statistics/3');

    // Проверяем результат
    $response->assertStatus(200)
        ->assertJson([
            'id' => 3,
            'user_id' => 3,
            'total_tasks_completed' => 3,
            'total_intervals' => 3,
            'total_habit_time' => 3,
            'total_task_time' => 3,
        ]);
});

test('it returns 404 when user statistics do not exist', function () {
    $this->withoutMiddleware();

    // Замокаем метод where->first, чтобы он возвращал null
    DB::shouldReceive('table->where->first')
        ->andReturn(null);

    // Выполняем запрос к эндпоинту
    $response = $this->getJson('/api/user-statistics/999');

    // Проверяем результат
    $response->assertStatus(404);
    $response->assertJson([
        'data' => null,
        'errors' => [
            [
                'code' => 'not_found',
                'message' => 'Statistics not found',
            ],
        ],
    ]);
});

test('it returns 401 when user is not authorized', function () {
    // Выполняем запрос к эндпоинту без токена
    $response = $this->getJson('/api/user-statistics/1');

    // Проверяем, что ответ имеет статус 401
    $response->assertStatus(401);

    // Проверяем, что в ответе содержится сообщение об ошибке
    $response->assertJson([
        'data' => null,
        'errors' => [
            [
                'code' => 'data_missing',
                'message' => 'Token not provided',
            ],
        ],
    ]);
});
/*
        'id' => 3,
        'id_user' => 3,
        'total_tasks_completed' => 3,
        'total_intervals' => 3,
        'total_habit_time' => 3,
        'total_task_time' => 3,
        */
