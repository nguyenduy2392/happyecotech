<?php

namespace App\Filament\Resources\Services\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $title = 'Tính năng / Lợi ích';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
            TextInput::make('icon')->label('Icon (class LineIcons)')->maxLength(255),
            Textarea::make('description')->label('Mô tả')->rows(2)->columnSpanFull(),
            TextInput::make('sort_order')->label('Thứ tự')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')->label('Tiêu đề'),
                TextColumn::make('description')->label('Mô tả')->limit(60),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
