<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'recipient_name',
        'recipient_email',
        'recipient_type',
        'subject',
        'message',
        'message_type',
        'template_name',
        'status',
        'error_message',
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
}
