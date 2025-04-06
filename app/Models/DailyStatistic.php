<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    use HasFactory;

    protected $table = 'daily_statistics';
    protected $fillable = [
        'user_id',
        'tag_id',
        'title',
        'intervalable_id',
        'type',
        'total_intervals',
        'time_spent',
    ];

    public $timestamps = true;
}
