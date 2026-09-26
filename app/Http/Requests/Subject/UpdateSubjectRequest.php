<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_code'     => ['required', 'string', 'max:50'],
            'subject_name'     => ['required', 'string', 'max:255'],
            'subject_type_id'  => ['nullable', 'exists:subject_types,id'],
            'subject_type'     => ['nullable', 'string', 'max:100'],
            'class_name'       => ['required', 'string', 'max:100'],
            'staff_id'         => ['nullable', 'exists:staff,id'],
            'status'           => ['required', 'string', 'in:active,inactive'],
            'description'      => ['nullable', 'string'],
        ];
    }
}
