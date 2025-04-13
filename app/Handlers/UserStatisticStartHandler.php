<?php

namespace App\Handlers;

use App\Models\UserStatistic;
use InvalidArgumentException;

class UserStatisticStartHandler implements MessageHandlerInterface
{
    public static function handle(array $payload): void
    {
        // Проверяем обязательные поля
        if (!isset($payload['user_id'], $payload['type'])) {
            throw new InvalidArgumentException('Missing required fields in payload for start');
        }

        // Находим или создаём новую запись по user_id
        $statistic = UserStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
        ]);

        // Увеличиваем общее количество интервалов
        $statistic->total_intervals = ($statistic->total_intervals ?? 0) + 1;

        // Добавляем время для привычек или задач
        switch ($payload['type']) {
            case 'habit':
                $statistic->total_habit_time = ($statistic->total_habit_time ?? 0) + ($payload['duration'] ?? 0);
                break;

            case 'task':
                $statistic->total_task_time = ($statistic->total_task_time ?? 0) + ($payload['duration'] ?? 0);
                break;

            default:
                throw new InvalidArgumentException('Invalid type in payload');
        }

        $statistic->save();
    }
}