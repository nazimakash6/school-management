<?php

namespace App\Http\Requests\AcademicSession;

use App\Enum\AcademinSessionEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAcademicSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_name' => [
                'required',
                'string',
                'max:255',
                'unique:academic_sessions,session_name',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'status' => [
                'required',
                new Enum(AcademinSessionEnum::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'session_name.required' => 'Session name is required.',
            'session_name.unique'   => 'This academic session already exists.',
            'start_date.required'   => 'Start date is required.',
            'end_date.required'     => 'End date is required.',
            'end_date.after'        => 'End date must be after the start date.',
            'status.required'       => 'Status is required.',
        ];
    }
}
