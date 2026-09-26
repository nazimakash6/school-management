<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeWork extends Model
{
    use HasFactory;

    protected $table = 'home_works';

    protected $fillable = [
        'student_class_id',
        'subject_id',
        'title',
        'description',
        'subject_tasks',
        'assigned_date',
        'due_date',
        'attachment',
        'status',
        'created_by',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'due_date'      => 'date',
        'subject_tasks' => 'array',
    ];

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'student_class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTasksAttribute(): array
    {
        if (is_array($this->subject_tasks) && count($this->subject_tasks) > 0) {
            $tasks = [];
            foreach ($this->subject_tasks as $task) {
                if (isset($task['subject_id'])) {
                    $subject = Subject::find($task['subject_id']);
                    if ($subject) {
                        $task['subject_name'] = $subject->subject_name;
                        $task['subject_code'] = $subject->subject_code;
                    }
                }
                $tasks[] = $task;
            }
            return $tasks;
        }

        if ($this->subject) {
            return [
                [
                    'subject_id'   => $this->subject_id,
                    'subject_name' => $this->subject->subject_name,
                    'subject_code' => $this->subject->subject_code ?? null,
                    'title'        => $this->title,
                    'description'  => $this->description,
                    'due_date'     => $this->due_date ? $this->due_date->format('Y-m-d') : null,
                    'attachment'   => $this->attachment,
                    'status'       => $this->status,
                ]
            ];
        }

        return [];
    }
}

