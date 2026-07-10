<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Models\PageBlock;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlocksRelationManager extends RelationManager
{
    protected static string $relationship = 'blocks';

    protected static ?string $title = 'Các block nội dung';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('block_type')
                    ->label('Loại block')
                    ->options(PageBlock::TYPES)
                    ->required(),
                TextInput::make('title')
                    ->label('Tiêu đề')
                    ->maxLength(255),
                Toggle::make('is_enabled')
                    ->label('Kích hoạt')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->default(0),
                KeyValue::make('data')
                    ->label('Dữ liệu (JSON key-value)')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('block_type')
                    ->label('Loại block')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => PageBlock::TYPES[$state] ?? $state),
                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable(),
                IconColumn::make('is_enabled')
                    ->label('Kích hoạt')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),
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
