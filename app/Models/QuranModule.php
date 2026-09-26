<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranModule extends Model
{
    use HasFactory;

    protected $table = 'quran_modules';

    protected $guarded = [];

    protected $casts = [
        'entry_date' => 'date',
        'score'      => 'float',
        'total_parahs_memorized' => 'integer',
        'mistakes_count' => 'integer',
        'para_no'    => 'integer',
        'ayah_from'  => 'integer',
        'ayah_to'    => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'student_class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedAyahRangeAttribute(): string
    {
        if ($this->ayah_from && $this->ayah_to) {
            return "v{$this->ayah_from}-{$this->ayah_to}";
        } elseif ($this->ayah_from) {
            return "v{$this->ayah_from}";
        }
        return '';
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            'Hifz'     => 'bg-purple-subtle text-purple border-purple-subtle',
            'Nazra'    => 'bg-primary-subtle text-primary border-primary-subtle',
            'Qaida'    => 'bg-info-subtle text-info border-info-subtle',
            'Tajweed'  => 'bg-warning-subtle text-warning border-warning-subtle',
            'Hadith'   => 'bg-success-subtle text-success border-success-subtle',
            'Dua'      => 'bg-teal-subtle text-teal border-teal-subtle',
            default    => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Completed', 'Passed', 'Excellent' => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            'In Progress'                     => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',
            'Needs Improvement'               => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
            default                           => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
        };
    }
}
