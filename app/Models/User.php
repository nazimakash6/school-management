<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'last_login_at', 'last_login_ip', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'Admin';
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

    public function hasRole(string $role): bool
    {
        $userRole = strtolower(trim($this->role ?? ''));
        $targetRole = strtolower(trim($role));

        if ($userRole === $targetRole) {
            return true;
        }

        $aliases = [
            'admin'     => ['admin', 'director', 'director / owner', 'super admin', 'owner'],
            'principal' => ['principal', 'principle'],
            'teacher'   => ['teacher', 'teachers'],
            'staff'     => ['staff', 'staff users', 'accountant', 'receptionist'],
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
        if ($this->hasRole('admin')) {
            return true;
        }

        $roleObj = \App\Models\Role::where('name', $this->role)
            ->orWhere('slug', \Illuminate\Support\Str::slug($this->role ?? ''))
            ->first();

        if (!$roleObj || empty($roleObj->permissions)) {
            return false;
        }

        if (in_array('*', $roleObj->permissions, true) || in_array($permission, $roleObj->permissions, true)) {
            return true;
        }

        // Fuzzy match base module (e.g. 'students', 'teachers', 'fees', 'attendance')
        $baseModule = explode('.', $permission)[0] ?? $permission;
        foreach ($roleObj->permissions as $p) {
            $pBase = explode('.', $p)[0] ?? $p;
            if ($pBase === $baseModule) {
                return true;
            }
        }

        return false;
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
