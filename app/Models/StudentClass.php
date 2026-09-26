<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClass extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'level',
        'group',
        'staff_table_id',
        'section_count',
        'section_names',
        'status',
        'description',
    ];

    protected $casts = [
        'section_names' => 'array',
        'status'        => \App\Enum\StatusEnum::class,
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_name', 'name');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'staff_table_id');
    }

    public function classIncharge()
    {
        return $this->belongsTo(Staff::class, 'staff_table_id');
    }

    public function homeworks(): HasMany
    {
        return $this->hasMany(HomeWork::class, 'student_class_id');
    }

    public function classworks(): HasMany
    {
        return $this->hasMany(ClassWork::class, 'student_class_id');
    }
}
