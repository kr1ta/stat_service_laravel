<?php

namespace App\Handlers;

use App\Models\UserStatistic;
use InvalidArgumentException;

class UserStatisticStartHandler implements MessageHandlerInterface
{
    public static function handle(array $payload): void
    {
        if (! isset($payload['user_id'], $payload['type'])) {
            throw new InvalidArgumentException('Missing required fields in payload for start');
        }

        $statistic = UserStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
        ]);

        $statistic->total_intervals = ($statistic->total_intervals ?? 0) + 1;

        switch ($payload['type']) {
            case 'habits':
                $statistic->total_habit_time = ($statistic->total_habit_time ?? 0) + ($payload['duration'] ?? 0);
                break;

            case 'tasks':
                $statistic->total_task_time = ($statistic->total_task_time ?? 0) + ($payload['duration'] ?? 0);
                break;

            default:
                throw new InvalidArgumentException('Invalid type in payload');
        }

        $statistic->save();
    }
}
