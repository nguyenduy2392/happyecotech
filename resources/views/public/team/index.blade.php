@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => 'Đội ngũ',
        'subtitle' => 'Những con người tạo nên Happy Ecotech.',
        'breadcrumbs' => [['label' => 'Đội ngũ']],
    ])

    <section class="team-section pt-100 pb-70">
        <div class="container">
            <div class="row g-4">
                @forelse ($members as $member)
                    <div class="col-lg-3 col-md-6">
                        <div class="single-team h-100 text-center">
                            <div class="team-image">
                                <img src="{{ $member->photo?->url ?? asset('assets/img/team/team-1.jpg') }}"
                                    alt="{{ $member->name }}" loading="lazy"
                                    onerror="this.src='{{ asset('assets/img/team/team-1.jpg') }}'">
                            </div>
                            <div class="team-content">
                                <h3>{{ $member->name }}</h3>
                                @if ($member->position)
                                    <span class="team-position">{{ $member->position }}</span>
                                @endif
                                @if ($member->short_bio)
                                    <p>{{ $member->short_bio }}</p>
                                @endif
                                <ul class="team-socials">
                                    @if ($member->email)
                                        <li><a href="mailto:{{ $member->email }}" aria-label="Email"><i class="lni lni-envelope"></i></a></li>
                                    @endif
                                    @if ($member->phone)
                                        <li><a href="tel:{{ preg_replace('/\s+/', '', $member->phone) }}" aria-label="Điện thoại"><i class="lni lni-phone"></i></a></li>
                                    @endif
                                    @foreach (($member->socials ?? []) as $platform => $url)
                                        @continue(blank($url))
                                        <li><a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $platform }}">
                                            <i class="{{ \App\Models\SocialLink::PLATFORMS[$platform] ?? 'lni lni-link' }}"></i></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center"><p>Thông tin đội ngũ đang được cập nhật.</p></div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
