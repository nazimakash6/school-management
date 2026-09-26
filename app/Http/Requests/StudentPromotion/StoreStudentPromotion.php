<?php

namespace App\Http\Requests\StudentPromotion;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentPromotion extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation()
    {
        if (empty($this->target_academic_session_id)) {
            $this->merge([
                'target_academic_session_id' => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:admissions,id'],
            'promote_to_class' => ['required', 'string'],
            'target_academic_session_id' => ['nullable', 'exists:academic_sessions,id'],
            'promotion_type' => ['required', 'in:exam,teacher'],
            'promotion_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
