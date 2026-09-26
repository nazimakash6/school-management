<?php

namespace App\Http\Requests\SubjectType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('subject_type') ?? $this->route('id');
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:50', 'unique:subject_types,code,' . $id],
            'badge_class' => ['nullable', 'string', 'max:100'],
            'status'      => ['required', 'string', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ];
    }
}
