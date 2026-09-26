<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payrolls';

    protected $fillable = [
        'staff_id',
        'payroll_month',
        'basic_salary',
        'allowance',
        'deduction',
        'net_salary',
        'payment_method',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowance'    => 'decimal:2',
        'deduction'    => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function getFormattedNetSalaryAttribute(): string
    {
        $val = (float) $this->net_salary;
        if ($val < 0) {
            return '-Rs. ' . number_format(abs($val), 2);
        }
        return 'Rs. ' . number_format($val, 2);
    }

    protected static function booted(): void
    {
        static::saving(function (Payroll $payroll) {
            $payroll->net_salary = ((float) $payroll->basic_salary + (float) $payroll->allowance - (float) $payroll->deduction);
        });
    }
}
