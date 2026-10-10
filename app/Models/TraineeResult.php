<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class TraineeResult extends Model
{
    protected $fillable = [
        'username',
        'score',
        'hazards_found',
        'hazards_missed',
        'wrong_clicks',
        'completion_time',
        'performance_rating',
        'is_certified',
        'found_hazards',
        'missed_hazards',
    ];

    protected $casts = [
        'found_hazards' => 'array',
        'missed_hazards' => 'array',
        'is_certified' => 'boolean',
        'score' => 'float',
        'completion_time' => 'float',
        'wrong_clicks' => 'integer',
        'hazards_found' => 'integer',
        'hazards_missed' => 'integer',
    ];
}
