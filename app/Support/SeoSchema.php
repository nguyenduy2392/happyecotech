<?php

namespace App\Support;

class SeoSchema
{
    public static function graph(array $data, string $url, string $siteName, string $image): array
    {
        $home = seo_url('/');
        $organization = [
            '@type' => 'Organization',
            '@id' => $home.'#organization',
            'name' => setting('company_name') ?: $siteName,
            'description' => ProductPositioning::ORGANIZATION_DESCRIPTION,
            'url' => $home,
        ];
        $sameAs = array_values(array_filter([
            CompanyProfile::url('social_facebook'),
            CompanyProfile::url('contact_zalo'),
        ]));
        if ($sameAs !== []) {
            $organization['sameAs'] = $sameAs;
        }
        $graph = [];
        if ($url === $home) {
            $parts = array_map('trim', explode(',', (string) setting('contact_address'), 2));
            if (preg_match('/\d/', $parts[0]) && filled($parts[1] ?? null)) {
                $organization['@type'] = ['Organization', 'LocalBusiness'];
                $organization['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $parts[0], 'addressLocality' => $parts[1]];
            }
            $graph[] = $organization + array_filter([
                'telephone' => setting('contact_phone'),
                'email' => setting('contact_email'),
                'logo' => self::absoluteUrl(setting('logo_media_id')?->url),
            ]);
            $graph[] = ['@type' => 'WebSite', '@id' => $home.'#website', 'url' => $home,
                'name' => $siteName, 'publisher' => ['@id' => $organization['@id']]];
        } else {
            $crumbs = [[$siteName, $home], ...($data['breadcrumbs'] ?? []), [$data['title'] ?? $siteName, $url]];
            $items = [];
            foreach ($crumbs as [$name, $item]) {
                $items[] = ['@type' => 'ListItem', 'position' => count($items) + 1, 'name' => $name, 'item' => $item];
            }
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
        }

        if (isset($data['entity'])) {
            $entity = $data['entity'] + ['url' => $url, 'image' => $image];
            $entity['image'] = self::absoluteUrl($entity['image']);
            if ($url !== $home && in_array($organization['@id'], [$entity['provider']['@id'] ?? null, $entity['publisher']['@id'] ?? null], true)) {
                $graph[] = $organization;
            }
            $graph[] = array_filter($entity);
        }

        $questions = [];
        foreach ($data['faq'] ?? [] as $item) {
            if (filled($item['question'] ?? null) && filled($item['answer'] ?? null)) {
                $questions[] = ['@type' => 'Question', 'name' => $item['question'], 'acceptedAnswer' => [
                    '@type' => 'Answer', 'text' => $item['answer'],
                ]];
            }
        }
        if ($questions !== []) {
            $graph[] = ['@type' => 'FAQPage', 'mainEntity' => $questions];
        }

        foreach ($data['people'] ?? [] as $person) {
            $person['image'] = self::absoluteUrl($person['image'] ?? null);
            $graph[] = array_filter(['@type' => 'Person'] + $person);
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    private static function absoluteUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }
        if (preg_match('~^https?://~i', $url)) {
            return $url;
        }

        return str_starts_with($url, '//') ? parse_url(config('app.url'), PHP_URL_SCHEME).':'.$url : seo_url($url);
    }
}
