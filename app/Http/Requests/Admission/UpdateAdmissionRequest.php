<?php

namespace App\Http\Requests\Admission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $cnicFields = ['cnic_bform', 'father_cnic', 'mother_cnic', 'guardian_cnic'];
        $phoneFields = ['student_mobile_no', 'father_phone', 'mother_phone', 'guardian_primary_mobile_no', 'guardian_secondary_mobile_no', 'emergency_contact_mobile_no'];

        $merges = [
            'hostel' => $this->input('hostel') ?: 'no',
            'transportation_required' => $this->input('transportation_required') ?: 'no',
        ];

        foreach ($cnicFields as $field) {
            if ($this->has($field) && $this->input($field) !== null) {
                $cleaned = preg_replace('/[^0-9]/', '', (string) $this->input($field));
                $merges[$field] = $cleaned !== '' ? $cleaned : null;
            }
        }

        foreach ($phoneFields as $field) {
            if ($this->has($field) && $this->input($field) !== null) {
                $cleaned = preg_replace('/[^0-9]/', '', (string) $this->input($field));
                if (str_starts_with($cleaned, '92') && strlen($cleaned) === 12) {
                    $cleaned = '0' . substr($cleaned, 2);
                } elseif (str_starts_with($cleaned, '0092') && strlen($cleaned) === 14) {
                    $cleaned = '0' . substr($cleaned, 4);
                }
                $merges[$field] = $cleaned !== '' ? $cleaned : null;
            }
        }

        $this->merge($merges);
    }

    public function rules(): array
    {
        $admission = $this->route('admission');
        $admissionId = is_object($admission) ? $admission->id : $admission;

        return [
            'sibling_id' => ['nullable', 'integer'],
            // Academic Session
            'academic_session_id' => ['required', Rule::exists('academic_sessions', 'id')],

            // Personal Information
            'admission_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('admissions', 'admission_no')->ignore($admissionId),
            ],
            'admission_date' => ['required', 'date'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'cnic_bform' => ['nullable', 'string', 'digits:13'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other', 'male', 'female', 'other'])],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'student_mobile_no' => ['nullable', 'string', 'digits:11'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'student_medical_notes' => ['nullable', 'string', 'max:1000'],

            // Guardian Information
            'father_name' => ['required', 'string', 'max:255'],
            'father_cnic' => ['nullable', 'string', 'digits:13'],
            'father_phone' => ['nullable', 'string', 'digits:11'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_cnic' => ['nullable', 'string', 'digits:13'],
            'mother_phone' => ['nullable', 'string', 'digits:11'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_cnic' => ['nullable', 'string', 'digits:13'],
            'guardian_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_primary_mobile_no' => ['nullable', 'string', 'digits:11'],
            'guardian_secondary_mobile_no' => ['nullable', 'string', 'digits:11'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_address' => ['nullable', 'string', 'max:500'],

            // Academic Information
            'class_name' => ['required', 'string', 'max:255'],
            'section_name' => ['nullable', 'string', 'max:255'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:255'],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'class_shift' => ['nullable', 'string', 'max:100'],
            'admission_type' => ['nullable', 'string', 'max:100'],
            'fee_plan' => ['nullable', 'string', 'max:100'],
            'fee_status' => ['nullable', Rule::in(['pending', 'paid', 'partial'])],
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'quarterly_fee' => ['nullable', 'numeric', 'min:0'],
            'annual_fee' => ['nullable', 'numeric', 'min:0'],
            'scholarship_discount' => ['nullable', 'max:255'],
            'academic_notes' => ['nullable', 'string'],

            // Address & Transportation
            'current_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'transportation_required' => ['nullable', 'string'],
            'transportation_route' => ['nullable', 'string', 'max:255'],
            'hostel' => ['nullable', Rule::in(['yes', 'no'])],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'emergency_contact_mobile_no' => ['nullable', 'string', 'digits:11'],

            // Documents
            'birth_certificate' => ['nullable', 'string'],
            'bform_cnic_copy' => ['nullable', 'string'],
            'guardian_cnic_copy' => ['nullable', 'string'],
            'school_leaving_certificate' => ['nullable', 'string'],
            'student_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,avif,webp', 'max:2048'],

            'attached_documents' => ['nullable', 'array'],
            'attached_documents.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,zip,txt', 'max:10240'],

            // Status & Remarks
            'admission_status' => ['nullable', 'string', 'max:50'],
            'admission_remarks' => ['nullable', 'string'],
            'is_confirmed' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'cnic_bform.digits' => 'Student CNIC / B-Form must be exactly 13 digits.',
            'father_cnic.digits' => 'Father CNIC must be exactly 13 digits.',
            'mother_cnic.digits' => 'Mother CNIC must be exactly 13 digits.',
            'guardian_cnic.digits' => 'Guardian CNIC must be exactly 13 digits.',
            'student_mobile_no.digits' => 'Student Mobile number must be exactly 11 digits.',
            'father_phone.digits' => 'Father Phone number must be exactly 11 digits.',
            'mother_phone.digits' => 'Mother Phone number must be exactly 11 digits.',
            'guardian_primary_mobile_no.digits' => 'Guardian Primary Mobile number must be exactly 11 digits.',
            'guardian_secondary_mobile_no.digits' => 'Guardian Secondary Mobile number must be exactly 11 digits.',
            'emergency_contact_mobile_no.digits' => 'Emergency Contact Phone number must be exactly 11 digits.',
        ];
    }
}
