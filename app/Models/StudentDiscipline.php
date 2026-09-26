<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDiscipline extends Model
{
    use HasFactory;

    protected $table = 'student_disciplines';

    protected $fillable = [
        'student_id',
        'student_class_id',
        'entry_date',
        'category',
        'category_ratings',
        'performance_period',
        'title',
        'total_score',
        'obtained_score',
        'star_rating',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'category_ratings' => 'array',
        'total_score' => 'float',
        'obtained_score' => 'float',
        'star_rating' => 'float',
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

    /**
     * Helper to compute star rating from obtained & total score
     */
    public static function calculateStarRating(float|int $obtained, float|int $total = 100): float
    {
        if ($total <= 0) return 0.0;
        $rating = ($obtained / $total) * 5.0;
        return round(max(0, min(5.0, $rating)), 1);
    }
}
