<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendee extends Model
{
    use HasFactory;

    protected $table = 'meeting_attendees';

    protected $fillable = [
        'meeting_id',
        'attendee_type',
        'attendee_id',
        'name',
        'email',
        'role',
        'attendance_status',
        'remarks',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->attendance_status) {
            'Attended' => 'bg-success text-white',
            'Excused'  => 'bg-info text-dark',
            'Absent'   => 'bg-danger text-white',
            default    => 'bg-secondary text-white',
        };
    }
}
