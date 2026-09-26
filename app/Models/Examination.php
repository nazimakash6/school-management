<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    protected $fillable = [
        'title',
        'exam_type_id',
        'academic_session_id',
        'class_name',
        'section_name',
        'start_date',
        'end_date',
        'total_marks',
        'pass_marks',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
    ];

    public function getTargetClassesArrayAttribute(): array
    {
        $val = trim((string) $this->class_name);
        if (empty($val) || in_array(strtolower($val), ['all', 'all classes', 'all_classes'])) {
            return ['All Classes'];
        }
        return array_values(array_filter(array_map('trim', explode(',', $val))));
    }

    public function isAllClasses(): bool
    {
        $val = strtolower(trim((string) $this->class_name));
        return empty($val) || in_array($val, ['all', 'all classes', 'all_classes']);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }
}
