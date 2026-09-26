<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportStudent extends Model
{
    use HasFactory;

    protected $table = 'transport_students';

    protected $guarded = [];

    protected $casts = [
        'monthly_fare' => 'float',
        'joining_date' => 'date',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Active'   => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            'Paused'   => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle',
            'Cancelled'=> 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
            default    => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
        };
    }
}
