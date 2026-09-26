<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fee_payments';

    protected $fillable = [
        'fee_management_id',
        'admission_id',
        'receipt_no',
        'amount',
        'payment_method',
        'payment_date',
        'note',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function feeManagement(): BelongsTo
    {
        return $this->belongsTo(FeeManagement::class, 'fee_management_id');
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
