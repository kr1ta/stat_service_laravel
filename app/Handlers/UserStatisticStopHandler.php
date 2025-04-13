<?php

namespace App\Handlers;

use App\Models\UserStatistic;
use InvalidArgumentException;

class UserStatisticStopHandler implements MessageHandlerInterface
{
    public static function handle(array $payload): void
    {
        // Проверяем обязательные поля
        if (!isset($payload['user_id'], $payload['type'], $payload['unspent_time'])) {
            throw new InvalidArgumentException('Missing required fields in payload for stop');
        }

        // Находим или создаём новую запись по user_id
        $statistic = UserStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
        ]);

        // Вычитаем неиспользованное время
        $duration = 0 - ($payload['unspent_time'] ?? 0);

        switch ($payload['type']) {
            case 'habit':
                $statistic->total_habit_time = ($statistic->total_habit_time ?? 0) + $duration;
                break;

            case 'task':
                $statistic->total_task_time = ($statistic->total_task_time ?? 0) + $duration;
                break;

            default:
                throw new InvalidArgumentException('Invalid type in payload');
        }

        $statistic->save();
    }
}