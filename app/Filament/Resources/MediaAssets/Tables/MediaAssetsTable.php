<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use App\Models\MediaAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('url')
                    ->label('Xem')
                    ->square()
                    ->size(50),
                TextColumn::make('file_name')
                    ->label('Tên tệp')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('folder')
                    ->label('Thư mục')
                    ->badge()
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->label('Loại')
                    ->searchable(),
                TextColumn::make('size')
                    ->label('Dung lượng')
                    ->formatStateUsing(fn ($state) => $state ? round($state / 1024).' KB' : '—')
                    ->sortable(),
                TextColumn::make('dimensions')
                    ->label('Kích thước')
                    ->state(fn (MediaAsset $record) => $record->width ? $record->width.'×'.$record->height : '—'),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('folder')
                    ->label('Thư mục')
                    ->options(fn () => MediaAsset::query()
                        ->whereNotNull('folder')
                        ->distinct()
                        ->pluck('folder', 'folder')
                        ->all()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (MediaAsset $record, DeleteAction $action) {
                        if ($record->isInUse()) {
                            Notification::make()
                                ->danger()
                                ->title('Không thể xoá')
                                ->body('Media đang được sử dụng ở nơi khác.')
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
