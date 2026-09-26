<?php

namespace App\Http\Requests\StudentClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateStudentClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('student_class') ?? $this->route('id');
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'name'            => ['required', 'string', 'max:255', 'unique:student_classes,name,' . $id],
            'level'           => ['required', 'string', 'max:255'],
            'group'           => ['nullable', 'string', 'max:255'],
            'staff_table_id'  => ['nullable', 'exists:staff,id'],
            'section_names'   => ['required', 'array', 'min:1'],
            'section_names.*' => ['required', 'string', 'max:50'],
            'status'          => ['required', 'in:active,inactive'],
            'description'     => ['nullable', 'string'],
        ];
    }

    public function passedValidation(): void
    {
        $sectionNames = collect($this->input('section_names', []))
            ->map(fn($sectionName) => trim((string) $sectionName))
            ->filter(fn($sectionName) => $sectionName !== '')
            ->unique()
            ->values();

        if ($sectionNames->isEmpty()) {
            throw ValidationException::withMessages([
                'section_names' => ['At least one section name is required.'],
            ]);
        }

        $this->merge([
            'section_names' => $sectionNames->all(),
            'section_count' => $sectionNames->count(),
        ]);
    }
}
