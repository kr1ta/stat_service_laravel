<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DailyStatisticsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Очищаем таблицу перед заполнением
        DB::table('daily_statistics')->truncate();

        $statistics = [
            [
                'user_id' => 1,
                'total_intervals' => 10,
                'early_completed_intervals' => 2,
                'tag_stats' => json_encode([
                    '1' => ['time' => 3600, 'interval_amount' => 5],
                    '2' => ['time' => 1800, 'interval_amount' => 3],
                ]),
                'intervalable_stats' => json_encode([
                    'task' => [
                        '101' => ['time' => 2400, 'interval_amount' => 4],
                        '102' => ['time' => 1200, 'interval_amount' => 2],
                    ],
                    'habit' => [
                        '201' => ['time' => 1800, 'interval_amount' => 3],
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'total_intervals' => 15,
                'early_completed_intervals' => 5,
                'tag_stats' => json_encode([
                    '3' => ['time' => 7200, 'interval_amount' => 10],
                    '4' => ['time' => 3600, 'interval_amount' => 6],
                ]),
                'intervalable_stats' => json_encode([
                    'task' => [
                        '301' => ['time' => 4800, 'interval_amount' => 8],
                        '302' => ['time' => 2400, 'interval_amount' => 4],
                    ],
                    'habit' => [
                        '401' => ['time' => 3600, 'interval_amount' => 6],
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('daily_statistics')->insert($statistics);
    }
}