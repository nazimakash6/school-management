<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';
    protected $fillable = [
        'staff_id',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'cnic',
        'marital_status',
        'blood_group',
        'religion',
        'nationality',
        'mobile_no',
        'alternate_mobile_no',
        'email',
        'current_address',
        'permanent_address',
        'joining_date',
        'leaving_date',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relation',
        'department',
        'designation',
        'qualification',
        'experience',
        'employment_type',
        'shift',
        'salary',
        'salary_type',
        'bank_name',
        'bank_account_title',
        'bank_account_number',
        'iban',
        'driving_license_number',
        'driving_license_expiry',
        'vehicle_registration_number',
        'vehicle_type',
        'cv',
        'profile_picture',
        'status',
        'note',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'dob' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getFormattedDepartmentAttribute(): string
    {
        if (strtolower($this->department ?? '') === 'it') {
            return 'IT';
        }
        return ucwords(str_replace(['_', '-'], ' ', $this->department ?? ''));
    }

    public function getFormattedDesignationAttribute(): string
    {
        if (strtolower($this->designation ?? '') === 'it_support') {
            return 'IT Support';
        }
        return ucwords(str_replace(['_', '-'], ' ', $this->designation ?? ''));
    }

    public function getFullNameAttribute(): string
    {
        return ucwords(strtolower(trim("{$this->first_name} {$this->last_name}")));
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'staff_id');
    }

    public function advances()
    {
        return $this->hasMany(StaffAdvance::class, 'staff_id');
    }

    public function getActiveAdvanceInstallmentAttribute(): float
    {
        return (float) $this->advances()
            ->whereIn('status', ['approved'])
            ->sum('monthly_installment');
    }

    public function getActiveAdvanceBalanceAttribute(): float
    {
        if ($this->relationLoaded('advances')) {
            return (float) $this->advances
                ->filter(function ($a) {
                    $status = is_object($a->status) ? $a->status->value : $a->status;
                    return in_array($status, ['approved', 'pending']);
                })
                ->sum(fn($a) => (float) $a->advance_amount - (float) $a->repaid_amount);
        }

        return (float) $this->advances()
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->sum(fn($a) => (float) $a->advance_amount - (float) $a->repaid_amount);
    }
}
