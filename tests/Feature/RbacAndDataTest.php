<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacAndDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        $user = User::create([
            'name' => $role.' User',
            'email' => strtolower($role).'@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole($role);

        return $user;
    }

    public function test_slugs_are_unique_per_content_type(): void
    {
        $a = Service::create(['name' => 'Alpha', 'status' => 'draft']);
        $b = Service::create(['name' => 'Alpha', 'status' => 'draft']);

        $this->assertSame('alpha', $a->slug);
        $this->assertNotSame($a->slug, $b->slug);
        $this->assertSame('alpha-2', $b->slug);
    }

    public function test_viewer_cannot_open_service_create_page(): void
    {
        $viewer = $this->userWithRole('Viewer');

        $this->actingAs($viewer)->get('/admin/services/create')->assertForbidden();
    }

    public function test_editor_can_open_service_create_page(): void
    {
        $editor = $this->userWithRole('Editor');

        $this->actingAs($editor)->get('/admin/services/create')->assertOk();
    }

    public function test_author_can_only_update_own_posts(): void
    {
        $author = $this->userWithRole('Author');
        $other = $this->userWithRole('Editor');

        $ownPost = Post::create(['title' => 'Của tôi', 'status' => 'draft', 'author_id' => $author->id]);
        $othersPost = Post::create(['title' => 'Của người khác', 'status' => 'draft', 'author_id' => $other->id]);

        $this->assertTrue($author->can('update', $ownPost));
        $this->assertFalse($author->can('update', $othersPost));
    }

    public function test_author_cannot_publish_posts(): void
    {
        $author = $this->userWithRole('Author');
        $this->assertFalse($author->can('publish_post'));
    }

    public function test_public_settings_map_exposes_no_secret_keys(): void
    {
        $this->seed(SiteSettingSeeder::class);

        foreach (array_keys(SiteSetting::map()) as $key) {
            $this->assertStringNotContainsStringIgnoringCase('password', $key);
            $this->assertStringNotContainsStringIgnoringCase('secret', $key);
        }
    }
}
