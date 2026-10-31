<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_invite_admin_into_new_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post('/invitations', [
            'company_name' => 'Stark Industries',
            'email' => 'tony@stark.com',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('companies', ['name' => 'Stark Industries']);

        $company = Company::where('name', 'Stark Industries')->first();
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'tony@stark.com',
            'role' => UserRole::Admin->value,
            'inviter_id' => $superAdmin->id,
        ]);
    }

    public function test_admin_can_invite_admin_into_their_own_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin($company)->create();

        $response = $this->actingAs($admin)->post('/invitations', [
            'email' => 'newadmin@myco.com',
            'role' => UserRole::Admin->value,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'newadmin@myco.com',
            'role' => UserRole::Admin->value,
            'inviter_id' => $admin->id,
        ]);
    }

    public function test_admin_can_invite_member_into_their_own_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin($company)->create();

        $response = $this->actingAs($admin)->post('/invitations', [
            'email' => 'member1@myco.com',
            'role' => UserRole::Member->value,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'member1@myco.com',
            'role' => UserRole::Member->value,
            'inviter_id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_invite_user_into_another_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $adminA = User::factory()->admin($companyA)->create();

        // Attempt to pass company_id of Company B
        $response = $this->actingAs($adminA)->post('/invitations', [
            'company_id' => $companyB->id,
            'email' => 'hacked@myco.com',
            'role' => UserRole::Member->value,
        ]);

        $response->assertSessionHas('success');

        // Invitation must still belong strictly to Company A, ignoring user-supplied company_id
        $this->assertDatabaseHas('invitations', [
            'company_id' => $companyA->id,
            'email' => 'hacked@myco.com',
        ]);
        $this->assertDatabaseMissing('invitations', [
            'company_id' => $companyB->id,
            'email' => 'hacked@myco.com',
        ]);
    }

    public function test_members_cannot_invite_users(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member($company)->create();

        $response = $this->actingAs($member)->post('/invitations', [
            'email' => 'friend@myco.com',
            'role' => UserRole::Member->value,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_invitations_cannot_grant_superadmin_privileges(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin($company)->create();

        $response = $this->actingAs($admin)->post('/invitations', [
            'email' => 'trying_superadmin@test.com',
            'role' => UserRole::SuperAdmin->value,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_invitation_acceptance_creates_user_with_correct_company_and_role(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin($company)->create();

        // Admin invites a new Member
        $this->actingAs($admin)->post('/invitations', [
            'email' => 'accepted_user@company.com',
            'role' => UserRole::Member->value,
        ]);

        $invitation = Invitation::where('email', 'accepted_user@company.com')->first();
        $this->assertNotNull($invitation);

        // Get invitation URL token from session
        $invitationUrl = session('invitation_url');
        $token = basename(parse_url($invitationUrl, PHP_URL_PATH));

        // Accept invitation
        $response = $this->post("/invitations/accept/{$token}", [
            'name' => 'Accepted User',
            'password' => 'StrongP@ss123',
            'password_confirmation' => 'StrongP@ss123',
        ]);

        $response->assertRedirect('/dashboard');

        // Check user creation
        $this->assertDatabaseHas('users', [
            'email' => 'accepted_user@company.com',
            'company_id' => $company->id,
            'role' => UserRole::Member->value,
            'name' => 'Accepted User',
        ]);

        // Check invitation accepted_at timestamp is populated
        $invitation->refresh();
        $this->assertNotNull($invitation->accepted_at);

        // Verify newly registered user is authenticated
        $this->assertAuthenticated();
    }

    public function test_expired_reused_or_revoked_invitations_are_rejected(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin($company)->create();

        // 1. Expired invitation
        $expiredToken = 'token_expired_123';
        Invitation::factory()->expired()->create([
            'company_id' => $company->id,
            'inviter_id' => $admin->id,
            'email' => 'expired@example.com',
            'token_hash' => hash('sha256', $expiredToken),
        ]);

        $expiredResponse = $this->post("/invitations/accept/{$expiredToken}", [
            'name' => 'Expired User',
            'password' => 'Pass123456!',
            'password_confirmation' => 'Pass123456!',
        ]);
        $expiredResponse->assertRedirect('/login');
        $this->assertDatabaseMissing('users', ['email' => 'expired@example.com']);

        // 2. Already Accepted (reused) invitation
        $reusedToken = 'token_reused_123';
        Invitation::factory()->accepted()->create([
            'company_id' => $company->id,
            'inviter_id' => $admin->id,
            'email' => 'reused@example.com',
            'token_hash' => hash('sha256', $reusedToken),
        ]);

        $reusedResponse = $this->post("/invitations/accept/{$reusedToken}", [
            'name' => 'Reused User',
            'password' => 'Pass123456!',
            'password_confirmation' => 'Pass123456!',
        ]);
        $reusedResponse->assertRedirect('/login');
        $this->assertDatabaseMissing('users', ['email' => 'reused@example.com']);
    }
}

