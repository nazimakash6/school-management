<?php

namespace App\Models;

use App\Enum\AcademinSessionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSession extends Model
{
    use SoftDeletes, HasFactory;

     protected $table = 'academic_sessions';

    protected $fillable = [
        'session_name',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => AcademinSessionEnum::class,
    ];

    public function getNameAttribute(): ?string
    {
        return $this->attributes['session_name'] ?? ($this->attributes['name'] ?? null);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'academic_session_id');
    }
}
