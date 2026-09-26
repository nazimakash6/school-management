<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transports';

    protected $guarded = [];

    protected $casts = [
        'vehicle_capacity' => 'integer',
        'fare_amount'      => 'float',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(TransportStudent::class, 'transport_id');
    }

    public function activeAllocations(): HasMany
    {
        return $this->hasMany(TransportStudent::class, 'transport_id')->where('status', 'Active');
    }

    protected static function booted(): void
    {
        static::saving(function (Transport $transport) {
            // Auto generate Route Code if empty
            if (empty($transport->route_code)) {
                $prefix = match($transport->vehicle_type) {
                    'Bus'           => 'TR-BUS',
                    'Coaster'       => 'TR-CST',
                    'Van'           => 'TR-VAN',
                    'Auto Ricksha', 'Auto Rickshaw' => 'TR-RSH',
                    'Chandi Gari'   => 'TR-CG',
                    default         => 'TR-RTE',
                };
                $transport->route_code = $prefix . '-' . rand(100, 999);
            }

            // Sync driver info from staff model if driver_id is present
            if ($transport->driver_id && $transport->isDirty('driver_id')) {
                $staff = Staff::find($transport->driver_id);
                if ($staff) {
                    $transport->driver_name = $staff->full_name;
                    $transport->driver_contact = $staff->mobile_no;
                    $transport->driver_license = $staff->driving_license_number;
                }
            }
        });
    }

    public function getAssignedStudentsCountAttribute(): int
    {
        return $this->activeAllocations()->count();
    }

    public function getAvailableCapacityAttribute(): int
    {
        return max(0, $this->vehicle_capacity - $this->assigned_students_count);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Active'      => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            'Maintenance' => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle',
            'Suspended'   => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
            default       => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
        };
    }

    public function getVehicleTypeBadgeClassAttribute(): string
    {
        return match ($this->vehicle_type) {
            'Bus'           => 'bg-primary-subtle text-primary border-primary-subtle',
            'Coaster'       => 'bg-purple-subtle text-purple border-purple-subtle',
            'Van'           => 'bg-info-subtle text-info border-info-subtle',
            'Auto Ricksha', 'Auto Rickshaw' => 'bg-warning bg-opacity-10 text-dark border border-warning-subtle',
            'Chandi Gari'   => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            default         => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }
}
