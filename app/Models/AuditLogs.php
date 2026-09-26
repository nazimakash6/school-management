<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLogs extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'event_type',
        'action',
        'description',
        'route',
        'method',
        'ip_address',
        'user_agent',
        'context',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $eventType, string $action, string $description, array $context = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'event_type' => $eventType,
            'action' => $action,
            'description' => $description,
            'route' => request()->path(),
            'method' => request()->method(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => json_encode($context),
        ]);
    }
}
