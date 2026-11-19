<?php

namespace App\Filament\Support;

use App\Models\MediaAsset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
            TextInput::make('cta_url')->label('Đường dẫn nút')->regex('~^(?:/(?![/\\\\])|https?://)[^\\s]*$~iD'),
        ];
        $limit = TextInput::make('limit')->label('Số mục')->integer()->minValue(1)->maxValue(24);

        return match ($type) {
            'hero' => [TextInput::make('h1')->label('H1 trang')->maxLength(255), $heading, $subheading, ...$cta, self::image('image_media_id', 'Ảnh')],
            'cta' => [$heading, $subheading, ...$cta],
            'partners' => [$heading, self::image('logos', 'Logo')->multiple()],
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
                TextInput::make('ios_url')->label('App Store')->rules(['url:http,https']),
                TextInput::make('android_url')->label('Google Play')->rules(['url:http,https']),
                self::image('ios_qr_media_id', 'QR iOS'), self::image('android_qr_media_id', 'QR Android'),
            ],
            'featured_services' => [$heading, $subheading, $limit],
            'latest_news' => [$heading, $limit],
            'richtext' => [TextInput::make('title')->label('Tiêu đề'), Textarea::make('html')->label('Nội dung HTML')],
            'faq' => [$heading, $subheading, self::faq('items')],
            default => [],
        };
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
