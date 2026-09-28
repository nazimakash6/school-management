<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'role_id', 'student_id', 'staff_id', 'father_cnic', 'status', 'last_login_at', 'last_login_ip', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'Admin';
    public const ROLE_OPERATOR = 'Operator';
    public const ROLE_DIRECTOR = 'Director / Owner';
    public const ROLE_PRINCIPAL = 'Principal';
    public const ROLE_ACCOUNTANT = 'Accountant';
    public const ROLE_TEACHER = 'Teacher';
    public const ROLE_RECEPTIONIST = 'Receptionist';
    public const ROLE_STAFF = 'Staff';
    public const ROLE_STUDENT = 'Student';
    public const ROLE_PARENT = 'Parent';

    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_OPERATOR,
            self::ROLE_DIRECTOR,
            self::ROLE_PRINCIPAL,
            self::ROLE_ACCOUNTANT,
            self::ROLE_TEACHER,
            self::ROLE_RECEPTIONIST,
            self::ROLE_STAFF,
            self::ROLE_STUDENT,
            self::ROLE_PARENT,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function isSuperAdmin(): bool
    {
        return $this->id === 1 || strtolower(trim($this->email ?? '')) === 'admin@school.com';
    }

    public function roleRelation(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('is_granted')
            ->withTimestamps();
    }

    public function hasRole(string $role): bool
    {
        $userRole = strtolower(trim($this->role ?? ''));
        $targetRole = strtolower(trim($role));

        if ($userRole === $targetRole) {
            return true;
        }

        if ($this->roleRelation && strtolower(trim($this->roleRelation->name)) === $targetRole) {
            return true;
        }

        $aliases = [
            'admin'     => ['admin', 'director', 'director / owner', 'super admin', 'owner'],
            'operator'  => ['operator', 'receptionist'],
            'principal' => ['principal', 'principle'],
            'teacher'   => ['teacher', 'teachers'],
            'staff'     => ['staff', 'staff users', 'accountant', 'receptionist', 'operator'],
            'student'   => ['student', 'students'],
            'parent'    => ['parent', 'parents', 'guardian'],
        ];

        foreach ($aliases as $key => $variants) {
            $normalizedVariants = array_map('strtolower', $variants);
            if (in_array($targetRole, $normalizedVariants, true) && in_array($userRole, $normalizedVariants, true)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $r) {
            if ($this->hasRole($r)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin or admin role gets full access
        if ($this->hasRole('admin') || $this->hasRole('director / owner') || $this->isSuperAdmin()) {
            return true;
        }

        // 1. Direct User Permission check (Allow vs Deny)
        $directPermission = $this->directPermissions()
            ->where('name', $permission)
            ->first();

        if ($directPermission) {
            return (bool) $directPermission->pivot->is_granted;
        }

        // 2. Role Permissions check
        $roleObj = $this->roleRelation;
        if (!$roleObj && !empty($this->role)) {
            $roleObj = Role::where('name', $this->role)
                ->orWhere('slug', Str::slug($this->role))
                ->first();
        }

        if (!$roleObj) {
            return false;
        }

        // Check relational DB permissions if loaded or present
        $dbPermissionNames = $roleObj->permissionsRelation()->pluck('name')->toArray();
        if (in_array($permission, $dbPermissionNames, true)) {
            return true;
        }

        // Check JSON permissions array on Role model
        $permissions = $roleObj->permissions ?? [];
        if (in_array('*', $permissions, true) || in_array($permission, $permissions, true)) {
            return true;
        }

        // Module-wide wildcard match (e.g. 'students.manage' or 'students.*')
        $parts = explode('.', $permission);
        $module = $parts[0] ?? $permission;

        if (in_array("{$module}.*", $permissions, true) || in_array("{$module}.manage", $permissions, true)) {
            return true;
        }

        return false;
    }

    public function getDirectPermissionState(string $permissionName): ?bool
    {
        $dp = $this->directPermissions()->where('name', $permissionName)->first();
        return $dp ? (bool) $dp->pivot->is_granted : null;
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(\App\Models\LoginHistory::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(\App\Models\AuditLog::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(\App\Models\ActivityLog::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
