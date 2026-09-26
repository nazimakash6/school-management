<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'promoted_from_class',
        'promoted_to_class',
        'promotion_type',
        'promotion_date',
        'notes',
    ];

    protected $casts = [
        'promotion_date' => 'date',
        'promotion_type' => \App\Enum\PromotionTypeEnum::class,
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
