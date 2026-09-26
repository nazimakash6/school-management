<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = [
        'examination_id',
        'admission_id',
        'subject_name',
        'marks_obtained',
        'total_marks',
        'pass_marks',
        'is_absent',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'is_absent' => 'boolean',
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }
}
