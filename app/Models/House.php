<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'color',
        'staff_id',
        'student_ids',
        'description',
        'status',
    ];

    protected $casts = [
        'student_ids' => 'array',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'house_name', 'name');
    }
}
