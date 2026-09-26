<?php

namespace App\Http\Requests\StudentPromotion;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentPromotion extends FormRequest
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
    public function rules(): array
    {
        return [
            'admission_id' => ['sometimes', 'required', 'exists:admissions,id'],
            'promoted_from_class' => ['sometimes', 'required', 'string'],
            'promoted_to_class' => ['sometimes', 'required', 'string'],
            'promotion_type' => ['sometimes', 'required', 'in:exam,teacher'],
            'promotion_date' => ['sometimes', 'required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
