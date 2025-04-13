<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    public $timestamps = false;

    protected $table = 'daily_statistics';

    protected $fillable = [
        'user_id',
        'date',
        'total_intervals',
        'early_completed_intervals',
        'tag_stats',
        'intervalable_stats',
    ];
}