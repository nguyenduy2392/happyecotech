<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Forms;
use App\Models\Service;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin dịch vụ')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên dịch vụ')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->helperText('Để trống sẽ tự sinh từ tên.'),
                        Textarea::make('short_description')
                            ->label('Mô tả ngắn')
                            ->rows(2)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Nội dung chi tiết')
                            ->columnSpanFull(),
                    ]),

                Section::make('Hình ảnh')
                    ->columns(3)
                    ->schema([
                        Forms::media('icon_media_id', 'icon', 'Icon / Logo'),
                        Forms::media('cover_media_id', 'cover', 'Ảnh cover'),
                        Forms::media('og_image_media_id', 'ogImage', 'Ảnh OG (SEO)'),
                    ]),

                Section::make('Liên kết & CTA')
                    ->columns(2)
                    ->schema([
                        TextInput::make('external_url')->label('Website sản phẩm')->url(),
                        TextInput::make('app_store_url')->label('App Store URL')->url(),
                        TextInput::make('google_play_url')->label('Google Play URL')->url(),
                        TextInput::make('cta_text')->label('Nội dung nút CTA'),
                        TextInput::make('cta_url')->label('URL nút CTA'),
                    ]),

                Section::make('Xuất bản')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options(Service::STATUSES)
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at')->label('Thời gian đăng'),
                        Toggle::make('is_featured')->label('Dịch vụ nổi bật'),
                        TextInput::make('sort_order')->label('Thứ tự hiển thị')->numeric()->default(0),
                    ]),

                Section::make('SEO')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo_title')->label('SEO title')->maxLength(255),
                        TextInput::make('seo_canonical')->label('Canonical URL')->url(),
                        Textarea::make('seo_description')->label('SEO description')->rows(2)->columnSpanFull(),
                    ]),
            ]);
    }
}
