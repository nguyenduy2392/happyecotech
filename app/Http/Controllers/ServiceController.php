<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->published()->with(['icon', 'cover'])->ordered()->get();

        return view('public.services.index', [
            'services' => $services,
            'seo' => he_seo([
                'title' => 'Dịch vụ',
                'description' => 'Danh sách dịch vụ của '.setting('site_name', config('app.name')).'.',
            ]),
        ]);
    }

    public function show(Service $service)
    {
        abort_unless($service->isPublished(), 404);

        $service->load(['features', 'gallery', 'icon', 'cover', 'ogImage']);

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
                'og_image' => ($image = $service->ogImage ?? $service->cover)?->url,
                'image_alt' => $image?->alt ?: $service->name,
                'breadcrumbs' => [['Dịch vụ', seo_url('/dich-vu')]],
                'entity' => [
                    '@type' => 'Service',
                    'name' => $service->name,
                    'description' => $service->short_description,
                    'provider' => ['@id' => seo_url('/').'#organization'],
                ],
            ]),
        ]);
    }
}
