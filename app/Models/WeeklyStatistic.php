<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyStatistic extends Model
{
    use HasFactory;

    protected $table = 'weekly_statistics';
    protected $fillable = [
        'user_id',
        'top_tags',
        'top_intervalables',
    ];

    public $timestamps = true;
}
