<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatistic extends Model
{
    use HasFactory;

    protected $table = 'user_statistics';

    protected $fillable = [
        'user_id',
        'total_tasks_completed',
        'total_intervals',
        'total_habit_time',
        'total_task_time',
    ];

    public $timestamps = true;
}
