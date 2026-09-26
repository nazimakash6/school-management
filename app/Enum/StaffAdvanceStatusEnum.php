<?php

namespace App\Enum;

enum StaffAdvanceStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FULLY_REPAID = 'fully_repaid';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}
