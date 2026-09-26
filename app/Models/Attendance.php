<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'date',
        'session1_status',
        'session1_time',
        'session1_remarks',
        'session2_status',
        'session2_time',
        'session2_remarks',
        'final_status',
    ];

    protected $casts = [
        'session1_status' => \App\Enum\AttendanceSessionStatus::class,
        'session2_status' => \App\Enum\AttendanceSessionStatus::class,
        'final_status'    => \App\Enum\AttendanceStatus::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    /**
     * Compute final status based on session 1 & session 2 statuses.
     */
    public static function calculateFinalStatus($s1, $s2): ?string
    {
        $s1 = $s1 instanceof \BackedEnum ? $s1->value : (string) $s1;
        $s2 = $s2 instanceof \BackedEnum ? $s2->value : (string) $s2;
        if ($s1 === 'absent' || $s2 === 'absent') {
            if ($s1 === 'absent' && $s2 === 'absent') {
                return 'absent';
            }
            if ($s1 === 'absent' || $s2 === 'absent') {
                return 'half_day';
            }
        }

        if ($s1 === 'leave' || $s2 === 'leave') {
            return 'leave';
        }

        if ($s1 === 'present' && $s2 === 'present') {
            return 'present';
        }

        if ($s1 === 'late' || $s2 === 'late') {
            return 'late';
        }

        return $s1 ?: $s2;
    }
}
