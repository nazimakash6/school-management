<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpBan extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'banned_by_user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by_user_id');
    }
}
