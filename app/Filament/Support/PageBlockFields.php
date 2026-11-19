<?php

namespace App\Filament\Support;

use App\Models\MediaAsset;
use App\Models\Service;
use App\Rules\SafeUrl;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PageBlockFields
{
    public static function for(?string $type): array
    {
        $heading = TextInput::make('heading')->label('Tiêu đề');
        $subheading = Textarea::make('subheading')->label('Tiêu đề phụ');
        $cta = [
            TextInput::make('cta_text')->label('Nhãn nút'),
            TextInput::make('cta_url')->label('Đường dẫn nút')->rules([new SafeUrl]),
        ];
        $limit = TextInput::make('limit')->label('Số mục')->integer()->minValue(1)->maxValue(24);

        return match ($type) {
            'hero' => [
                TextInput::make('h1')->label('H1 trang')->maxLength(255), $heading, $subheading, ...$cta,
                TextInput::make('secondary_cta_text')->label('Nhãn nút phụ'),
                TextInput::make('secondary_cta_url')->label('URL nút phụ')->rules([new SafeUrl]),
                Select::make('art')->label('Minh họa mặc định')->options(['hero-taskflow' => 'TaskFlow', 'hero-smartlabel' => 'SmartLabel']),
                TextInput::make('image_alt')->label('Mô tả ảnh')->maxLength(255),
                self::image('image_media_id', 'Ảnh'),
            ],
            'cta' => [$heading, $subheading, ...$cta, Toggle::make('show_form')->label('Hiện biểu mẫu tư vấn')->default(false)],
            'partners' => [$heading, Repeater::make('logos')->label('Logo')->schema([
                self::image('media_id', 'Logo')->required(),
                TextInput::make('name')->label('Tên đối tác')->maxLength(255),
                TextInput::make('url')->label('URL đối tác')->rules([new SafeUrl(absolute: true)]),
            ])->defaultItems(0)],
            'about', 'why_choose' => [$heading, Textarea::make('description')->label('Mô tả')],
            'vision_mission' => [
                $heading, Textarea::make('vision')->label('Tầm nhìn'), Textarea::make('mission')->label('Sứ mệnh'),
                self::textItems('values', 'Giá trị'),
            ],
            'core_values' => [$heading, self::textItems('items', 'Giá trị')],
            'stats' => [
                $heading, Toggle::make('unverified')->label('Chưa xác minh'),
                Repeater::make('items')->label('Số liệu')->schema([
                    TextInput::make('value')->label('Giá trị')->required(),
                    TextInput::make('label')->label('Nhãn')->required(),
                    TextInput::make('suffix')->label('Hậu tố'),
                    TextInput::make('icon')->label('Lớp biểu tượng'),
                ])->defaultItems(0),
            ],
            'app_download' => [
                TextInput::make('kicker')->label('Nhãn'), $heading, Textarea::make('lead')->label('Mô tả'),
                TextInput::make('ios_url')->label('App Store')->rules([new SafeUrl(absolute: true)]),
                TextInput::make('android_url')->label('Google Play')->rules([new SafeUrl(absolute: true)]),
                self::image('ios_qr_media_id', 'QR iOS'), self::image('android_qr_media_id', 'QR Android'),
            ],
            'featured_services' => [$heading, $subheading, $limit, Repeater::make('groups')->label('Nhóm sản phẩm')->schema([
                TextInput::make('title')->label('Tên nhóm')->required(),
                Textarea::make('description')->label('Mô tả'),
                Select::make('icon')->label('Biểu tượng')->options(['workflow' => 'Công việc', 'layers' => 'Giải pháp', 'qr' => 'Truy xuất', 'building' => 'Hành chính']),
                Select::make('art')->label('Ảnh nhóm')->options(['group-operations' => 'Vận hành', 'group-traceability' => 'Truy xuất', 'group-egov' => 'Hành chính']),
                Select::make('slugs')->label('Sản phẩm')->multiple()->options(fn () => Service::ordered()->pluck('name', 'slug')),
            ])->defaultItems(0)],
            'latest_news' => [$heading, $limit],
            'ecosystem_overview' => [
                $heading, Textarea::make('intro')->label('Giới thiệu'),
                Repeater::make('items')->label('Sản phẩm và giải pháp')->schema([
                    ...self::ecosystemIdentity(),
                    Textarea::make('summary')->label('Tóm tắt')->required(),
                    TextInput::make('anchor_or_url')->label('Liên kết')->rules([new SafeUrl])->required(),
                ])->defaultItems(0),
            ],
            'other_solutions' => [
                $heading,
                Repeater::make('items')->label('Giải pháp')->schema([
                    ...self::ecosystemIdentity(),
                    TextInput::make('title')->label('Tiêu đề lợi ích')->required(),
                    Textarea::make('description')->label('Mô tả')->required(),
                    TagsInput::make('highlights')->label('Điểm nổi bật')->maxItems(3),
                    TextInput::make('image')->label('Tên ảnh trong v2')->regex('/\A[a-z0-9-]+\.webp\z/')->required(),
                    TextInput::make('cta_label')->label('Nhãn nút')->required(),
                    TextInput::make('url')->label('Liên kết')->rules([new SafeUrl]),
                ])->defaultItems(0),
            ],
            'richtext' => [TextInput::make('title')->label('Tiêu đề'), Textarea::make('html')->label('Nội dung HTML')],
            'faq' => [$heading, $subheading, self::faq('items')],
            default => [],
        };
    }

    private static function ecosystemIdentity(): array
    {
        return [
            TextInput::make('key')->label('Slug')->regex('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/')->maxLength(80)->required(),
            TextInput::make('name')->label('Tên sản phẩm')->required(),
            TextInput::make('category')->label('Nhãn giá trị')->required(),
        ];
    }

    public static function faq(string $key): Repeater
    {
        return Repeater::make($key)->label('Câu hỏi thường gặp')->schema([
            TextInput::make('question')->label('Câu hỏi')->required(),
            Textarea::make('answer')->label('Trả lời')->required(),
        ])->defaultItems(0);
    }

    private static function image(string $key, string $label): Select
    {
        return Select::make($key)->label($label)->searchable()
            ->options(fn () => MediaAsset::where('mime_type', 'like', 'image/%')
                ->orderBy('file_name')->pluck('file_name', 'id'));
    }

    private static function textItems(string $key, string $label): Repeater
    {
        return Repeater::make($key)->label($label)->schema([
            TextInput::make('title')->label('Tiêu đề')->required(),
            Textarea::make('description')->label('Mô tả')->required(),
        ])->defaultItems(0);
    }
}
