<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';

    protected $fillable = [
        'pass_code',
        'visitor_name',
        'phone',
        'cnic_id',
        'num_persons',
        'purpose',
        'meet_type',
        'student_id',
        'staff_id',
        'person_to_meet',
        'visit_date',
        'check_in_time',
        'check_out_time',
        'vehicle_no',
        'gate_no',
        'status',
        'id_proof_image',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'visit_date'     => 'date',
        'check_in_time'  => 'datetime',
        'check_out_time' => 'datetime',
        'num_persons'     => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Checked-In'  => 'bg-warning text-dark border border-warning',
            'Checked-Out' => 'bg-success text-white',
            'Blocked'     => 'bg-danger text-white',
            default       => 'bg-secondary text-white',
        };
    }

    public function getMeetTypeBadgeClassAttribute(): string
    {
        return match ($this->meet_type) {
            'Student' => 'bg-primary text-white',
            'Staff'   => 'bg-info text-dark',
            default   => 'bg-secondary text-white',
        };
    }

    public function getDurationTextAttribute(): string
    {
        if (!$this->check_in_time) return 'N/A';
        $endTime = $this->check_out_time ?: Carbon::now();
        $diffMinutes = $this->check_in_time->diffInMinutes($endTime);

        if ($diffMinutes < 60) {
            return "{$diffMinutes} min" . ($this->check_out_time ? '' : ' (So far)');
        }
        $hours = floor($diffMinutes / 60);
        $remMin = $diffMinutes % 60;
        return "{$hours}h {$remMin}m" . ($this->check_out_time ? '' : ' (So far)');
    }

    public function getIdProofUrlAttribute(): ?string
    {
        if (!$this->id_proof_image) return null;
        if (str_starts_with($this->id_proof_image, 'http://') || str_starts_with($this->id_proof_image, 'https://')) {
            return $this->id_proof_image;
        }
        return asset('storage/' . $this->id_proof_image);
    }
}
