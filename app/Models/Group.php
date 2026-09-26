<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'name',
        'subjects',
        'description',
        'status',
    ];

    protected $casts = [
        'subjects' => 'array',
    ];

    /**
     * Get subjects count attribute.
     */
    public function getSubjectCountAttribute(): int
    {
        return is_array($this->subjects) ? count($this->subjects) : 0;
    }
}
