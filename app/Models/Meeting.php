<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasFactory;

    protected $table = 'meetings';

    protected $fillable = [
        'title',
        'meeting_type',
        'mode',
        'location',
        'meeting_link',
        'meeting_date',
        'start_time',
        'end_time',
        'target_audience',
        'organizer_id',
        'status',
        'agenda',
        'minutes_of_meeting',
        'action_items',
        'attachment_path',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class, 'meeting_id');
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        if (!$this->start_time) return 'N/A';
        $start = Carbon::parse($this->start_time)->format('g:i A');
        $end = $this->end_time ? Carbon::parse($this->end_time)->format('g:i A') : '';
        return $end ? "{$start} - {$end}" : $start;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Completed'   => 'bg-success text-white',
            'In-Progress' => 'bg-warning text-dark',
            'Scheduled'   => 'bg-primary text-white',
            'Postponed'   => 'bg-info text-dark',
            'Cancelled'   => 'bg-danger text-white',
            default       => 'bg-secondary text-white',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->meeting_type) {
            'Staff Meeting'                 => 'bg-purple-100 text-purple-800 border-purple',
            'Parent Teacher Meeting (PTM)'  => 'bg-info text-dark',
            'Management / Board'            => 'bg-dark text-white',
            'Departmental HOD'              => 'bg-primary text-white',
            'Academic Council'              => 'bg-success text-white',
            default                         => 'bg-secondary text-white',
        };
    }

    public function getModeBadgeClassAttribute(): string
    {
        return match ($this->mode) {
            'Online (Zoom/Google Meet)' => 'bg-info text-dark',
            'Hybrid'                    => 'bg-warning text-dark',
            default                     => 'bg-light text-dark border',
        };
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) return null;
        if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
            return $this->attachment_path;
        }
        return asset('storage/' . $this->attachment_path);
    }
}
