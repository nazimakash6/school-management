<?php

namespace App\Enum;

enum AttendanceSessionStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case LEAVE = 'leave';
}
