<?php

namespace App\Services;

use App\Mail\CredentialsMail;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Central place for auto-generating account credentials.
 *
 * Used by: Super Admin creating a company, Super Admin resetting a company
 * password, and a Company Admin creating a user. Every account it touches is
 * flagged `must_change_password` so the recipient sets their own password on
 * first login, and the generated password is emailed (never stored in clear).
 */
class CredentialProvisioner
{
    /** Generate a readable temporary password (letters + digits, no symbols). */
    public function generatePassword(int $length = 12): string
    {
        return Str::password($length, letters: true, numbers: true, symbols: false);
    }

    /**
     * Create a user, attach them to a tenant with a role, and email the
     * credentials. If $password is null one is generated. $forceReset flags the
     * account for a first-login password change. Returns [user, password].
     *
     * @return array{user: User, password: string}
     */
    public function provisionMember(
        Tenant $tenant,
        Role $role,
        string $name,
        string $email,
        ?string $password = null,
        bool $forceReset = true,
        ?array $jobCategoryIds = null,
    ): array {
        $password ??= $this->generatePassword();

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // hashed by the model cast
            'status' => 'active',
            'is_admin' => false,
            'must_change_password' => $forceReset,
        ]);

        Membership::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'job_category_ids' => $jobCategoryIds,
        ]);

        $this->email($user, $password, $tenant);

        return ['user' => $user, 'password' => $password];
    }

    /**
     * Regenerate a user's password, re-flag for first-login change, and email it.
     * Returns the new plain password (for the show-once confirmation, if used).
     */
    public function resetPassword(User $user, ?Tenant $tenant = null): string
    {
        $password = $this->generatePassword();

        $user->update([
            'password' => $password,
            'must_change_password' => true,
        ]);

        $this->email($user, $password, $tenant);

        return $password;
    }

    private function email(User $user, string $password, ?Tenant $tenant): void
    {
        Mail::to($user->email)->send(new CredentialsMail($user, $password, $tenant));
    }
}
