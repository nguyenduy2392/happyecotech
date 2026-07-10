<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin menu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên menu')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('location')
                            ->label('Vị trí')
                            ->helperText('header, footer_quick...')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
