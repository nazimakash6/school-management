<?php

namespace App\Enum;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case HALF_DAY = 'half_day';
    case LATE = 'late';
    case LEAVE = 'leave';
}
