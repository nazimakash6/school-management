<?php

namespace App\Http\Requests\House;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $house = $this->route('house');
        $id = is_object($house) ? $house->id : $house;

        return [
            'name'        => ['required', 'string', 'max:255', 'unique:houses,name,' . $id],
            'code'        => ['nullable', 'string', 'max:50'],
            'color'       => ['nullable', 'string', 'max:50'],
            'staff_id'      => ['nullable', 'exists:staff,id'],
            'student_ids'   => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'description'   => ['nullable', 'string'],
            'status'      => ['required', 'in:active,inactive'],
        ];
    }
}
