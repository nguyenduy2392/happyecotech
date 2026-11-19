<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\ProductPositioning;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->published()->with(['icon', 'cover'])->ordered()->get();

        return view('public.services.index', [
            'services' => $services,
            'seo' => he_seo([
                'title' => 'Sản phẩm và giải pháp',
            ]),
        ]);
    }

    public function show(Service $service)
    {
        abort_unless($service->isPublished(), 404);

        $service->load(['features', 'gallery', 'icon', 'cover', 'ogImage']);
        // Suppress the exact placeholder shipped by the earlier seeder without rewriting database content.
        $faq = array_values(array_filter($service->faq ?? [], fn ($item) => ! (
            preg_match('/\ATìm hiểu .+ ở đâu\?\z/u', (string) ($item['question'] ?? ''))
            && ($item['answer'] ?? null) === 'Bạn có thể liên hệ Happy Ecotech để trao đổi nhu cầu và phạm vi triển khai phù hợp.'
        )));
        $service->faq = $faq;

        $related = Service::query()->published()->with(['icon', 'cover'])
            ->where('id', '!=', $service->id)
            ->ordered()->limit(3)->get();

        return view('public.services.show', [
            'service' => $service,
            'related' => $related,
            'seo' => he_seo([
                'title' => $service->seo_title ?: $service->name,
                'description' => $service->seo_description ?: $service->short_description,
                'canonical' => seo_url(route('services.show', $service, false)),
                'og_image' => $this->socialImage($service),
                'image_alt' => $service->ogImage?->alt ?: $service->name,
                'breadcrumbs' => [['Sản phẩm', seo_url('/dich-vu')]],
                'faq' => $faq,
                'entity' => $this->entity($service),
            ]),
        ]);
    }

    private function entity(Service $service): array
    {
        $entity = [
            '@type' => 'Service',
            'name' => $service->name,
            'description' => $service->short_description,
            'provider' => ['@id' => seo_url('/').'#organization'],
        ];
        if (isset(ProductPositioning::PRODUCTS[$service->slug])) {
            $entity['@type'] = 'SoftwareApplication';
            $entity['applicationCategory'] = 'BusinessApplication';
            if ($service->slug === 'taskflow') {
                $entity['operatingSystem'] = ['iOS', 'Android'];
            }
        }

        return $entity;
    }

    private function socialImage(Service $service): ?string
    {
        $path = '/assets/img/v2/og-service-'.$service->slug.'.webp';

        return $service->ogImage?->url ?? (is_file(public_path($path)) ? seo_url($path) : $service->cover?->url);
    }
}
