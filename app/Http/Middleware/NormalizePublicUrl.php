<?php

namespace App\Http\Middleware;

use App\Models\Page;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizePublicUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! ($request->isMethod('GET') || $request->isMethod('HEAD')) || $request->expectsJson()) {
            return $next($request);
        }

        $path = $request->getPathInfo();
        $normalized = strtolower(rtrim($path, '/')) ?: '/';
        if (preg_match('~^/(?:admin|api|livewire[^/]*|storage|assets|build|css|js|fonts|up)(?:/|$)~', $normalized)) {
            return $next($request);
        }
        $uriPath = explode('?', $request->getRequestUri(), 2)[0];
        if ($normalized === '/index.php' || ($normalized === '/' && preg_match('~/index\.php/*$~i', $uriPath))) {
            $path = '/index.php';
            $normalized = '/';
        }
        if ($normalized === '/home') {
            $normalized = '/';
        }
        if ($path !== $normalized && preg_match('~^/(?:[a-z0-9-]+(?:/[a-z0-9-]+)*|robots\.txt|sitemap\.xml)?$~D', $normalized)) {
            return self::redirect($request, Page::redirectPath($normalized) ?? $normalized);
        }

        return $next($request);
    }

    public static function redirect(Request $request, string $path): RedirectResponse
    {
        $query = $request->getQueryString();

        return redirect()->away(seo_url($path).($query ? '?'.$query : ''), 301);
    }
}
