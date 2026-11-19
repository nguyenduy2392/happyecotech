@php
    // Ban lãnh đạo: ưu tiên thành viên đội ngũ (group leadership / chức danh Tổng giám đốc) trong CMS; thiếu thì dùng ảnh trong img/v2/team/.
    // Họ tên và giới thiệu CHỈ hiển thị khi CMS có; thiếu thì chỉ ảnh + chức danh.
    $ceo = \App\Models\TeamMember::query()->published()->with('photo')
        ->where(fn ($q) => $q->where('group', 'leadership')->orWhere('position', 'like', '%Tổng giám đốc%'))
        ->ordered()->first();
    $position = $ceo?->position ?: 'Tổng giám đốc';
    $name = $ceo && filled($ceo->name) && mb_strtolower(trim($ceo->name)) !== mb_strtolower($position) ? $ceo->name : null;
    $bio = $ceo?->short_bio;
    $file = null;
    if (! $ceo?->photo) {
        foreach (glob(public_path('assets/img/v2/team/*.{webp,jpg,jpeg,png}'), GLOB_BRACE) ?: [] as $f) {
            if (preg_match('/-(sm|md)\./', $f) || str_contains($f, 'fallback')) { continue; }
            $file = $f;
            break;
        }
    }
    $dim = $file ? (@getimagesize($file) ?: [361, 481]) : null;
@endphp
@if ($ceo?->photo || $file)
    <section class="section {{ $withBg ?? false ? 'section-tint' : '' }}" aria-labelledby="leadership-title">
        <div class="container-page">
            <div class="section-head"><h2 id="leadership-title">Ban lãnh đạo</h2></div>
            <div class="grid max-w-4xl items-center gap-8 rounded-2xl bg-brand-50 p-6 sm:grid-cols-[auto_1fr] md:gap-12 md:p-10">
                <div class="mx-auto w-full max-w-60 sm:mx-0 sm:w-60">
                    @if ($ceo?->photo)
                        @php
                            $photo = $ceo->photo;
                            $isPortrait = str_contains((string) $photo->path, 'ceo-portrait');
                            $pw = $photo->width ?: 360;
                            $ph = $photo->height ?: 450;
                            $altText = $name ? $name.', '.$position : 'Tổng giám đốc Happy Ecotech';
                            // Chân dung nhỏ, không upscale: srcset khai báo đúng 160/240/360w (không dùng 480/960).
                            $portraitSet = $isPortrait
                                ? implode(', ', [
                                    preg_replace('/-(lg|md|sm)(\.\w+)$/', '-sm$2', $photo->url).' 160w',
                                    preg_replace('/-(lg|md|sm)(\.\w+)$/', '-md$2', $photo->url).' 240w',
                                    preg_replace('/-(lg|md|sm)(\.\w+)$/', '-lg$2', $photo->url).' 360w',
                                ])
                                : null;
                        @endphp
                        @if ($portraitSet)
                            <img src="{{ preg_replace('/-(lg|md|sm)(\.\w+)$/', '-lg$2', $photo->url) }}" srcset="{{ $portraitSet }}" sizes="240px"
                                alt="{{ $altText }}" width="{{ $pw }}" height="{{ $ph }}" loading="lazy" decoding="async"
                                class="aspect-[360/450] w-full rounded-2xl object-cover" />
                        @else
                            <x-media-img :media="$photo" :alt="$altText" :width="$pw" :height="$ph"
                                class="aspect-[360/450] w-full rounded-2xl object-cover" sizes="240px" />
                        @endif
                    @else
                        <img src="{{ asset('assets/img/v2/team/'.basename($file)) }}" alt="Tổng giám đốc Happy Ecotech" width="{{ $dim[0] }}" height="{{ $dim[1] }}"
                            loading="lazy" decoding="async" class="aspect-[360/450] w-full rounded-2xl object-cover" />
                    @endif
                </div>
                <div>
                    <h3 class="text-h2">{{ $position }}</h3>
                    @if ($name)<p class="mt-2 text-xl font-semibold text-brand-700">{{ $name }}</p>@endif
                    @if ($bio)<p class="mt-4 max-w-xl text-lead text-ink-700">{{ $bio }}</p>@endif
                    <p class="mt-6 text-ink-700">Happy Ecotech</p>
                </div>
            </div>
        </div>
    </section>
@endif
