<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class EventActivity extends Model
{
    use HasFactory;

    protected $table = 'event_activities';

    protected $fillable = [
        'event_id',
        'name',
        'category',
        'event_category_id',
        'activity_date',
        'start_time',
        'end_time',
        'venue',
        'house_ids',
        'status',
        'winner_house_id',
        'runner_up_house_id',
        'third_place_house_id',
        'rules_notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'house_ids'     => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function categoryRel(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(EventCategory::class, 'event_activity_event_category', 'event_activity_id', 'event_category_id');
    }

    public function winnerHouse(): BelongsTo
    {
        return $this->belongsTo(House::class, 'winner_house_id');
    }

    public function runnerUpHouse(): BelongsTo
    {
        return $this->belongsTo(House::class, 'runner_up_house_id');
    }

    public function thirdPlaceHouse(): BelongsTo
    {
        return $this->belongsTo(House::class, 'third_place_house_id');
    }

    public function getParticipatingHousesAttribute(): Collection
    {
        $ids = $this->house_ids ?: [];
        if (empty($ids)) {
            return collect();
        }
        return House::whereIn('id', $ids)->get();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Scheduled' => 'bg-info text-dark',
            'Ongoing'   => 'bg-warning text-dark',
            'Completed' => 'bg-success text-white',
            'Cancelled' => 'bg-danger text-white',
            default     => 'bg-secondary text-white',
        };
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        if ($this->categoryRel && $this->categoryRel->badge_class) {
            return $this->categoryRel->badge_class;
        }

        return match ($this->category) {
            'Sports'    => 'bg-primary text-white',
            'Academic'  => 'bg-info text-white',
            'Cultural'  => 'bg-purple text-white',
            'Literary'  => 'bg-teal text-white',
            default     => 'bg-secondary text-white',
        };
    }
}
