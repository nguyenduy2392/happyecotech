<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\CompanyProfile;
use App\Support\ProductPositioning;
use Database\Seeders\LandingCopy;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApplyPositioning extends Command
{
    protected $signature = 'ecotech:apply-positioning {--dry-run : Show the plan without writing} {--force : Apply the listed changes}';

    protected $description = 'Preview or apply TaskFlow-first positioning to stock CMS fields';

    private const LEGACY_DESCRIPTION = 'Hệ sinh thái công nghệ minh bạch dữ liệu, tem điện tử, SmartLabel, truy xuất nguồn gốc, TaskFlow, MiniCRM và Farm2Home.';

    private bool $write = false;

    public function handle(): int
    {
        $this->write = $this->option('force') && ! $this->option('dry-run');
        $this->info($this->write ? 'Áp dụng định vị sản phẩm.' : 'Dry-run: chỉ xem trước, dùng --force để áp dụng.');

        foreach (['img/v2/hero-taskflow.webp', CompanyProfile::CEO_PHOTO] as $asset) {
            if (! is_file(public_path('assets/'.$asset))) {
                $this->error("Thiếu public/assets/{$asset}.");

                return self::FAILURE;
            }
        }

        if ($this->write) {
            DB::transaction(fn () => $this->apply());
        } else {
            $this->apply();
        }

        return self::SUCCESS;
    }

    private function apply(): void
    {
        $home = Page::whereSlug('home')->lock($this->write ?: null)->first();
        if ($home) {
            $this->updateFields($home, 'home', [
                'seo_title' => ProductPositioning::HOME_TITLE,
                'seo_description' => ProductPositioning::HOME_DESCRIPTION,
            ], [
                'seo_title' => 'HappyEcotech | Minh bạch từ gốc – Hạnh phúc từ công nghệ',
                'seo_description' => self::LEGACY_DESCRIPTION,
            ]);
            $this->updateHomeBlocks($home);
        }
        $this->updateServices();

        foreach (MenuItem::where('route_name', 'services.index')->where('link_type', 'route')->lock($this->write ?: null)->get() as $item) {
            $this->updateFields($item, "menu.{$item->id}", ['label' => 'Sản phẩm'], ['label' => 'Dịch vụ']);
        }
        $setting = SiteSetting::where('key', 'seo_default_description')->lock($this->write ?: null)->first();
        if ($setting) {
            $this->updateFields($setting, 'seo_default_description', ['value' => ProductPositioning::HOME_DESCRIPTION], ['value' => self::LEGACY_DESCRIPTION]);
        }
        $this->updateCompanyProfile();
    }

    private function updateCompanyProfile(): void
    {
        foreach ([
            ['social_facebook', 'social', CompanyProfile::FACEBOOK, []],
            ['contact_zalo', 'contact', CompanyProfile::ZALO, ['http://zalo.me/1747723032009789626?src=qr', CompanyProfile::ZALO.'?src=qr']],
        ] as [$key, $group, $url, $legacy]) {
            $setting = SiteSetting::where('key', $key)->lock($this->write ?: null)->first();
            if (! $setting) {
                $this->line("  {$key} → {$url}");
                if ($this->write) {
                    SiteSetting::firstOrCreate(['key' => $key], ['group' => $group, 'type' => 'string', 'value' => $url]);
                }
            } else {
                $before = in_array($setting->value, $legacy, true) ? ['value' => $setting->value] : [];
                $this->updateFields($setting, $key, ['value' => $url], $before);
            }
        }

        $leader = CompanyProfile::chiefExecutive($this->write);
        if ($leader?->photo_media_id === null) {
            $this->line('  team.tong-giam-doc.photo → '.CompanyProfile::CEO_FILE);
            if ($this->write) {
                CompanyProfile::attachPortrait($leader);
            }
        }
    }

    private function updateHomeBlocks(Page $home): void
    {
        foreach ($home->blocks()->whereIn('block_type', ['hero', 'featured_services'])->lock($this->write ?: null)->get() as $block) {
            $data = $block->data ?? [];
            $isHero = $block->block_type === 'hero';
            $next = $isHero ? ProductPositioning::HERO : [
                'heading' => 'Sản phẩm và giải pháp',
                'subheading' => 'Tìm công cụ quản lý công việc, khách hàng hoặc giải pháp cho nhu cầu vận hành của bạn.',
                'groups' => ProductPositioning::GROUPS,
            ];
            $before = $isHero ? [
                'h1' => 'Giải pháp truy xuất nguồn gốc và vận hành số cho doanh nghiệp',
                'heading' => 'Minh bạch từ gốc – Hạnh phúc từ công nghệ',
                'subheading' => self::LEGACY_DESCRIPTION,
                'cta_text' => 'Liên hệ hợp tác',
                'cta_url' => '/lien-he#contact-form',
                'art' => 'hero-smartlabel',
            ] : ['heading' => 'Dịch vụ', 'subheading' => self::LEGACY_DESCRIPTION];
            $label = "home.{$block->block_type}.{$block->id}";
            $changes = $this->changes($data, $next, $before, $label);
            if ($isHero) {
                $currentId = $data['image_media_id'] ?? null;
                $oldMedia = $currentId ? MediaAsset::withTrashed()->find($currentId) : null;
                if ($currentId === null || ($oldMedia?->disk === 'assets' && in_array($oldMedia->path, [
                    'img/v2/hero.webp', 'img/hero/hero-1.png',
                ], true))) {
                    $this->line("  {$label}.image_media_id → hero-taskflow.webp");
                    if ($this->write) {
                        $changes['image_media_id'] = MediaAsset::firstOrCreate(
                            ['disk' => 'assets', 'path' => 'img/v2/hero-taskflow.webp'],
                            ['alt' => ProductPositioning::HERO['image_alt'], 'folder' => 'home'],
                        )->id;
                    }
                }
            }
            if ($this->write && $changes !== []) {
                $block->update(['data' => array_replace($data, $changes)]);
            }
        }
    }

    private function updateServices(): void
    {
        $previousOrder = ['smartlabel' => 1, 'taskflow' => 2, 'minicrm' => 3, 'farm2home' => 4];
        $previousDescription = [
            'taskflow' => 'TaskFlow là giải pháp quản lý dự án và công việc, giúp doanh nghiệp tối ưu hóa quy trình làm việc, giảm thiểu sai sót và nâng cao hiệu suất.',
            'minicrm' => 'MiniCRM là giải pháp quản lý khách hàng và mối quan hệ khách hàng.',
        ];
        foreach (Service::whereIn('slug', array_keys($previousOrder))->lock($this->write ?: null)->get() as $service) {
            $slug = $service->slug;
            $next = ['sort_order' => ProductPositioning::ORDER[$slug]];
            $before = ['sort_order' => $previousOrder[$slug]];
            if (isset(ProductPositioning::PRODUCTS[$slug])) {
                $next += ProductPositioning::PRODUCTS[$slug];
                $next['content'] = '<p>'.e($next['short_description']).'</p>';
                $before += LandingCopy::SERVICES[$slug];
                $before['short_description'] = $previousDescription[$slug];
                $before['content'] = '<p>'.e($previousDescription[$slug]).'</p>';
                $legacyFaq = [[
                    'question' => "Tìm hiểu {$service->name} ở đâu?",
                    'answer' => 'Bạn có thể liên hệ Happy Ecotech để trao đổi nhu cầu và phạm vi triển khai phù hợp.',
                ]];
                if ($service->faq === $legacyFaq) {
                    $before['faq'] = $legacyFaq;
                } elseif ($slug === 'taskflow') {
                    $before['faq'] = [ProductPositioning::PRODUCTS['taskflow']['faq'][0]];
                }
            }
            $this->updateFields($service, "service.{$slug}", $next, $before);
            if (isset(ProductPositioning::FEATURES[$slug]) && ! $service->features()->exists()) {
                $this->line("  service.{$slug}.features");
                if ($this->write) {
                    foreach (ProductPositioning::FEATURES[$slug] as $index => $feature) {
                        $service->features()->create($feature + ['sort_order' => $index + 1]);
                    }
                }
            }
        }
    }

    private function updateFields(Model $record, string $label, array $next, array $before): void
    {
        $changes = $this->changes($record->attributesToArray(), $next, $before, $label);
        if ($this->write && $changes !== []) {
            $record->update($changes);
        }
    }

    private function changes(array $current, array $next, array $before, string $label): array
    {
        $changes = [];
        foreach ($next as $field => $value) {
            $existing = $current[$field] ?? null;
            if ($existing === $value) {
                continue;
            }
            if ((blank($existing) && ! is_array($existing)) || (array_key_exists($field, $before) && $existing === $before[$field])) {
                $changes[$field] = $value;
                $summary = is_array($value) ? count($value).' mục' : (string) $value;
                $this->line("  {$label}.{$field} → {$summary}");
            } else {
                $this->line("  Giữ nội dung CMS: {$label}.{$field}");
            }
        }

        return $changes;
    }
}
