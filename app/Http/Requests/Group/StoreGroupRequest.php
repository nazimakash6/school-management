<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:groups,name'],
            'subject'     => ['nullable', 'array'],
            'subject.*'   => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'string', 'in:active,inactive'],
        ];
    }
}
