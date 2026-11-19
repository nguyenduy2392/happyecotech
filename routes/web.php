<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TeamController;
use App\Http\Middleware\NormalizePublicUrl;
use App\Models\Page;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/gioi-thieu', [HomeController::class, 'about'])->name('about');

Route::get('/dich-vu', [ServiceController::class, 'index'])->name('services.index');
Route::get('/dich-vu/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/doi-ngu', [TeamController::class, 'index'])->name('team');

Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/danh-muc/{category:slug}', [PostController::class, 'category'])->name('posts.category');
Route::get('/tin-tuc/{post:slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/lien-he', [ContactController::class, 'show'])->name('contact');
Route::post('/lien-he', [ContactController::class, 'submit'])
    ->middleware('throttle:contact')
    ->name('contact.submit');

Route::middleware([])->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
    PreventRequestForgery::class,
])->group(function () {
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
    Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
});
Route::get('/csrf-token', fn () => response()->json(
    ['token' => csrf_token()], 200, ['Cache-Control' => 'private, no-store'],
))->middleware('throttle:30,1')->name('csrf.token');

// Trang tĩnh động (chính sách, điều khoản...). PHẢI đặt cuối và loại trừ các prefix hệ thống
// để không bao giờ che route /admin, /livewire, /storage...
Route::get('/{page:slug}', [HomeController::class, 'page'])
    ->where('page', Page::PUBLIC_SLUG_PATTERN)
    ->missing(function (Request $request) {
        $path = Page::redirectPath($request->getPathInfo());

        return $path ? NormalizePublicUrl::redirect($request, $path) : abort(404);
    })
    ->name('page.show');
