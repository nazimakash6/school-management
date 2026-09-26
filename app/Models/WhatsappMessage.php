<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'recipient_type',
        'recipient_name',
        'phone_number',
        'message_type',
        'template_name',
        'message',
        'attachment_path',
        'status',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Read'      => 'bg-success text-white',
            'Delivered' => 'bg-info text-dark',
            'Sent'      => 'bg-primary text-white',
            'Failed'    => 'bg-danger text-white',
            default     => 'bg-secondary text-white',
        };
    }
}
