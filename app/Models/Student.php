<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'students';

    protected $guarded = [];

    protected $casts = [
        'admission_date' => 'date',
        'date_of_birth'  => 'date',
        'is_confirmed'   => 'boolean',
        'class_fee'      => 'decimal:2',
    ];

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_name', 'name');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_no', 'admission_no');
    }

    public function getFullNameAttribute(): string
    {
        return ucwords(strtolower(trim("{$this->first_name} {$this->last_name}")));
    }

    public function disciplines()
    {
        return $this->hasMany(StudentDiscipline::class, 'student_id');
    }

    public function getWeeklyStarRatingAttribute(): float
    {
        $avg = $this->disciplines()
            ->whereBetween('entry_date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])
            ->avg('star_rating');

        return $avg ? round($avg, 1) : 0.0;
    }

    public function getMonthlyStarRatingAttribute(): float
    {
        $avg = $this->disciplines()
            ->whereMonth('entry_date', \Carbon\Carbon::now()->month)
            ->whereYear('entry_date', \Carbon\Carbon::now()->year)
            ->avg('star_rating');

        return $avg ? round($avg, 1) : 0.0;
    }

    public function getYearlyStarRatingAttribute(): float
    {
        $avg = $this->disciplines()
            ->whereYear('entry_date', \Carbon\Carbon::now()->year)
            ->avg('star_rating');

        return $avg ? round($avg, 1) : 0.0;
    }

    public function skills()
    {
        return $this->hasMany(\App\Models\StudentSkill::class, 'student_id');
    }

    public function getSkillStarRatingAttribute(): float
    {
        $avg = $this->skills()->avg('star_rating');
        return $avg ? round($avg, 1) : 0.0;
    }

    public function sibling(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'sibling_id');
    }

    public function getSiblingsAttribute()
    {
        $siblingIds = array_filter([$this->sibling_id]);
        $pointedToMe = Student::where('sibling_id', $this->id)->pluck('id')->toArray();
        $siblingIds = array_merge($siblingIds, $pointedToMe);

        if ($this->sibling_id) {
            $sharedSibling = Student::where('sibling_id', $this->sibling_id)->pluck('id')->toArray();
            $siblingIds = array_merge($siblingIds, $sharedSibling);
        }

        $query = Student::query()->where('id', '!=', $this->id);

        $fatherCnic = $this->father_cnic;
        $guardianCnic = $this->guardian_cnic;

        $query->where(function ($q) use ($siblingIds, $fatherCnic, $guardianCnic) {
            if (!empty($siblingIds)) {
                $q->whereIn('id', array_unique($siblingIds));
            }
            if (!empty($fatherCnic)) {
                $q->orWhere('father_cnic', $fatherCnic);
            }
            if (!empty($guardianCnic)) {
                $q->orWhere('guardian_cnic', $guardianCnic);
            }
        });

        if (empty($siblingIds) && empty($fatherCnic) && empty($guardianCnic)) {
            return collect();
        }

        return $query->get();
    }
}

