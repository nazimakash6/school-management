<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectType extends Model
{
    use HasFactory;

    protected $table = 'subject_types';

    protected $fillable = [
        'name',
        'code',
        'badge_class',
        'status',
        'description',
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'subject_type_id');
    }
}
