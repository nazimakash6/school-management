<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_type'                  => ['required', 'in:student,staff'],
            'date'                       => ['required', 'date'],
            'session'                    => ['nullable', 'string'],
            'attendance'                 => ['required', 'array'],
            'attendance.*.user_id'       => ['required', 'integer'],
            'attendance.*.status'        => ['nullable', 'in:present,absent,late,leave'],
            'attendance.*.s1_status'     => ['nullable', 'in:present,absent,late,leave'],
            'attendance.*.remarks'       => ['nullable', 'string', 'max:255'],
        ];
    }
}
