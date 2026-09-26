<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Library extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'libraries';

    protected $guarded = [];

    protected $casts = [
        'total_copies'     => 'integer',
        'available_copies' => 'integer',
        'issued_copies'    => 'integer',
        'price'            => 'float',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'library_id');
    }

    public function activeIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'library_id')->where('status', 'Issued');
    }

    protected static function booted(): void
    {
        static::saving(function (Library $book) {
            // Auto generate Book Code if empty
            if (empty($book->book_code)) {
                $prefix = match($book->category) {
                    'Islamic Studies'     => 'BK-ISL',
                    'Science & Tech'      => 'BK-SCI',
                    'Mathematics'         => 'BK-MTH',
                    'Literature & Fiction'=> 'BK-LIT',
                    'History & Geography' => 'BK-HIS',
                    default               => 'BK-GEN',
                };
                $book->book_code = $prefix . '-' . rand(100, 999);
            }

            // Sync available copies and status
            if ($book->available_copies <= 0) {
                $book->status = 'Fully Issued';
            } else {
                $book->status = 'Available';
            }
        });
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
                return $this->cover_image;
            }
            return asset('storage/' . $this->cover_image);
        }
        
        // Fallback default book cover image
        return "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&auto=format&fit=crop&q=80";
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            'Islamic Studies'      => 'bg-purple-subtle text-purple border-purple-subtle',
            'Science & Tech'       => 'bg-primary-subtle text-primary border-primary-subtle',
            'Mathematics'          => 'bg-info-subtle text-info border-info-subtle',
            'Literature & Fiction' => 'bg-teal-subtle text-teal border-teal-subtle',
            'History & Geography'  => 'bg-warning-subtle text-warning border-warning-subtle',
            default                => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Available'          => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            'Fully Issued'       => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle',
            'Lost / Out of Print'=> 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
            default              => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
        };
    }
}
