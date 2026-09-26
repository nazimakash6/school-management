<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffAdvance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff_advances';

    protected $fillable = [
        'staff_id',
        'advance_amount',
        'repaid_amount',
        'monthly_installment',
        'advance_date',
        'payment_method',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'advance_amount'      => 'decimal:2',
        'repaid_amount'       => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'advance_date'        => 'date',
        'status'              => \App\Enum\StaffAdvanceStatusEnum::class,
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->advance_amount - (float) $this->repaid_amount);
    }

    public function getRepaymentProgressAttribute(): float
    {
        if ((float) $this->advance_amount <= 0) {
            return 0;
        }
        return min(100, round(((float) $this->repaid_amount / (float) $this->advance_amount) * 100, 1));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        $statusVal = is_object($this->status) ? $this->status->value : $this->status;
        return match ($statusVal) {
            'approved'         => 'bg-primary-subtle text-primary border-primary-subtle',
            'fully_repaid'     => 'bg-success-subtle text-success border-success-subtle',
            'pending'          => 'bg-info-subtle text-info border-info-subtle',
            'rejected'         => 'bg-danger-subtle text-danger border-danger-subtle',
            'cancelled'        => 'bg-secondary-subtle text-secondary border-secondary-subtle',
            default            => 'bg-light text-dark',
        };
    }

    protected static function booted(): void
    {
        static::saving(function (StaffAdvance $advance) {
            if ((float) $advance->repaid_amount >= (float) $advance->advance_amount && (float) $advance->advance_amount > 0) {
                $advance->status = \App\Enum\StaffAdvanceStatusEnum::FULLY_REPAID;
            }
        });
    }
}
