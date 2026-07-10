<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Models\MenuItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Các mục menu';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Nhãn')
                    ->required()
                    ->maxLength(255),
                Select::make('link_type')
                    ->label('Loại liên kết')
                    ->options(MenuItem::LINK_TYPES)
                    ->default('url')
                    ->required(),
                TextInput::make('url')
                    ->label('URL')
                    ->maxLength(255),
                TextInput::make('route_name')
                    ->label('Tên route')
                    ->maxLength(255),
                TextInput::make('reference_id')
                    ->label('ID tham chiếu')
                    ->numeric(),
                Select::make('parent_id')
                    ->label('Mục cha')
                    ->options(fn (RelationManager $livewire) => MenuItem::where('menu_id', $livewire->getOwnerRecord()->id)
                        ->whereNull('parent_id')
                        ->pluck('label', 'id')
                        ->all())
                    ->nullable(),
                Toggle::make('target_blank')
                    ->label('Mở tab mới'),
                Toggle::make('is_enabled')
                    ->label('Kích hoạt')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('label')
                    ->label('Nhãn')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('link_type')
                    ->label('Loại liên kết')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => MenuItem::LINK_TYPES[$state] ?? $state),
                TextColumn::make('url')
                    ->state(fn ($record) => $record->resolveUrl())
                    ->label('Liên kết')
                    ->limit(40),
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
