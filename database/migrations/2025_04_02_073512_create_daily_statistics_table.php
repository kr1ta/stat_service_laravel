<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatisticsTable extends Migration
{
    public function up(): void
    {
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('total_intervals')->default(0);
            $table->integer('early_completed_intervals')->default(0); // Досрочно завершённые интервалы

            $table->json('tag_stats')->nullable(); // Массив данных о тегах
            $table->json('intervalable_stats')->nullable(); // Массив данных о задачах/привычках

            $table->date('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
}
