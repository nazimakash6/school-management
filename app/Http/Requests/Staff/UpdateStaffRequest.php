<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'dob' => ['required', 'date'],
            'cnic' => ['required', 'string', 'max:13'],
            'marital_status' => ['required', 'in:single,married,divorced,widowed'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'mobile_no' => ['required', 'string', 'max:20'],
            'alternate_mobile_no' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'current_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['required', 'string', 'max:500'],
            'joining_date' => ['required', 'date'],
            'leaving_date' => ['nullable', 'date'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship'],
            'shift' => ['nullable', 'string', 'max:255'],
            'salary' => ['required', 'numeric'],
            'salary_type' => ['nullable', 'in:monthly,weekly,daily,hourly'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_title' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:34'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'note' => ['nullable', 'string'],
        ];
    }
}
