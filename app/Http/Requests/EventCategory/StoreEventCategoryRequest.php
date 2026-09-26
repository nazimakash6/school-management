<?php

namespace App\Http\Requests\EventCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:event_categories,name',
            'code' => 'nullable|string|max:50',
            'badge_class' => 'nullable|string|max:50',
            'status' => 'required|in:0,1',
            'description' => 'nullable|string|max:500',
        ];
    }
}
