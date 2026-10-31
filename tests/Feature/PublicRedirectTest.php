<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_short_code_redirects_to_stored_destination_for_guest(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member($company)->create();

        $shortUrl = ShortUrl::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'original_url' => 'https://laravel.com/docs',
            'code' => 'laradoc',
        ]);

        // Guest accesses short URL
        $response = $this->get('/s/laradoc');

        $response->assertStatus(302);
        $response->assertRedirect('https://laravel.com/docs');
    }

    public function test_unknown_short_code_returns_404(): void
    {
        $response = $this->get('/s/nonexistentcode');

        $response->assertStatus(404);
    }

    public function test_request_cannot_override_stored_destination(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member($company)->create();

        ShortUrl::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'original_url' => 'https://trusted-site.com',
            'code' => 'securecode',
        ]);

        // Attacker attempts to pass query parameters or payload to alter destination
        $response = $this->get('/s/securecode?destination=https://malicious-site.com');

        $response->assertStatus(302);
        $response->assertRedirect('https://trusted-site.com');
    }

    public function test_short_url_resolves_publicly_without_leaking_company_information(): void
    {
        $company = Company::factory()->create(['name' => 'TopSecret Enterprise']);
        $user = User::factory()->member($company)->create();

        ShortUrl::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'original_url' => 'https://public-page.com',
            'code' => 'openlink',
        ]);

        $response = $this->get('/s/openlink');

        $response->assertStatus(302);
        $this->assertStringNotContainsString('TopSecret Enterprise', (string) $response->getContent());
        $this->assertStringNotContainsString((string) $company->id, (string) $response->headers->get('Location'));
    }
}

