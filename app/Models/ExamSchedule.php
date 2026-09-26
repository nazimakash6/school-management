<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = [
        'examination_id',
        'subject_name',
        'max_marks',
        'pass_marks',
        'exam_date',
        'start_time',
        'end_time',
        'room_no',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'max_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }
}
