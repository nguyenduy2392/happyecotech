<?php

namespace App\Support;

class SeoSchema
{
    public static function graph(array $data, string $url, string $siteName, string $image): array
    {
        $home = seo_url('/');
        $organization = $home.'#organization';
        $graph = [];
        if ($url === $home) {
            $address = setting('contact_address');
            $graph[] = array_filter([
                '@type' => ['Organization', 'LocalBusiness'],
                '@id' => $organization,
                'name' => setting('company_name') ?: $siteName,
                'url' => $home,
                'telephone' => setting('contact_phone'),
                'email' => setting('contact_email'),
                'address' => filled($address) ? ['@type' => 'PostalAddress', 'streetAddress' => $address] : null,
                'logo' => setting('logo_media_id')?->url,
            ]);
            $graph[] = ['@type' => 'WebSite', '@id' => $home.'#website', 'url' => $home,
                'name' => $siteName, 'publisher' => ['@id' => $organization]];
        } else {
            $crumbs = [[$siteName, $home], ...($data['breadcrumbs'] ?? []), [$data['title'] ?? $siteName, $url]];
            $items = [];
            foreach ($crumbs as [$name, $item]) {
                $items[] = ['@type' => 'ListItem', 'position' => count($items) + 1, 'name' => $name, 'item' => $item];
            }
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
        }

        if (isset($data['entity'])) {
            $graph[] = array_filter($data['entity'] + ['url' => $url, 'image' => $image]);
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

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
}
