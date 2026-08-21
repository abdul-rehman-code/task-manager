<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyPerformance extends Model
{
    protected $guarded = [];

    // Database View binding
    protected $table = 'daily_performances';

    // View mein timestamps columns nahi hotay
    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
        'tasks_completed' => 'integer',
        'total_estimated_minutes' => 'integer',
        'total_time_spent' => 'integer',
        'efficiency_percentage' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}