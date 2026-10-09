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
        'completion_time',
        'performance_rating',
        'found_hazards',
        'missed_hazards',
    ];

    protected $casts = [
        'found_hazards' => 'array',
        'missed_hazards' => 'array',
    ];
}
