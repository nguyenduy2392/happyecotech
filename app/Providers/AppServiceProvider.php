<?php

namespace App\Providers;

use App\Console\LocalServeCommand;
use App\Http\Middleware\CachePublicHtml;
use App\Http\Requests\ContactSubmissionRequest;
use App\Models\AuditLog;
use App\Models\ContactSubmission;
use App\Models\MediaAsset;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Policies\RolePolicy;
use App\Support\RequestMedia;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(RequestMedia::class);
        $this->app->extend(ServeCommand::class, fn () => $this->app->make(LocalServeCommand::class));
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        foreach ([
            Page::class, PageBlock::class, Post::class, PostCategory::class,
            Service::class, ServiceFeature::class, TeamMember::class, MediaAsset::class,
            SiteSetting::class, Menu::class, MenuItem::class, SocialLink::class, Tag::class,
        ] as $model) {
            $invalidate = static fn (Model $record) => $record->getConnection()->afterCommit(
                fn () => CachePublicHtml::invalidate(),
            );
            $model::saved($invalidate);
            $model::deleted($invalidate);
        }

        // SuperAdmin có toàn quyền — áp dụng cho mọi Gate/policy.
        Gate::before(fn ($user) => $user->hasRole('SuperAdmin') ? true : null);

        // Role của spatie nằm ngoài App\Models nên map policy thủ công.
        Gate::policy(Role::class, RolePolicy::class);

        // Chống spam/brute-force cho form liên hệ.
        RateLimiter::for('contact', function ($request) {
            $key = ContactSubmissionRequest::normalizeIdempotencyKey($request->input('idempotency_key'));
            if ($key !== null && blank($request->input('website'))
                && ContactSubmission::withTrashed()->where('idempotency_key', $key)->exists()) {
                $request->attributes->set('contact.replay', true);

                return Limit::none();
            }

            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perDay(50)->by($request->ip()),
            ];
        });

        // Audit log cho model nội dung (chỉ thao tác của user đã đăng nhập).
        foreach ([Service::class, Post::class, Page::class, TeamMember::class, Menu::class, User::class] as $model) {
            $model::observe(AuditObserver::class);
        }

        // Audit log cho sự kiện xác thực.
        Event::listen(Login::class, fn (Login $e) => AuditLog::record(
            'login', $e->user instanceof Model ? $e->user : null, 'Đăng nhập', [], $e->user?->getAuthIdentifier()
        ));
        Event::listen(Logout::class, fn (Logout $e) => AuditLog::record(
            'logout', null, 'Đăng xuất', [], $e->user?->getAuthIdentifier()
        ));
        Event::listen(Failed::class, fn (Failed $e) => AuditLog::record(
            'login_failed', null, 'Đăng nhập thất bại: '.($e->credentials['email'] ?? '')
        ));

        // Chia sẻ dữ liệu chung (settings, menu, social, footer) cho layout công khai.
        View::composer('layouts.public', function ($view) {
            $view->with('siteSettings', SiteSetting::map());
            $view->with('headerMenu', optional(Menu::where('location', 'header')->first())?->tree() ?? collect());
            $view->with('footerQuickMenu', optional(Menu::where('location', 'footer_quick')->first())?->tree() ?? collect());
            $view->with('socialLinks', SocialLink::query()->enabled()->ordered()->get());
            $view->with('footerServices', setting('footer_show_services', true)
                ? Service::query()->published()->ordered()->limit((int) setting('footer_services_count', 4))->get()
                : collect());
        });
    }
}
