<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\Forms;
use App\Models\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Trang')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, ?Page $record) {
                                if ($record?->slug !== 'home') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Đường dẫn (slug)')
                            ->disabled(fn (?Page $record) => $record?->slug === 'home')
                            ->regex('/^'.Page::PUBLIC_SLUG_PATTERN.'$/D')
                            ->rule(fn (?Page $record) => Rule::notIn(array_diff(Page::FIXED_ROUTES, [$record?->slug])))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Trạng thái')
                            ->disabled(fn (?Page $record) => $record?->slug === 'home')
                            ->options(Page::STATUSES)
                            ->default('draft')
                            ->required(),
                        Toggle::make('is_system')
                            ->label('Trang hệ thống')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
                Section::make('SEO')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Tiêu đề SEO')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label('Mô tả SEO')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms::media('og_image_media_id', 'ogImage', 'Ảnh OG'),
                    ]),
            ]);
    }
}
