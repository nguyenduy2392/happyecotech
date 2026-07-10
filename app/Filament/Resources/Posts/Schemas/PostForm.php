<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\Forms;
use App\Models\Post;
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

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Nội dung')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->maxLength(255)
                            ->helperText('Để trống sẽ tự sinh từ tiêu đề.'),
                        Textarea::make('excerpt')
                            ->label('Tóm tắt')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Nội dung')
                            ->columnSpanFull(),
                    ]),

                Section::make('Phân loại')
                    ->columns(2)
                    ->schema([
                        Select::make('author_id')
                            ->label('Tác giả')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('categories')
                            ->label('Danh mục')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('tags')
                            ->label('Thẻ')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Tên thẻ')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Ảnh')
                    ->columns(2)
                    ->schema([
                        Forms::media('featured_media_id', 'featuredMedia', 'Ảnh đại diện'),
                        Forms::media('og_image_media_id', 'ogImage', 'Ảnh OG'),
                    ]),

                Section::make('Xuất bản')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options(Post::STATUSES)
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Ngày đăng'),
                        Toggle::make('is_featured')
                            ->label('Nổi bật'),
                    ]),

                Section::make('SEO')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Tiêu đề SEO')
                            ->maxLength(255),
                        TextInput::make('seo_canonical')
                            ->label('URL Canonical')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label('Mô tả SEO')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
