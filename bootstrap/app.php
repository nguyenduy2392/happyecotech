<?php

use App\Http\Middleware\CachePublicHtml;
use App\Http\Middleware\NormalizePublicUrl;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(NormalizePublicUrl::class);
        $middleware->web(append: [CachePublicHtml::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->is('admin', 'admin/*', 'api/*') && ! $request->expectsJson()) {
                return response()->view('errors.404', ['seo' => he_seo([
                    'title' => 'Không tìm thấy trang',
                    'description' => 'Trang bạn tìm không tồn tại hoặc đã được di chuyển.',
                    'robots' => 'noindex,follow',
                ])], 404)->header('Cache-Control', 'private, no-store');
            }
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
