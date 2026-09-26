<?php

namespace App\Http\Requests\SchoolInfo;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name'       => ['required', 'string', 'max:255'],
            'school_code'       => ['nullable', 'string', 'max:50'],
            'tagline'           => ['nullable', 'string', 'max:255'],
            'email'             => ['nullable', 'email', 'max:255'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'alternate_phone'   => ['nullable', 'string', 'max:50'],
            'website'           => ['nullable', 'string', 'max:255'],
            'address'           => ['nullable', 'string'],
            'city'              => ['nullable', 'string', 'max:100'],
            'state'             => ['nullable', 'string', 'max:100'],
            'postal_code'       => ['nullable', 'string', 'max:30'],
            'established_year'  => ['nullable', 'string', 'max:10'],
            'affiliation_board' => ['nullable', 'string', 'max:150'],
            'registration_no'   => ['nullable', 'string', 'max:100'],
            'principal_name'    => ['nullable', 'string', 'max:150'],
            'currency_symbol'   => ['nullable', 'string', 'max:10'],
            'logo'              => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'stamp'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'social_facebook'   => ['nullable', 'string', 'max:255'],
            'social_instagram'  => ['nullable', 'string', 'max:255'],
            'social_twitter'    => ['nullable', 'string', 'max:255'],
        ];
    }
}
