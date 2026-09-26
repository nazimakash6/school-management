<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admissions';

    protected $fillable = [
        'academic_session_id',

        // Personal Information
        'admission_no',
        'admission_date',
        'first_name',
        'last_name',
        'cnic_bform',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'nationality',
        'student_mobile_no',
        'student_email',
        'previous_school',
        'student_medical_notes',

        // Guardian Information
        'father_name',
        'father_cnic',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_cnic',
        'mother_phone',
        'mother_occupation',
        'guardian_name',
        'guardian_relation',
        'guardian_cnic',
        'guardian_occupation',
        'guardian_primary_mobile_no',
        'guardian_secondary_mobile_no',
        'guardian_email',
        'guardian_address',

        // Academic Information
        'class_name',
        'section_name',
        'group_name',
        'group',
        'roll_no',
        'class_shift',
        'admission_type',
        'fee_plan',
        'fee_status',
        'registration_fee',
        'monthly_fee',
        'quarterly_fee',
        'annual_fee',
        'scholarship_discount',
        'academic_notes',

        // Address & Transportation
        'current_address',
        'permanent_address',
        'transportation_required',
        'transportation_route',
        'hostel',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_mobile_no',

        // Documents
        'birth_certificate',
        'bform_cnic_copy',
        'guardian_cnic_copy',
        'school_leaving_certificate',
        'student_photo',
        'attached_documents',

        // Admission
        'admission_status',
        'admission_remarks',
        'is_confirmed',
        'sibling_id',
    ];

    protected $casts = [
        'admission_date'   => 'date',
        'date_of_birth'    => 'date',
        'attached_documents' => 'array',
        'is_confirmed'     => 'boolean',
    ];

    public function getStudentNameAttribute(): string
    {
        return ucwords(strtolower(trim("{$this->first_name} {$this->last_name}")));
    }

    public function getStatusAttribute(): string
    {
        return $this->admission_status ?: 'active';
    }

    public function promotions()
    {
        return $this->hasMany(StudentPromotion::class, 'admission_id')->orderBy('id', 'desc');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    protected static function booted(): void
    {
        static::updating(function (Admission $admission) {
            if ($admission->isDirty('admission_no')) {
                $oldNo = $admission->getOriginal('admission_no');
                if ($oldNo) {
                    Student::withTrashed()->where('admission_no', $oldNo)->update(['admission_no' => $admission->admission_no]);
                }
            }
        });

        static::saved(function (Admission $admission) {
            $classId = StudentClass::where('name', $admission->class_name)->value('id');

            $admStatus = strtolower((string) ($admission->admission_status ?: 'active'));
            if (in_array($admStatus, ['approved', 'confirmed'], true)) {
                $status = 'active';
            } elseif (in_array($admStatus, ['active', 'pending', 'inactive'], true)) {
                $status = $admStatus;
            } else {
                $status = 'active';
            }

            $activeSessionId = $admission->academic_session_id ?: (\App\Models\AcademicSession::where('status', 'Active')->value('id') ?: 1);

            $studentData = [
                'academic_session_id'          => $activeSessionId,
                'admission_no'                 => $admission->admission_no,
                'admission_date'               => $admission->admission_date ?: now(),
                'first_name'                   => $admission->first_name,
                'last_name'                    => $admission->last_name,
                'cnic_bform'                   => $admission->cnic_bform,
                'date_of_birth'                => $admission->date_of_birth,
                'gender'                       => $admission->gender ?: 'Male',
                'blood_group'                  => $admission->blood_group,
                'religion'                     => $admission->religion,
                'nationality'                  => $admission->nationality,
                'student_mobile_no'            => $admission->student_mobile_no,
                'student_email'                => $admission->student_email,
                'previous_school'              => $admission->previous_school,
                'student_medical_notes'        => $admission->student_medical_notes,

                'father_name'                  => $admission->father_name ?: ($admission->guardian_name ?: 'N/A'),
                'father_cnic'                  => $admission->father_cnic,
                'father_phone'                 => $admission->father_phone,
                'father_occupation'            => $admission->father_occupation ?: $admission->guardian_occupation,
                'mother_name'                  => $admission->mother_name,
                'mother_cnic'                  => $admission->mother_cnic,
                'mother_phone'                 => $admission->mother_phone,
                'mother_occupation'            => $admission->mother_occupation,
                'guardian_name'                => $admission->guardian_name ?: $admission->father_name,
                'guardian_relation'            => $admission->guardian_relation,
                'guardian_cnic'                => $admission->guardian_cnic ?: ($admission->father_cnic ?: $admission->cnic_bform),
                'guardian_occupation'          => $admission->guardian_occupation,
                'guardian_primary_mobile_no'   => $admission->guardian_primary_mobile_no ?: $admission->father_phone,
                'guardian_secondary_mobile_no' => $admission->guardian_secondary_mobile_no,
                'guardian_email'               => $admission->guardian_email,
                'guardian_address'             => $admission->guardian_address ?: $admission->current_address,

                'class_name'                   => $admission->class_name,
                'section_name'                 => $admission->section_name,
                'group_name'                   => $admission->group_name ?: $admission->group,
                'group'                        => $admission->group ?: $admission->group_name,
                'roll_no'                      => $admission->roll_no,
                'class_shift'                  => $admission->class_shift,
                'admission_type'               => $admission->admission_type,
                'academic_notes'               => $admission->academic_notes,

                'current_address'              => $admission->current_address ?: ($admission->guardian_address ?: 'N/A'),
                'permanent_address'            => $admission->permanent_address,
                'transportation_required'      => $admission->transportation_required,
                'transportation_route'         => $admission->transportation_route,
                'hostel'                       => $admission->hostel ?: 'no',
                'emergency_contact_name'       => $admission->emergency_contact_name ?: $admission->father_name,
                'emergency_contact_relation'   => $admission->emergency_contact_relation,
                'emergency_contact_mobile_no'  => $admission->emergency_contact_mobile_no ?: $admission->guardian_primary_mobile_no,

                'admission_remarks'            => $admission->admission_remarks,
                'is_confirmed'                 => (bool) ($admission->is_confirmed ?? false),
                'sibling_id'                    => $admission->sibling_id,
                'status'                       => $status,
            ];

            // Update admission if academic_session_id was missing
            if (!$admission->academic_session_id) {
                \DB::table('admissions')->where('id', $admission->id)->update(['academic_session_id' => $activeSessionId]);
            }

            Student::withTrashed()->updateOrCreate(
                ['admission_no' => $admission->admission_no],
                $studentData
            );
        });

        static::deleted(function (Admission $admission) {
            Student::where('admission_no', $admission->admission_no)->delete();
        });

        static::restored(function (Admission $admission) {
            Student::withTrashed()->where('admission_no', $admission->admission_no)->restore();
        });

        static::forceDeleted(function (Admission $admission) {
            Student::withTrashed()->where('admission_no', $admission->admission_no)->forceDelete();
        });
    }

    public function siblingStudent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class, 'sibling_id');
    }

    public function getSiblingsAttribute()
    {
        $student = Student::where('admission_no', $this->admission_no)->first();
        $studentId = $student ? $student->id : null;

        $siblingIds = array_filter([$this->sibling_id]);
        if ($studentId) {
            $pointedToMe = Student::where('sibling_id', $studentId)->pluck('id')->toArray();
            $siblingIds = array_merge($siblingIds, $pointedToMe);
        }
        if ($this->sibling_id) {
            $sharedSibling = Student::where('sibling_id', $this->sibling_id)->pluck('id')->toArray();
            $siblingIds = array_merge($siblingIds, $sharedSibling);
        }

        $query = Student::query();
        if ($studentId) {
            $query->where('id', '!=', $studentId);
        }
        if ($this->admission_no) {
            $query->where('admission_no', '!=', $this->admission_no);
        }

        $fatherCnic = $this->father_cnic;
        $guardianCnic = $this->guardian_cnic;

        $query->where(function ($q) use ($siblingIds, $fatherCnic, $guardianCnic) {
            if (!empty($siblingIds)) {
                $q->whereIn('id', array_unique($siblingIds));
            }
            if (!empty($fatherCnic)) {
                $q->orWhere('father_cnic', $fatherCnic);
            }
            if (!empty($guardianCnic)) {
                $q->orWhere('guardian_cnic', $guardianCnic);
            }
        });

        if (empty($siblingIds) && empty($fatherCnic) && empty($guardianCnic)) {
            return collect();
        }

        return $query->get();
    }

    public function feeInvoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeManagement::class, 'admission_id');
    }
}
