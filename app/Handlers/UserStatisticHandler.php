<?php

namespace App\Handlers;

use App\Models\UserStatistic;
use InvalidArgumentException;

class UserStatisticHandler implements MessageHandlerInterface
{
    public static function handle(array $payload): void
    {
        // Находим или создаём новую запись по user_id
        $statistic = UserStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
        ]);

        $duration = $payload['update_type'] === 'stop'
            ? 0 - $payload['unspent_time']
            : $payload['duration'];

        switch ($payload['type']) {
            case 'habit':
                $statistic->total_habit_time = ($statistic->total_habit_time ?? 0) + $duration;
                break;

            case 'task':
                $statistic->total_task_time = ($statistic->total_task_time ?? 0) + $duration;
                // $statistic->total_tasks_completed = ($statistic->total_tasks_completed ?? 0) + ($payload['update_type'] === 'stop' ? 1 : 0);
                break;

            default:
                throw new InvalidArgumentException('Invalid type in payload');
        }

        // Увеличиваем общее количество интервалов только для "start"
        if ($payload['update_type'] === 'start') {
            $statistic->total_intervals = ($statistic->total_intervals ?? 0) + 1;
        }

        $statistic->save();
    }
}