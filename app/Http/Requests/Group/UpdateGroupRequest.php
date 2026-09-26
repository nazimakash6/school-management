<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('group') ?? $this->route('id');
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'name'        => ['required', 'string', 'max:255', 'unique:groups,name,' . $id],
            'subject'     => ['nullable', 'array'],
            'subject.*'   => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'string', 'in:active,inactive'],
        ];
    }
}
