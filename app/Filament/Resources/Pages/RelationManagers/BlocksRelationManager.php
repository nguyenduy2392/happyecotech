<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Filament\Support\PageBlockFields;
use App\Http\Middleware\CachePublicHtml;
use App\Models\PageBlock;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->live()
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
                Group::make()
                    ->statePath('data')
                    ->schema(fn (Get $get) => PageBlockFields::for($get('block_type')))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->afterReordering(fn () => CachePublicHtml::invalidate())
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
                EditAction::make()->mutateDataUsing(function (array $data, PageBlock $record): array {
                    // Existing keys outside the typed fields, such as seed notes, survive edits.
                    $data['data'] = array_replace($record->data ?? [], $data['data'] ?? []);

                    return $data;
                }),
                DeleteAction::make(),
            ]);
    }
}
