<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->published()->ordered()->get();

        return view('public.services.index', [
            'services' => $services,
            'seo' => he_seo([
                'title' => 'Dịch vụ',
                'description' => 'Danh sách dịch vụ của Happy Ecotech.',
            ]),
        ]);
    }

    public function show(Service $service)
    {
        abort_unless($service->isPublished(), 404);

        $service->load(['features', 'gallery', 'icon', 'cover']);

        $related = Service::query()->published()
            ->where('id', '!=', $service->id)
            ->ordered()->limit(3)->get();

        return view('public.services.show', [
            'service' => $service,
            'related' => $related,
            'seo' => he_seo([
                'title' => $service->seo_title ?: $service->name,
                'description' => $service->seo_description ?: $service->short_description,
                'canonical' => $service->seo_canonical ?: route('services.show', $service),
                'og_image' => $service->ogImage?->url ?? $service->cover?->url,
            ]),
        ]);
    }
}
