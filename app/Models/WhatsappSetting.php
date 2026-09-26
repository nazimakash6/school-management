<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'is_connected',
        'phone_number',
        'device_name',
        'connected_at',
        'auto_attendance_alert',
        'auto_fee_reminder',
        'auto_exam_result',
        'auto_visitor_alert',
    ];

    protected $casts = [
        'is_connected'          => 'boolean',
        'connected_at'          => 'datetime',
        'auto_attendance_alert' => 'boolean',
        'auto_fee_reminder'     => 'boolean',
        'auto_exam_result'      => 'boolean',
        'auto_visitor_alert'    => 'boolean',
    ];

    public static function getSingleton(): self
    {
        return self::firstOrCreate([], [
            'is_connected'          => true,
            'phone_number'          => '+92 300 9876543',
            'device_name'           => 'School Official WhatsApp Web',
            'connected_at'          => now(),
            'auto_attendance_alert' => true,
            'auto_fee_reminder'     => true,
            'auto_exam_result'      => true,
            'auto_visitor_alert'    => true,
        ]);
    }
}
