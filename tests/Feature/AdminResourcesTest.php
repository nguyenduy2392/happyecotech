<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::where('email', env('HE_ADMIN_EMAIL', 'admin@happyecotech.com'))->firstOrFail();
    }

    public static function resourceRoutes(): array
    {
        return [
            'services' => ['admin/services'],
            'posts' => ['admin/posts'],
            'post-categories' => ['admin/post-categories'],
            'tags' => ['admin/tags'],
            'pages' => ['admin/pages'],
            'team-members' => ['admin/team-members'],
            'media-assets' => ['admin/media-assets'],
            'menus' => ['admin/menus'],
            'contact-submissions' => ['admin/contact-submissions'],
            'users' => ['admin/users'],
            'roles' => ['admin/roles'],
            'audit-logs' => ['admin/audit-logs'],
        ];
    }

    #[DataProvider('resourceRoutes')]
    public function test_resource_list_renders_for_superadmin(string $path): void
    {
        $this->actingAs($this->superAdmin())
            ->get('/'.$path)
            ->assertOk();
    }
}
