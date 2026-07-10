<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tệp')
                    ->schema([
                        FileUpload::make('path')
                            ->label('Tệp tải lên')
                            ->disk('public')
                            ->directory('media')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),
                Section::make('Thông tin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('alt')
                            ->label('Alt text'),
                        TextInput::make('folder')
                            ->label('Thư mục'),
                        Textarea::make('caption')
                            ->label('Chú thích')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
