<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_page(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member($company)->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Profile Information');
        $response->assertSee($user->email);
    }

    public function test_user_can_update_name_and_email(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member($company)->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertSessionHas('status_profile');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_user_cannot_update_email_to_existing_user_email(): void
    {
        $company = Company::factory()->create();
        $userA = User::factory()->member($company)->create(['email' => 'usera@example.com']);
        $userB = User::factory()->member($company)->create(['email' => 'userb@example.com']);

        $response = $this->actingAs($userA)->patch('/profile', [
            'name' => 'User A',
            'email' => 'userb@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_update_password(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member($company)->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHas('status_password');
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member($company)->create([
            'password' => Hash::make('RealPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'WrongPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }
}
