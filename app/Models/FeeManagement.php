<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fee_managements';

    protected $fillable = [
        'admission_id',
        'academic_session_id',
        'invoice_no',
        'fee_type',
        'fee_details',
        'fee_month',
        'amount',
        'discount',
        'paid_amount',
        'due_date',
        'payment_date',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'discount'     => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_date'     => 'date',
        'payment_date' => 'date',
        'fee_details'  => 'array',
    ];

    /**
     * Get structured list of fee items (heads) included in this invoice voucher
     */
    public function getFeeItemsAttribute(): array
    {
        if (!empty($this->fee_details) && is_array($this->fee_details)) {
            return $this->fee_details;
        }

        return [
            [
                'fee_type' => $this->fee_type ?: 'school_fee',
                'title'    => ucwords(str_replace('_', ' ', $this->fee_type ?: 'School Fee')),
                'amount'   => (float) $this->amount,
            ]
        ];
    }

    /**
     * Get user friendly formatted fee type summary
     */
    public function getFeeTypeFormattedAttribute(): string
    {
        $items = $this->fee_items;
        if (count($items) > 1) {
            $titles = array_map(fn($item) => $item['title'] ?? ucwords(str_replace('_', ' ', $item['fee_type'] ?? '')), $items);
            return 'Multiple Fees (' . count($items) . '): ' . implode(', ', $titles);
        }

        $single = $items[0]['title'] ?? $items[0]['fee_type'] ?? $this->fee_type;
        return ucwords(str_replace('_', ' ', $single));
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class, 'fee_management_id');
    }

    public function getNetAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->discount);
    }

    public function getDueBalanceAttribute(): float
    {
        return max(0, $this->net_amount - (float) $this->paid_amount);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'bg-success text-white',
            'partial'   => 'bg-warning text-dark',
            'unpaid'    => 'bg-danger text-white',
            'cancelled' => 'bg-secondary text-white',
            default     => 'bg-light text-dark',
        };
    }

    protected static function booted(): void
    {
        static::saving(function (FeeManagement $fee) {
            $netAmount = max(0, (float) $fee->amount - (float) $fee->discount);
            if ($fee->status !== 'cancelled') {
                if ((float) $fee->paid_amount >= $netAmount && $netAmount > 0) {
                    $fee->status = 'paid';
                    if (! $fee->payment_date) {
                        $fee->payment_date = now()->toDateString();
                    }
                } elseif ((float) $fee->paid_amount > 0) {
                    $fee->status = 'partial';
                } else {
                    $fee->status = 'unpaid';
                }
            }
        });
    }
}
