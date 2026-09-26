<?php

namespace App\Http\Requests\EventActivity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id'              => ['required', 'exists:events,id'],
            'categories'            => ['nullable', 'array'],
            'categories.*'          => ['exists:event_categories,id'],
            'event_category_id'     => ['nullable', 'exists:event_categories,id'],
            'name'                  => ['required', 'string', 'max:255'],
            'category'              => ['nullable', 'string', 'max:100'],
            'activity_date'         => ['nullable', 'date'],
            'start_time'            => ['nullable'],
            'end_time'              => ['nullable'],
            'venue'                 => ['nullable', 'string', 'max:255'],
            'house_ids'             => ['nullable', 'array'],
            'house_ids.*'           => ['exists:houses,id'],
            'status'                => ['required', 'string', 'in:Scheduled,Ongoing,Completed,Cancelled'],
            'winner_house_id'       => ['nullable', 'exists:houses,id'],
            'runner_up_house_id'    => ['nullable', 'exists:houses,id'],
            'third_place_house_id'  => ['nullable', 'exists:houses,id'],
            'rules_notes'           => ['nullable', 'string'],
        ];
    }
}
