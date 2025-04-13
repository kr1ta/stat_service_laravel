<?php

namespace App\Handlers;

use App\Models\DailyStatistic;
use Illuminate\Support\Facades\Log;

class DailyStatisticStartHandler implements MessageHandlerInterface
{
    public static function handle(array $payload): void
    {
        // Проверяем обязательные поля в payload
        if (!isset($payload['user_id'], $payload['type'], $payload['tag_stats'])) {
            throw new InvalidArgumentException('Missing required fields in payload for start');
        }

        // Находим или создаём запись за текущий день для пользователя
        $statistic = DailyStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
            'date' => now()->toDateString(), // Используем поле date вместо created_at
        ]);

        // Логируем исходное состояние $statistic
        Log::info('Initial statistic data for start:', [
            'statistic' => $statistic->toArray(),
        ]);

        // Увеличиваем общее количество интервалов
        $statistic->total_intervals = ($statistic->total_intervals ?? 0) + 1;

        // Работаем с tag_stats через локальную переменную
        $tagStats = json_decode($statistic->tag_stats ?? '{}', true);
        foreach ($payload['tag_stats'] as $tagId => $tagData) {
            // Если тег уже существует, суммируем значения
            if (isset($tagStats[$tagId])) {
                $tagStats[$tagId]['time'] += $tagData['time'];
                $tagStats[$tagId]['interval_amount'] += $tagData['interval_amount'];
            } else {
                // Если тега нет, добавляем его с новыми данными
                $tagStats[$tagId] = [
                    'time' => $tagData['time'],
                    'interval_amount' => $tagData['interval_amount'],
                ];
            }
        }

        // Работаем с intervalable_stats
        $intervalableStats = json_decode($statistic->intervalable_stats ?? '{}', true);
        if (in_array($payload['type'], ['task', 'habit'])) {
            $intervalableId = $payload['intervalable_id'];

            if (!isset($intervalableStats[$payload['type']][$intervalableId])) {
                $intervalableStats[$payload['type']][$intervalableId] = [
                    'time' => 0,
                    'interval_amount' => 0,
                ];
            }

            $intervalableStats[$payload['type']][$intervalableId]['time'] += $payload['duration'] ?? 0;
            $intervalableStats[$payload['type']][$intervalableId]['interval_amount'] += 1;
        }

        // Сохраняем данные обратно в JSON формат
        $statistic->tag_stats = json_encode($tagStats);
        $statistic->intervalable_stats = json_encode($intervalableStats);

        // Логируем обновлённое состояние $statistic перед сохранением
        Log::info('Updated statistic data before saving for start:', [
            'statistic' => $statistic->toArray(),
        ]);

        // Сохраняем обновлённую запись
        $statistic->save();
    }
}