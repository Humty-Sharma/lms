<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_short_url(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin($company)->create();

        $response = $this->actingAs($admin)->post('/short-urls', [
            'original_url' => 'https://laravel.com/docs/11.x',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('short_urls', [
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'original_url' => 'https://laravel.com/docs/11.x',
        ]);
    }

    public function test_member_can_create_short_url(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member($company)->create();

        $response = $this->actingAs($member)->post('/short-urls', [
            'original_url' => 'https://php.net/manual',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('short_urls', [
            'company_id' => $company->id,
            'user_id' => $member->id,
            'original_url' => 'https://php.net/manual',
        ]);
    }

    public function test_superadmin_cannot_create_short_url(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_invalid_destination_urls_are_rejected(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member($company)->create();

        $invalidUrls = [
            'javascript:alert(1)',
            'ftp://files.example.com',
            'not-a-valid-url',
            '',
        ];

        foreach ($invalidUrls as $badUrl) {
            $response = $this->actingAs($member)->post('/short-urls', [
                'original_url' => $badUrl,
            ]);

            $response->assertSessionHasErrors('original_url');
        }

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_company_and_creator_ids_cannot_be_spoofed(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $userA = User::factory()->member($companyA)->create();
        $userB = User::factory()->member($companyB)->create();

        // Attempting to inject company_id and user_id of company B
        $response = $this->actingAs($userA)->post('/short-urls', [
            'original_url' => 'https://security.example.org',
            'company_id' => $companyB->id,
            'user_id' => $userB->id,
        ]);

        $response->assertSessionHas('success');

        // Verify the created URL belongs strictly to userA and companyA
        $created = ShortUrl::where('original_url', 'https://security.example.org')->first();
        $this->assertNotNull($created);
        $this->assertEquals($companyA->id, $created->company_id);
        $this->assertEquals($userA->id, $created->user_id);
    }

    public function test_generated_short_codes_are_unique(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin($company)->create();

        $this->actingAs($user)->post('/short-urls', ['original_url' => 'https://example1.com']);
        $this->actingAs($user)->post('/short-urls', ['original_url' => 'https://example2.com']);

        $urls = ShortUrl::all();
        $this->assertCount(2, $urls);
        $this->assertNotEquals($urls[0]->code, $urls[1]->code);
    }
}

