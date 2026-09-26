<?php

namespace App\Http\Requests\SubjectType;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:50', 'unique:subject_types,code'],
            'badge_class' => ['nullable', 'string', 'max:100'],
            'status'      => ['required', 'string', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ];
    }
}
