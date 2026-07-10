<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page
{
    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Giao diện & Menu';

    protected static ?string $navigationLabel = 'Cài đặt website';

    protected static ?string $title = 'Cài đặt website';

    protected static ?int $navigationSort = 20;

    /** @var array<string,mixed> */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage_settings') ?? false;
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
        $this->form->fill($this->currentValues());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('settings')->columnSpanFull()->tabs([
                    Tab::make('Chung')->schema([
                        TextInput::make('site_name')->label('Tên website')->required(),
                        TextInput::make('tagline')->label('Tagline'),
                        $this->mediaSelect('logo_media_id', 'Logo (header)'),
                        $this->mediaSelect('footer_logo_media_id', 'Logo footer'),
                        $this->mediaSelect('favicon_media_id', 'Favicon'),
                    ])->columns(2),

                    Tab::make('Liên hệ')->schema([
                        TextInput::make('company_name')->label('Tên công ty'),
                        TextInput::make('contact_phone')->label('Điện thoại'),
                        TextInput::make('contact_email')->label('Email')->email(),
                        TextInput::make('contact_address')->label('Địa chỉ'),
                        TextInput::make('working_hours')->label('Giờ làm việc'),
                        Textarea::make('map_embed_url')->label('URL nhúng Google Maps')->rows(2)->columnSpanFull(),
                    ])->columns(2),

                    Tab::make('Mạng xã hội')->schema([
                        Repeater::make('social_links')
                            ->label('Liên kết mạng xã hội')
                            ->schema([
                                Select::make('platform')->label('Nền tảng')
                                    ->options(collect(SocialLink::PLATFORMS)->keys()->mapWithKeys(fn ($k) => [$k => ucfirst($k)])->all())
                                    ->required(),
                                TextInput::make('label')->label('Nhãn'),
                                TextInput::make('url')->label('URL')->url(),
                                Toggle::make('is_enabled')->label('Hiển thị')->default(true),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                    Tab::make('SEO')->schema([
                        TextInput::make('seo_title_template')->label('Mẫu tiêu đề')->helperText('Dùng %s cho tên trang'),
                        TextInput::make('seo_robots')->label('Robots'),
                        Textarea::make('seo_default_description')->label('Mô tả mặc định')->rows(2)->columnSpanFull(),
                        $this->mediaSelect('seo_default_og_image_media_id', 'Ảnh OG mặc định'),
                        TextInput::make('zalo_site_verification')->label('Zalo verification'),
                    ])->columns(2),

                    Tab::make('Footer')->schema([
                        Textarea::make('footer_description')->label('Mô tả footer')->rows(2)->columnSpanFull(),
                        TextInput::make('footer_copyright')->label('Copyright'),
                        TextInput::make('footer_services_count')->label('Số dịch vụ hiển thị')->numeric()->default(4),
                        Toggle::make('footer_show_services')->label('Hiển thị nhóm dịch vụ'),
                        Toggle::make('footer_show_social')->label('Hiển thị mạng xã hội'),
                    ])->columns(2),

                    Tab::make('Tải ứng dụng')->schema([
                        TextInput::make('app_store_url')->label('App Store URL')->url(),
                        TextInput::make('google_play_url')->label('Google Play URL')->url(),
                        $this->mediaSelect('app_qr_ios_media_id', 'QR iOS'),
                        $this->mediaSelect('app_qr_android_media_id', 'QR Android'),
                    ])->columns(2),

                    Tab::make('Email / SMTP')->schema([
                        TextInput::make('_smtp_mailer')->label('Mailer hiện tại')
                            ->default(config('mail.default'))->disabled()->dehydrated(false),
                        TextInput::make('_smtp_notify_to')->label('Nhận thông báo lead (CONTACT_NOTIFY_TO)')
                            ->default(config('happyecotech.contact_notify_to') ?: '(chưa cấu hình)')->disabled()->dehydrated(false),
                        Toggle::make('smtp_notify_enabled')->label('Bật gửi email thông báo lead'),
                        Textarea::make('_smtp_note')->label('Ghi chú')
                            ->default('Thông tin đăng nhập SMTP đọc từ biến môi trường (.env), không lưu trong CSDL và không hiển thị mật khẩu.')
                            ->disabled()->dehydrated(false)->rows(2)->columnSpanFull(),
                    ])->columns(2),
                ]),
            ]);
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);

        $data = $this->form->getState();
        $socialLinks = $data['social_links'] ?? [];
        unset($data['social_links']);

        $existing = SiteSetting::whereIn('key', array_keys($data))->get()->keyBy('key');

        foreach ($data as $key => $value) {
            $row = $existing->get($key);
            SiteSetting::put($key, $value, $row?->group ?? 'general', $row?->type ?? 'string');
        }

        $this->syncSocialLinks($socialLinks);

        AuditLog::record('setting_changed', null, 'Cập nhật cài đặt website');

        Notification::make()->success()->title('Đã lưu cài đặt')->send();
    }

    /**
     * @param  array<int,array<string,mixed>>  $links
     */
    private function syncSocialLinks(array $links): void
    {
        SocialLink::query()->delete();

        foreach (array_values($links) as $i => $row) {
            if (blank($row['platform'] ?? null)) {
                continue;
            }
            SocialLink::create([
                'platform' => $row['platform'],
                'label' => $row['label'] ?? null,
                'url' => $row['url'] ?? '',
                'sort_order' => $i,
                'is_enabled' => (bool) ($row['is_enabled'] ?? true),
            ]);
        }
    }

    /**
     * @return array<string,mixed>
     */
    private function currentValues(): array
    {
        $values = [];
        foreach (SiteSetting::all() as $setting) {
            $values[$setting->key] = match ($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                default => $setting->value,
            };
        }

        $values['social_links'] = SocialLink::query()->ordered()->get()
            ->map(fn (SocialLink $s) => [
                'platform' => $s->platform,
                'label' => $s->label,
                'url' => $s->url,
                'is_enabled' => $s->is_enabled,
            ])->all();

        return $values;
    }

    private function mediaSelect(string $name, string $label): Select
    {
        return Select::make($name)
            ->label($label)
            ->options(fn () => MediaAsset::query()->orderBy('folder')->orderBy('file_name')->get()
                ->mapWithKeys(fn (MediaAsset $m) => [$m->id => trim(($m->alt ? $m->alt.' — ' : '').$m->file_name)])
                ->all())
            ->searchable()
            ->nullable();
    }
}
