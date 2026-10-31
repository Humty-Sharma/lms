<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_see_urls_across_multiple_companies(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $companyA = Company::factory()->create(['name' => 'Company Alpha']);
        $companyB = Company::factory()->create(['name' => 'Company Beta']);

        $userA = User::factory()->admin($companyA)->create();
        $userB = User::factory()->admin($companyB)->create();

        $urlA = ShortUrl::factory()->create(['company_id' => $companyA->id, 'user_id' => $userA->id, 'code' => 'alpha1']);
        $urlB = ShortUrl::factory()->create(['company_id' => $companyB->id, 'user_id' => $userB->id, 'code' => 'beta1']);

        $response = $this->actingAs($superAdmin)->get('/short-urls');

        $response->assertStatus(200);
        $response->assertSee('alpha1');
        $response->assertSee('beta1');
        $response->assertSee('Company Alpha');
        $response->assertSee('Company Beta');
    }

    public function test_admin_sees_only_urls_belonging_to_own_company(): void
    {
        $companyA = Company::factory()->create(['name' => 'Company Alpha']);
        $companyB = Company::factory()->create(['name' => 'Company Beta']);

        $adminA = User::factory()->admin($companyA)->create();
        $memberA = User::factory()->member($companyA)->create();
        $adminB = User::factory()->admin($companyB)->create();

        $urlAdminA = ShortUrl::factory()->create(['company_id' => $companyA->id, 'user_id' => $adminA->id, 'code' => 'adminA_code']);
        $urlMemberA = ShortUrl::factory()->create(['company_id' => $companyA->id, 'user_id' => $memberA->id, 'code' => 'memberA_code']);
        $urlB = ShortUrl::factory()->create(['company_id' => $companyB->id, 'user_id' => $adminB->id, 'code' => 'companyB_secret_code']);

        $response = $this->actingAs($adminA)->get('/short-urls');

        $response->assertStatus(200);
        $response->assertSee('adminA_code');
        $response->assertSee('memberA_code');
        $response->assertDontSee('companyB_secret_code');
    }

    public function test_member_sees_only_urls_created_by_themselves(): void
    {
        $company = Company::factory()->create();

        $member1 = User::factory()->member($company)->create();
        $member2 = User::factory()->member($company)->create();
        $admin = User::factory()->admin($company)->create();

        ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $member1->id, 'code' => 'my_private_url']);
        ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $member2->id, 'code' => 'colleague_url']);
        ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $admin->id, 'code' => 'admin_url']);

        $response = $this->actingAs($member1)->get('/short-urls');

        $response->assertStatus(200);
        $response->assertSee('my_private_url');
        $response->assertDontSee('colleague_url');
        $response->assertDontSee('admin_url');
    }

    public function test_pagination_does_not_leak_unauthorized_records(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $adminA = User::factory()->admin($companyA)->create();
        $adminB = User::factory()->admin($companyB)->create();

        // Create 20 records for company B
        ShortUrl::factory()->count(20)->create([
            'company_id' => $companyB->id,
            'user_id' => $adminB->id,
        ]);

        // Create 1 record for company A
        $urlA = ShortUrl::factory()->create([
            'company_id' => $companyA->id,
            'user_id' => $adminA->id,
            'code' => 'sole_alpha_code',
        ]);

        $response = $this->actingAs($adminA)->get('/short-urls');
        $response->assertStatus(200);
        $response->assertSee('sole_alpha_code');

        // Check the paginated dataset on page 1 has exactly 1 item
        $shortUrlsPage = $response->viewData('shortUrls');
        $this->assertEquals(1, $shortUrlsPage->total());
        $this->assertEquals(1, $shortUrlsPage->count());
    }
}

