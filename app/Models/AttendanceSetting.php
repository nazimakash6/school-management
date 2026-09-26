<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $table = 'attendance_settings';

    protected $fillable = [
        'school_open_time',
        'school_start_time',
        'school_break_time',
        'school_end_time',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'school_open_time'  => '07:30:00',
            'school_start_time' => '08:00:00',
            'school_break_time' => '12:00:00',
            'school_end_time'   => '14:00:00',
        ]);
    }
}
