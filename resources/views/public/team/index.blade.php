@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => 'Đội ngũ Happy Ecotech',
        'lead' => 'Những người xây dựng và vận hành hệ sinh thái giải pháp của chúng tôi.',
        'crumbs' => [['label' => 'Đội ngũ']],
    ])

    @include('partials.leadership', ['withBg' => false])
    @php $members = $members->reject(fn ($m) => $m->photo && ($m->group === 'leadership' || str_contains((string) $m->position, 'Tổng giám đốc')))->values(); @endphp

    <section class="section">
        <div class="container-page">
            @if ($members->isEmpty())
                <p class="text-lead text-ink-700">Thông tin đội ngũ đang được cập nhật.</p>
            @else
                <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($members as $member)
                        <li class="card !p-0 overflow-hidden">
                            <div class="aspect-square bg-ink-100">
                                @if ($member->photo)
                                    <x-media-img :media="$member->photo" :alt="$member->name" width="400" height="400"
                                        class="size-full object-cover" sizes="(min-width: 1024px) 280px, (min-width: 640px) 45vw, 100vw" />
                                @else
                                    <x-art name="avatar-fallback" width="400" height="400" :alt="''" class="size-full object-cover" sizes="280px" />
                                @endif
                            </div>
                            <div class="p-5">
                                <h2 class="text-h3">{{ $member->name }}</h2>
                                @if ($member->position)<p class="mt-1 font-semibold text-brand-700">{{ $member->position }}</p>@endif
                                @if ($member->short_bio)<p class="mt-3 text-ink-700">{{ $member->short_bio }}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection
