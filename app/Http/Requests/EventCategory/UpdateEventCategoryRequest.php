<?php

namespace App\Http\Requests\EventCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('event_category') ? $this->route('event_category')->id : $this->route('id');

        return [
            'name' => 'required|string|max:100|unique:event_categories,name,' . $id,
            'code' => 'nullable|string|max:50',
            'badge_class' => 'nullable|string|max:50',
            'status' => 'required|in:0,1',
            'description' => 'nullable|string|max:500',
        ];
    }
}
