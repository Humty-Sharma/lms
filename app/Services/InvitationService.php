<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InvitationService
{
    /**
     * Lifetime of an invitation token in days.
     */
    public const EXPIRATION_DAYS = 7;

    /**
     * Create an invitation initiated by a SuperAdmin.
     * Automatically creates the new company and links an Admin invitation.
     *
     * @return array{invitation: Invitation, plainToken: string, company: Company}
     */
    public function createBySuperAdmin(User $inviter, string $companyName, string $email): array
    {
        if (! $inviter->isSuperAdmin()) {
            throw new InvalidArgumentException('Only a SuperAdmin can invite into a new company.');
        }

        return DB::transaction(function () use ($inviter, $companyName, $email) {
            $company = Company::create([
                'name' => $companyName,
            ]);

            $plainToken = Str::random(40);
            $tokenHash = hash('sha256', $plainToken);

            $invitation = Invitation::create([
                'company_id' => $company->id,
                'inviter_id' => $inviter->id,
                'email' => strtolower(trim($email)),
                'role' => UserRole::Admin,
                'token_hash' => $tokenHash,
                'expires_at' => Carbon::now()->addDays(self::EXPIRATION_DAYS),
            ]);

            return [
                'invitation' => $invitation,
                'plainToken' => $plainToken,
                'company' => $company,
            ];
        });
    }

    /**
     * Create an invitation initiated by an Admin.
     * Restricts target company to the authenticated admin's company and role to Admin or Member.
     *
     * @return array{invitation: Invitation, plainToken: string, company: Company}
     */
    public function createByAdmin(User $inviter, string $email, UserRole $role): array
    {
        if (! $inviter->isAdmin()) {
            throw new InvalidArgumentException('Only an Admin can create company invitations.');
        }

        if (! in_array($role, [UserRole::Admin, UserRole::Member], true)) {
            throw new InvalidArgumentException('Admins can only invite Admins or Members.');
        }

        return DB::transaction(function () use ($inviter, $email, $role) {
            $plainToken = Str::random(40);
            $tokenHash = hash('sha256', $plainToken);

            $invitation = Invitation::create([
                'company_id' => $inviter->company_id,
                'inviter_id' => $inviter->id,
                'email' => strtolower(trim($email)),
                'role' => $role,
                'token_hash' => $tokenHash,
                'expires_at' => Carbon::now()->addDays(self::EXPIRATION_DAYS),
            ]);

            return [
                'invitation' => $invitation,
                'plainToken' => $plainToken,
                'company' => $inviter->company,
            ];
        });
    }

    /**
     * Find a valid, unaccepted, unrevoked, unexpired invitation by plain token.
     */
    public function findValidInvitation(string $plainToken): ?Invitation
    {
        $tokenHash = hash('sha256', $plainToken);

        $invitation = Invitation::with(['company', 'inviter'])
            ->where('token_hash', $tokenHash)
            ->first();

        if (! $invitation || ! $invitation->isValid()) {
            return null;
        }

        return $invitation;
    }

    /**
     * Accept invitation and create the user account.
     */
    public function acceptInvitation(Invitation $invitation, string $name, string $password): User
    {
        if (! $invitation->isValid()) {
            throw new InvalidArgumentException('Invitation is expired, revoked, or already accepted.');
        }

        return DB::transaction(function () use ($invitation, $name, $password) {
            $user = User::create([
                'company_id' => $invitation->company_id,
                'name' => $name,
                'email' => $invitation->email,
                'password' => $password, // Auto-hashed via User model cast
                'role' => $invitation->role,
            ]);

            $invitation->update([
                'accepted_at' => Carbon::now(),
            ]);

            return $user;
        });
    }
}

