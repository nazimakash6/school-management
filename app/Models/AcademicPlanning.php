<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicPlanning extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'AcademicPlanning';

    protected $fillable = [
        'title',
        'plan_type',
        'class_name',
        'subject_name',
        'academic_session',
        'teacher_id',
        'start_date',
        'end_date',
        'objectives',
        'topics_covered',
        'teaching_methodology',
        'assessment_plan',
        'status',
        'attachment',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match (strtolower((string)$this->status)) {
            'active', 'approved'  => 'bg-success-subtle text-success border border-success-subtle',
            'completed'           => 'bg-info-subtle text-info border border-info-subtle',
            'draft', 'pending'    => 'bg-warning-subtle text-warning border border-warning-subtle',
            'archived', 'inactive'=> 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            default               => 'bg-light text-dark border',
        };
    }

    public function getPlanTypeLabelAttribute(): string
    {
        return match (strtolower((string)$this->plan_type)) {
            'annual'   => 'Annual Plan',
            'monthly'  => 'Monthly Plan',
            'weekly'   => 'Weekly Plan',
            'daily'    => 'Daily Lesson Plan',
            'unit'     => 'Unit Syllabus',
            default    => ucfirst((string)$this->plan_type),
        };
    }
}
