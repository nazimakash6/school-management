<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'event_type',
        'event_category_id',
        'target_audience',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_all_day',
        'location',
        'organizer',
        'status',
        'budget_pkr',
        'banner_image',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_all_day' => 'boolean',
        'budget_pkr' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categoryRel(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(EventCategory::class, 'event_event_category', 'event_id', 'event_category_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(EventActivity::class, 'event_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Upcoming'    => 'bg-info text-dark',
            'In-Progress' => 'bg-warning text-dark',
            'Completed'   => 'bg-success text-white',
            'Postponed'   => 'bg-secondary text-white',
            'Cancelled'   => 'bg-danger text-white',
            default       => 'bg-light text-dark border',
        };
    }

    public function getEventTypeBadgeClassAttribute(): string
    {
        if ($this->categoryRel && $this->categoryRel->badge_class) {
            return $this->categoryRel->badge_class;
        }

        return match ($this->event_type) {
            'Academic'           => 'bg-primary text-white',
            'Sports'             => 'bg-success text-white',
            'Cultural'           => 'bg-purple text-white',
            'Islamic / Religious'=> 'bg-emerald text-white',
            'Parent-Teacher'     => 'bg-info text-dark',
            'Holiday / Vacation' => 'bg-warning text-dark',
            default              => 'bg-secondary text-white',
        };
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if (!$this->start_date) return 'N/A';
        if ($this->end_date && !$this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
        }
        return $this->start_date->format('M d, Y');
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        if ($this->is_all_day) {
            return 'All Day Event';
        }
        if ($this->start_time && $this->end_time) {
            $st = Carbon::createFromFormat('H:i:s', strlen($this->start_time) === 5 ? $this->start_time . ':00' : $this->start_time)->format('g:i A');
            $et = Carbon::createFromFormat('H:i:s', strlen($this->end_time) === 5 ? $this->end_time . ':00' : $this->end_time)->format('g:i A');
            return "{$st} - {$et}";
        }
        if ($this->start_time) {
            return Carbon::createFromFormat('H:i:s', strlen($this->start_time) === 5 ? $this->start_time . ':00' : $this->start_time)->format('g:i A');
        }
        return 'TBD';
    }

    public function getBannerUrlAttribute(): string
    {
        if ($this->banner_image) {
            if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
                return $this->banner_image;
            }
            return asset('storage/' . $this->banner_image);
        }
        // Aesthetic default cover gradient background SVG / photo
        return 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80';
    }
}
