<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BookIssue extends Model
{
    use HasFactory;

    protected $table = 'book_issues';

    protected $guarded = [];

    protected $casts = [
        'issue_date'  => 'date',
        'due_date'    => 'date',
        'return_date' => 'date',
        'fine_amount' => 'float',
        'fine_paid'   => 'boolean',
    ];

    public function library(): BelongsTo
    {
        return $this->belongsTo(Library::class, 'library_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    protected static function booted(): void
    {
        static::creating(function (BookIssue $issue) {
            if (empty($issue->issue_code)) {
                $issue->issue_code = 'ISS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->status === 'Issued' && $this->due_date && $this->due_date->isPast()) {
            return 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
        }

        return match ($this->status) {
            'Issued'   => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',
            'Returned' => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            'Overdue'  => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
            'Lost'     => 'bg-dark text-white',
            default    => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
        };
    }
}
