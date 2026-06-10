<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\TenantContext;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'status', 'is_admin', 'is_super', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    protected $keyType = 'string';

    public $incrementing = false;

    protected function attributes(): array
    {
        return [
            'status' => 'active',
        ];
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
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /** Label for the admin area: "Super Admin" for the owner, else the sub-admin's role name. */
    public function adminRoleLabel(): string
    {
        if ($this->is_super) {
            return 'Super Admin';
        }

        $role = $this->memberships()->whereNull('tenant_id')->with('role')->first()?->role;

        return $role?->name ?? 'Sub Admin';
    }

    /** The single company this user belongs to (one company per user). */
    public function company(): ?Tenant
    {
        return $this->memberships()
            ->whereNotNull('tenant_id')
            ->with('tenant')
            ->first()?->tenant;
    }

    /**
     * Does this user hold the given permission within a tenant?
     *
     * Platform admins (is_admin) always pass. Otherwise we look at the user's
     * membership for the tenant (or the platform membership when $tenantId is
     * null) and check that membership's role permissions. $tenantId defaults to
     * the tenant currently resolved on the request.
     */
    public function hasPermission(string $key, ?string $tenantId = null): bool
    {
        // Only the top platform owner bypasses every check. Sub Admins
        // (is_admin but not is_super) are limited by their assigned role.
        if ($this->is_super) {
            return true;
        }

        $tenantId ??= app(TenantContext::class)->tenantId();

        return $this->memberships()
            ->when(
                $tenantId,
                fn ($q) => $q->where('tenant_id', $tenantId),
                fn ($q) => $q->whereNull('tenant_id'),
            )
            ->whereHas('role.permissions', fn ($q) => $q->where('key', $key))
            ->exists();
    }
}
