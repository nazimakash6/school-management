<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $table = 'event_categories';

    protected $fillable = [
        'name',
        'code',
        'badge_class',
        'status',
        'description',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_category_id');
    }

    public function eventsMany(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_event_category', 'event_category_id', 'event_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(EventActivity::class, 'event_category_id');
    }

    public function activitiesMany(): BelongsToMany
    {
        return $this->belongsToMany(EventActivity::class, 'event_activity_event_category', 'event_category_id', 'event_activity_id');
    }
}
