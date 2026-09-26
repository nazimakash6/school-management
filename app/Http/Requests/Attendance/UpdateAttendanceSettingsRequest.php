<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_open_time'  => ['required', 'date_format:H:i,H:i:s'],
            'school_start_time' => ['required', 'date_format:H:i,H:i:s'],
            'school_break_time' => ['required', 'date_format:H:i,H:i:s'],
            'school_end_time'   => ['required', 'date_format:H:i,H:i:s'],
        ];
    }
}
