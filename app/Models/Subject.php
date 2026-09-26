<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'subject_code',
        'subject_name',
        'subject_type',
        'subject_type_id',
        'class_name',
        'student_class_id',
        'staff_id',
        'status',
        'description',
    ];

    public function subjectType(): BelongsTo
    {
        return $this->belongsTo(SubjectType::class, 'subject_type_id');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'student_class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function homeworks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HomeWork::class, 'subject_id');
    }

    public function classworks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClassWork::class, 'subject_id');
    }

    public function getTypeBadgeClassAttribute(): string
    {
        if ($this->subjectType && $this->subjectType->badge_class) {
            return $this->subjectType->badge_class;
        }

        $typeKey = strtolower((string) ($this->subject_type ?? ''));
        return match ($typeKey) {
            'core'       => 'bg-primary text-white',
            'elective'   => 'bg-info text-white',
            'islamic'    => 'bg-success text-white',
            'optional'   => 'bg-warning text-dark',
            default      => 'bg-secondary text-white',
        };
    }

    public function getTypeNameAttribute(): string
    {
        return $this->subjectType?->name ?? ucfirst((string) ($this->subject_type ?? 'General'));
    }
}
