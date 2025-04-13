<?php

namespace App\Handlers;

use App\Models\DailyStatistic;
use Illuminate\Support\Facades\Log;

class DailyStatisticStopHandler implements MessageHandlerInterface
{
    public static function handle(array $payload): void
    {
        // Проверяем обязательные поля в payload
        if (!isset($payload['user_id'], $payload['type'], $payload['unspent_time'])) {
            throw new InvalidArgumentException('Missing required fields in payload for stop');
        }

        // Находим или создаём запись за текущий день для пользователя
        $statistic = DailyStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
            'date' => now()->toDateString(), // Используем поле date вместо created_at
        ]);

        // Логируем исходное состояние $statistic
        Log::info('Initial statistic data for stop:', [
            'statistic' => $statistic->toArray(),
        ]);

        // Работаем с tag_stats через локальную переменную
        $tagStats = json_decode($statistic->tag_stats ?? '{}', true);
        foreach ($payload['tag_stats'] as $tagId => $tagData) {
            if (isset($tagStats[$tagId])) {
                $tagStats[$tagId]['time'] -= $payload['unspent_time'] ?? 0;
                $tagStats[$tagId]['time'] = max(0, $tagStats[$tagId]['time']);
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

            $intervalableStats[$payload['type']][$intervalableId]['time'] -= $payload['unspent_time'] ?? 0;
            $intervalableStats[$payload['type']][$intervalableId]['time'] = max(0, $intervalableStats[$payload['type']][$intervalableId]['time']);
        }

        // Обновляем early_completed_intervals для раннего завершения
        if (isset($payload['early_completed']) && $payload['early_completed']) {
            $statistic->early_completed_intervals += 1;
        }

        // Сохраняем данные обратно в JSON формат
        $statistic->tag_stats = json_encode($tagStats);
        $statistic->intervalable_stats = json_encode($intervalableStats);

        // Логируем обновлённое состояние $statistic перед сохранением
        Log::info('Updated statistic data before saving for stop:', [
            'statistic' => $statistic->toArray(),
        ]);

        // Сохраняем обновлённую запись
        $statistic->save();
    }
}