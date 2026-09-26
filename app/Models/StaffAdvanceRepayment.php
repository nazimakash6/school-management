<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAdvanceRepayment extends Model
{
    use HasFactory;

    protected $table = 'staff_advance_repayments';

    protected $fillable = [
        'staff_advance_id',
        'staff_id',
        'payroll_id',
        'amount',
        'repayment_date',
        'repayment_type',
        'notes',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'repayment_date' => 'date',
    ];

    public function staffAdvance(): BelongsTo
    {
        return $this->belongsTo(StaffAdvance::class, 'staff_advance_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
