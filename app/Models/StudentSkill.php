<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSkill extends Model
{
    use HasFactory;

    protected $table = 'student_skills';

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'student_class_id',
        'evaluation_date',
        'skill_category',
        'skill_name',
        'assessment_type',
        'performance_period',
        'total_score',
        'obtained_score',
        'star_rating',
        'badge_level',
        'instructor_notes',
        'skills_data',
        'created_by',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'total_score'     => 'float',
        'obtained_score'  => 'float',
        'star_rating'     => 'float',
        'skills_data'     => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'student_class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get structured array of all skills in this evaluation
     */
    public function getSkillsListAttribute(): array
    {
        if (!empty($this->skills_data) && is_array($this->skills_data)) {
            return $this->skills_data;
        }

        return [
            [
                'skill_name'         => $this->skill_name,
                'skill_category'     => $this->skill_category,
                'assessment_type'    => $this->assessment_type,
                'performance_period' => $this->performance_period,
                'total_score'        => $this->total_score,
                'obtained_score'     => $this->obtained_score,
                'star_rating'        => $this->star_rating,
                'badge_level'        => $this->badge_level,
                'instructor_notes'   => $this->instructor_notes,
            ]
        ];
    }

    /**
     * Compute star rating (0.0 to 5.0) from obtained and total score
     */
    public static function calculateStarRating(float|int $obtained, float|int $total = 100): float
    {
        if ($total <= 0) return 0.0;
        $rating = ($obtained / $total) * 5.0;
        return round(max(0, min(5.0, $rating)), 1);
    }

    /**
     * Determine badge level string based on star rating
     */
    public static function determineBadgeLevel(float $starRating): string
    {
        if ($starRating >= 4.8) return 'Master Skilled (5★)';
        if ($starRating >= 4.0) return 'Expert (4.0 - 4.7★)';
        if ($starRating >= 3.0) return 'Proficient (3.0 - 3.9★)';
        if ($starRating >= 2.0) return 'Developing (2.0 - 2.9★)';
        return 'Beginner (<2.0★)';
    }

    /**
     * Badge CSS class for star rating
     */
    public function getBadgeClassAttribute(): string
    {
        $stars = $this->star_rating;
        if ($stars >= 4.8) return 'bg-warning text-dark border border-warning';
        if ($stars >= 4.0) return 'bg-success text-white';
        if ($stars >= 3.0) return 'bg-info text-dark';
        if ($stars >= 2.0) return 'bg-secondary text-white';
        return 'bg-danger text-white';
    }
}
