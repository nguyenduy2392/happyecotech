<?php

namespace App\Http\Middleware;

use Closure;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CachePublicHtml
{
    private const VERSION = 'public-html:version';

    public static function invalidate(): void
    {
        // Requests that began before publication can only populate an old cache key.
        Cache::forever(self::VERSION, (string) Str::uuid());
    }

    public static function key(string $uri): string
    {
        return 'public-content:'.hash('sha256', Cache::get(self::VERSION, 'initial').'|'.seo_url($uri));
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('home', 'about', 'page.show', 'services.*', 'posts.*', 'team', 'contact', 'contact.submit')) {
            return $next($request);
        }

        if (! $this->cacheableRequest($request)) {
            $response = $next($request);
            $response->headers->set('Cache-Control', 'private, no-store');

            return $response;
        }

        $key = self::key($request->getPathInfo().($request->query() === [] ? '' : '?page='.$request->query('page')));
        $cached = Cache::get($key);
        if (is_string($cached)) {
            return response($cached, 200, ['Cache-Control' => 'private, no-cache']);
        }

        $response = $next($request);
        $html = $response->getContent();
        $token = $request->session()->token();
        if ($response->getStatusCode() !== 200 || ! is_string($html)
            || ! str_starts_with((string) $response->headers->get('Content-Type'), 'text/html')
            || $response->headers->getCookies() !== []
            || preg_match('/\bwire:id\s*=/i', $html)
            || ($token && str_contains($html, $token))
            || ! $this->hasOnlyDeferredLeadForms($html)) {
            $response->headers->set('Cache-Control', 'private, no-store');

            return $response;
        }

        Cache::put($key, $html, now()->addMinutes(5));
        $response->headers->set('Cache-Control', 'private, no-cache');

        return $response;
    }

    private function hasOnlyDeferredLeadForms(string $html): bool
    {
        if (! preg_match('/<form\b/i', $html)) {
            return true;
        }

        $document = new DOMDocument;
        if (! $document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return false;
        }
        $forms = $document->getElementsByTagName('form');
        if ($forms->length === 0) {
            return false;
        }
        foreach ($forms as $form) {
            if (! $form->hasAttribute('data-lead-form')) {
                return false;
            }
            $tokens = 0;
            foreach ($form->getElementsByTagName('input') as $input) {
                if ($input->getAttribute('name') !== '_token') {
                    continue;
                }
                if ($input->getAttribute('type') !== 'hidden' || $input->getAttribute('value') !== ''
                    || $input->getAttribute('data-csrf-from') !== route('csrf.token', [], false)) {
                    return false;
                }
                $tokens++;
            }
            if ($tokens !== 1) {
                return false;
            }
        }

        return true;
    }

    private function cacheableRequest(Request $request): bool
    {
        $query = $request->query();
        $validQuery = $query === [] || (
            $request->routeIs('posts.index', 'posts.category')
            && count($query) === 1 && is_scalar($query['page'] ?? null)
            && preg_match('/^[1-9][0-9]{0,2}$/D', (string) $query['page'])
        );

        return $request->isMethod('GET') && $this->isCanonicalHost($request)
            && ! $request->routeIs('contact') && ! $request->user()
            && ! $request->expectsJson() && $validQuery
            && ! $request->session()->hasAny(['errors', '_old_input'])
            && $request->session()->get('_flash.old', []) === []
            && $request->session()->get('_flash.new', []) === [];
    }

    private function isCanonicalHost(Request $request): bool
    {
        $url = parse_url(config('app.url')) ?: [];
        $host = strtolower($url['host'] ?? '');
        $authority = $host.(isset($url['port']) ? ':'.$url['port'] : '');
        foreach ([$request->headers->get('host', ''), ...$request->headers->all('x-forwarded-host')] as $header) {
            if (strtolower($header) !== $authority) {
                return false;
            }
        }

        return $host !== '' && $request->getHttpHost() === $authority && $request->getScheme() === ($url['scheme'] ?? null);
    }
}
