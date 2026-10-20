<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\TraineeResult
 *
 * Eloquent model representing an individual trainee's VR inspection drill session.
 *
 * @property int $id
 * @property string $username
 * @property float $score
 * @property int $hazards_found
 * @property int $hazards_missed
 * @property int $wrong_clicks
 * @property float $completion_time
 * @property string $performance_rating
 * @property bool $is_certified
 * @property array|null $found_hazards
 * @property array|null $missed_hazards
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read string $formatted_time
 * @property-read string $formatted_score
 * @property-read int $wrong_clicks_penalty
 * @property-read string $rating_badge_class
 *
 * @method static Builder|TraineeResult leaderboard()
 * @method static Builder|TraineeResult certified()
 */
class TraineeResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    // =========================================================================
    // ELOQUENT ACCESSORS & ATTRIBUTES
    // =========================================================================

    /**
     * Get human-friendly completion time (e.g. "1m 24s").
     */
    public function getFormattedTimeAttribute(): string
    {
        $seconds = (float) $this->completion_time;
        $minutes = floor($seconds / 60);
        $remainderSeconds = round(fmod($seconds, 60));

        return "{$minutes}m " . sprintf('%02ds', $remainderSeconds);
    }

    /**
     * Get formatted percentage score (e.g. "92.3%").
     */
    public function getFormattedScoreAttribute(): string
    {
        return number_format((float) $this->score, 1) . '%';
    }

    /**
     * Calculate total score penalty percentage incurred from safe object distraction clicks.
     */
    public function getWrongClicksPenaltyAttribute(): int
    {
        return ((int) $this->wrong_clicks) * 5;
    }

    /**
     * Get Tailwind badge classes corresponding to trainee performance rating.
     */
    public function getRatingBadgeClassAttribute(): string
    {
        return match ($this->performance_rating) {
            'Locked In'    => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
            'Great'        => 'bg-sky-500/15 text-sky-300 border-sky-500/30',
            'Valid Effort' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
            default        => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
        };
    }

    // =========================================================================
    // QUERY SCOPES
    // =========================================================================

    /**
     * Scope query to order by official leaderboard ranking (Score DESC, Velocity ASC).
     */
    public function scopeLeaderboard(Builder $query): Builder
    {
        return $query->orderByDesc('score')->orderBy('completion_time');
    }

    /**
     * Scope query to include only trainees who satisfied the >=80% passing threshold.
     */
    public function scopeCertified(Builder $query): Builder
    {
        return $query->where('is_certified', true);
    }
}
