<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    public static function getByKey(string $key, $default = null)
    {
        if (in_array($key, ['school_name', 'school_tagline', 'tagline', 'school_email', 'email', 'school_phone', 'phone', 'school_address', 'address', 'currency_symbol', 'school_logo', 'school_code', 'principal_name'])) {
            if (\Illuminate\Support\Facades\Schema::hasTable('school_infos')) {
                $schoolInfo = \App\Models\SchoolInfo::first();
                if ($schoolInfo) {
                    switch ($key) {
                        case 'school_name':
                            return $schoolInfo->school_name ?: $default;
                        case 'school_tagline':
                        case 'tagline':
                            return $schoolInfo->tagline ?: $default;
                        case 'school_email':
                        case 'email':
                            return $schoolInfo->email ?: $default;
                        case 'school_phone':
                        case 'phone':
                            return $schoolInfo->phone ?: $default;
                        case 'school_address':
                        case 'address':
                            return $schoolInfo->address ?: $default;
                        case 'currency_symbol':
                            return $schoolInfo->currency_symbol ?: $default;
                        case 'school_logo':
                            return $schoolInfo->logo_path ?: $default;
                        case 'school_code':
                            return $schoolInfo->school_code ?: $default;
                        case 'principal_name':
                            return $schoolInfo->principal_name ?: $default;
                    }
                }
            }
        }

        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setByKey(string $key, $value, ?string $group = null, ?string $type = null, ?string $description = null)
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            $setting->value = $value;
            if ($group) $setting->group = $group;
            if ($type) $setting->type = $type;
            if ($description) $setting->description = $description;
            $setting->save();
            return $setting;
        }

        return static::create([
            'key' => $key,
            'value' => $value,
            'group' => $group ?? 'general',
            'type' => $type ?? 'text',
            'description' => $description,
        ]);
    }
}
