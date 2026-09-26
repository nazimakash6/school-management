<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolInfo extends Model
{
    use HasFactory;

    protected $table = 'school_infos';

    protected $fillable = [
        'school_name',
        'school_code',
        'tagline',
        'email',
        'phone',
        'alternate_phone',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'established_year',
        'affiliation_board',
        'registration_no',
        'principal_name',
        'currency_symbol',
        'logo_path',
        'stamp_path',
        'social_facebook',
        'social_instagram',
        'social_twitter',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }

    public function getStampUrlAttribute(): ?string
    {
        if ($this->stamp_path && Storage::disk('public')->exists($this->stamp_path)) {
            return asset('storage/' . $this->stamp_path);
        }
        return null;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->state]);
        return implode(', ', $parts) ?: 'Main Campus, Educational Complex';
    }
}
