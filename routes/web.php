<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

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

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Trang tĩnh động (chính sách, điều khoản...). PHẢI đặt cuối và loại trừ các prefix hệ thống
// để không bao giờ che route /admin, /livewire, /storage...
Route::get('/{page:slug}', [HomeController::class, 'page'])
    ->where('page', '^(?!admin|livewire|storage|assets|api|sitemap).+$')
    ->name('page.show');
