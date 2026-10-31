<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_protected_dashboards(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $urlsResponse = $this->get('/short-urls');
        $urlsResponse->assertRedirect('/login');

        $invitationsResponse = $this->get('/invitations');
        $invitationsResponse->assertRedirect('/login');
    }

    public function test_user_can_view_login_form(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'admin@test.com',
            'password' => Hash::make('Secret123!'),
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $company = Company::factory()->create();
        User::factory()->create([
            'company_id' => $company->id,
            'email' => 'admin@test.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

